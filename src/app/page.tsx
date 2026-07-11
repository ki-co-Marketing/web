import Image from "next/image";
import Link from "next/link";
import {
  Flame,
  MoonCrescent,
  Sparkles,
  Star,
  TarotCard,
} from "@/components/icons";
import { buttonStyles } from "@/components/ui/Button";
import { Card } from "@/components/ui/Card";

const previewCards = [
  { src: "/cards/tarot/major/00-the-fool.jpg", alt: "The Fool tarot card" },
  {
    src: "/cards/tarot/major/02-the-high-priestess.jpg",
    alt: "The High Priestess tarot card",
  },
  { src: "/cards/tarot/major/17-the-star.jpg", alt: "The Star tarot card" },
];

const zodiacGlyphs = ["♈︎", "♌︎", "♒︎", "♋︎", "♉︎", "♎︎"];

export default function LandingPage() {
  return (
    <div className="mx-auto flex min-h-dvh max-w-5xl flex-col px-6">
      <header className="flex items-center justify-between py-6">
        <div className="flex items-center gap-2">
          <MoonCrescent size={26} className="text-gold-400" />
          <span className="font-display text-2xl font-semibold tracking-wide">
            Arcana
          </span>
        </div>
        <Link href="/learn" className={buttonStyles("ghost", "sm")}>
          I already have progress
        </Link>
      </header>

      <main className="flex flex-1 flex-col">
        <section className="flex flex-col items-center py-16 text-center sm:py-24">
          <h1 className="font-display text-6xl font-semibold tracking-wide text-moon-100 sm:text-7xl">
            Arcana
          </h1>
          <p className="mt-4 max-w-xl text-lg text-moon-300">
            Learn tarot &amp; astrology, one daily ritual at a time. Bite-size
            lessons, streaks, and a grimoire that grows with you.
          </p>
          <Link
            href="/learn"
            className={buttonStyles("primary", "lg", "mt-8 shadow-glow")}
            data-testid="cta-begin"
          >
            Begin your journey
          </Link>
          <p className="mt-3 text-sm text-moon-500">
            Free · no account needed · 3 minutes a day
          </p>
        </section>

        <section className="grid gap-6 pb-16 sm:grid-cols-2">
          <Card className="group overflow-hidden p-6 transition-shadow hover:shadow-glow">
            <div className="flex items-center gap-2 text-gold-300">
              <TarotCard size={20} />
              <h2 className="font-display text-2xl font-semibold">Tarot</h2>
            </div>
            <p className="mt-1 text-sm text-moon-300">
              Walk the Fool&apos;s Journey through the Major Arcana — 8 cards
              in your first unit.
            </p>
            <div className="mt-6 flex justify-center">
              {previewCards.map((card, i) => (
                <Image
                  key={card.src}
                  src={card.src}
                  alt={card.alt}
                  width={200}
                  height={342}
                  className="h-auto w-24 rounded-lg border border-white/10 shadow-lg transition-transform group-hover:translate-y-[-4px] sm:w-28"
                  style={{
                    rotate: `${(i - 1) * 9}deg`,
                    translate: `0 ${Math.abs(i - 1) * 10}px`,
                    zIndex: i === 1 ? 2 : 1,
                    marginInline: "-0.5rem",
                    transitionDelay: `${i * 40}ms`,
                  }}
                />
              ))}
            </div>
          </Card>

          <Card className="group overflow-hidden p-6 transition-shadow hover:shadow-glow">
            <div className="flex items-center gap-2 text-amethyst-300">
              <Sparkles size={20} />
              <h2 className="font-display text-2xl font-semibold">
                Astrology
              </h2>
            </div>
            <p className="mt-1 text-sm text-moon-300">
              Meet the twelve signs, their elements, and the wheel that turns
              behind your birth chart.
            </p>
            <div className="mt-8 flex flex-wrap justify-center gap-3">
              {zodiacGlyphs.map((glyph, i) => (
                <span
                  key={glyph}
                  className="flex h-14 w-14 items-center justify-center rounded-full border border-white/10 bg-midnight-800 text-2xl text-gold-300 transition-transform group-hover:scale-105"
                  style={{ transitionDelay: `${i * 30}ms` }}
                >
                  {glyph}
                </span>
              ))}
            </div>
          </Card>
        </section>

        <section className="grid gap-6 pb-20 sm:grid-cols-3">
          {[
            {
              icon: <Flame size={22} className="text-gold-400" />,
              title: "Daily rituals",
              body: "Keep your streak alive with one short lesson a day.",
            },
            {
              icon: <Star size={22} className="text-gold-400" />,
              title: "Earn XP & ranks",
              body: "Climb from Seeker to Luminary as your practice deepens.",
            },
            {
              icon: <MoonCrescent size={22} className="text-gold-400" />,
              title: "Grow your grimoire",
              body: "Every card and sign you learn joins your reference deck.",
            },
          ].map((f) => (
            <div key={f.title} className="text-center sm:text-left">
              <div className="mb-2 inline-flex items-center justify-center rounded-xl border border-white/8 bg-midnight-900 p-2.5">
                {f.icon}
              </div>
              <h3 className="font-display text-xl font-semibold">{f.title}</h3>
              <p className="mt-1 text-sm text-moon-300">{f.body}</p>
            </div>
          ))}
        </section>
      </main>

      <footer className="border-t border-white/8 py-8 text-center text-xs text-moon-500">
        <p>✶ For insight, learning &amp; entertainment ✶</p>
        <p className="mt-2">
          Tarot imagery from the Rider–Waite–Smith deck (1909), illustrated by
          Pamela Colman Smith — public domain.
        </p>
      </footer>
    </div>
  );
}
