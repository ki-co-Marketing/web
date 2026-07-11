import { expect, type Page } from "@playwright/test";
import type { Exercise } from "../src/content/types";

/** Answer one exercise correctly, deriving the answer from content data. */
export async function answerCorrectly(page: Page, exercise: Exercise) {
  switch (exercise.type) {
    case "multiple-choice":
    case "image-choice":
      await page
        .getByTestId(`choice-${exercise.id}-${exercise.correctChoiceId}`)
        .click();
      await page.getByTestId("check-button").click();
      break;
    case "true-false":
      await page
        .getByTestId(`choice-${exercise.id}-${exercise.answer}`)
        .click();
      await page.getByTestId("check-button").click();
      break;
    case "fill-blank":
      await page
        .getByTestId(`fill-input-${exercise.id}`)
        .fill(exercise.answer);
      await page.getByTestId("check-button").click();
      break;
    case "match-pairs":
      // Click matching left/right pairs; the exercise resolves itself.
      for (const pair of exercise.pairs) {
        await page
          .getByTestId(`pair-left-${exercise.id}-${pair.id}`)
          .click();
        await page
          .getByTestId(`pair-right-${exercise.id}-${pair.id}`)
          .click();
      }
      break;
  }
  await expect(page.getByTestId("check-feedback")).toContainText(
    "Nicely done",
  );
  await page.getByTestId("continue-button").click();
}

function localDateString(d: Date): string {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  return `${y}-${m}-${day}`;
}

/** A believable mid-journey snapshot for screenshots and seeded tests. */
export function richSnapshot() {
  const now = new Date();
  const days = [2, 1, 0].map((back) => {
    const d = new Date(now);
    d.setDate(d.getDate() - back);
    return localDateString(d);
  });
  const at = now.toISOString();
  const lesson = (bestPct: number) => ({
    completedAt: at,
    timesCompleted: 1,
    bestPct,
  });
  return {
    version: 1,
    xpTotal: 118,
    xpEvents: [],
    hearts: { count: 4, updatedAt: at },
    streak: { count: 3, lastRitualDate: days[2], history: days },
    lessons: {
      "tarot.u1.l1": lesson(1),
      "tarot.u1.l2": lesson(0.86),
      "tarot.u1.l3": lesson(1),
    },
    activeCourseSlug: "tarot",
  };
}

export async function seedProgress(page: Page) {
  const snapshot = richSnapshot();
  await page.addInitScript((data) => {
    window.localStorage.setItem("arcana.progress.v1", JSON.stringify(data));
  }, snapshot);
}
