import type { Course, Lesson, Unit } from "./types";
import { astrologyCourse } from "./courses/astrology";
import { tarotCourse } from "./courses/tarot";

export const allCourses: Course[] = [tarotCourse, astrologyCourse];

export const DEFAULT_COURSE_SLUG = tarotCourse.slug;

export function getCourse(slug: string): Course | undefined {
  return allCourses.find((c) => c.slug === slug);
}

export interface LessonRef {
  course: Course;
  unit: Unit;
  lesson: Lesson;
}

export function getLessonBySlug(
  courseSlug: string,
  lessonSlug: string,
): LessonRef | undefined {
  const course = getCourse(courseSlug);
  if (!course) return undefined;
  for (const unit of course.units) {
    const lesson = unit.lessons.find((l) => l.slug === lessonSlug);
    if (lesson) return { course, unit, lesson };
  }
  return undefined;
}

export function getLessonById(lessonId: string): LessonRef | undefined {
  for (const course of allCourses) {
    for (const unit of course.units) {
      const lesson = unit.lessons.find((l) => l.id === lessonId);
      if (lesson) return { course, unit, lesson };
    }
  }
  return undefined;
}

/** Course lessons in play order, with their units. */
export function flattenLessons(
  course: Course,
): { unit: Unit; lesson: Lesson }[] {
  return course.units.flatMap((unit) =>
    unit.lessons.map((lesson) => ({ unit, lesson })),
  );
}
