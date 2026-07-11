"use client";

import { Button } from "@/components/ui/Button";
import { cn } from "@/lib/utils";

export function CheckBar({
  phase,
  canCheck,
  lastCorrect,
  correctAnswerText,
  explanation,
  isLast,
  onCheck,
  onContinue,
}: {
  phase: "answering" | "checked";
  canCheck: boolean;
  lastCorrect: boolean;
  correctAnswerText: string;
  explanation?: string;
  isLast: boolean;
  onCheck: () => void;
  onContinue: () => void;
}) {
  return (
    <div className="sticky bottom-0 -mx-4 border-t border-white/8 bg-midnight-950/95 px-4 py-4 backdrop-blur">
      <div className="mx-auto max-w-2xl">
        {phase === "answering" ? (
          <Button
            variant="primary"
            size="lg"
            className="w-full"
            disabled={!canCheck}
            onClick={onCheck}
            data-testid="check-button"
          >
            Check
          </Button>
        ) : (
          <div
            aria-live="polite"
            className={cn(
              "animate-rise-fade rounded-2xl border p-4",
              lastCorrect
                ? "border-success-600/40 bg-success-400/10"
                : "border-danger-600/40 bg-danger-400/10",
            )}
          >
            <p
              className={cn(
                "font-display text-xl font-semibold",
                lastCorrect ? "text-success-400" : "text-danger-400",
              )}
              data-testid="check-feedback"
            >
              {lastCorrect ? "✦ Nicely done" : "The cards say otherwise…"}
            </p>
            {!lastCorrect && correctAnswerText && (
              <p className="mt-1 text-sm">
                <span className="text-moon-500">Answer: </span>
                <span className="font-semibold text-moon-100">
                  {correctAnswerText}
                </span>
              </p>
            )}
            {explanation && (
              <p className="mt-1 text-sm text-moon-300">{explanation}</p>
            )}
            <Button
              variant={lastCorrect ? "success" : "danger"}
              size="lg"
              className="mt-3 w-full"
              onClick={onContinue}
              data-testid="continue-button"
            >
              {isLast ? "Finish" : "Continue"}
            </Button>
          </div>
        )}
      </div>
    </div>
  );
}
