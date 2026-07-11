/**
 * The Major Arcana fact table — single source for lesson content, the
 * Grimoire reference deck, and card image paths.
 *
 * Imagery: Rider–Waite–Smith deck (1909), illustrated by Pamela Colman
 * Smith — public domain. Keywords are conventional upright meanings.
 */

/** Flip to "svg" if the stylized fallback deck is generated instead. */
export const CARD_IMAGE_EXT = "jpg" as const;

export interface MajorArcanaCard {
  /** Stable content id, e.g. "fool" */
  id: string;
  /** 0–21 */
  number: number;
  /** Display numeral, e.g. "0", "I", "XXI" */
  numeral: string;
  name: string;
  /** Image slug: `${number}-${slug}.${ext}` under /cards/tarot/major/ */
  slug: string;
  /** Conventional upright keywords */
  keywords: [string, string, string];
  /** A notable symbol or scene on the RWS card */
  symbol: string;
}

export function cardImage(
  card: Pick<MajorArcanaCard, "number" | "slug">,
): string {
  const nn = String(card.number).padStart(2, "0");
  return `/cards/tarot/major/${nn}-${card.slug}.${CARD_IMAGE_EXT}`;
}

export const majorArcana: MajorArcanaCard[] = [
  {
    id: "fool",
    number: 0,
    numeral: "0",
    name: "The Fool",
    slug: "the-fool",
    keywords: ["new beginnings", "innocence", "a leap of faith"],
    symbol:
      "A traveler steps toward a cliff edge, white rose in hand, a loyal little dog at his heels.",
  },
  {
    id: "magician",
    number: 1,
    numeral: "I",
    name: "The Magician",
    slug: "the-magician",
    keywords: ["manifestation", "willpower", "resourcefulness"],
    symbol:
      "One hand raised to the sky, one pointing to earth — as above, so below. All four suit emblems rest on his table.",
  },
  {
    id: "high-priestess",
    number: 2,
    numeral: "II",
    name: "The High Priestess",
    slug: "the-high-priestess",
    keywords: ["intuition", "mystery", "inner knowledge"],
    symbol:
      "Enthroned between the pillars Boaz and Jachin, a crescent moon at her feet and pomegranates behind her veil.",
  },
  {
    id: "empress",
    number: 3,
    numeral: "III",
    name: "The Empress",
    slug: "the-empress",
    keywords: ["abundance", "nurturing", "creativity"],
    symbol:
      "The symbol of Venus marks her heart-shaped shield beside a ripening field of wheat.",
  },
  {
    id: "emperor",
    number: 4,
    numeral: "IV",
    name: "The Emperor",
    slug: "the-emperor",
    keywords: ["authority", "structure", "stability"],
    symbol:
      "Four ram heads — the mark of Aries — adorn his stone throne among barren mountains.",
  },
  {
    id: "hierophant",
    number: 5,
    numeral: "V",
    name: "The Hierophant",
    slug: "the-hierophant",
    keywords: ["tradition", "spiritual guidance", "conformity"],
    symbol:
      "Crossed keys rest at his feet as two acolytes kneel to receive his blessing.",
  },
  {
    id: "lovers",
    number: 6,
    numeral: "VI",
    name: "The Lovers",
    slug: "the-lovers",
    keywords: ["love", "harmony", "meaningful choices"],
    symbol:
      "The angel Raphael blesses two figures in a garden that echoes Eden — serpent, fruit tree and all.",
  },
  {
    id: "chariot",
    number: 7,
    numeral: "VII",
    name: "The Chariot",
    slug: "the-chariot",
    keywords: ["willpower", "determination", "victory"],
    symbol:
      "Drawn by one black and one white sphinx — opposing forces steered by will alone.",
  },
  {
    id: "strength",
    number: 8,
    numeral: "VIII",
    name: "Strength",
    slug: "strength",
    keywords: ["courage", "compassion", "inner strength"],
    symbol:
      "A maiden gently closes a lion's jaws beneath the sign of infinity.",
  },
  {
    id: "hermit",
    number: 9,
    numeral: "IX",
    name: "The Hermit",
    slug: "the-hermit",
    keywords: ["introspection", "solitude", "inner guidance"],
    symbol:
      "An old man on a snowy peak lifts a lantern holding a six-pointed star.",
  },
  {
    id: "wheel-of-fortune",
    number: 10,
    numeral: "X",
    name: "Wheel of Fortune",
    slug: "wheel-of-fortune",
    keywords: ["cycles", "destiny", "turning points"],
    symbol:
      "A great wheel inscribed T-A-R-O turns among the clouds, sphinx perched on top.",
  },
  {
    id: "justice",
    number: 11,
    numeral: "XI",
    name: "Justice",
    slug: "justice",
    keywords: ["fairness", "truth", "cause and effect"],
    symbol:
      "An upright sword in one hand, balanced scales in the other.",
  },
  {
    id: "hanged-man",
    number: 12,
    numeral: "XII",
    name: "The Hanged Man",
    slug: "the-hanged-man",
    keywords: ["surrender", "new perspective", "sacred pause"],
    symbol:
      "Suspended upside-down from a living tree, serene, his head crowned with light.",
  },
  {
    id: "death",
    number: 13,
    numeral: "XIII",
    name: "Death",
    slug: "death",
    keywords: ["endings", "transformation", "renewal"],
    symbol:
      "A skeletal knight bears a white-rose banner while the sun rises between two distant towers.",
  },
  {
    id: "temperance",
    number: 14,
    numeral: "XIV",
    name: "Temperance",
    slug: "temperance",
    keywords: ["balance", "moderation", "alchemy"],
    symbol:
      "An angel pours water between two cups, one foot on land and one in the stream.",
  },
  {
    id: "devil",
    number: 15,
    numeral: "XV",
    name: "The Devil",
    slug: "the-devil",
    keywords: ["bondage", "materialism", "the shadow self"],
    symbol:
      "A horned figure crowned with an inverted pentagram — yet the chains below hang loose.",
  },
  {
    id: "tower",
    number: 16,
    numeral: "XVI",
    name: "The Tower",
    slug: "the-tower",
    keywords: ["sudden upheaval", "revelation", "awakening"],
    symbol:
      "Lightning strikes a crowned tower; figures leap into the dark as sparks rain down.",
  },
  {
    id: "star",
    number: 17,
    numeral: "XVII",
    name: "The Star",
    slug: "the-star",
    keywords: ["hope", "renewal", "serenity"],
    symbol:
      "A maiden pours water onto land and pool beneath one great star and seven lesser ones.",
  },
  {
    id: "moon",
    number: 18,
    numeral: "XVIII",
    name: "The Moon",
    slug: "the-moon",
    keywords: ["illusion", "intuition", "the unconscious"],
    symbol:
      "A dog and a wolf howl at the moon between two towers while a crayfish climbs from the pool.",
  },
  {
    id: "sun",
    number: 19,
    numeral: "XIX",
    name: "The Sun",
    slug: "the-sun",
    keywords: ["joy", "vitality", "success"],
    symbol:
      "A radiant child rides a white horse beneath a beaming sun and a wall of sunflowers.",
  },
  {
    id: "judgement",
    number: 20,
    numeral: "XX",
    name: "Judgement",
    slug: "judgement",
    keywords: ["awakening", "reckoning", "rebirth"],
    symbol:
      "An angel sounds a trumpet as figures rise, arms open, from their graves.",
  },
  {
    id: "world",
    number: 21,
    numeral: "XXI",
    name: "The World",
    slug: "the-world",
    keywords: ["completion", "wholeness", "fulfillment"],
    symbol:
      "A dancer floats within a laurel wreath, the four living creatures watching from the corners.",
  },
];

const byId = new Map(majorArcana.map((c) => [c.id, c]));

/** Content-authoring helper — throws at module load if the id is wrong. */
export function getCard(cardId: string): MajorArcanaCard {
  const card = byId.get(cardId);
  if (!card) throw new Error(`Unknown Major Arcana card id: ${cardId}`);
  return card;
}
