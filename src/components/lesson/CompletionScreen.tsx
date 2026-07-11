"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { Flame, Heart, Sparkles } from "@/components/icons";
import { buttonStyles } from "@/components/ui/Button";
import {
  isPerfect,
  type LessonResult,
  type LessonXpBreakdown,
} from "@/lib/game";

function useCountUp(target: number, duration = 900): number {
  const [value, setValue] = useState(0);
  useEffect(() => {
    let raf: number;
    const start = performance.now();
    const tick = (t: number) => {
      const p = Math.min(1, (t - start) / duration);
      setValue(Math.round(target * (1 - Math.pow(1 - p, 3))));
      if (p < 1) raf = requestAnimationFrame(tick);
    };
    raf = requestAnimationFrame(tick);
    return () => cancelAnimationFrame(raf);
  }, [target, duration]);
  return value;
}

const BURSTS = [
  { top: "18%", left: "22%", delay: "0ms", size: 22 },
  { top: "12%", left: "68%", delay: "120ms", size: 18 },
  { top: "32%", left: "84%", delay: "240ms", size: 24 },
  { top: "58%", left: "12%", delay: "180ms", size: 20 },
  { top: "70%", left: "78%", delay: "60ms", size: 18 },
  { top: "44%", left: "50%", delay: "300ms", size: 26 },
];

export function CompletionScreen({
  result,
  breakdown,
  streakCount,
  courseSlug,
}: {
  result: LessonResult;
  breakdown: LessonXpBreakdown;
  streakCount: number;
  courseSlug: string;
}) {
  const perfect = isPerfect(result);
  const displayTotal = useCountUp(breakdown.total);

  return (
    <div className="relative mx-auto flex min-h-dvh w-full max-w-md flex-col items-center justify-center px-6 py-16 text-center">
      <div className="pointer-events-none absolute inset-0" aria-hidden>
        {BURSTS.map((b, i) => (
          <Sparkles
            key={i}
            size={b.size}
            className="animate-burst absolute text-gold-300"
            style={{ top: b.top, left: b.left, animationDelay: b.delay }}
          />
        ))}
      </div>

      <Sparkles size={44} className="text-gold-400" />
      <h1 className="font-display mt-4 text-4xl font-semibold">
        Ritual complete
      </h1>
      {perfect && (
        <p className="mt-1 text-gold-300">Flawless — the cards bow to you.</p>
      )}

      <p
        className="mt-5 text-5xl font-bold text-gold-400"
        data-testid="xp-total"
        aria-label={`${breakdown.total} XP earned`}
      >
        +{displayTotal} XP
      </p>

      <dl className="mt-6 w-full space-y-2 rounded-2xl border border-white/8 bg-midnight-900 p-4 text-sm">
        <div className="flex justify-between">
          <dt className="text-moon-300">
            Correct on the first try ({result.correctFirstTry}/
            {result.totalExercises})
          </dt>
          <dd className="font-semibold">+{breakdown.exerciseXp}</dd>
        </div>
        <div className="flex justify-between">
          <dt className="text-moon-300">
            {result.isPractice ? "Practice ritual" : "Ritual complete"}
          </dt>
          <dd className="font-semibold">+{breakdown.completionXp}</dd>
        </div>
        {breakdown.perfectBonus > 0 && (
          <div className="flex justify-between text-gold-300">
            <dt>Perfect bonus</dt>
            <dd className="font-semibold">+{breakdown.perfectBonus}</dd>
          </div>
        )}
      </dl>

      <p
        className="mt-5 flex items-center gap-2 text-moon-300"
        data-testid="streak-line"
      >
        <Flame size={18} className="text-gold-400" />
        {streakCount <= 1
          ? "Streak started — day 1"
          : `${streakCount}-day streak`}
      </p>
      {perfect && (
        <p className="mt-1 flex items-center gap-1.5 text-sm text-danger-400">
          <Heart size={14} /> +1 heart restored
        </p>
      )}

      <Link
        href={`/learn/${courseSlug}`}
        data-testid="return-to-path"
        className={buttonStyles("primary", "lg", "mt-8 w-full shadow-glow")}
      >
        Return to your path
      </Link>
    </div>
  );
}
