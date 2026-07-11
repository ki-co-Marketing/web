import type { Metadata, Viewport } from "next";
import "@fontsource-variable/cormorant-garamond";
import "@fontsource-variable/inter";
import "./globals.css";
import { Starfield } from "@/components/effects/Starfield";
import { ProgressProvider } from "@/lib/progress/ProgressProvider";

export const metadata: Metadata = {
  title: {
    default: "Arcana — learn tarot & astrology",
    template: "%s · Arcana",
  },
  description:
    "A gamified path into tarot, astrology, and the mystic arts — one daily ritual at a time.",
};

export const viewport: Viewport = {
  themeColor: "#0b0b1a",
};

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="en">
      <body className="min-h-dvh antialiased">
        <div
          aria-hidden
          className="nebula-bg pointer-events-none fixed inset-0 -z-10"
        >
          <Starfield />
        </div>
        <ProgressProvider>{children}</ProgressProvider>
      </body>
    </html>
  );
}
