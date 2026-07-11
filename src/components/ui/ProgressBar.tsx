import { cn } from "@/lib/utils";

export function ProgressBar({
  value,
  className,
  barClassName,
}: {
  /** 0..1 */
  value: number;
  className?: string;
  barClassName?: string;
}) {
  const pct = Math.round(Math.min(1, Math.max(0, value)) * 100);
  return (
    <div
      role="progressbar"
      aria-valuenow={pct}
      aria-valuemin={0}
      aria-valuemax={100}
      className={cn(
        "h-3 w-full overflow-hidden rounded-full bg-midnight-800",
        className,
      )}
    >
      <div
        className={cn(
          "h-full rounded-full bg-gold-400 transition-[width] duration-500 ease-out",
          barClassName,
        )}
        style={{ width: `${pct}%` }}
      />
    </div>
  );
}
