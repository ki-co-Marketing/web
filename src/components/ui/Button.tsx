import type { ButtonHTMLAttributes } from "react";
import { cn } from "@/lib/utils";

export type ButtonVariant =
  | "primary"
  | "gold"
  | "ghost"
  | "success"
  | "danger";
export type ButtonSize = "sm" | "md" | "lg";

/** Shared style recipe so <Link> elements can dress as buttons too. */
export function buttonStyles(
  variant: ButtonVariant = "primary",
  size: ButtonSize = "md",
  className?: string,
): string {
  return cn(
    "inline-flex items-center justify-center gap-2 rounded-xl font-semibold tracking-wide transition-all duration-150 select-none",
    "focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amethyst-400",
    "disabled:pointer-events-none disabled:opacity-40",
    // Duolingo-style pressed depth: thick bottom edge that collapses on press
    variant === "primary" &&
      "bg-amethyst-500 text-white border-b-4 border-amethyst-700 hover:bg-amethyst-400 active:border-b-0 active:translate-y-1",
    variant === "gold" &&
      "bg-gold-400 text-midnight-950 border-b-4 border-gold-600 hover:bg-gold-300 active:border-b-0 active:translate-y-1",
    variant === "success" &&
      "bg-success-400 text-midnight-950 border-b-4 border-success-600 hover:brightness-110 active:border-b-0 active:translate-y-1",
    variant === "danger" &&
      "bg-danger-400 text-white border-b-4 border-danger-600 hover:brightness-110 active:border-b-0 active:translate-y-1",
    variant === "ghost" &&
      "bg-transparent text-moon-300 border border-midnight-700 hover:bg-midnight-800 hover:text-moon-100",
    size === "sm" && "px-3 py-1.5 text-sm",
    size === "md" && "px-5 py-2.5 text-base",
    size === "lg" && "px-8 py-3.5 text-lg",
    className,
  );
}

export function Button({
  variant = "primary",
  size = "md",
  className,
  ...props
}: ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: ButtonVariant;
  size?: ButtonSize;
}) {
  return (
    <button className={buttonStyles(variant, size, className)} {...props} />
  );
}
