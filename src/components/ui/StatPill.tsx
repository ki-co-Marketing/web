import type { ReactNode } from "react";
import { cn } from "@/lib/utils";

export function StatPill({
  icon,
  value,
  label,
  className,
  ...rest
}: {
  icon: ReactNode;
  value: ReactNode;
  /** Accessible description, e.g. "day streak" */
  label: string;
  className?: string;
} & React.HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      title={label}
      aria-label={`${typeof value === "string" || typeof value === "number" ? value : ""} ${label}`}
      className={cn(
        "flex items-center gap-1.5 rounded-full border border-white/8 bg-midnight-900 px-3 py-1 text-sm font-bold",
        className,
      )}
      {...rest}
    >
      {icon}
      <span>{value}</span>
    </div>
  );
}
