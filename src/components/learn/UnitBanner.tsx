import { Lock } from "@/components/icons";
import type { CourseAccent, Unit } from "@/content/types";
import { cn } from "@/lib/utils";

export function UnitBanner({
  unit,
  index,
  accent,
}: {
  unit: Unit;
  index: number;
  accent: CourseAccent;
}) {
  return (
    <div
      className={cn(
        "relative overflow-hidden rounded-2xl border p-5",
        unit.comingSoon
          ? "border-white/8 bg-midnight-900 opacity-60"
          : accent === "gold"
            ? "border-gold-500/30 bg-gradient-to-r from-gold-500/15 to-midnight-900"
            : "border-amethyst-500/30 bg-gradient-to-r from-amethyst-600/25 to-midnight-900",
      )}
    >
      <p className="text-xs font-semibold tracking-[0.2em] text-moon-500 uppercase">
        Unit {index + 1}
        {unit.comingSoon && " · Coming soon"}
      </p>
      <h2 className="font-display mt-1 text-2xl font-semibold">
        {unit.title}
      </h2>
      <p className="mt-1 text-sm text-moon-300">{unit.description}</p>
      {unit.comingSoon && (
        <Lock
          size={22}
          className="absolute top-5 right-5 text-moon-500"
          aria-hidden
        />
      )}
    </div>
  );
}
