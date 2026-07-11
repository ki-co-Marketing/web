"use client";

import Link from "next/link";
import { Flame, Heart, MoonCrescent, Star } from "@/components/icons";
import { StatPill } from "@/components/ui/StatPill";
import {
  GAME_CONFIG,
  effectiveStreak,
  isRitualDoneToday,
} from "@/lib/game";
import { useProgress } from "@/lib/progress/ProgressProvider";

export function StatBar() {
  const { snapshot, hydrated } = useProgress();
  const now = new Date();
  const streak = snapshot ? effectiveStreak(snapshot.streak, now) : 0;
  const ritualDone = snapshot
    ? isRitualDoneToday(snapshot.streak, now)
    : false;
  const hearts = snapshot?.hearts.count ?? GAME_CONFIG.HEARTS_MAX;
  const xp = snapshot?.xpTotal ?? 0;

  return (
    <header className="sticky top-0 z-40 border-b border-white/8 bg-midnight-950/80 backdrop-blur">
      <div className="mx-auto flex max-w-3xl items-center justify-between gap-2 px-4 py-3">
        <Link href="/" className="flex items-center gap-1.5">
          <MoonCrescent size={20} className="text-gold-400" />
          <span className="font-display text-lg font-semibold tracking-wide">
            Arcana
          </span>
        </Link>
        <div className="flex items-center gap-2">
          <StatPill
            data-testid="stat-streak"
            label="day streak"
            value={hydrated ? streak : "–"}
            icon={
              <Flame
                size={16}
                className={ritualDone ? "text-gold-400" : "text-moon-500"}
              />
            }
          />
          <StatPill
            data-testid="stat-xp"
            label="total XP"
            value={hydrated ? xp : "–"}
            icon={<Star size={16} className="text-gold-400" />}
          />
          <StatPill
            data-testid="stat-hearts"
            label="hearts"
            value={hydrated ? hearts : "–"}
            icon={<Heart size={16} className="text-danger-400" />}
          />
        </div>
      </div>
    </header>
  );
}
