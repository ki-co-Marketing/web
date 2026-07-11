"use client";

import type { TrueFalseExercise } from "@/content/types";
import { cn } from "@/lib/utils";

export function ExerciseTrueFalse({
  exercise,
  phase,
  value,
  onChange,
}: {
  exercise: TrueFalseExercise;
  phase: "answering" | "checked";
  value: boolean | null;
  onChange: (answer: boolean) => void;
}) {
  return (
    <div>
      <p className="rounded-xl border border-white/8 bg-midnight-900 p-4 text-lg">
        “{exercise.statement}”
      </p>
      <div className="mt-4 grid grid-cols-2 gap-3">
        {([true, false] as const).map((option) => {
          const selected = value === option;
          const isCorrect = option === exercise.answer;
          const showCorrect = phase === "checked" && isCorrect;
          const showWrong = phase === "checked" && selected && !isCorrect;
          return (
            <button
              key={String(option)}
              type="button"
              disabled={phase !== "answering"}
              data-testid={`choice-${exercise.id}-${option}`}
              onClick={() => onChange(option)}
              className={cn(
                "rounded-xl border-2 px-4 py-4 text-lg font-semibold transition-colors",
                selected
                  ? "border-amethyst-400 bg-amethyst-500/15"
                  : "border-midnight-700 bg-midnight-900 text-moon-300 hover:border-amethyst-500/60 hover:text-moon-100",
                showCorrect &&
                  "border-success-400 bg-success-400/10 text-success-400",
                showWrong &&
                  "border-danger-400 bg-danger-400/10 text-danger-400",
              )}
            >
              {option ? "True" : "False"}
            </button>
          );
        })}
      </div>
    </div>
  );
}
