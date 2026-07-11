import { expect, test } from "@playwright/test";
import { tarotCourse } from "../src/content/courses/tarot";
import { answerCorrectly } from "./helpers";

const lesson1 = tarotCourse.units[0].lessons[0];

test("first lesson end-to-end: play, earn 29 XP, start a streak, unlock the next node", async ({
  page,
}) => {
  await page.goto("/");
  await page.getByTestId("cta-begin").click();
  await page.waitForURL("**/learn/tarot");

  // The frontier lesson is pre-selected; start it.
  await expect(page.getByTestId("start-lesson")).toContainText("Start");
  await page.getByTestId("start-lesson").click();
  await page.waitForURL(`**/learn/tarot/${lesson1.slug}`);

  for (const exercise of lesson1.exercises) {
    await answerCorrectly(page, exercise);
  }

  // Completion: 7 first-try correct ×2 + 10 completion + 5 perfect = 29.
  await expect(page.getByTestId("xp-total")).toHaveText("+29 XP");
  await expect(page.getByTestId("streak-line")).toContainText(
    "Streak started",
  );

  await page.getByTestId("return-to-path").click();
  await page.waitForURL("**/learn/tarot");

  // Node states: lesson 1 completed, lesson 2 now available.
  await expect(
    page.getByTestId(`lesson-node-${lesson1.id}`),
  ).toHaveAttribute("aria-label", /completed/);
  await expect(
    page.getByTestId("lesson-node-tarot.u1.l2"),
  ).toHaveAttribute("aria-label", /available/);

  // Stat bar reflects the ritual; hearts untouched on a perfect run
  // (already full, so the perfect +1 clamps).
  await expect(page.getByTestId("stat-xp")).toContainText("29");
  await expect(page.getByTestId("stat-streak")).toContainText("1");
  await expect(page.getByTestId("stat-hearts")).toContainText("5");

  // Profile: 29 XP sits exactly one point below Initiate (30).
  await page.goto("/profile");
  await expect(page.getByTestId("rank-name")).toHaveText("Seeker");
  await expect(page.getByTestId("tile-xp")).toHaveText("29");
  await expect(page.getByTestId("tile-lessons")).toHaveText("1");
  await expect(page.getByTestId("tile-perfected")).toHaveText("1");
});

test("a wrong answer costs a heart and reveals the correct answer", async ({
  page,
}) => {
  await page.goto(`/learn/tarot/${lesson1.slug}`);

  const first = lesson1.exercises[0];
  if (first.type !== "multiple-choice") {
    throw new Error("test expects lesson 1 to open with multiple choice");
  }
  const wrong = first.choices.find((c) => c.id !== first.correctChoiceId)!;

  await expect(page.getByTestId("hearts-row")).toHaveAttribute(
    "aria-label",
    "5 of 5 hearts",
  );
  await page.getByTestId(`choice-${first.id}-${wrong.id}`).click();
  await page.getByTestId("check-button").click();

  await expect(page.getByTestId("check-feedback")).toContainText(
    "The cards say otherwise",
  );
  await expect(page.getByTestId("hearts-row")).toHaveAttribute(
    "aria-label",
    "4 of 5 hearts",
  );

  // The lesson continues to the next exercise.
  await page.getByTestId("continue-button").click();
  await expect(
    page.getByText(`2 / ${lesson1.exercises.length}`),
  ).toBeVisible();
});
