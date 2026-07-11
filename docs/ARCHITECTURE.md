# Architecture

## Layers

```
┌─────────────────────────────────────────────────────┐
│ UI  (src/app routes + src/components)               │
│   course map · lesson player · profile · grimoire   │
├─────────────────────────────────────────────────────┤
│ Progress  (src/lib/progress)                        │
│   ProgressProvider (Context + useReducer)           │
│   ProgressStore ⇄ LocalStorageProgressStore (v0)    │
│                 ⇄ SupabaseProgressStore (planned)   │
├─────────────────────────────────────────────────────┤
│ Game engine  (src/lib/game) — pure functions        │
│   xp · hearts · streak · ranks · unlocks · config   │
├─────────────────────────────────────────────────────┤
│ Content  (src/content) — typed content-as-code      │
│   courses → units → lessons → exercises (+ zod)     │
└─────────────────────────────────────────────────────┘
```

Dependencies point downward only. The reducer orchestrates but does no
math — every rule lives in `lib/game` as a pure `(state, now) → state`
function, so the future server port is mechanical.

## Routing

Two route groups share URL space but not chrome:

- `(shell)` — stat bar + bottom nav: `/learn`, `/learn/[courseSlug]`,
  `/grimoire`, `/profile`.
- `(player)` — distraction-free full screen:
  `/learn/[courseSlug]/[lessonSlug]`.

Course and lesson pages are SSG via `generateStaticParams` from the
content registry; unknown slugs and coming-soon lessons `notFound()`.

## The store contract (the load-bearing seam)

```ts
interface ProgressStore {
  load(): Promise<ProgressSnapshot | null>;
  save(snapshot: ProgressSnapshot): Promise<void>;
  clear(): Promise<void>;
}
```

- v0: `LocalStorageProgressStore` (key `arcana.progress.v1`), zod-guarded
  on load — corrupt payloads heal to a fresh start.
- SSR/tests: `MemoryProgressStore`.
- **Planned** `SupabaseProgressStore`: same interface backed by the
  tables below. First sign-in merges local → server (max of XP, union of
  completions), then localStorage becomes a write-through cache.
  No UI file changes — that is the contract.

`ProgressProvider` hydrates in an effect (`hydrated` flag gates display,
so SSR and first client render always match), persists on every
post-hydration change, and materializes lazy heart regen once a minute.

## Future database mapping (Supabase / Postgres)

| Snapshot field | Table | Notes |
| --- | --- | --- |
| `xpTotal`, `xpEvents` | `xp_events(id, user_id, amount, source, kind, created_at)` | total = server sum |
| `lessons` | `lesson_completions(user_id, lesson_id, completed_at, best_pct, times_completed)` | PK `(user_id, lesson_id)` |
| `streak` | `streaks(user_id, count, last_ritual_date, history)` | server-side with profile timezone |
| `hearts` | `profiles.hearts_count, hearts_updated_at` | same lazy-regen function |
| — | `profiles(id → auth.users, display_name, timezone)` | RLS: `auth.uid() = user_id` everywhere |

Content stays in code (versioned, reviewed, zod-validated) until
non-engineers author courses — see ROADMAP Phase 6.

## Content model

Stable string IDs chain by prefix (`tarot` → `tarot.u1` → `tarot.u1.l3`
→ `tarot.u1.l3.e5`) and are **never reused or renamed** once shipped —
progress keys on them. `src/content/content.test.ts` enforces schema
validity, global ID uniqueness, the prefix chain, per-course slug
uniqueness, image-file existence on disk, and ≥3 exercises per playable
lesson. It runs in plain `npm test` — content cannot merge broken.

## Testing

- **Vitest** (node env, no jsdom): game rules at their boundaries,
  store round-trips, content validation.
- **Playwright**: a full lesson playthrough deriving answers from the
  content import (choices render unshuffled in v0 precisely to keep
  this deterministic), plus the heart-loss path; a separate spec
  captures the core screens as PNGs.
