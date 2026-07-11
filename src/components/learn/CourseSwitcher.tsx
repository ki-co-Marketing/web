"use client";

import { useRouter } from "next/navigation";
import { allCourses } from "@/content";
import { courseProgress } from "@/lib/game";
import { useProgress } from "@/lib/progress/ProgressProvider";
import { cn } from "@/lib/utils";

export function CourseSwitcher({ activeSlug }: { activeSlug: string }) {
  const { snapshot, actions } = useProgress();
  const router = useRouter();
  const completed = snapshot?.lessons ?? {};

  return (
    <div className="flex gap-2" role="tablist" aria-label="Courses">
      {allCourses.map((course) => {
        const active = course.slug === activeSlug;
        const { done, total } = courseProgress(course, completed);
        return (
          <button
            key={course.slug}
            role="tab"
            aria-selected={active}
            data-testid={`course-chip-${course.slug}`}
            onClick={() => {
              actions.switchCourse(course.slug);
              router.push(`/learn/${course.slug}`);
            }}
            className={cn(
              "rounded-full border px-4 py-1.5 text-sm font-semibold transition-colors",
              active
                ? course.accent === "gold"
                  ? "border-gold-500 bg-midnight-800 text-gold-300"
                  : "border-amethyst-500 bg-midnight-800 text-amethyst-300"
                : "border-white/8 bg-midnight-900 text-moon-500 hover:text-moon-300",
            )}
          >
            {course.title}
            <span className="ml-1.5 text-xs font-normal opacity-70">
              {done}/{total}
            </span>
          </button>
        );
      })}
    </div>
  );
}
