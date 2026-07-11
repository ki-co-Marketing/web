/**
 * Offline fallback deck: emits 22 stylized SVG cards (midnight ground,
 * double gold frame, roman numeral, name, per-card geometric motif) into
 * public/cards/tarot/major/. Use only if the Wikimedia fetch is blocked;
 * then set CARD_IMAGE_EXT = "svg" in src/content/courses/tarot-cards.ts.
 */
import { mkdir, writeFile } from "node:fs/promises";
import path from "node:path";
import { cards } from "./card-manifest.mjs";

const OUT_DIR = path.resolve("public/cards/tarot/major");
const NUMERALS = "0,I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII,XIII,XIV,XV,XVI,XVII,XVIII,XIX,XX,XXI".split(
  ",",
);

const displayName = (slug) =>
  slug
    .split("-")
    .map((w) => w[0].toUpperCase() + w.slice(1))
    .join(" ");

/** A simple centered motif per card — stars, moons, and geometry. */
function motif(number) {
  const gold = "#E4B95B";
  const amethyst = "#A78BFA";
  switch (number) {
    case 17: // Star
      return `<path d="M200 240l14 42 44 4-33 29 10 43-35-23-35 23 10-43-33-29 44-4z" fill="${gold}"/>`;
    case 18: // Moon
      return `<path d="M245 250a55 55 0 1 1-70-70 44 44 0 1 0 70 70z" fill="${gold}"/>`;
    case 19: // Sun
      return `<circle cx="200" cy="290" r="42" fill="${gold}"/>${Array.from(
        { length: 12 },
        (_, i) => {
          const a = (i * Math.PI) / 6;
          const x1 = 200 + Math.cos(a) * 56;
          const y1 = 290 + Math.sin(a) * 56;
          const x2 = 200 + Math.cos(a) * 74;
          const y2 = 290 + Math.sin(a) * 74;
          return `<line x1="${x1.toFixed(1)}" y1="${y1.toFixed(1)}" x2="${x2.toFixed(1)}" y2="${y2.toFixed(1)}" stroke="${gold}" stroke-width="6" stroke-linecap="round"/>`;
        },
      ).join("")}`;
    case 16: // Tower
      return `<rect x="170" y="230" width="60" height="120" fill="${amethyst}"/><path d="M160 230h80l-12-26h-56z" fill="${gold}"/><path d="M262 176l-40 44 18 4-30 34" stroke="${gold}" stroke-width="8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>`;
    default: {
      // Rotating set of celestial motifs for the rest of the deck
      const variants = [
        `<circle cx="200" cy="290" r="46" fill="none" stroke="${gold}" stroke-width="5"/><path d="M200 252l11 30 32 2-25 21 8 31-26-17-26 17 8-31-25-21 32-2z" fill="${amethyst}"/>`,
        `<path d="M232 320a48 48 0 1 1-64-64 38 38 0 1 0 64 64z" fill="${amethyst}"/><circle cx="238" cy="252" r="7" fill="${gold}"/>`,
        `<rect x="162" y="252" width="76" height="76" rx="6" transform="rotate(45 200 290)" fill="none" stroke="${gold}" stroke-width="5"/><circle cx="200" cy="290" r="14" fill="${amethyst}"/>`,
      ];
      return variants[number % variants.length];
    }
  }
}

await mkdir(OUT_DIR, { recursive: true });
for (const card of cards) {
  const name = displayName(card.slug);
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 684">
  <rect width="400" height="684" rx="18" fill="#12122B"/>
  <rect x="12" y="12" width="376" height="660" rx="12" fill="none" stroke="#C99936" stroke-width="3"/>
  <rect x="22" y="22" width="356" height="640" rx="8" fill="none" stroke="#E4B95B" stroke-width="1.5"/>
  ${[40, 360].flatMap((x) => [46, 638].map((y) => `<circle cx="${x}" cy="${y}" r="4" fill="#F2D98D"/>`)).join("\n  ")}
  <text x="200" y="96" text-anchor="middle" font-family="Georgia, serif" font-size="44" fill="#E4B95B">${NUMERALS[card.number]}</text>
  ${motif(card.number)}
  <text x="200" y="600" text-anchor="middle" font-family="Georgia, serif" font-size="30" fill="#F4F1FA">${name}</text>
</svg>\n`;
  const file = `${String(card.number).padStart(2, "0")}-${card.slug}.svg`;
  await writeFile(path.join(OUT_DIR, file), svg);
  console.log(`✓ ${file}`);
}
console.log(`\n${cards.length} fallback SVG cards generated`);
