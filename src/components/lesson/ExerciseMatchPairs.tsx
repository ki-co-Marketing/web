"use client";

import { useMemo, useRef, useState } from "react";
import { Check } from "@/components/icons";
import type { MatchPairsExercise } from "@/content/types";
import { cn, seededShuffle } from "@/lib/utils";

/**
 * Self-resolving exercise: reports up once every pair is matched.
 * Mismatches never cost hearts, but any mismatch forfeits the XP.
 */
export function ExerciseMatchPairs({
  exercise,
  disabled,
  onResolved,
}: {
  exercise: MatchPairsExercise;
  disabled: boolean;
  onResolved: (hadMismatch: boolean) => void;
}) {
  const rightOrder = useMemo(
    () => seededShuffle(exercise.pairs, exercise.id),
    [exercise],
  );
  const [selectedLeft, setSelectedLeft] = useState<string | null>(null);
  const [selectedRight, setSelectedRight] = useState<string | null>(null);
  const [matched, setMatched] = useState<ReadonlySet<string>>(new Set());
  const [flash, setFlash] = useState<{ left: string; right: string } | null>(
    null,
  );
  const hadMismatch = useRef(false);

  function resolve(leftId: string, rightId: string) {
    if (leftId === rightId) {
      const next = new Set(matched);
      next.add(leftId);
      setMatched(next);
      setSelectedLeft(null);
      setSelectedRight(null);
      if (next.size === exercise.pairs.length) {
        onResolved(hadMismatch.current);
      }
    } else {
      hadMismatch.current = true;
      setFlash({ left: leftId, right: rightId });
      setTimeout(() => {
        setFlash(null);
        setSelectedLeft(null);
        setSelectedRight(null);
      }, 450);
    }
  }

  const pickLeft = (id: string) => {
    if (disabled || flash || matched.has(id)) return;
    setSelectedLeft(id);
    if (selectedRight !== null) resolve(id, selectedRight);
  };
  const pickRight = (id: string) => {
    if (disabled || flash || matched.has(id)) return;
    setSelectedRight(id);
    if (selectedLeft !== null) resolve(selectedLeft, id);
  };

  const buttonClass = (
    id: string,
    side: "left" | "right",
    selected: boolean,
  ) =>
    cn(
      "flex w-full items-center justify-between gap-2 rounded-xl border-2 px-3 py-3 text-sm font-medium transition-colors sm:text-base",
      matched.has(id)
        ? "border-success-600/40 bg-success-400/10 text-moon-500"
        : selected
          ? "border-amethyst-400 bg-amethyst-500/15 text-moon-100"
          : "border-midnight-700 bg-midnight-900 text-moon-300 hover:border-amethyst-500/60 hover:text-moon-100",
      flash &&
        (side === "left" ? flash.left === id : flash.right === id) &&
        "animate-shake border-danger-400 text-danger-400",
    );

  return (
    <div className="grid grid-cols-2 gap-3">
      <div className="space-y-3">
        {exercise.pairs.map((pair) => (
          <button
            key={pair.id}
            type="button"
            disabled={disabled || matched.has(pair.id)}
            data-testid={`pair-left-${exercise.id}-${pair.id}`}
            onClick={() => pickLeft(pair.id)}
            className={buttonClass(pair.id, "left", selectedLeft === pair.id)}
          >
            <span>{pair.left}</span>
            {matched.has(pair.id) && (
              <Check size={16} className="shrink-0 text-success-400" />
            )}
          </button>
        ))}
      </div>
      <div className="space-y-3">
        {rightOrder.map((pair) => (
          <button
            key={pair.id}
            type="button"
            disabled={disabled || matched.has(pair.id)}
            data-testid={`pair-right-${exercise.id}-${pair.id}`}
            onClick={() => pickRight(pair.id)}
            className={buttonClass(pair.id, "right", selectedRight === pair.id)}
          >
            <span>{pair.right}</span>
            {matched.has(pair.id) && (
              <Check size={16} className="shrink-0 text-success-400" />
            )}
          </button>
        ))}
      </div>
    </div>
  );
}
