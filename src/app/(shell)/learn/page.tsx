"use client";

import { useRouter } from "next/navigation";
import { useEffect } from "react";
import { DEFAULT_COURSE_SLUG, getCourse } from "@/content";
import { useProgress } from "@/lib/progress/ProgressProvider";

/** Client redirect — the active course lives in local progress. */
export default function LearnIndexPage() {
  const { snapshot, hydrated } = useProgress();
  const router = useRouter();

  useEffect(() => {
    if (!hydrated) return;
    const slug = snapshot?.activeCourseSlug;
    const target = slug && getCourse(slug) ? slug : DEFAULT_COURSE_SLUG;
    router.replace(`/learn/${target}`);
  }, [hydrated, snapshot?.activeCourseSlug, router]);

  return (
    <p className="py-24 text-center text-moon-500">Consulting the stars…</p>
  );
}
