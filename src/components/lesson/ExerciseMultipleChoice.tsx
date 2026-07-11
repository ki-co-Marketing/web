"use client";

import type { MultipleChoiceExercise } from "@/content/types";
import { cn } from "@/lib/utils";

export function ExerciseMultipleChoice({
  exercise,
  phase,
  value,
  onChange,
}: {
  exercise: MultipleChoiceExercise;
  phase: "answering" | "checked";
  value: string | null;
  onChange: (choiceId: string) => void;
}) {
  return (
    <div className="grid gap-3" role="radiogroup" aria-label={exercise.prompt}>
      {exercise.choices.map((choice) => {
        const selected = value === choice.id;
        const isCorrect = choice.id === exercise.correctChoiceId;
        const showCorrect = phase === "checked" && isCorrect;
        const showWrong = phase === "checked" && selected && !isCorrect;
        return (
          <button
            key={choice.id}
            type="button"
            role="radio"
            aria-checked={selected}
            disabled={phase !== "answering"}
            data-testid={`choice-${exercise.id}-${choice.id}`}
            onClick={() => onChange(choice.id)}
            className={cn(
              "rounded-xl border-2 px-4 py-3 text-left font-medium transition-colors",
              selected
                ? "border-amethyst-400 bg-amethyst-500/15 text-moon-100"
                : "border-midnight-700 bg-midnight-900 text-moon-300 hover:border-amethyst-500/60 hover:text-moon-100",
              showCorrect &&
                "border-success-400 bg-success-400/10 text-success-400",
              showWrong && "border-danger-400 bg-danger-400/10 text-danger-400",
            )}
          >
            {choice.text}
          </button>
        );
      })}
    </div>
  );
}
