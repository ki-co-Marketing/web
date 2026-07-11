import { RANKS } from "./config";

export interface RankInfo {
  name: string;
  minXp: number;
  next: { name: string; minXp: number } | null;
  /** 0..1 toward the next rank; 1 at the final rank. */
  progress: number;
}

export function getRank(xpTotal: number): RankInfo {
  const xp = Math.max(0, xpTotal);
  let idx = 0;
  for (let i = 0; i < RANKS.length; i++) {
    if (xp >= RANKS[i].minXp) idx = i;
  }
  const rank = RANKS[idx];
  const next = idx + 1 < RANKS.length ? RANKS[idx + 1] : null;
  const progress = next
    ? (xp - rank.minXp) / (next.minXp - rank.minXp)
    : 1;
  return {
    name: rank.name,
    minXp: rank.minXp,
    next: next ? { name: next.name, minXp: next.minXp } : null,
    progress,
  };
}
