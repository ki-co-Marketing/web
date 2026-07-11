import type { Course, Lesson, Unit } from "@/content/types";

export type LessonStatus = "locked" | "available" | "completed";

/** Presence of a lesson id as a key marks it completed. */
export type CompletedLessons = Record<string, unknown>;

export function isUnitComplete(
  unit: Unit,
  completed: CompletedLessons,
): boolean {
  return unit.lessons.every((l) => l.id in completed);
}

/**
 * Gating: strictly linear within a unit; a unit opens when every prior
 * unit is fully complete; comingSoon units (and anything after them)
 * are always locked. Completed lessons stay accessible for practice.
 */
export function getLessonStatus(
  course: Course,
  lessonId: string,
  completed: CompletedLessons,
): LessonStatus {
  let priorUnitsComplete = true;
  for (const unit of course.units) {
    for (let i = 0; i < unit.lessons.length; i++) {
      const lesson = unit.lessons[i];
      if (lesson.id !== lessonId) continue;
      if (lesson.id in completed) return "completed";
      if (unit.comingSoon || !priorUnitsComplete) return "locked";
      const priorInUnit = unit.lessons.slice(0, i);
      return priorInUnit.every((l) => l.id in completed)
        ? "available"
        : "locked";
    }
    priorUnitsComplete =
      priorUnitsComplete && !unit.comingSoon && isUnitComplete(unit, completed);
  }
  return "locked";
}

/** First lesson the learner can act on (available beats practice). */
export function firstAvailableLesson(
  course: Course,
  completed: CompletedLessons,
): { unit: Unit; lesson: Lesson } | null {
  for (const unit of course.units) {
    for (const lesson of unit.lessons) {
      if (getLessonStatus(course, lesson.id, completed) === "available") {
        return { unit, lesson };
      }
    }
  }
  return null;
}

/** Completed / playable counts across a course (comingSoon excluded). */
export function courseProgress(
  course: Course,
  completed: CompletedLessons,
): { done: number; total: number } {
  let done = 0;
  let total = 0;
  for (const unit of course.units) {
    if (unit.comingSoon) continue;
    for (const lesson of unit.lessons) {
      total += 1;
      if (lesson.id in completed) done += 1;
    }
  }
  return { done, total };
}
