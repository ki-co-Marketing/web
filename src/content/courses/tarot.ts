import type { Course } from "../types";
import { cardImage, getCard } from "./tarot-cards";

const img = (cardId: string) => cardImage(getCard(cardId));

/**
 * Tarot — Course 1. Unit 1 walks the first eight Major Arcana
 * ("The Fool's Journey" begins); Units 2–3 are visible but coming soon.
 */
export const tarotCourse = {
  id: "course_tarot",
  slug: "tarot",
  title: "Tarot",
  tagline: "Walk the Fool's Journey through the Major Arcana.",
  icon: "tarot",
  accent: "gold",
  units: [
    {
      id: "tarot.u1",
      slug: "the-fools-journey",
      title: "The Fool's Journey",
      description:
        "The deck's shape, and the first eight cards of the Major Arcana.",
      lessons: [
        {
          id: "tarot.u1.l1",
          slug: "what-is-tarot",
          title: "What Is Tarot?",
          description: "78 cards, two arcana, four suits.",
          xpReward: 10,
          exercises: [
            {
              id: "tarot.u1.l1.e1",
              type: "multiple-choice",
              prompt: "How many cards are in a full tarot deck?",
              choices: [
                { id: "a", text: "78" },
                { id: "b", text: "52" },
                { id: "c", text: "64" },
                { id: "d", text: "22" },
              ],
              correctChoiceId: "a",
              explanation:
                "A full tarot deck holds 78 cards — 22 Major Arcana and 56 Minor Arcana.",
            },
            {
              id: "tarot.u1.l1.e2",
              type: "multiple-choice",
              prompt:
                "The Major Arcana — the deck's great mysteries — number how many cards?",
              choices: [
                { id: "a", text: "22" },
                { id: "b", text: "10" },
                { id: "c", text: "56" },
                { id: "d", text: "12" },
              ],
              correctChoiceId: "a",
              explanation:
                "Twenty-two cards run from The Fool (0) to The World (XXI), tracing life's big turning points.",
            },
            {
              id: "tarot.u1.l1.e3",
              type: "fill-blank",
              prompt: "Complete the sentence.",
              before: "The word arcana means",
              after: ".",
              answer: "secrets",
              acceptable: ["mysteries", "secret", "mystery"],
              explanation:
                "Arcana is Latin for secrets or mysteries — the Major Arcana are the deck's great mysteries.",
            },
            {
              id: "tarot.u1.l1.e4",
              type: "match-pairs",
              prompt: "Match each suit to its classical element.",
              pairs: [
                { id: "p1", left: "Wands", right: "Fire" },
                { id: "p2", left: "Cups", right: "Water" },
                { id: "p3", left: "Swords", right: "Air" },
                { id: "p4", left: "Pentacles", right: "Earth" },
              ],
              explanation:
                "Wands burn with passion, Cups hold feeling, Swords cut with thought, Pentacles ground us in the material.",
            },
            {
              id: "tarot.u1.l1.e5",
              type: "multiple-choice",
              prompt: "Which of these is NOT a tarot suit?",
              choices: [
                { id: "a", text: "Crowns" },
                { id: "b", text: "Wands" },
                { id: "c", text: "Cups" },
                { id: "d", text: "Swords" },
              ],
              correctChoiceId: "a",
              explanation:
                "The four suits are Wands, Cups, Swords, and Pentacles.",
            },
            {
              id: "tarot.u1.l1.e6",
              type: "true-false",
              prompt: "True or false?",
              statement: "The Minor Arcana contains 56 cards.",
              answer: true,
              explanation:
                "Four suits of fourteen cards each — ace through ten, plus Page, Knight, Queen, and King.",
            },
            {
              id: "tarot.u1.l1.e7",
              type: "multiple-choice",
              prompt:
                "Which suit speaks of emotion, intuition, and relationships?",
              choices: [
                { id: "a", text: "Cups" },
                { id: "b", text: "Wands" },
                { id: "c", text: "Pentacles" },
                { id: "d", text: "Swords" },
              ],
              correctChoiceId: "a",
              explanation:
                "Cups carry the element of Water — the suit of the heart.",
            },
          ],
        },
        {
          id: "tarot.u1.l2",
          slug: "the-fools-leap",
          title: "The Fool's Leap",
          description: "The Fool (0) and The Magician (I).",
          xpReward: 10,
          exercises: [
            {
              id: "tarot.u1.l2.e1",
              type: "image-choice",
              prompt: "Which card is The Fool?",
              choices: [
                { id: "fool", image: img("fool") },
                { id: "magician", image: img("magician") },
                { id: "high-priestess", image: img("high-priestess") },
                { id: "empress", image: img("empress") },
              ],
              correctChoiceId: "fool",
              explanation:
                "The Fool steps toward the cliff's edge, white rose in hand — the journey begins with a leap.",
            },
            {
              id: "tarot.u1.l2.e2",
              type: "multiple-choice",
              prompt: "What number does The Fool carry?",
              choices: [
                { id: "a", text: "0" },
                { id: "b", text: "1" },
                { id: "c", text: "13" },
                { id: "d", text: "21" },
              ],
              correctChoiceId: "a",
              explanation:
                "The Fool is unnumbered potential — zero, the open circle before the journey starts.",
            },
            {
              id: "tarot.u1.l2.e3",
              type: "true-false",
              prompt: "True or false?",
              statement: "A small dog accompanies The Fool at the cliff's edge.",
              answer: true,
              explanation:
                "The little white dog is loyalty and instinct, bounding along beside every new beginning.",
            },
            {
              id: "tarot.u1.l2.e4",
              type: "multiple-choice",
              prompt:
                "The Magician stands with one hand to the sky and one to the earth. Which principle is this?",
              choices: [
                { id: "a", text: "As above, so below" },
                { id: "b", text: "Know thyself" },
                { id: "c", text: "Nothing in excess" },
                { id: "d", text: "Fortune favors the bold" },
              ],
              correctChoiceId: "a",
              explanation:
                "The Magician channels the heavens into the material world — as above, so below.",
            },
            {
              id: "tarot.u1.l2.e5",
              type: "true-false",
              prompt: "True or false?",
              statement:
                "The Magician's table holds a symbol of every tarot suit.",
              answer: true,
              explanation:
                "Wand, cup, sword, and pentacle all rest on his table — every tool, ready to hand.",
            },
            {
              id: "tarot.u1.l2.e6",
              type: "multiple-choice",
              prompt: "The Magician's core meaning is…",
              choices: [
                { id: "a", text: "Manifesting your will" },
                { id: "b", text: "Surrendering control" },
                { id: "c", text: "Grieving a loss" },
                { id: "d", text: "Avoiding a decision" },
              ],
              correctChoiceId: "a",
              explanation:
                "Manifestation, willpower, resourcefulness — the power to make ideas real.",
            },
            {
              id: "tarot.u1.l2.e7",
              type: "fill-blank",
              prompt: "Complete The Fool's keyword.",
              before: "The Fool stands for new",
              after: ".",
              answer: "beginnings",
              acceptable: ["starts", "beginning"],
              explanation:
                "New beginnings, innocence, and a leap of faith — that is The Fool's gift.",
            },
          ],
        },
        {
          id: "tarot.u1.l3",
          slug: "secrets-and-abundance",
          title: "Secrets & Abundance",
          description: "The High Priestess (II) and The Empress (III).",
          xpReward: 10,
          exercises: [
            {
              id: "tarot.u1.l3.e1",
              type: "image-choice",
              prompt: "Which card is The High Priestess?",
              choices: [
                { id: "high-priestess", image: img("high-priestess") },
                { id: "empress", image: img("empress") },
                { id: "fool", image: img("fool") },
                { id: "magician", image: img("magician") },
              ],
              correctChoiceId: "high-priestess",
              explanation:
                "She sits veiled between two pillars, the crescent moon at her feet — keeper of what is not yet spoken.",
            },
            {
              id: "tarot.u1.l3.e2",
              type: "multiple-choice",
              prompt:
                "The High Priestess sits between two pillars. What letters mark them?",
              choices: [
                { id: "a", text: "B and J" },
                { id: "b", text: "A and Z" },
                { id: "c", text: "X and Y" },
                { id: "d", text: "M and N" },
              ],
              correctChoiceId: "a",
              explanation:
                "Boaz and Jachin — the pillars of Solomon's temple, severity and mercy, darkness and light.",
            },
            {
              id: "tarot.u1.l3.e3",
              type: "multiple-choice",
              prompt:
                "Which card represents intuition and the life beneath the surface?",
              choices: [
                { id: "a", text: "The High Priestess" },
                { id: "b", text: "The Emperor" },
                { id: "c", text: "The Chariot" },
                { id: "d", text: "The Hierophant" },
              ],
              correctChoiceId: "a",
              explanation:
                "The High Priestess asks you to listen inward — the answer is already known, below the surface.",
            },
            {
              id: "tarot.u1.l3.e4",
              type: "true-false",
              prompt: "True or false?",
              statement: "The Empress is associated with the planet Venus.",
              answer: true,
              explanation:
                "The symbol of Venus rests on her heart-shaped shield — love, beauty, and fertility.",
            },
            {
              id: "tarot.u1.l3.e5",
              type: "match-pairs",
              prompt: "Match each card to its keyword.",
              pairs: [
                { id: "p1", left: "The High Priestess", right: "intuition" },
                { id: "p2", left: "The Empress", right: "abundance" },
                { id: "p3", left: "The Magician", right: "manifestation" },
                { id: "p4", left: "The Fool", right: "new beginnings" },
              ],
              explanation:
                "Four cards in, and the journey already has its compass points.",
            },
            {
              id: "tarot.u1.l3.e6",
              type: "fill-blank",
              prompt: "Complete the scene.",
              before: "A crescent",
              after: "rests at the High Priestess's feet.",
              answer: "moon",
              explanation:
                "The moon is her element — cycles, tides, and the subconscious.",
            },
            {
              id: "tarot.u1.l3.e7",
              type: "multiple-choice",
              prompt: "The Empress reigns over which landscape?",
              choices: [
                { id: "a", text: "A ripening wheat field" },
                { id: "b", text: "A barren desert" },
                { id: "c", text: "A stormy sea" },
                { id: "d", text: "A walled city" },
              ],
              correctChoiceId: "a",
              explanation:
                "Everything around The Empress grows — she is nature, nurture, and creative abundance.",
            },
          ],
        },
        {
          id: "tarot.u1.l4",
          slug: "order-and-tradition",
          title: "Order & Tradition",
          description: "The Emperor (IV) and The Hierophant (V).",
          xpReward: 10,
          exercises: [
            {
              id: "tarot.u1.l4.e1",
              type: "image-choice",
              prompt: "Which card is The Emperor?",
              choices: [
                { id: "emperor", image: img("emperor") },
                { id: "hierophant", image: img("hierophant") },
                { id: "empress", image: img("empress") },
                { id: "chariot", image: img("chariot") },
              ],
              correctChoiceId: "emperor",
              explanation:
                "Armored beneath his robes on a stone throne — structure holding the wild world steady.",
            },
            {
              id: "tarot.u1.l4.e2",
              type: "multiple-choice",
              prompt:
                "The ram heads on the Emperor's throne tie him to which zodiac sign?",
              choices: [
                { id: "a", text: "Aries" },
                { id: "b", text: "Taurus" },
                { id: "c", text: "Leo" },
                { id: "d", text: "Pisces" },
              ],
              correctChoiceId: "a",
              explanation:
                "The ram is Aries — bold, first, and commanding. Tarot and astrology share a language; you'll meet Aries again in the Astrology course.",
            },
            {
              id: "tarot.u1.l4.e3",
              type: "multiple-choice",
              prompt: "The Emperor stands for…",
              choices: [
                { id: "a", text: "Authority and structure" },
                { id: "b", text: "Chaos and luck" },
                { id: "c", text: "Secrets and silence" },
                { id: "d", text: "Endless wandering" },
              ],
              correctChoiceId: "a",
              explanation:
                "Authority, structure, stability — the architecture that lets things flourish.",
            },
            {
              id: "tarot.u1.l4.e4",
              type: "true-false",
              prompt: "True or false?",
              statement: "The Hierophant represents rebellion against tradition.",
              answer: false,
              explanation:
                "Quite the opposite — The Hierophant is tradition, teaching, and the wisdom passed down through institutions.",
            },
            {
              id: "tarot.u1.l4.e5",
              type: "fill-blank",
              prompt: "Complete the scene.",
              before: "Crossed",
              after: "rest at the Hierophant's feet.",
              answer: "keys",
              explanation:
                "The crossed keys unlock the outer and inner mysteries — teaching as a sacred trust.",
            },
            {
              id: "tarot.u1.l4.e6",
              type: "multiple-choice",
              prompt: "Who kneels before the Hierophant?",
              choices: [
                { id: "a", text: "Two acolytes" },
                { id: "b", text: "Two sphinxes" },
                { id: "c", text: "A lion" },
                { id: "d", text: "No one — he is alone" },
              ],
              correctChoiceId: "a",
              explanation:
                "Two acolytes receive his blessing — knowledge moving from teacher to student.",
            },
            {
              id: "tarot.u1.l4.e7",
              type: "match-pairs",
              prompt: "Match each card to its keyword.",
              pairs: [
                { id: "p1", left: "The Emperor", right: "structure" },
                { id: "p2", left: "The Hierophant", right: "tradition" },
                { id: "p3", left: "The Empress", right: "nurturing" },
                { id: "p4", left: "The High Priestess", right: "mystery" },
              ],
              explanation:
                "Mother, father, teacher, oracle — the court around the young Fool.",
            },
          ],
        },
        {
          id: "tarot.u1.l5",
          slug: "hearts-and-victory",
          title: "Hearts & Victory",
          description: "The Lovers (VI) and The Chariot (VII).",
          xpReward: 10,
          exercises: [
            {
              id: "tarot.u1.l5.e1",
              type: "image-choice",
              prompt: "Which card is The Lovers?",
              choices: [
                { id: "lovers", image: img("lovers") },
                { id: "chariot", image: img("chariot") },
                { id: "hierophant", image: img("hierophant") },
                { id: "magician", image: img("magician") },
              ],
              correctChoiceId: "lovers",
              explanation:
                "An angel blesses two figures in a garden that remembers Eden.",
            },
            {
              id: "tarot.u1.l5.e2",
              type: "multiple-choice",
              prompt: "Which angel watches over The Lovers?",
              choices: [
                { id: "a", text: "Raphael" },
                { id: "b", text: "Michael" },
                { id: "c", text: "Gabriel" },
                { id: "d", text: "Uriel" },
              ],
              correctChoiceId: "a",
              explanation:
                "Raphael, angel of healing — love as a healing, harmonizing force.",
            },
            {
              id: "tarot.u1.l5.e3",
              type: "true-false",
              prompt: "True or false?",
              statement:
                "Beyond romance, The Lovers can signal an important choice.",
              answer: true,
              explanation:
                "The Lovers often marks a values-defining crossroads — choosing what you truly align with.",
            },
            {
              id: "tarot.u1.l5.e4",
              type: "multiple-choice",
              prompt: "The Chariot is pulled by two…",
              choices: [
                { id: "a", text: "Sphinxes" },
                { id: "b", text: "Horses" },
                { id: "c", text: "Lions" },
                { id: "d", text: "Oxen" },
              ],
              correctChoiceId: "a",
              explanation:
                "One black sphinx, one white — riddling, opposing forces yoked to a single purpose.",
            },
            {
              id: "tarot.u1.l5.e5",
              type: "multiple-choice",
              prompt:
                "The sphinxes pull in different directions. The Chariot's lesson is…",
              choices: [
                { id: "a", text: "Steer opposing forces with will" },
                { id: "b", text: "Let the road decide" },
                { id: "c", text: "Travel only alone" },
                { id: "d", text: "Never leave home" },
              ],
              correctChoiceId: "a",
              explanation:
                "The charioteer holds no reins — the vehicle moves by willpower alone.",
            },
            {
              id: "tarot.u1.l5.e6",
              type: "fill-blank",
              prompt: "Complete The Chariot's keyword.",
              before: "The Chariot's core meaning is",
              after: "through willpower.",
              answer: "victory",
              acceptable: ["triumph", "winning"],
              explanation:
                "Victory through determination — momentum earned, not given.",
            },
            {
              id: "tarot.u1.l5.e7",
              type: "match-pairs",
              prompt: "Match each card to its keyword.",
              pairs: [
                { id: "p1", left: "The Lovers", right: "harmony" },
                { id: "p2", left: "The Chariot", right: "victory" },
                { id: "p3", left: "The Fool", right: "innocence" },
                { id: "p4", left: "The Empress", right: "creativity" },
              ],
              explanation:
                "Heart and will, beginning and bloom — the journey gathers speed.",
            },
          ],
        },
        {
          id: "tarot.u1.l6",
          slug: "review-the-journey-begins",
          title: "Review: The Journey Begins",
          description: "All eight cards, mixed and remembered.",
          xpReward: 15,
          exercises: [
            {
              id: "tarot.u1.l6.e1",
              type: "image-choice",
              prompt: "Which card is The Chariot?",
              choices: [
                { id: "chariot", image: img("chariot") },
                { id: "fool", image: img("fool") },
                { id: "lovers", image: img("lovers") },
                { id: "emperor", image: img("emperor") },
              ],
              correctChoiceId: "chariot",
              explanation:
                "The armored charioteer beneath a canopy of stars, sphinxes at rest before him.",
            },
            {
              id: "tarot.u1.l6.e2",
              type: "match-pairs",
              prompt: "Match each card to its keyword.",
              pairs: [
                { id: "p1", left: "The Fool", right: "a leap of faith" },
                { id: "p2", left: "The Magician", right: "manifestation" },
                { id: "p3", left: "The High Priestess", right: "intuition" },
                { id: "p4", left: "The Hierophant", right: "tradition" },
              ],
            },
            {
              id: "tarot.u1.l6.e3",
              type: "match-pairs",
              prompt: "Match each card to its numeral.",
              pairs: [
                { id: "p1", left: "The Fool", right: "0" },
                { id: "p2", left: "The Empress", right: "III" },
                { id: "p3", left: "The Emperor", right: "IV" },
                { id: "p4", left: "The Chariot", right: "VII" },
              ],
            },
            {
              id: "tarot.u1.l6.e4",
              type: "multiple-choice",
              prompt: "Which card carries the numeral II?",
              choices: [
                { id: "a", text: "The High Priestess" },
                { id: "b", text: "The Empress" },
                { id: "c", text: "The Magician" },
                { id: "d", text: "The Lovers" },
              ],
              correctChoiceId: "a",
              explanation:
                "II is The High Priestess — the still water after The Magician's spark.",
            },
            {
              id: "tarot.u1.l6.e5",
              type: "true-false",
              prompt: "True or false?",
              statement:
                "The Empress and The Emperor sit side by side as III and IV.",
              answer: true,
              explanation:
                "Nature's abundance and civilization's order — a matched pair at the heart of the early journey.",
            },
            {
              id: "tarot.u1.l6.e6",
              type: "multiple-choice",
              prompt:
                "Manifestation, willpower, resourcefulness — whose keywords are these?",
              choices: [
                { id: "a", text: "The Magician" },
                { id: "b", text: "The Fool" },
                { id: "c", text: "The Hierophant" },
                { id: "d", text: "The Chariot" },
              ],
              correctChoiceId: "a",
              explanation: "The Magician — as above, so below.",
            },
            {
              id: "tarot.u1.l6.e7",
              type: "fill-blank",
              prompt: "Complete the sentence.",
              before: "The Major Arcana's story is called the",
              after: "'s journey.",
              answer: "fool",
              explanation:
                "Card by card, the Fool meets teachers and trials — and so do we.",
            },
            {
              id: "tarot.u1.l6.e8",
              type: "image-choice",
              prompt: "Which card is The Empress?",
              choices: [
                { id: "empress", image: img("empress") },
                { id: "high-priestess", image: img("high-priestess") },
                { id: "lovers", image: img("lovers") },
                { id: "magician", image: img("magician") },
              ],
              correctChoiceId: "empress",
              explanation:
                "Crowned with stars in her wheat field, Venus on her shield — abundance embodied.",
            },
          ],
        },
      ],
    },
    {
      id: "tarot.u2",
      slug: "trials-and-turning-points",
      title: "Trials & Turning Points",
      description:
        "Strength through Temperance — the journey's middle passage.",
      comingSoon: true,
      lessons: [
        {
          id: "tarot.u2.l1",
          slug: "strength-and-solitude",
          title: "Strength & Solitude",
          description: "Strength (VIII) and The Hermit (IX).",
          xpReward: 10,
          exercises: [],
        },
        {
          id: "tarot.u2.l2",
          slug: "fate-and-fairness",
          title: "Fate & Fairness",
          description: "Wheel of Fortune (X) and Justice (XI).",
          xpReward: 10,
          exercises: [],
        },
        {
          id: "tarot.u2.l3",
          slug: "surrender-and-change",
          title: "Surrender & Change",
          description: "The Hanged Man (XII) and Death (XIII).",
          xpReward: 10,
          exercises: [],
        },
        {
          id: "tarot.u2.l4",
          slug: "the-alchemists-path",
          title: "The Alchemist's Path",
          description: "Temperance (XIV), and a review of the middle passage.",
          xpReward: 15,
          exercises: [],
        },
      ],
    },
    {
      id: "tarot.u3",
      slug: "shadow-and-light",
      title: "Shadow & Light",
      description: "The Devil through The World — descent, dawn, completion.",
      comingSoon: true,
      lessons: [
        {
          id: "tarot.u3.l1",
          slug: "chains-and-lightning",
          title: "Chains & Lightning",
          description: "The Devil (XV) and The Tower (XVI).",
          xpReward: 10,
          exercises: [],
        },
        {
          id: "tarot.u3.l2",
          slug: "hope-and-illusion",
          title: "Hope & Illusion",
          description: "The Star (XVII) and The Moon (XVIII).",
          xpReward: 10,
          exercises: [],
        },
        {
          id: "tarot.u3.l3",
          slug: "dawn-and-judgement",
          title: "Dawn & Judgement",
          description: "The Sun (XIX) and Judgement (XX).",
          xpReward: 10,
          exercises: [],
        },
        {
          id: "tarot.u3.l4",
          slug: "the-world-complete",
          title: "The World, Complete",
          description: "The World (XXI), and the whole journey reviewed.",
          xpReward: 15,
          exercises: [],
        },
      ],
    },
  ],
} satisfies Course;
