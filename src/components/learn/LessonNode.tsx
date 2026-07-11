import { Check, Lock, Star } from "@/components/icons";
import type { Lesson } from "@/content/types";
import type { LessonStatus } from "@/lib/game";
import { cn } from "@/lib/utils";

const statusIcon = {
  completed: Check,
  available: Star,
  locked: Lock,
} as const;

export function LessonNode({
  lesson,
  status,
  offset,
  selected,
  onSelect,
}: {
  lesson: Lesson;
  status: LessonStatus;
  offset: number;
  selected: boolean;
  onSelect: () => void;
}) {
  const Icon = statusIcon[status];
  return (
    <div
      className="flex justify-center"
      style={{ transform: `translateX(${offset}px)` }}
    >
      <button
        type="button"
        disabled={status === "locked"}
        onClick={onSelect}
        data-testid={`lesson-node-${lesson.id}`}
        aria-label={`${lesson.title} — ${status}`}
        className={cn(
          "flex h-16 w-16 items-center justify-center rounded-full border-b-4 transition-transform",
          status === "completed" &&
            "border-gold-600 bg-gold-400 text-midnight-950 hover:scale-105",
          status === "available" &&
            "animate-node-pulse border-amethyst-700 bg-amethyst-500 text-white hover:scale-105",
          status === "locked" &&
            "border-midnight-700 bg-midnight-800 text-moon-500",
          selected && "ring-2 ring-moon-100 ring-offset-2 ring-offset-midnight-950",
        )}
      >
        <Icon size={26} />
      </button>
    </div>
  );
}
