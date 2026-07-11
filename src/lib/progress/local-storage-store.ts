import {
  snapshotSchema,
  type ProgressSnapshot,
  type ProgressStore,
} from "./types";

export const STORAGE_KEY = "arcana.progress.v1";

type StorageLike = Pick<Storage, "getItem" | "setItem" | "removeItem">;

export class LocalStorageProgressStore implements ProgressStore {
  constructor(private readonly storage: StorageLike) {}

  async load(): Promise<ProgressSnapshot | null> {
    try {
      const raw = this.storage.getItem(STORAGE_KEY);
      if (!raw) return null;
      const parsed = snapshotSchema.safeParse(JSON.parse(raw));
      // Corrupt or outdated payloads heal to a fresh start rather than crash.
      return parsed.success ? parsed.data : null;
    } catch {
      return null;
    }
  }

  async save(snapshot: ProgressSnapshot): Promise<void> {
    this.storage.setItem(STORAGE_KEY, JSON.stringify(snapshot));
  }

  async clear(): Promise<void> {
    this.storage.removeItem(STORAGE_KEY);
  }
}
