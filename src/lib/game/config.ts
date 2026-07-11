/**
 * Every gameplay tunable in one place. docs/GAMEPLAY.md mirrors this
 * table — update both together.
 */
export const GAME_CONFIG = {
  /** XP per exercise answered correctly on the first attempt. */
  XP_PER_CORRECT_FIRST_TRY: 2,
  /** Bonus when every exercise is first-try correct and no hearts lost. */
  XP_PERFECT_BONUS: 5,
  /** Practice (replay) completion pays floor(xpReward / divisor). */
  PRACTICE_COMPLETION_DIVISOR: 2,
  /** Maximum and starting hearts. */
  HEARTS_MAX: 5,
  /** One heart regenerates per this many minutes (lazy computation). */
  HEART_REGEN_MINUTES: 30,
  /** Hearts restored for a perfect lesson. */
  PERFECT_LESSON_HEART_BONUS: 1,
  /** XP event ledger cap kept in the snapshot. */
  XP_EVENTS_CAP: 200,
  /** Days of ritual history kept for the profile calendar. */
  STREAK_HISTORY_CAP: 60,
} as const;

export const RANKS = [
  { name: "Seeker", minXp: 0 },
  { name: "Initiate", minXp: 30 },
  { name: "Apprentice", minXp: 75 },
  { name: "Adept", minXp: 150 },
  { name: "Mystic", minXp: 250 },
  { name: "Sage", minXp: 400 },
  { name: "Oracle", minXp: 600 },
  { name: "Luminary", minXp: 850 },
] as const;
