"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";
import { Heart, XMark } from "@/components/icons";
import { ProgressBar } from "@/components/ui/ProgressBar";
import type { Lesson } from "@/content/types";
import {
  GAME_CONFIG,
  computeLessonXp,
  type LessonResult,
  type LessonXpBreakdown,
} from "@/lib/game";
import { useProgress } from "@/lib/progress/ProgressProvider";
import { cn, normalizeAnswer } from "@/lib/utils";
import { CheckBar } from "./CheckBar";
import { CompletionScreen } from "./CompletionScreen";
import { ExerciseFillBlank } from "./ExerciseFillBlank";
import { ExerciseImageChoice } from "./ExerciseImageChoice";
import { ExerciseMatchPairs } from "./ExerciseMatchPairs";
import { ExerciseMultipleChoice } from "./ExerciseMultipleChoice";
import { ExerciseTrueFalse } from "./ExerciseTrueFalse";
import { HeartsOutModal } from "./HeartsOutModal";

type Phase = "answering" | "checked" | "complete";
type AnswerValue = string | boolean;

export function LessonPlayer({
  courseSlug,
  lesson,
}: {
  courseSlug: string;
  lesson: Lesson;
}) {
  const router = useRouter();
  const { snapshot, hydrated, actions } = useProgress();

  const [runId, setRunId] = useState(0);
  const [index, setIndex] = useState(0);
  const [phase, setPhase] = useState<Phase>("answering");
  const [value, setValue] = useState<AnswerValue | null>(null);
  const [lastCorrect, setLastCorrect] = useState(false);
  const [correctFirstTry, setCorrectFirstTry] = useState(0);
  const [heartsLost, setHeartsLost] = useState(0);
  const [outcome, setOutcome] = useState<{
    result: LessonResult;
    breakdown: LessonXpBreakdown;
  } | null>(null);

  const exercises = lesson.exercises;
  const exercise = exercises[Math.min(index, exercises.length - 1)];
  const hearts = snapshot?.hearts.count ?? GAME_CONFIG.HEARTS_MAX;

  if (!hydrated) {
    return (
      <p className="py-24 text-center text-moon-500">Preparing the ritual…</p>
    );
  }

  if (phase === "complete" && outcome) {
    return (
      <CompletionScreen
        result={outcome.result}
        breakdown={outcome.breakdown}
        streakCount={snapshot?.streak.count ?? 1}
        courseSlug={courseSlug}
      />
    );
  }

  const evaluate = (): boolean => {
    switch (exercise.type) {
      case "multiple-choice":
      case "image-choice":
        return value === exercise.correctChoiceId;
      case "true-false":
        return value === exercise.answer;
      case "fill-blank": {
        if (typeof value !== "string") return false;
        const normalized = normalizeAnswer(value);
        return [exercise.answer, ...(exercise.acceptable ?? [])].some(
          (accepted) => normalizeAnswer(accepted) === normalized,
        );
      }
      case "match-pairs":
        return false; // resolves itself via onResolved
    }
  };

  const handleCheck = () => {
    if (phase !== "answering") return;
    const correct = evaluate();
    setLastCorrect(correct);
    setPhase("checked");
    if (correct) {
      setCorrectFirstTry((n) => n + 1);
    } else {
      setHeartsLost((n) => n + 1);
      actions.loseHeart();
    }
  };

  const handlePairsResolved = (hadMismatch: boolean) => {
    if (phase !== "answering") return;
    setLastCorrect(!hadMismatch);
    setPhase("checked");
    if (!hadMismatch) setCorrectFirstTry((n) => n + 1);
  };

  const handleContinue = () => {
    if (index + 1 < exercises.length) {
      setIndex(index + 1);
      setPhase("answering");
      setValue(null);
      return;
    }
    const result: LessonResult = {
      lessonId: lesson.id,
      totalExercises: exercises.length,
      correctFirstTry,
      heartsLost,
      isPractice: lesson.id in (snapshot?.lessons ?? {}),
      xpReward: lesson.xpReward,
    };
    setOutcome({ result, breakdown: computeLessonXp(result) });
    actions.completeLesson(result);
    setPhase("complete");
  };

  const handleRestart = () => {
    actions.refillHearts();
    setRunId((n) => n + 1);
    setIndex(0);
    setPhase("answering");
    setValue(null);
    setLastCorrect(false);
    setCorrectFirstTry(0);
    setHeartsLost(0);
  };

  const handleExit = () => {
    if (
      window.confirm(
        "Leave this ritual? Your progress in this lesson will be lost.",
      )
    ) {
      router.push(`/learn/${courseSlug}`);
    }
  };

  const canCheck =
    phase === "answering" &&
    (exercise.type === "fill-blank"
      ? typeof value === "string" && value.trim() !== ""
      : exercise.type === "match-pairs"
        ? false
        : value !== null);

  const correctAnswerText = (() => {
    switch (exercise.type) {
      case "multiple-choice":
        return (
          exercise.choices.find((c) => c.id === exercise.correctChoiceId)
            ?.text ?? ""
        );
      case "image-choice":
        return (
          exercise.choices.find((c) => c.id === exercise.correctChoiceId)
            ?.label || "the card glowing gold"
        );
      case "true-false":
        return exercise.answer ? "True" : "False";
      case "fill-blank":
        return exercise.answer;
      case "match-pairs":
        return "";
    }
  })();

  const body = (() => {
    const rendererPhase = phase === "checked" ? "checked" : "answering";
    switch (exercise.type) {
      case "multiple-choice":
        return (
          <ExerciseMultipleChoice
            exercise={exercise}
            phase={rendererPhase}
            value={typeof value === "string" ? value : null}
            onChange={setValue}
          />
        );
      case "image-choice":
        return (
          <ExerciseImageChoice
            exercise={exercise}
            phase={rendererPhase}
            value={typeof value === "string" ? value : null}
            onChange={setValue}
          />
        );
      case "true-false":
        return (
          <ExerciseTrueFalse
            exercise={exercise}
            phase={rendererPhase}
            value={typeof value === "boolean" ? value : null}
            onChange={setValue}
          />
        );
      case "fill-blank":
        return (
          <ExerciseFillBlank
            exercise={exercise}
            phase={rendererPhase}
            value={typeof value === "string" ? value : null}
            onChange={setValue}
            onSubmit={handleCheck}
          />
        );
      case "match-pairs":
        return (
          <ExerciseMatchPairs
            exercise={exercise}
            disabled={phase !== "answering"}
            onResolved={handlePairsResolved}
          />
        );
    }
  })();

  return (
    <div className="mx-auto flex min-h-dvh w-full max-w-2xl flex-col px-4">
      <header className="flex items-center gap-3 py-4">
        <button
          type="button"
          aria-label="Exit lesson"
          data-testid="exit-lesson"
          onClick={handleExit}
          className="shrink-0 text-moon-500 transition-colors hover:text-moon-100"
        >
          <XMark size={22} />
        </button>
        <ProgressBar
          value={(phase === "checked" ? index + 1 : index) / exercises.length}
          className="h-4"
        />
        <div
          className="flex shrink-0 items-center gap-1"
          data-testid="hearts-row"
          aria-label={`${hearts} of ${GAME_CONFIG.HEARTS_MAX} hearts`}
        >
          {Array.from({ length: GAME_CONFIG.HEARTS_MAX }, (_, i) => (
            <Heart
              key={i}
              size={18}
              filled={i < hearts}
              className={i < hearts ? "text-danger-400" : "text-midnight-700"}
            />
          ))}
        </div>
      </header>

      <div
        key={`${exercise.id}:${runId}`}
        className="animate-rise-fade flex flex-1 flex-col pt-4 pb-6"
      >
        <p className="text-xs font-semibold tracking-[0.2em] text-moon-500 uppercase">
          {lesson.title} · {index + 1} / {exercises.length}
        </p>
        <h1 className="font-display mt-2 text-2xl font-semibold sm:text-3xl">
          {exercise.prompt}
        </h1>
        <div
          className={cn(
            "mt-6",
            phase === "checked" && !lastCorrect && "animate-shake",
          )}
        >
          {body}
        </div>
      </div>

      <CheckBar
        phase={phase === "checked" ? "checked" : "answering"}
        canCheck={canCheck}
        lastCorrect={lastCorrect}
        correctAnswerText={correctAnswerText}
        explanation={phase === "checked" ? exercise.explanation : undefined}
        isLast={index + 1 >= exercises.length}
        onCheck={handleCheck}
        onContinue={handleContinue}
      />

      <HeartsOutModal
        open={hearts === 0}
        courseSlug={courseSlug}
        onRestart={handleRestart}
      />
    </div>
  );
}
