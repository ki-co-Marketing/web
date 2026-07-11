# Gameplay rules

Every number below lives in [`src/lib/game/config.ts`](../src/lib/game/config.ts) —
this document mirrors it; update both together. All rules are pure
functions in `src/lib/game/` taking `(state, now)` — no hidden clocks —
which is what makes them unit-testable and trivially portable to a
server later.

## XP

| Rule | Value |
| --- | --- |
| Correct exercise (first attempt) | **+2 XP** |
| Lesson completion | **+10 XP** (review lessons **+15**) |
| Perfect lesson bonus (all first-try, no hearts lost) | **+5 XP** |
| Practice replay completion | `floor(xpReward / 2)`, no perfect bonus |

A standard 7-exercise lesson played perfectly pays **29 XP**
(7×2 + 10 + 5). Every award is recorded as an `XpEvent`
(`exercise` / `lesson` / `perfect-bonus`), capped at the most recent
200 — the shape of the future `xp_events` table.

## Hearts

- Maximum **5**, start full.
- A wrong submission reveals the correct answer + explanation, marks the
  exercise missed, advances, and costs **1 heart** (max one per exercise).
- **Match-pairs mismatches never cost hearts** — but any mismatch
  forfeits that exercise's XP.
- At 0 hearts mid-lesson: *"Your energy is spent, seeker."* Choices:
  **Rest & return** (exit; lesson progress discarded) or **Begin the
  ritual anew** (hearts refill to 5, lesson restarts). No paywall in v0.
- Regeneration is lazy: **+1 heart per 30 minutes**, computed on read;
  partial progress toward the next heart is preserved.
- A **perfect lesson restores +1 heart**.

## Streaks — "daily rituals"

- A ritual day = completing **≥1 lesson** that local calendar day
  (device timezone in v0; a profile timezone arrives with accounts).
- Same day → unchanged (idempotent) · yesterday → **+1** · gap → reset to **1**.
- The displayed streak shows 0 once the chain is broken (last ritual
  before yesterday). History keeps the last 60 ritual days and drives
  the profile calendar.

## Ranks

| Rank | XP |
| --- | --- |
| Seeker | 0 |
| Initiate | 30 |
| Apprentice | 75 |
| Adept | 150 |
| Mystic | 250 |
| Sage | 400 |
| Oracle | 600 |
| Luminary | 850 |

Completing everything playable in v0 (~210 XP) lands at **Adept**,
40 XP shy of Mystic — replay/practice closes the gap.

## Unlocking

- Strictly linear within a unit: lesson *n+1* unlocks when lesson *n*
  is completed.
- A unit's first lesson unlocks when every prior unit is fully complete.
- `comingSoon` units are always locked; completed lessons stay
  accessible for practice.
- Courses are freely switchable — no course gating.
