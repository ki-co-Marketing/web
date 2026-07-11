import type { ProgressSnapshot, ProgressStore } from "./types";

/** In-memory store for SSR passes and tests. */
export class MemoryProgressStore implements ProgressStore {
  private snapshot: ProgressSnapshot | null = null;

  async load(): Promise<ProgressSnapshot | null> {
    return this.snapshot;
  }

  async save(snapshot: ProgressSnapshot): Promise<void> {
    this.snapshot = structuredClone(snapshot);
  }

  async clear(): Promise<void> {
    this.snapshot = null;
  }
}
