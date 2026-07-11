import { describe, expect, it } from "vitest";
import { defaultSnapshot } from "./types";
import { LocalStorageProgressStore, STORAGE_KEY } from "./local-storage-store";

function fakeStorage(initial: Record<string, string> = {}) {
  const map = new Map(Object.entries(initial));
  return {
    getItem: (k: string) => map.get(k) ?? null,
    setItem: (k: string, v: string) => void map.set(k, v),
    removeItem: (k: string) => void map.delete(k),
  };
}

const now = new Date(2026, 6, 11, 12);

describe("LocalStorageProgressStore", () => {
  it("round-trips a snapshot", async () => {
    const store = new LocalStorageProgressStore(fakeStorage());
    const snapshot = defaultSnapshot(now);
    snapshot.xpTotal = 42;
    snapshot.lessons["tarot.u1.l1"] = {
      completedAt: now.toISOString(),
      timesCompleted: 1,
      bestPct: 1,
    };
    await store.save(snapshot);
    expect(await store.load()).toEqual(snapshot);
  });

  it("returns null when nothing is stored", async () => {
    const store = new LocalStorageProgressStore(fakeStorage());
    expect(await store.load()).toBeNull();
  });

  it("self-heals corrupt JSON to null", async () => {
    const store = new LocalStorageProgressStore(
      fakeStorage({ [STORAGE_KEY]: "{not json" }),
    );
    expect(await store.load()).toBeNull();
  });

  it("rejects snapshots with an unknown version", async () => {
    const bad = JSON.stringify({ ...defaultSnapshot(now), version: 99 });
    const store = new LocalStorageProgressStore(
      fakeStorage({ [STORAGE_KEY]: bad }),
    );
    expect(await store.load()).toBeNull();
  });

  it("clears stored progress", async () => {
    const storage = fakeStorage();
    const store = new LocalStorageProgressStore(storage);
    await store.save(defaultSnapshot(now));
    await store.clear();
    expect(await store.load()).toBeNull();
  });
});
