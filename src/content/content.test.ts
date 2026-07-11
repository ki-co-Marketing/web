import { existsSync } from "node:fs";
import path from "node:path";
import { describe, expect, it } from "vitest";
import { cardImage, majorArcana } from "./courses/tarot-cards";
import { allCourses, getLessonBySlug } from "./index";
import { courseSchema } from "./schema";

const PUBLIC_DIR = path.resolve(process.cwd(), "public");

function allLessons() {
  return allCourses.flatMap((course) =>
    course.units.flatMap((unit) =>
      unit.lessons.map((lesson) => ({ course, unit, lesson })),
    ),
  );
}

describe("content validation", () => {
  it("every course parses against the schema", () => {
    for (const course of allCourses) {
      const parsed = courseSchema.safeParse(course);
      if (!parsed.success) {
        throw new Error(
          `course ${course.slug} invalid:\n${parsed.error.message}`,
        );
      }
    }
  });

  it("course slugs and ids are unique", () => {
    const slugs = allCourses.map((c) => c.slug);
    const ids = allCourses.map((c) => c.id);
    expect(new Set(slugs).size).toBe(slugs.length);
    expect(new Set(ids).size).toBe(ids.length);
  });

  it("unit, lesson, and exercise ids are globally unique", () => {
    const ids: string[] = [];
    for (const course of allCourses) {
      for (const unit of course.units) {
        ids.push(unit.id);
        for (const lesson of unit.lessons) {
          ids.push(lesson.id);
          for (const exercise of lesson.exercises) {
            ids.push(exercise.id);
          }
        }
      }
    }
    const dupes = ids.filter((id, i) => ids.indexOf(id) !== i);
    expect(dupes).toEqual([]);
  });

  it("lesson slugs are unique within each course (they are route segments)", () => {
    for (const course of allCourses) {
      const slugs = course.units.flatMap((u) => u.lessons.map((l) => l.slug));
      const dupes = slugs.filter((s, i) => slugs.indexOf(s) !== i);
      expect(dupes, `course ${course.slug}`).toEqual([]);
    }
  });

  it("ids follow the prefix chain course-slug → unit → lesson → exercise", () => {
    for (const course of allCourses) {
      for (const unit of course.units) {
        expect(unit.id.startsWith(`${course.slug}.`), unit.id).toBe(true);
        for (const lesson of unit.lessons) {
          expect(lesson.id.startsWith(`${unit.id}.`), lesson.id).toBe(true);
          for (const exercise of lesson.exercises) {
            expect(
              exercise.id.startsWith(`${lesson.id}.`),
              exercise.id,
            ).toBe(true);
          }
        }
      }
    }
  });

  it("every referenced exercise image exists on disk", () => {
    for (const { lesson } of allLessons()) {
      for (const exercise of lesson.exercises) {
        if (exercise.type !== "image-choice") continue;
        for (const choice of exercise.choices) {
          const file = path.join(PUBLIC_DIR, choice.image);
          expect(existsSync(file), `${exercise.id} → ${choice.image}`).toBe(
            true,
          );
        }
      }
    }
  });

  it("playable lessons have at least 3 exercises", () => {
    for (const { unit, lesson } of allLessons()) {
      if (unit.comingSoon) continue;
      expect(
        lesson.exercises.length,
        `${lesson.id} has too few exercises`,
      ).toBeGreaterThanOrEqual(3);
    }
  });

  it("lesson lookups by slug resolve", () => {
    expect(
      getLessonBySlug("tarot", "what-is-tarot")?.lesson.id,
    ).toBe("tarot.u1.l1");
    expect(
      getLessonBySlug("astrology", "the-wheel-of-twelve")?.lesson.id,
    ).toBe("astrology.u1.l1");
    expect(getLessonBySlug("tarot", "nope")).toBeUndefined();
  });
});

describe("major arcana fact table", () => {
  it("holds all 22 cards, numbered 0–21 without gaps", () => {
    expect(majorArcana).toHaveLength(22);
    const numbers = majorArcana.map((c) => c.number).sort((a, b) => a - b);
    expect(numbers).toEqual(Array.from({ length: 22 }, (_, i) => i));
    const ids = majorArcana.map((c) => c.id);
    expect(new Set(ids).size).toBe(22);
  });

  it("every card image exists on disk (the Grimoire shows them all)", () => {
    for (const card of majorArcana) {
      const file = path.join(PUBLIC_DIR, cardImage(card));
      expect(existsSync(file), `${card.id} → ${cardImage(card)}`).toBe(true);
    }
  });

  it("every card carries three keywords and a symbol note", () => {
    for (const card of majorArcana) {
      expect(card.keywords).toHaveLength(3);
      expect(card.symbol.length).toBeGreaterThan(10);
    }
  });
});
