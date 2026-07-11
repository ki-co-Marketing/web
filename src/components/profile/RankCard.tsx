import { Sparkles } from "@/components/icons";
import { Card } from "@/components/ui/Card";
import { ProgressBar } from "@/components/ui/ProgressBar";
import { getRank } from "@/lib/game";

export function RankCard({ xpTotal }: { xpTotal: number }) {
  const rank = getRank(xpTotal);
  return (
    <Card className="border-amethyst-500/30 bg-gradient-to-br from-amethyst-600/25 to-midnight-900 p-6 text-center">
      <p className="text-xs font-semibold tracking-[0.25em] text-moon-300 uppercase">
        Your rank
      </p>
      <h1
        className="font-display mt-1 text-5xl font-semibold text-moon-100"
        data-testid="rank-name"
      >
        {rank.name}
      </h1>
      {rank.next ? (
        <div className="mx-auto mt-4 max-w-xs">
          <ProgressBar
            value={rank.progress}
            barClassName="bg-amethyst-400"
          />
          <p className="mt-2 text-sm text-moon-300">
            {xpTotal} / {rank.next.minXp} XP to{" "}
            <span className="font-semibold text-amethyst-300">
              {rank.next.name}
            </span>
          </p>
        </div>
      ) : (
        <p className="mt-3 flex items-center justify-center gap-1 text-sm text-gold-300">
          <Sparkles size={16} /> The highest rank — the stars know your name.
        </p>
      )}
    </Card>
  );
}
