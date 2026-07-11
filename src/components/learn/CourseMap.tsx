"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { Star, XMark } from "@/components/icons";
import { buttonStyles } from "@/components/ui/Button";
import type { Course } from "@/content/types";
import {
  computeLessonXp,
  firstAvailableLesson,
  getLessonStatus,
} from "@/lib/game";
import { useProgress } from "@/lib/progress/ProgressProvider";
import { CourseSwitcher } from "./CourseSwitcher";
import { LessonNode } from "./LessonNode";
import { UnitBanner } from "./UnitBanner";

/** Winding-path horizontal offsets, cycled per lesson within a unit. */
const OFFSETS = [0, 56, 0, -56];

export function CourseMap({ course }: { course: Course }) {
  const { snapshot, hydrated, actions } = useProgress();
  const completed = snapshot?.lessons ?? {};
  // undefined = untouched → default to the frontier lesson once hydrated.
  const [selectedIdState, setSelectedId] = useState<string | null | undefined>(
    undefined,
  );
  const selectedId =
    selectedIdState === undefined
      ? hydrated
        ? (firstAvailableLesson(course, completed)?.lesson.id ?? null)
        : null
      : selectedIdState;

  // Keep the persisted active course in sync with direct navigation.
  useEffect(() => {
    if (hydrated) actions.switchCourse(course.slug);
  }, [hydrated, course.slug, actions]);

  const selected = course.units
    .flatMap((u) => u.lessons)
    .find((l) => l.id === selectedId);
  const selectedStatus = selected
    ? getLessonStatus(course, selected.id, completed)
    : null;
  const selectedIsPractice = selectedStatus === "completed";
  const selectedXp = selected
    ? computeLessonXp({
        lessonId: selected.id,
        totalExercises: selected.exercises.length,
        correctFirstTry: selected.exercises.length,
        heartsLost: 0,
        isPractice: selectedIsPractice,
        xpReward: selected.xpReward,
      }).total
    : 0;

  return (
    <div className="py-6">
      <CourseSwitcher activeSlug={course.slug} />

      <div className="mt-6 space-y-10">
        {course.units.map((unit, unitIndex) => (
          <section key={unit.id} aria-label={unit.title}>
            <UnitBanner unit={unit} index={unitIndex} accent={course.accent} />
            <div className="mt-6 space-y-5">
              {unit.lessons.map((lesson, lessonIndex) => (
                <LessonNode
                  key={lesson.id}
                  lesson={lesson}
                  status={
                    hydrated
                      ? getLessonStatus(course, lesson.id, completed)
                      : "locked"
                  }
                  offset={OFFSETS[lessonIndex % OFFSETS.length]}
                  selected={selectedId === lesson.id}
                  onSelect={() =>
                    setSelectedId((prev) =>
                      prev === lesson.id ? null : lesson.id,
                    )
                  }
                />
              ))}
            </div>
          </section>
        ))}
      </div>

      {selected && selectedStatus !== "locked" && (
        <div className="animate-rise-fade fixed inset-x-0 bottom-16 z-30 px-4 pb-2">
          <div className="mx-auto flex max-w-3xl items-center justify-between gap-4 rounded-2xl border border-white/8 bg-midnight-800 p-4 shadow-glow">
            <div className="min-w-0">
              <h3 className="font-display truncate text-xl font-semibold">
                {selected.title}
              </h3>
              <p className="truncate text-sm text-moon-300">
                {selected.description}
              </p>
            </div>
            <div className="flex shrink-0 items-center gap-2">
              <Link
                href={`/learn/${course.slug}/${selected.slug}`}
                data-testid="start-lesson"
                className={buttonStyles(
                  selectedIsPractice ? "gold" : "primary",
                  "md",
                )}
              >
                <Star size={16} />
                {selectedIsPractice ? "Practice" : "Start"} +{selectedXp} XP
              </Link>
              <button
                type="button"
                aria-label="Close"
                onClick={() => setSelectedId(null)}
                className="rounded-full p-1.5 text-moon-500 hover:text-moon-100"
              >
                <XMark size={18} />
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
