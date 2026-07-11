# ✶ Arcana

**Learn tarot & astrology, one daily ritual at a time.**

Arcana is a gamified learning app — Duolingo, but for the mystic arts.
Bite-size lessons build into full courses; streaks ("daily rituals"),
XP, hearts, and ranks (Seeker → Luminary) keep the practice alive; and
everything you learn joins your Grimoire reference deck.

> ✶ For insight, learning & entertainment ✶

## What's here today (v0 — playable demo)

- **Two courses** — Tarot (Unit 1, "The Fool's Journey": 6 playable
  lessons, ~43 exercises covering the first eight Major Arcana) and
  Astrology ("The Wheel of Twelve"), with later units visible on the
  map as coming soon.
- **Five exercise types** — multiple choice, image choice (real
  Rider–Waite–Smith card art), match pairs, true/false, fill-in-the-blank.
- **The full game loop** — XP with perfect-lesson bonuses, 5 hearts with
  30-minute regeneration, daily-ritual streaks, XP ranks, and linear
  lesson/unit unlocking.
- **Local-first progress** — no account needed; progress lives in
  `localStorage` behind a swappable `ProgressStore` interface (a
  Supabase adapter drops in later without touching UI code — see
  [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)).
- **A mystical design system** — midnight/amethyst/gold tokens,
  Cormorant Garamond + Inter, starfield atmosphere, CSS-only motion.

## Quickstart

```bash
npm install
npm run dev        # http://localhost:3000
```

## Scripts

| Script | What it does |
| --- | --- |
| `npm run dev` | Dev server |
| `npm run build` / `start` | Production build / serve |
| `npm run lint` | ESLint (flat config, next/core-web-vitals) |
| `npm run typecheck` | `next typegen` + strict `tsc` |
| `npm test` | Vitest — game engine, progress store, **content validation** |
| `npm run test:e2e` | Playwright: full lesson playthrough + heart-loss flow |
| `npm run screenshots` | Capture the core screens to `e2e/screenshots/` |
| `npm run fetch:cards` | Re-download RWS card art from Wikimedia Commons |
| `npm run generate:cards-fallback` | Offline stylized SVG deck (see script header) |

## Where things live

```
src/content/   courses, lessons, exercises — typed content-as-code (+ zod gate)
src/lib/game/  pure rules: XP, hearts, streaks, ranks, unlocks (unit-tested)
src/lib/progress/  ProgressStore abstraction + localStorage impl + React provider
src/components/    icons, ui primitives, course map, lesson player, profile
src/app/           routes: / · /learn/[course] · /learn/[course]/[lesson] · /grimoire · /profile
docs/              ROADMAP · ARCHITECTURE · GAMEPLAY · CONTENT
```

- Game rules and every tunable number: [docs/GAMEPLAY.md](docs/GAMEPLAY.md)
- Authoring a new course: [docs/CONTENT.md](docs/CONTENT.md)
- The path to production (accounts, spaced repetition, PWA, freemium,
  covens, native app): [docs/ROADMAP.md](docs/ROADMAP.md)

## Licensing notes

Tarot imagery is from the Rider–Waite–Smith deck (1909), illustrated by
Pamela Colman Smith — **public domain**. Card scans are fetched from
Wikimedia Commons at build-tooling time and committed under
`public/cards/`. Course text is original.

The two `*.php` files at the repository root are unrelated legacy
WordPress snippets that predate this project; leave them untouched
(extraction to a dedicated repo is Roadmap Phase 1).
