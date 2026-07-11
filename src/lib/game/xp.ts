import { GAME_CONFIG } from "./config";

export interface XpEvent {
  id: string;
  /** ISO timestamp */
  at: string;
  amount: number;
  /** Lesson id the XP came from. */
  source: string;
  kind: "exercise" | "lesson" | "perfect-bonus";
}

/** Outcome of one lesson run, produced by the lesson player. */
export interface LessonResult {
  lessonId: string;
  /** Scorable exercises presented. */
  totalExercises: number;
  correctFirstTry: number;
  heartsLost: number;
  /** True when the lesson had already been completed before this run. */
  isPractice: boolean;
  /** The lesson's base completion XP. */
  xpReward: number;
}

export interface LessonXpBreakdown {
  exerciseXp: number;
  completionXp: number;
  perfectBonus: number;
  total: number;
}

export function isPerfect(result: LessonResult): boolean {
  return (
    result.correctFirstTry === result.totalExercises && result.heartsLost === 0
  );
}

export function computeLessonXp(result: LessonResult): LessonXpBreakdown {
  const exerciseXp =
    result.correctFirstTry * GAME_CONFIG.XP_PER_CORRECT_FIRST_TRY;
  const completionXp = result.isPractice
    ? Math.floor(result.xpReward / GAME_CONFIG.PRACTICE_COMPLETION_DIVISOR)
    : result.xpReward;
  const perfectBonus =
    !result.isPractice && isPerfect(result) ? GAME_CONFIG.XP_PERFECT_BONUS : 0;
  return {
    exerciseXp,
    completionXp,
    perfectBonus,
    total: exerciseXp + completionXp + perfectBonus,
  };
}

export function xpEventsForLesson(
  result: LessonResult,
  breakdown: LessonXpBreakdown,
  now: Date,
): XpEvent[] {
  const at = now.toISOString();
  const events: XpEvent[] = [];
  if (breakdown.exerciseXp > 0) {
    events.push({
      id: `${result.lessonId}:exercise:${at}`,
      at,
      amount: breakdown.exerciseXp,
      source: result.lessonId,
      kind: "exercise",
    });
  }
  events.push({
    id: `${result.lessonId}:lesson:${at}`,
    at,
    amount: breakdown.completionXp,
    source: result.lessonId,
    kind: "lesson",
  });
  if (breakdown.perfectBonus > 0) {
    events.push({
      id: `${result.lessonId}:perfect-bonus:${at}`,
      at,
      amount: breakdown.perfectBonus,
      source: result.lessonId,
      kind: "perfect-bonus",
    });
  }
  return events;
}

/** Append while keeping only the most recent XP_EVENTS_CAP entries. */
export function appendXpEvents(
  existing: XpEvent[],
  added: XpEvent[],
): XpEvent[] {
  return [...existing, ...added].slice(-GAME_CONFIG.XP_EVENTS_CAP);
}
