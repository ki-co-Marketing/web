import { test } from "@playwright/test";
import { tarotCourse } from "../src/content/courses/tarot";
import { answerCorrectly, seedProgress } from "./helpers";

/**
 * Captures the core screens with a believable mid-journey snapshot
 * (3-day streak, 118 XP, three lessons done). Output: e2e/screenshots/.
 */

const OUT = "e2e/screenshots";
// With lessons 1–3 complete, lesson 4 is the frontier.
const lesson4 = tarotCourse.units[0].lessons[3];

test("landing", async ({ page }) => {
  await page.goto("/");
  await page.waitForTimeout(400);
  await page.screenshot({ path: `${OUT}/01-landing.png`, fullPage: true });
});

test("course map", async ({ page }) => {
  await seedProgress(page);
  await page.goto("/learn/tarot");
  await page.getByTestId("start-lesson").waitFor();
  await page.waitForTimeout(400);
  await page.screenshot({ path: `${OUT}/02-course-map.png` });
});

test("lesson player mid-exercise", async ({ page }) => {
  await seedProgress(page);
  await page.goto(`/learn/tarot/${lesson4.slug}`);
  const first = lesson4.exercises[0];
  if (first.type === "image-choice" || first.type === "multiple-choice") {
    // Select (but don't submit) so the amethyst selection state shows.
    await page
      .getByTestId(`choice-${first.id}-${first.correctChoiceId}`)
      .click();
  }
  await page.waitForTimeout(300);
  await page.screenshot({ path: `${OUT}/03-lesson-player.png` });
});

test("completion celebration", async ({ page }) => {
  await seedProgress(page);
  await page.goto(`/learn/tarot/${lesson4.slug}`);
  for (const exercise of lesson4.exercises) {
    await answerCorrectly(page, exercise);
  }
  await page.getByTestId("xp-total").waitFor();
  await page.waitForTimeout(1100); // let the XP count-up land
  await page.screenshot({ path: `${OUT}/04-completion.png` });
});

test("profile", async ({ page }) => {
  await seedProgress(page);
  await page.goto("/profile");
  await page.getByTestId("rank-name").waitFor();
  await page.waitForTimeout(300);
  await page.screenshot({ path: `${OUT}/05-profile.png`, fullPage: true });
});

test("grimoire", async ({ page }) => {
  await seedProgress(page);
  await page.goto("/grimoire");
  await page.waitForTimeout(500);
  await page.screenshot({ path: `${OUT}/06-grimoire.png` });
});
