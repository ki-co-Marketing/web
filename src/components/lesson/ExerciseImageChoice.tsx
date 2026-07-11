"use client";

import Image from "next/image";
import type { ImageChoiceExercise } from "@/content/types";
import { cn } from "@/lib/utils";

export function ExerciseImageChoice({
  exercise,
  phase,
  value,
  onChange,
}: {
  exercise: ImageChoiceExercise;
  phase: "answering" | "checked";
  value: string | null;
  onChange: (choiceId: string) => void;
}) {
  return (
    <div
      className="mx-auto grid max-w-xs grid-cols-2 gap-3"
      role="radiogroup"
      aria-label={exercise.prompt}
    >
      {exercise.choices.map((choice, i) => {
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
              "overflow-hidden rounded-xl border-2 transition-all",
              selected
                ? "border-amethyst-400 shadow-glow"
                : "border-midnight-700 hover:border-amethyst-500/60",
              showCorrect && "border-gold-400 shadow-glow-gold",
              showWrong && "border-danger-400 opacity-60",
            )}
          >
            {/* Alt text must not reveal which card is which. */}
            <Image
              src={choice.image}
              alt={choice.label || `Card option ${i + 1}`}
              width={200}
              height={342}
              className="h-auto w-full"
            />
            {choice.label && (
              <span className="block py-1.5 text-center text-sm">
                {choice.label}
              </span>
            )}
          </button>
        );
      })}
    </div>
  );
}
