# Authoring content

Courses are TypeScript modules in `src/content/courses/` — versioned,
code-reviewed, and validated by `npm test` before they can ship.

## Adding a course

1. Create `src/content/courses/<slug>.ts` exporting a
   `... satisfies Course` object (see `tarot.ts` for the fullest example).
2. Register it in `src/content/index.ts` (`allCourses`).
3. Run `npm test` — the content suite will tell you everything you got
   wrong. Fix until green.

## ID discipline (important)

IDs chain by prefix and are **forever**:

```
course slug   tarot
unit          tarot.u1
lesson        tarot.u1.l3
exercise      tarot.u1.l3.e5
```

Progress is keyed on lesson IDs. Never rename or reuse an ID once
shipped — add new ones instead. Slugs (kebab-case) are URL segments;
lesson slugs must be unique within their course.

## Exercise cookbook

| Type | Shape | Notes |
| --- | --- | --- |
| `multiple-choice` | 2–4 `choices`, one `correctChoiceId` | The workhorse |
| `image-choice` | exactly 4 image choices | 2×2 grid; alt text must not give the answer away; omit `label` for card-recognition drills |
| `match-pairs` | exactly 4 `pairs` | Right column shuffles at render; mismatches don't cost hearts but forfeit the XP |
| `true-false` | `statement` + `answer` | Great for myth-busting |
| `fill-blank` | `before`/`after` + `answer` (+ `acceptable[]`) | Compared case/whitespace-insensitively; author answers lowercase |

Every exercise should carry an `explanation` — it shows after answering
(right or wrong) and is where the actual teaching happens. Keep it to
1–2 sentences in the app's voice: warm, celestial, a little playful,
never scolding.

Playable lessons need **≥3 exercises** (aim for 6–8); mark a unit
`comingSoon: true` to show it locked on the map with empty lessons.

## Structure & pacing conventions

- Lesson 1 of a course teaches the *system* (deck shape, the wheel)
  before individual entities.
- Body lessons introduce **two related entities** (e.g. two cards) and
  interleave recall of earlier ones.
- Close each unit with a **review lesson** (`xpReward: 15`, ~8 mixed
  exercises) that recombines everything.
- Cross-reference sister subjects where natural (the Emperor's Aries
  rams tease the Astrology course).

## Tarot facts & imagery

`src/content/courses/tarot-cards.ts` is the Major Arcana fact table
(keywords + iconic symbols, standard upright RWS meanings) — lessons,
the Grimoire, and the image pipeline all read from it. Card art is the
public-domain Rider–Waite–Smith deck (1909, Pamela Colman Smith),
fetched from Wikimedia Commons by `npm run fetch:cards` into
`public/cards/tarot/major/` and committed. Any *new* imagery must be
public domain or licensed, and credited in the README.
