/**
 * Downloads the 22 RWS Major Arcana scans from Wikimedia Commons as
 * pre-sized ~400px thumbnails into public/cards/tarot/major/.
 * Verifies JPEG magic bytes and a sane minimum size; retries 3× per file.
 *
 * Fallback if Commons is unreachable: `npm run generate:cards-fallback`
 * then set CARD_IMAGE_EXT = "svg" in src/content/courses/tarot-cards.ts.
 */
import { mkdir, writeFile } from "node:fs/promises";
import path from "node:path";
import { cards } from "./card-manifest.mjs";

const OUT_DIR = path.resolve("public/cards/tarot/major");
const WIDTH = 400;
const MIN_BYTES = 10_000;

function localName(card) {
  return `${String(card.number).padStart(2, "0")}-${card.slug}.jpg`;
}

async function fetchCard(card, attempt = 1) {
  const url = `https://commons.wikimedia.org/wiki/Special:FilePath/${encodeURIComponent(
    card.commonsFile,
  )}?width=${WIDTH}`;
  try {
    const res = await fetch(url, { redirect: "follow" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const buf = Buffer.from(await res.arrayBuffer());
    if (buf.length < MIN_BYTES) throw new Error(`too small: ${buf.length}B`);
    if (buf[0] !== 0xff || buf[1] !== 0xd8) throw new Error("not a JPEG");
    await writeFile(path.join(OUT_DIR, localName(card)), buf);
    return { ok: true, bytes: buf.length };
  } catch (err) {
    if (attempt < 3) {
      await new Promise((r) => setTimeout(r, 1500 * attempt));
      return fetchCard(card, attempt + 1);
    }
    return { ok: false, error: String(err) };
  }
}

await mkdir(OUT_DIR, { recursive: true });

let failed = 0;
let totalBytes = 0;
for (const card of cards) {
  const result = await fetchCard(card);
  if (result.ok) {
    totalBytes += result.bytes;
    console.log(`✓ ${localName(card)} (${(result.bytes / 1024).toFixed(0)}KB)`);
  } else {
    failed += 1;
    console.error(`✗ ${localName(card)} — ${result.error}`);
  }
}

console.log(
  `\n${cards.length - failed}/${cards.length} cards fetched, ${(totalBytes / 1024 / 1024).toFixed(1)}MB total`,
);
if (failed > 0) process.exit(1);
