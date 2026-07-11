import { z } from "zod";
import { GAME_CONFIG } from "@/lib/game/config";
import { emptyStreak, type StreakState } from "@/lib/game/streak";
import { fullHearts, type HeartsState } from "@/lib/game/hearts";
import type { XpEvent } from "@/lib/game/xp";

export interface LessonProgress {
  completedAt: string;
  timesCompleted: number;
  /** Best first-try accuracy achieved, 0..1. */
  bestPct: number;
}

export interface ProgressSnapshot {
  version: 1;
  xpTotal: number;
  xpEvents: XpEvent[];
  hearts: HeartsState;
  streak: StreakState;
  /** Keyed by lesson id — presence marks the lesson completed. */
  lessons: Record<string, LessonProgress>;
  activeCourseSlug: string;
}

/**
 * The swappable persistence contract. v0 ships LocalStorageProgressStore;
 * a future SupabaseProgressStore implements the same interface (with a
 * first-login merge) without touching any UI code.
 */
export interface ProgressStore {
  load(): Promise<ProgressSnapshot | null>;
  save(snapshot: ProgressSnapshot): Promise<void>;
  clear(): Promise<void>;
}

export function defaultSnapshot(now: Date): ProgressSnapshot {
  return {
    version: 1,
    xpTotal: 0,
    xpEvents: [],
    hearts: fullHearts(now),
    streak: emptyStreak(),
    lessons: {},
    activeCourseSlug: "tarot",
  };
}

/** Runtime shape guard for persisted snapshots (self-healing on drift). */
export const snapshotSchema = z.object({
  version: z.literal(1),
  xpTotal: z.number().int().nonnegative(),
  xpEvents: z.array(
    z.object({
      id: z.string(),
      at: z.string(),
      amount: z.number(),
      source: z.string(),
      kind: z.enum(["exercise", "lesson", "perfect-bonus"]),
    }),
  ),
  hearts: z.object({
    count: z.number().int().min(0).max(GAME_CONFIG.HEARTS_MAX),
    updatedAt: z.string(),
  }),
  streak: z.object({
    count: z.number().int().nonnegative(),
    lastRitualDate: z.string().nullable(),
    history: z.array(z.string()),
  }),
  lessons: z.record(
    z.string(),
    z.object({
      completedAt: z.string(),
      timesCompleted: z.number().int().positive(),
      bestPct: z.number().min(0).max(1),
    }),
  ),
  activeCourseSlug: z.string(),
});
