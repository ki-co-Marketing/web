import { describe, expect, it } from "vitest";
import type { Course, Lesson } from "@/content/types";
import {
  courseProgress,
  firstAvailableLesson,
  getLessonStatus,
} from "./unlocks";

const lesson = (id: string): Lesson => ({
  id,
  slug: id.replace(/\./g, "-"),
  title: id,
  description: "test",
  xpReward: 10,
  exercises: [],
});

const course: Course = {
  id: "course_test",
  slug: "test",
  title: "Test",
  tagline: "test",
  icon: "tarot",
  accent: "gold",
  units: [
    {
      id: "t.u1",
      slug: "unit-1",
      title: "Unit 1",
      description: "test",
      lessons: [lesson("t.u1.l1"), lesson("t.u1.l2")],
    },
    {
      id: "t.u2",
      slug: "unit-2",
      title: "Unit 2",
      description: "test",
      lessons: [lesson("t.u2.l1")],
    },
    {
      id: "t.u3",
      slug: "unit-3",
      title: "Unit 3",
      description: "test",
      comingSoon: true,
      lessons: [lesson("t.u3.l1")],
    },
  ],
};

describe("getLessonStatus", () => {
  it("makes only the first lesson available on a fresh course", () => {
    expect(getLessonStatus(course, "t.u1.l1", {})).toBe("available");
    expect(getLessonStatus(course, "t.u1.l2", {})).toBe("locked");
    expect(getLessonStatus(course, "t.u2.l1", {})).toBe("locked");
  });

  it("unlocks linearly within a unit", () => {
    const completed = { "t.u1.l1": true };
    expect(getLessonStatus(course, "t.u1.l1", completed)).toBe("completed");
    expect(getLessonStatus(course, "t.u1.l2", completed)).toBe("available");
    expect(getLessonStatus(course, "t.u2.l1", completed)).toBe("locked");
  });

  it("opens the next unit only when the previous is fully complete", () => {
    const completed = { "t.u1.l1": true, "t.u1.l2": true };
    expect(getLessonStatus(course, "t.u2.l1", completed)).toBe("available");
  });

  it("keeps comingSoon units locked no matter the progress", () => {
    const completed = {
      "t.u1.l1": true,
      "t.u1.l2": true,
      "t.u2.l1": true,
    };
    expect(getLessonStatus(course, "t.u3.l1", completed)).toBe("locked");
  });

  it("treats unknown lesson ids as locked", () => {
    expect(getLessonStatus(course, "nope", {})).toBe("locked");
  });
});

describe("firstAvailableLesson / courseProgress", () => {
  it("finds the frontier lesson", () => {
    expect(firstAvailableLesson(course, {})?.lesson.id).toBe("t.u1.l1");
    expect(
      firstAvailableLesson(course, { "t.u1.l1": true })?.lesson.id,
    ).toBe("t.u1.l2");
  });

  it("returns null when everything playable is done", () => {
    const completed = {
      "t.u1.l1": true,
      "t.u1.l2": true,
      "t.u2.l1": true,
    };
    expect(firstAvailableLesson(course, completed)).toBeNull();
  });

  it("counts playable lessons only (comingSoon excluded)", () => {
    expect(courseProgress(course, { "t.u1.l1": true })).toEqual({
      done: 1,
      total: 3,
    });
  });
});
