import { mulberry32 } from "@/lib/utils";

export function Starfield({
  count = 48,
  seed = 7,
}: {
  count?: number;
  seed?: number;
}) {
  // Deterministic PRNG so server and client render identical stars.
  const rand = mulberry32(seed);
  const stars = Array.from({ length: count }, (_, i) => ({
    cx: +(rand() * 100).toFixed(2),
    cy: +(rand() * 100).toFixed(2),
    r: +(0.1 + rand() * 0.22).toFixed(3),
    group: i % 3,
  }));
  return (
    <svg
      className="h-full w-full"
      viewBox="0 0 100 100"
      preserveAspectRatio="xMidYMid slice"
      aria-hidden
    >
      {stars.map((s, i) => (
        <circle
          key={i}
          cx={s.cx}
          cy={s.cy}
          r={s.r}
          fill="#F2D98D"
          className="animate-twinkle"
          style={{
            animationDelay: `${s.group * 1.4}s`,
            animationDuration: `${3.2 + s.group * 1.1}s`,
          }}
        />
      ))}
    </svg>
  );
}
