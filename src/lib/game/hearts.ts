import { GAME_CONFIG } from "./config";

export interface HeartsState {
  count: number;
  /** ISO timestamp of the last mutation — the regen clock's anchor. */
  updatedAt: string;
}

export function fullHearts(now: Date): HeartsState {
  return { count: GAME_CONFIG.HEARTS_MAX, updatedAt: now.toISOString() };
}

/**
 * Lazy regeneration: +1 heart per HEART_REGEN_MINUTES elapsed, clamped to
 * max. Advances updatedAt only by whole consumed intervals so partial
 * progress toward the next heart is never lost.
 */
export function computeHearts(state: HeartsState, now: Date): HeartsState {
  if (state.count >= GAME_CONFIG.HEARTS_MAX) {
    return { count: GAME_CONFIG.HEARTS_MAX, updatedAt: now.toISOString() };
  }
  const anchor = new Date(state.updatedAt).getTime();
  const elapsed = now.getTime() - anchor;
  const regenMs = GAME_CONFIG.HEART_REGEN_MINUTES * 60_000;
  const regenerated = Math.floor(elapsed / regenMs);
  if (regenerated <= 0) return state;
  const count = Math.min(
    GAME_CONFIG.HEARTS_MAX,
    state.count + regenerated,
  );
  const updatedAt =
    count >= GAME_CONFIG.HEARTS_MAX
      ? now.toISOString()
      : new Date(anchor + regenerated * regenMs).toISOString();
  return { count, updatedAt };
}

export function loseHeart(state: HeartsState, now: Date): HeartsState {
  const current = computeHearts(state, now);
  return {
    count: Math.max(0, current.count - 1),
    updatedAt: now.toISOString(),
  };
}

export function addHearts(
  state: HeartsState,
  amount: number,
  now: Date,
): HeartsState {
  const current = computeHearts(state, now);
  return {
    count: Math.min(GAME_CONFIG.HEARTS_MAX, current.count + amount),
    updatedAt: now.toISOString(),
  };
}
