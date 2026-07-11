"use client";

import type { FillBlankExercise } from "@/content/types";
import { cn } from "@/lib/utils";

export function ExerciseFillBlank({
  exercise,
  phase,
  value,
  onChange,
  onSubmit,
}: {
  exercise: FillBlankExercise;
  phase: "answering" | "checked";
  value: string | null;
  onChange: (text: string) => void;
  onSubmit: () => void;
}) {
  return (
    <p className="rounded-xl border border-white/8 bg-midnight-900 p-5 text-lg leading-relaxed">
      {exercise.before}{" "}
      <input
        type="text"
        autoFocus
        disabled={phase !== "answering"}
        value={value ?? ""}
        data-testid={`fill-input-${exercise.id}`}
        onChange={(e) => onChange(e.target.value)}
        onKeyDown={(e) => {
          if (e.key === "Enter" && (value ?? "").trim() !== "") onSubmit();
        }}
        aria-label="Your answer"
        className={cn(
          "mx-1 inline-block w-36 border-b-2 bg-transparent text-center font-semibold outline-none",
          phase === "answering"
            ? "border-amethyst-400 focus:border-gold-400"
            : "border-moon-500",
        )}
      />{" "}
      {exercise.after}
    </p>
  );
}
