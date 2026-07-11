import { describe, expect, it } from "vitest";
import {
  appendXpEvents,
  computeLessonXp,
  isPerfect,
  xpEventsForLesson,
  type LessonResult,
  type XpEvent,
} from "./xp";

function result(overrides: Partial<LessonResult> = {}): LessonResult {
  return {
    lessonId: "tarot.u1.l1",
    totalExercises: 7,
    correctFirstTry: 7,
    heartsLost: 0,
    isPractice: false,
    xpReward: 10,
    ...overrides,
  };
}

describe("computeLessonXp", () => {
  it("pays 29 XP for the canonical perfect 7-exercise lesson", () => {
    expect(computeLessonXp(result())).toEqual({
      exerciseXp: 14,
      completionXp: 10,
      perfectBonus: 5,
      total: 29,
    });
  });

  it("drops the perfect bonus when hearts were lost", () => {
    const r = result({ correctFirstTry: 5, heartsLost: 2 });
    expect(computeLessonXp(r)).toEqual({
      exerciseXp: 10,
      completionXp: 10,
      perfectBonus: 0,
      total: 20,
    });
  });

  it("drops the bonus on a heartless miss (match-pairs mismatch)", () => {
    const r = result({ correctFirstTry: 6, heartsLost: 0 });
    expect(isPerfect(r)).toBe(false);
    expect(computeLessonXp(r).total).toBe(22);
  });

  it("pays halved completion and no bonus on practice replays", () => {
    const r = result({ isPractice: true });
    expect(computeLessonXp(r)).toEqual({
      exerciseXp: 14,
      completionXp: 5,
      perfectBonus: 0,
      total: 19,
    });
  });

  it("pays 36 XP for a perfect 8-exercise review lesson (reward 15)", () => {
    const r = result({ totalExercises: 8, correctFirstTry: 8, xpReward: 15 });
    expect(computeLessonXp(r).total).toBe(36);
  });
});

describe("xpEventsForLesson", () => {
  const now = new Date(2026, 6, 11, 12, 0, 0);

  it("emits exercise + lesson + bonus events summing to the total", () => {
    const r = result();
    const breakdown = computeLessonXp(r);
    const events = xpEventsForLesson(r, breakdown, now);
    expect(events.map((e) => e.kind)).toEqual([
      "exercise",
      "lesson",
      "perfect-bonus",
    ]);
    expect(events.reduce((sum, e) => sum + e.amount, 0)).toBe(breakdown.total);
    expect(events.every((e) => e.source === r.lessonId)).toBe(true);
  });

  it("omits zero-amount events", () => {
    const r = result({ correctFirstTry: 0, heartsLost: 5 });
    const events = xpEventsForLesson(r, computeLessonXp(r), now);
    expect(events.map((e) => e.kind)).toEqual(["lesson"]);
  });
});

describe("appendXpEvents", () => {
  it("caps the ledger at 200 most-recent events", () => {
    const mk = (i: number): XpEvent => ({
      id: `e${i}`,
      at: new Date(2026, 0, 1).toISOString(),
      amount: 1,
      source: "x",
      kind: "exercise",
    });
    const existing = Array.from({ length: 199 }, (_, i) => mk(i));
    const appended = appendXpEvents(existing, [mk(199), mk(200), mk(201)]);
    expect(appended).toHaveLength(200);
    expect(appended[0].id).toBe("e2");
    expect(appended.at(-1)?.id).toBe("e201");
  });
});
