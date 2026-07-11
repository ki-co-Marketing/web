import { z } from "zod";

/**
 * Zod mirror of types.ts — the runtime validation gate.
 * Shape + local invariants live here; cross-entity invariants
 * (global ID uniqueness, image files on disk) live in content.test.ts.
 */

const kebab = z
  .string()
  .regex(/^[a-z0-9]+(-[a-z0-9]+)*$/, "slug must be kebab-case");
const id = z.string().min(1);

const exerciseBase = {
  id,
  prompt: z.string().min(1),
  explanation: z.string().min(1).optional(),
};

function requireCorrectChoice<
  T extends { choices: { id: string }[]; correctChoiceId: string },
>(ex: T, ctx: z.RefinementCtx) {
  const ids = ex.choices.map((c) => c.id);
  if (new Set(ids).size !== ids.length) {
    ctx.addIssue({ code: "custom", message: `duplicate choice ids in ${ex}` });
  }
  if (!ids.includes(ex.correctChoiceId)) {
    ctx.addIssue({
      code: "custom",
      message: `correctChoiceId not among choices`,
    });
  }
}

export const multipleChoiceSchema = z
  .object({
    ...exerciseBase,
    type: z.literal("multiple-choice"),
    choices: z
      .array(z.object({ id, text: z.string().min(1) }))
      .min(2)
      .max(4),
    correctChoiceId: id,
  })
  .superRefine(requireCorrectChoice);

export const imageChoiceSchema = z
  .object({
    ...exerciseBase,
    type: z.literal("image-choice"),
    choices: z
      .array(
        z.object({
          id,
          image: z.string().startsWith("/"),
          label: z.string().optional(),
        }),
      )
      .length(4),
    correctChoiceId: id,
  })
  .superRefine(requireCorrectChoice);

export const matchPairsSchema = z.object({
  ...exerciseBase,
  type: z.literal("match-pairs"),
  pairs: z
    .array(z.object({ id, left: z.string().min(1), right: z.string().min(1) }))
    .length(4),
});

export const trueFalseSchema = z.object({
  ...exerciseBase,
  type: z.literal("true-false"),
  statement: z.string().min(1),
  answer: z.boolean(),
});

export const fillBlankSchema = z.object({
  ...exerciseBase,
  type: z.literal("fill-blank"),
  before: z.string(),
  after: z.string(),
  answer: z.string().min(1),
  acceptable: z.array(z.string().min(1)).optional(),
});

export const exerciseSchema = z.discriminatedUnion("type", [
  multipleChoiceSchema,
  imageChoiceSchema,
  matchPairsSchema,
  trueFalseSchema,
  fillBlankSchema,
]);

export const lessonSchema = z.object({
  id,
  slug: kebab,
  title: z.string().min(1),
  description: z.string().min(1),
  xpReward: z.number().int().positive(),
  exercises: z.array(exerciseSchema),
});

export const unitSchema = z
  .object({
    id,
    slug: kebab,
    title: z.string().min(1),
    description: z.string().min(1),
    comingSoon: z.boolean().optional(),
    lessons: z.array(lessonSchema).min(1),
  })
  .superRefine((unit, ctx) => {
    if (!unit.comingSoon) {
      for (const lesson of unit.lessons) {
        if (lesson.exercises.length < 3) {
          ctx.addIssue({
            code: "custom",
            message: `playable lesson ${lesson.id} needs ≥3 exercises`,
          });
        }
      }
    }
  });

export const courseSchema = z.object({
  id,
  slug: kebab,
  title: z.string().min(1),
  tagline: z.string().min(1),
  icon: z.enum(["tarot", "zodiac"]),
  accent: z.enum(["gold", "amethyst"]),
  units: z.array(unitSchema).min(1),
});
