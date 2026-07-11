"use client";

import type { ReactNode } from "react";
import { cn } from "@/lib/utils";

export function Modal({
  open,
  children,
  className,
  labelledBy,
}: {
  open: boolean;
  children: ReactNode;
  className?: string;
  labelledBy?: string;
}) {
  if (!open) return null;
  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby={labelledBy}
    >
      <div className="absolute inset-0 bg-midnight-950/80 backdrop-blur-sm" />
      <div
        className={cn(
          "animate-rise-fade relative w-full max-w-md rounded-2xl border border-white/8 bg-midnight-900 p-6 shadow-glow",
          className,
        )}
      >
        {children}
      </div>
    </div>
  );
}
