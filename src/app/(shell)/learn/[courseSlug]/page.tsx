import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { CourseMap } from "@/components/learn/CourseMap";
import { allCourses, getCourse } from "@/content";

interface Props {
  params: Promise<{ courseSlug: string }>;
}

export function generateStaticParams() {
  return allCourses.map((course) => ({ courseSlug: course.slug }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { courseSlug } = await params;
  return { title: getCourse(courseSlug)?.title ?? "Course" };
}

export default async function CoursePage({ params }: Props) {
  const { courseSlug } = await params;
  const course = getCourse(courseSlug);
  if (!course) notFound();
  return <CourseMap course={course} />;
}
