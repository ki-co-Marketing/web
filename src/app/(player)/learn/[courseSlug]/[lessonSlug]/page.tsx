import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { LessonPlayer } from "@/components/lesson/LessonPlayer";
import { allCourses, getLessonBySlug } from "@/content";

interface Props {
  params: Promise<{ courseSlug: string; lessonSlug: string }>;
}

export function generateStaticParams() {
  return allCourses.flatMap((course) =>
    course.units
      .filter((unit) => !unit.comingSoon)
      .flatMap((unit) =>
        unit.lessons
          .filter((lesson) => lesson.exercises.length > 0)
          .map((lesson) => ({
            courseSlug: course.slug,
            lessonSlug: lesson.slug,
          })),
      ),
  );
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { courseSlug, lessonSlug } = await params;
  return {
    title: getLessonBySlug(courseSlug, lessonSlug)?.lesson.title ?? "Lesson",
  };
}

export default async function LessonPage({ params }: Props) {
  const { courseSlug, lessonSlug } = await params;
  const ref = getLessonBySlug(courseSlug, lessonSlug);
  if (!ref || ref.unit.comingSoon || ref.lesson.exercises.length === 0) {
    notFound();
  }
  return <LessonPlayer courseSlug={courseSlug} lesson={ref.lesson} />;
}
