import { localDateString } from "@/lib/utils";
import { GAME_CONFIG } from "./config";

export interface StreakState {
  count: number;
  /** Local YYYY-MM-DD of the last ritual day, null before the first. */
  lastRitualDate: string | null;
  /** Recent ritual days (local YYYY-MM-DD), capped, oldest first. */
  history: string[];
}

export function emptyStreak(): StreakState {
  return { count: 0, lastRitualDate: null, history: [] };
}

function isYesterday(dateStr: string, now: Date): boolean {
  const yesterday = new Date(now);
  yesterday.setDate(yesterday.getDate() - 1);
  return localDateString(yesterday) === dateStr;
}

/**
 * Called when a lesson is completed. A "ritual day" counts once:
 * same day → unchanged; yesterday → +1; anything else → reset to 1.
 */
export function advanceStreak(state: StreakState, now: Date): StreakState {
  const today = localDateString(now);
  if (state.lastRitualDate === today) return state;
  const count =
    state.lastRitualDate !== null && isYesterday(state.lastRitualDate, now)
      ? state.count + 1
      : 1;
  const history = [...state.history, today].slice(
    -GAME_CONFIG.STREAK_HISTORY_CAP,
  );
  return { count, lastRitualDate: today, history };
}

/** Streak to display: 0 once the chain is broken (last ritual before yesterday). */
export function effectiveStreak(state: StreakState, now: Date): number {
  if (state.lastRitualDate === null) return 0;
  const today = localDateString(now);
  if (
    state.lastRitualDate === today ||
    isYesterday(state.lastRitualDate, now)
  ) {
    return state.count;
  }
  return 0;
}

export function isRitualDoneToday(state: StreakState, now: Date): boolean {
  return state.lastRitualDate === localDateString(now);
}
