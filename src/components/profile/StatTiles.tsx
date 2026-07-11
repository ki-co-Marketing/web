import type { ReactNode } from "react";
import { Check, Flame, Sparkles, Star } from "@/components/icons";
import { Card } from "@/components/ui/Card";
import type { ProgressSnapshot } from "@/lib/progress/types";
import { effectiveStreak } from "@/lib/game";

function Tile({
  icon,
  value,
  label,
  testId,
}: {
  icon: ReactNode;
  value: number;
  label: string;
  testId: string;
}) {
  return (
    <Card className="p-4">
      <div className="flex items-center gap-3">
        {icon}
        <div>
          <p className="text-2xl font-bold" data-testid={testId}>
            {value}
          </p>
          <p className="text-xs text-moon-500">{label}</p>
        </div>
      </div>
    </Card>
  );
}

export function StatTiles({ snapshot }: { snapshot: ProgressSnapshot }) {
  const lessons = Object.values(snapshot.lessons);
  const perfected = lessons.filter((l) => l.bestPct === 1).length;
  return (
    <div className="grid grid-cols-2 gap-4">
      <Tile
        icon={<Flame size={26} className="text-gold-400" />}
        value={effectiveStreak(snapshot.streak, new Date())}
        label="day streak"
        testId="tile-streak"
      />
      <Tile
        icon={<Star size={26} className="text-gold-400" />}
        value={snapshot.xpTotal}
        label="total XP"
        testId="tile-xp"
      />
      <Tile
        icon={<Check size={26} className="text-success-400" />}
        value={lessons.length}
        label="lessons completed"
        testId="tile-lessons"
      />
      <Tile
        icon={<Sparkles size={26} className="text-amethyst-300" />}
        value={perfected}
        label="lessons perfected"
        testId="tile-perfected"
      />
    </div>
  );
}
