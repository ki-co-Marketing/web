import { describe, expect, it } from "vitest";
import { addHearts, computeHearts, fullHearts, loseHeart } from "./hearts";

const T0 = new Date(2026, 6, 11, 12, 0, 0);
const minutes = (n: number) => new Date(T0.getTime() + n * 60_000);

describe("hearts", () => {
  it("starts full at 5", () => {
    expect(fullHearts(T0).count).toBe(5);
  });

  it("does not regenerate before 30 minutes", () => {
    const state = { count: 2, updatedAt: T0.toISOString() };
    expect(computeHearts(state, minutes(29)).count).toBe(2);
  });

  it("regenerates exactly one heart at 30 minutes", () => {
    const state = { count: 2, updatedAt: T0.toISOString() };
    const next = computeHearts(state, minutes(30));
    expect(next.count).toBe(3);
    expect(next.updatedAt).toBe(minutes(30).toISOString());
  });

  it("keeps partial progress toward the next heart", () => {
    const state = { count: 3, updatedAt: T0.toISOString() };
    const next = computeHearts(state, minutes(45));
    expect(next.count).toBe(4);
    // anchor advanced by one whole interval; 15 min credit retained
    expect(next.updatedAt).toBe(minutes(30).toISOString());
    expect(computeHearts(next, minutes(60)).count).toBe(5);
  });

  it("clamps regeneration at 5", () => {
    const state = { count: 0, updatedAt: T0.toISOString() };
    const next = computeHearts(state, minutes(200));
    expect(next.count).toBe(5);
    expect(next.updatedAt).toBe(minutes(200).toISOString());
  });

  it("loseHeart floors at 0", () => {
    const zero = loseHeart(
      { count: 0, updatedAt: T0.toISOString() },
      minutes(1),
    );
    expect(zero.count).toBe(0);
  });

  it("loseHeart applies pending regen first", () => {
    const state = { count: 1, updatedAt: T0.toISOString() };
    // 65 minutes later two hearts regenerated (3), then one lost → 2
    expect(loseHeart(state, minutes(65)).count).toBe(2);
  });

  it("addHearts clamps at 5", () => {
    const state = { count: 5, updatedAt: T0.toISOString() };
    expect(addHearts(state, 1, minutes(1)).count).toBe(5);
  });
});
