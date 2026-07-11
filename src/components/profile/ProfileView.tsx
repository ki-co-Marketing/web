"use client";

import { useProgress } from "@/lib/progress/ProgressProvider";
import { BadgesRow } from "./BadgesRow";
import { RankCard } from "./RankCard";
import { StatTiles } from "./StatTiles";
import { StreakCalendar } from "./StreakCalendar";

export function ProfileView() {
  const { snapshot, hydrated, actions } = useProgress();

  if (!hydrated || !snapshot) {
    return (
      <p className="py-24 text-center text-moon-500">Reading your chart…</p>
    );
  }

  return (
    <div className="space-y-6 py-6">
      <RankCard xpTotal={snapshot.xpTotal} />
      <StatTiles snapshot={snapshot} />
      <StreakCalendar history={snapshot.streak.history} />
      <BadgesRow />
      <div className="pb-4 text-center">
        <button
          type="button"
          data-testid="reset-progress"
          onClick={() => {
            if (
              window.confirm(
                "Scatter your progress to the winds? This clears XP, streaks, and completed lessons on this device.",
              )
            ) {
              actions.resetProgress();
            }
          }}
          className="text-sm text-danger-400 underline-offset-4 hover:underline"
        >
          Reset progress
        </button>
      </div>
    </div>
  );
}
