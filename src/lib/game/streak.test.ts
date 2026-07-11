import { describe, expect, it } from "vitest";
import {
  advanceStreak,
  effectiveStreak,
  emptyStreak,
  isRitualDoneToday,
} from "./streak";

const day = (y: number, m: number, d: number) => new Date(y, m - 1, d, 20, 0);

describe("advanceStreak", () => {
  it("starts a streak at 1 on the first ritual", () => {
    const s = advanceStreak(emptyStreak(), day(2026, 7, 11));
    expect(s).toEqual({
      count: 1,
      lastRitualDate: "2026-07-11",
      history: ["2026-07-11"],
    });
  });

  it("is idempotent within the same day", () => {
    const once = advanceStreak(emptyStreak(), day(2026, 7, 11));
    const twice = advanceStreak(once, new Date(2026, 6, 11, 23, 59));
    expect(twice.count).toBe(1);
    expect(twice.history).toHaveLength(1);
  });

  it("increments on consecutive days", () => {
    let s = advanceStreak(emptyStreak(), day(2026, 7, 10));
    s = advanceStreak(s, day(2026, 7, 11));
    expect(s.count).toBe(2);
  });

  it("resets to 1 after a gap", () => {
    let s = advanceStreak(emptyStreak(), day(2026, 7, 8));
    s = advanceStreak(s, day(2026, 7, 11));
    expect(s.count).toBe(1);
  });

  it("crosses month boundaries", () => {
    let s = advanceStreak(emptyStreak(), day(2026, 1, 31));
    s = advanceStreak(s, day(2026, 2, 1));
    expect(s.count).toBe(2);
  });

  it("crosses year boundaries", () => {
    let s = advanceStreak(emptyStreak(), day(2025, 12, 31));
    s = advanceStreak(s, day(2026, 1, 1));
    expect(s.count).toBe(2);
  });

  it("caps history at 60 days", () => {
    let s = emptyStreak();
    for (let i = 0; i < 70; i++) {
      s = advanceStreak(s, new Date(2026, 0, 1 + i, 20));
    }
    expect(s.count).toBe(70);
    expect(s.history).toHaveLength(60);
  });
});

describe("effectiveStreak / isRitualDoneToday", () => {
  it("shows the count on the ritual day and the day after", () => {
    const s = advanceStreak(emptyStreak(), day(2026, 7, 10));
    expect(effectiveStreak(s, day(2026, 7, 10))).toBe(1);
    expect(effectiveStreak(s, day(2026, 7, 11))).toBe(1);
  });

  it("shows 0 once the chain is broken", () => {
    const s = advanceStreak(emptyStreak(), day(2026, 7, 8));
    expect(effectiveStreak(s, day(2026, 7, 11))).toBe(0);
  });

  it("reports whether today's ritual is done", () => {
    const s = advanceStreak(emptyStreak(), day(2026, 7, 11));
    expect(isRitualDoneToday(s, day(2026, 7, 11))).toBe(true);
    expect(isRitualDoneToday(s, day(2026, 7, 12))).toBe(false);
  });
});
