import type { Metadata } from "next";
import Image from "next/image";
import { MoonCrescent, Sparkles } from "@/components/icons";
import { Card } from "@/components/ui/Card";
import { cardImage, majorArcana } from "@/content/courses/tarot-cards";

export const metadata: Metadata = { title: "Grimoire" };

const zodiac = [
  { name: "Aries", glyph: "♈︎", element: "Fire", dates: "Mar 21 – Apr 19" },
  { name: "Taurus", glyph: "♉︎", element: "Earth", dates: "Apr 20 – May 20" },
  { name: "Gemini", glyph: "♊︎", element: "Air", dates: "May 21 – Jun 20" },
  { name: "Cancer", glyph: "♋︎", element: "Water", dates: "Jun 21 – Jul 22" },
  { name: "Leo", glyph: "♌︎", element: "Fire", dates: "Jul 23 – Aug 22" },
  { name: "Virgo", glyph: "♍︎", element: "Earth", dates: "Aug 23 – Sep 22" },
  { name: "Libra", glyph: "♎︎", element: "Air", dates: "Sep 23 – Oct 22" },
  { name: "Scorpio", glyph: "♏︎", element: "Water", dates: "Oct 23 – Nov 21" },
  {
    name: "Sagittarius",
    glyph: "♐︎",
    element: "Fire",
    dates: "Nov 22 – Dec 21",
  },
  {
    name: "Capricorn",
    glyph: "♑︎",
    element: "Earth",
    dates: "Dec 22 – Jan 19",
  },
  { name: "Aquarius", glyph: "♒︎", element: "Air", dates: "Jan 20 – Feb 18" },
  { name: "Pisces", glyph: "♓︎", element: "Water", dates: "Feb 19 – Mar 20" },
];

export default function GrimoirePage() {
  return (
    <div className="space-y-10 py-6">
      <Card className="flex items-center gap-3 border-amethyst-500/30 bg-gradient-to-r from-amethyst-600/20 to-midnight-900 p-5">
        <Sparkles size={24} className="shrink-0 text-amethyst-300" />
        <div>
          <h1 className="font-display text-2xl font-semibold">
            Your Grimoire
          </h1>
          <p className="text-sm text-moon-300">
            Every mystery you study is recorded here. A spaced-repetition
            review deck awakens soon — for now, browse freely.
          </p>
        </div>
      </Card>

      <section>
        <h2 className="font-display mb-4 flex items-center gap-2 text-2xl font-semibold">
          <MoonCrescent size={20} className="text-gold-400" />
          The Major Arcana
        </h2>
        <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
          {majorArcana.map((card) => (
            <Card key={card.id} className="overflow-hidden">
              <Image
                src={cardImage(card)}
                alt={`${card.name} — Rider–Waite–Smith tarot card`}
                width={400}
                height={684}
                className="h-auto w-full"
              />
              <div className="p-3">
                <p className="text-xs text-moon-500">{card.numeral}</p>
                <h3 className="font-display text-lg leading-tight font-semibold">
                  {card.name}
                </h3>
                <p className="mt-1 text-xs text-moon-300">
                  {card.keywords.join(" · ")}
                </p>
              </div>
            </Card>
          ))}
        </div>
      </section>

      <section>
        <h2 className="font-display mb-4 flex items-center gap-2 text-2xl font-semibold">
          <Sparkles size={20} className="text-amethyst-300" />
          The Zodiac
        </h2>
        <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
          {zodiac.map((sign) => (
            <Card key={sign.name} className="p-4 text-center">
              <span className="text-4xl text-gold-300">{sign.glyph}</span>
              <h3 className="font-display mt-2 text-lg font-semibold">
                {sign.name}
              </h3>
              <p className="text-xs text-moon-300">{sign.element}</p>
              <p className="mt-1 text-xs text-moon-500">{sign.dates}</p>
            </Card>
          ))}
        </div>
      </section>
    </div>
  );
}
