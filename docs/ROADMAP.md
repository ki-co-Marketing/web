# Roadmap — from playable demo to real product

v0 (this repo) is a fully playable, tested, local-first web demo.
Each phase below is independently shippable and builds on the last.
Estimates assume one focused engineer (or one engineer + Claude).

## Phase 1 — Foundation & deploy (1–2 weeks)

**Goal: Arcana lives at a public URL with analytics.**

- Extract to a dedicated repo (`ki-co-Marketing/arcana`): import this
  branch's tree as the initial commit, dropping the legacy WordPress
  snippets; archive a pointer in `web`.
- Vercel project: connect repo, custom domain, flip
  `images.unoptimized` off, enable Vercel Analytics; add PostHog with
  the core event set (`lesson_started/completed`, `exercise_answered`,
  `streak_extended`, `hearts_empty`).
- Polish: seeded per-user choice shuffling (keep e2e deterministic via a
  fixed seed in test mode), accessibility pass (focus order, aria-live
  on feedback, `prefers-reduced-motion` already respected), OG images.
- **Done when:** public URL, CI green on the new repo, Lighthouse ≥90
  for accessibility & performance.

## Phase 2 — Accounts & sync (Supabase, 2–3 weeks)

**Goal: progress survives devices; anonymous mode keeps working.**

- Supabase Auth: email OTP + Google. Anonymous users keep
  localStorage; signing in merges local → server (max XP, union of
  completions) per the store contract in ARCHITECTURE.md.
- Schema: `profiles`, `lesson_completions`, `xp_events`, `streaks`,
  `achievements` — owner-only RLS (`auth.uid() = user_id`) on every
  table; XP writes move server-side (RPC) as the anti-cheat seam.
- `SupabaseProgressStore implements ProgressStore` — zero UI changes.
  Streak computation moves server-side using profile timezone.
- **Done when:** two devices show identical progress; RLS verified by
  tests; the app still works logged-out.

## Phase 3 — The Grimoire becomes a spaced-repetition deck (2 weeks)

**Goal: the retention loop.**

- SM-2-lite per learned item (card/sign): `review_items(user_id,
  item_id, ease, interval_days, due_at)`; items enter the deck when
  their lesson completes.
- A daily "Grimoire ritual" session of due items reusing the existing
  exercise renderers; +1 XP per review; reviews count as ritual days.
- **Done when:** due queues are correct across days and the review
  session is playable end-to-end.

## Phase 4 — PWA & streak reminders (1–2 weeks)

**Goal: the daily-habit mechanics.**

- Manifest + service worker (Serwist): installable, offline shell +
  content; already dark-themed for splash.
- Web Push streak reminders via a Supabase Edge Function cron
  ("Your ritual awaits — 4 hours left to keep your 12-day streak 🔮"),
  with a respectful opt-in flow.
- **Done when:** installs on iOS/Android home screens; reminders fire
  for at-risk streaks only.

## Phase 5 — Freemium (Stripe, 2–3 weeks)

**Goal: revenue without gutting the free experience.**

- Free: full intro units of every course, 5 hearts, 10 reviews/day.
- **Arcana+** (placeholder $6.99/mo or $49/yr): unlimited hearts,
  streak repair (1/month), premium courses, unlimited reviews,
  cosmetic card-back themes.
- Stripe Checkout + customer portal; webhook →
  `entitlements(user_id, product, status, current_period_end)`;
  a `useEntitlement()` gate in the UI.
- **Done when:** test-mode purchase unlocks instantly; webhooks are
  idempotent under retry.

## Phase 6 — Content expansion (ongoing)

- Tarot Units 2–3 (already scaffolded as coming-soon), then the Minor
  Arcana and spreads; Astrology elements/planets/houses (Units 2–3
  scaffolded); new courses: **Moon Phases**, **Crystals**
  (image-choice shines), **Numerology**.
- Content stays content-as-code while authors are engineers. When
  non-engineers join: evaluate Payload CMS (self-hosted, Postgres —
  fits Supabase) — migration is mechanical because content is already
  normalized rows-in-waiting.

## Phase 7 — Social: covens & leaderboards (2–3 weeks)

- Weekly XP leagues ("Circles": Moonstone → Amethyst → Obsidian) with
  promotion/demotion; friend follows; **Covens** — small groups with a
  shared weekly goal.
- Tables: `leagues`, `league_members`, `covens`, `coven_members`;
  server-side XP writes (Phase 2) are the anti-cheat foundation.

## Phase 8 — Native app (Expo, 4–6 weeks)

- Precondition: monorepo split — `packages/content`, `packages/game`,
  `packages/progress` are file moves thanks to v0 layering; `apps/web`
  + `apps/mobile`.
- Expo Router mirrors the route map; NativeWind consumes the same
  tokens; same Supabase backend; push via Expo Notifications; IAP via
  RevenueCat bridging into the same `entitlements` table.
- **Done when:** TestFlight/Play beta with lesson parity.

## Cross-cutting

- **Analytics** from Phase 1; **Sentry** from Phase 2.
- **Legal**: keep the "insight, learning & entertainment" framing;
  license-check any non-RWS imagery before it ships.
- **Voice**: warm, celestial, a little playful — the theme is the
  moat; protect it in every string.
