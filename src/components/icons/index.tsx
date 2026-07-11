import type { SVGProps } from "react";

export type IconProps = SVGProps<SVGSVGElement> & { size?: number };

function svgProps({ size = 24, ...rest }: IconProps) {
  return {
    width: size,
    height: size,
    viewBox: "0 0 24 24",
    "aria-hidden": true as const,
    focusable: false as const,
    ...rest,
  };
}

export function Flame(props: IconProps) {
  return (
    <svg {...svgProps(props)} fill="currentColor">
      <path d="M12 2c.6 3.6-.8 5.5-2.2 7.1C8.4 10.7 7 12.2 7 14.5A5.5 5.5 0 0 0 12.5 20a5.5 5.5 0 0 0 5.5-5.5c0-1.9-.9-3.5-1.9-4.8-.3 1.1-1 2-2 2.5.5-3.3-.5-7-2.1-10.2Z" />
    </svg>
  );
}

export function Heart({
  filled = true,
  ...props
}: IconProps & { filled?: boolean }) {
  return (
    <svg
      {...svgProps(props)}
      fill={filled ? "currentColor" : "none"}
      stroke="currentColor"
      strokeWidth={filled ? 0 : 2}
    >
      <path d="M12 20.6C7.2 16.4 3.5 13.3 3.5 9.5c0-2.8 2.1-4.6 4.4-4.6 1.6 0 3.1.8 4.1 2.2a5 5 0 0 1 4.1-2.2c2.3 0 4.4 1.8 4.4 4.6 0 3.8-3.7 6.9-8.5 11.1Z" />
    </svg>
  );
}

export function Star({
  filled = true,
  ...props
}: IconProps & { filled?: boolean }) {
  return (
    <svg
      {...svgProps(props)}
      fill={filled ? "currentColor" : "none"}
      stroke="currentColor"
      strokeWidth={filled ? 0 : 2}
      strokeLinejoin="round"
    >
      <path d="M12 2.8 14.8 8.5l6.3 1-4.6 4.4 1.1 6.3L12 17.2l-5.6 3 1.1-6.3L2.9 9.5l6.3-1L12 2.8Z" />
    </svg>
  );
}

export function Sparkles(props: IconProps) {
  return (
    <svg {...svgProps(props)} fill="currentColor">
      <path d="M11 4.5 12.6 9l4.4 1.5-4.4 1.5L11 16.5 9.4 12 5 10.5 9.4 9 11 4.5Z" />
      <path d="M18.5 2.5l.8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8.8-2.2Z" />
      <path d="M18 15.5l.7 1.8 1.8.7-1.8.7-.7 1.8-.7-1.8-1.8-.7 1.8-.7.7-1.8Z" />
    </svg>
  );
}

export function MoonCrescent(props: IconProps) {
  return (
    <svg {...svgProps(props)} fill="currentColor">
      <path d="M20.6 14.4A8.7 8.7 0 0 1 9.6 3.4a8.7 8.7 0 1 0 11 11Z" />
    </svg>
  );
}

export function Sun(props: IconProps) {
  return (
    <svg
      {...svgProps(props)}
      fill="none"
      stroke="currentColor"
      strokeWidth={2}
      strokeLinecap="round"
    >
      <circle cx="12" cy="12" r="4" fill="currentColor" stroke="none" />
      <path d="M12 2.5v2.5M12 19v2.5M2.5 12H5M19 12h2.5M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M19.1 4.9l-1.8 1.8M6.7 17.3l-1.8 1.8" />
    </svg>
  );
}

export function Lock(props: IconProps) {
  return (
    <svg
      {...svgProps(props)}
      fill="none"
      stroke="currentColor"
      strokeWidth={2}
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <rect x="5" y="11" width="14" height="9" rx="2" fill="currentColor" />
      <path d="M8 11V7.5a4 4 0 0 1 8 0V11" />
    </svg>
  );
}

export function Check(props: IconProps) {
  return (
    <svg
      {...svgProps(props)}
      fill="none"
      stroke="currentColor"
      strokeWidth={3}
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="M4.5 12.5l5 5L19.5 6.5" />
    </svg>
  );
}

export function XMark(props: IconProps) {
  return (
    <svg
      {...svgProps(props)}
      fill="none"
      stroke="currentColor"
      strokeWidth={2.5}
      strokeLinecap="round"
    >
      <path d="M6 6l12 12M18 6L6 18" />
    </svg>
  );
}

export function ChevronRight(props: IconProps) {
  return (
    <svg
      {...svgProps(props)}
      fill="none"
      stroke="currentColor"
      strokeWidth={2.5}
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="M9 5l7 7-7 7" />
    </svg>
  );
}

export function Wand(props: IconProps) {
  return (
    <svg
      {...svgProps(props)}
      fill="none"
      stroke="currentColor"
      strokeWidth={2}
      strokeLinecap="round"
    >
      <path d="M4.5 19.5 14 10" />
      <path
        d="M17.5 3.5l.9 2.1 2.1.9-2.1.9-.9 2.1-.9-2.1-2.1-.9 2.1-.9.9-2.1Z"
        fill="currentColor"
        stroke="none"
      />
      <path d="M19.5 12.5v.01M12.5 4.5v.01" strokeWidth={2.5} />
    </svg>
  );
}

export function Cup(props: IconProps) {
  return (
    <svg
      {...svgProps(props)}
      fill="none"
      stroke="currentColor"
      strokeWidth={2}
      strokeLinecap="round"
    >
      <path d="M7 4h10v4.5a5 5 0 0 1-10 0V4Z" />
      <path d="M12 13.5V18M8.5 21h7" />
    </svg>
  );
}

export function Sword(props: IconProps) {
  return (
    <svg
      {...svgProps(props)}
      fill="none"
      stroke="currentColor"
      strokeWidth={2}
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="M12 2.5 14 5v9l-2 2-2-2V5l2-2.5Z" fill="currentColor" />
      <path d="M7.5 16.5h9M12 16.5V21" />
    </svg>
  );
}

export function Pentacle(props: IconProps) {
  return (
    <svg
      {...svgProps(props)}
      fill="none"
      stroke="currentColor"
      strokeWidth={1.6}
      strokeLinejoin="round"
    >
      <circle cx="12" cy="12" r="9.2" />
      <path d="M12 5l4.11 12.66L5.34 9.84h13.32L7.89 17.66 12 5Z" />
    </svg>
  );
}

export function TarotCard(props: IconProps) {
  return (
    <svg
      {...svgProps(props)}
      fill="none"
      stroke="currentColor"
      strokeWidth={2}
      strokeLinejoin="round"
    >
      <rect x="6" y="3" width="12" height="18" rx="2" />
      <path
        d="M12 8.5l1.1 2.2 2.4.4-1.7 1.7.4 2.4-2.2-1.1-2.2 1.1.4-2.4-1.7-1.7 2.4-.4L12 8.5Z"
        fill="currentColor"
        stroke="none"
      />
    </svg>
  );
}
