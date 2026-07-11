import type { Course } from "../types";

// Zodiac glyphs carry U+FE0E so they render as text, not emoji.
const ARIES = "♈︎";
const TAURUS = "♉︎";
const GEMINI = "♊︎";
const CANCER = "♋︎";

/**
 * Astrology — Course 2. Unit 1 opens with the wheel itself; the
 * elements and the planets follow as coming-soon units.
 */
export const astrologyCourse = {
  id: "course_astrology",
  slug: "astrology",
  title: "Astrology",
  tagline: "Meet the twelve signs and the wheel behind your chart.",
  icon: "zodiac",
  accent: "amethyst",
  units: [
    {
      id: "astrology.u1",
      slug: "meet-the-zodiac",
      title: "Meet the Zodiac",
      description: "Twelve signs, four elements, one turning wheel.",
      lessons: [
        {
          id: "astrology.u1.l1",
          slug: "the-wheel-of-twelve",
          title: "The Wheel of Twelve",
          description: "The zodiac's shape and its four elements.",
          xpReward: 10,
          exercises: [
            {
              id: "astrology.u1.l1.e1",
              type: "multiple-choice",
              prompt: "How many signs make up the zodiac wheel?",
              choices: [
                { id: "a", text: "12" },
                { id: "b", text: "10" },
                { id: "c", text: "13" },
                { id: "d", text: "8" },
              ],
              correctChoiceId: "a",
              explanation:
                "Twelve signs divide the sky's wheel into equal 30° slices.",
            },
            {
              id: "astrology.u1.l1.e2",
              type: "fill-blank",
              prompt: "Complete the sentence.",
              before: "The first sign of the zodiac is",
              after: ".",
              answer: "aries",
              explanation:
                "Aries the ram opens the wheel at the spring equinox — the astrological new year.",
            },
            {
              id: "astrology.u1.l1.e3",
              type: "match-pairs",
              prompt: "Match each sign to its glyph.",
              pairs: [
                { id: "p1", left: "Aries", right: ARIES },
                { id: "p2", left: "Taurus", right: TAURUS },
                { id: "p3", left: "Gemini", right: GEMINI },
                { id: "p4", left: "Cancer", right: CANCER },
              ],
              explanation:
                "Ram's horns, bull's head, the twins, the crab — the glyphs sketch their signs.",
            },
            {
              id: "astrology.u1.l1.e4",
              type: "multiple-choice",
              prompt: "The twelve signs divide evenly among how many elements?",
              choices: [
                { id: "a", text: "4" },
                { id: "b", text: "3" },
                { id: "c", text: "6" },
                { id: "d", text: "2" },
              ],
              correctChoiceId: "a",
              explanation:
                "Fire, Earth, Air, and Water — three signs each, just like the tarot suits.",
            },
            {
              id: "astrology.u1.l1.e5",
              type: "true-false",
              prompt: "True or false?",
              statement: "Leo is a Water sign.",
              answer: false,
              explanation:
                "Leo blazes with Fire, alongside Aries and Sagittarius.",
            },
            {
              id: "astrology.u1.l1.e6",
              type: "multiple-choice",
              prompt: "Which trio are the Water signs?",
              choices: [
                { id: "a", text: "Cancer, Scorpio, Pisces" },
                { id: "b", text: "Aries, Leo, Sagittarius" },
                { id: "c", text: "Taurus, Virgo, Capricorn" },
                { id: "d", text: "Gemini, Libra, Aquarius" },
              ],
              correctChoiceId: "a",
              explanation:
                "The Water signs feel their way through the world — deep, intuitive, tidal.",
            },
            {
              id: "astrology.u1.l1.e7",
              type: "multiple-choice",
              prompt: "Scorpio's element is…",
              choices: [
                { id: "a", text: "Water" },
                { id: "b", text: "Fire" },
                { id: "c", text: "Air" },
                { id: "d", text: "Earth" },
              ],
              correctChoiceId: "a",
              explanation:
                "Scorpio is Water at its deepest — still surface, strong current.",
            },
          ],
        },
      ],
    },
    {
      id: "astrology.u2",
      slug: "the-elements",
      title: "The Elements",
      description: "Fire, Earth, Air, and Water — each trio in depth.",
      comingSoon: true,
      lessons: [
        {
          id: "astrology.u2.l1",
          slug: "fire-signs",
          title: "Fire Signs",
          description: "Aries, Leo, Sagittarius.",
          xpReward: 10,
          exercises: [],
        },
        {
          id: "astrology.u2.l2",
          slug: "earth-and-air",
          title: "Earth & Air",
          description: "Taurus, Virgo, Capricorn — Gemini, Libra, Aquarius.",
          xpReward: 10,
          exercises: [],
        },
        {
          id: "astrology.u2.l3",
          slug: "water-signs",
          title: "Water Signs",
          description: "Cancer, Scorpio, Pisces.",
          xpReward: 10,
          exercises: [],
        },
      ],
    },
    {
      id: "astrology.u3",
      slug: "planets-and-luminaries",
      title: "Planets & Luminaries",
      description: "Sun, Moon, and the wanderers that color every chart.",
      comingSoon: true,
      lessons: [
        {
          id: "astrology.u3.l1",
          slug: "sun-and-moon",
          title: "Sun & Moon",
          description: "The luminaries: core self and inner tide.",
          xpReward: 10,
          exercises: [],
        },
        {
          id: "astrology.u3.l2",
          slug: "the-inner-planets",
          title: "The Inner Planets",
          description: "Mercury, Venus, Mars.",
          xpReward: 10,
          exercises: [],
        },
      ],
    },
  ],
} satisfies Course;
