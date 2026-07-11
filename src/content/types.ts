/**
 * Content model — content-as-code today, database rows tomorrow.
 *
 * Every entity is plain JSON-serializable with a stable string ID
 * (e.g. "tarot.u1.l1.e3"), so each level maps 1:1 to a future Postgres
 * table (courses, units, lessons, exercises-with-jsonb-payload).
 * Never reuse or rename an ID once shipped — user progress keys on them.
 */

export type CourseIcon = "tarot" | "zodiac";
export type CourseAccent = "gold" | "amethyst";

export interface Course {
  id: string;
  slug: string;
  title: string;
  tagline: string;
  icon: CourseIcon;
  accent: CourseAccent;
  units: Unit[];
}

export interface Unit {
  id: string;
  slug: string;
  title: string;
  description: string;
  /** Visible on the map but locked; may have zero exercises per lesson. */
  comingSoon?: boolean;
  lessons: Lesson[];
}

export interface Lesson {
  id: string;
  slug: string;
  title: string;
  description: string;
  /** Base XP for completing the lesson (10 standard, 15 review). */
  xpReward: number;
  exercises: Exercise[];
}

export interface ExerciseBase {
  id: string;
  prompt: string;
  /** Teaching text revealed after answering — carries the narrative. */
  explanation?: string;
}

export interface MultipleChoiceExercise extends ExerciseBase {
  type: "multiple-choice";
  choices: { id: string; text: string }[];
  correctChoiceId: string;
}

export interface ImageChoiceExercise extends ExerciseBase {
  type: "image-choice";
  /** Exactly 4 — rendered as a 2×2 grid of card images. */
  choices: { id: string; image: string; label?: string }[];
  correctChoiceId: string;
}

export interface MatchPairsExercise extends ExerciseBase {
  type: "match-pairs";
  /** Exactly 4 pairs; right column is shuffled at render time. */
  pairs: { id: string; left: string; right: string }[];
}

export interface TrueFalseExercise extends ExerciseBase {
  type: "true-false";
  statement: string;
  answer: boolean;
}

export interface FillBlankExercise extends ExerciseBase {
  type: "fill-blank";
  before: string;
  after: string;
  answer: string;
  /** Additional accepted answers, compared after normalization. */
  acceptable?: string[];
}

export type Exercise =
  | MultipleChoiceExercise
  | ImageChoiceExercise
  | MatchPairsExercise
  | TrueFalseExercise
  | FillBlankExercise;

export type ExerciseType = Exercise["type"];
