"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { MoonCrescent, Star, TarotCard } from "@/components/icons";
import { cn } from "@/lib/utils";

const items = [
  { href: "/learn", label: "Learn", Icon: TarotCard },
  { href: "/grimoire", label: "Grimoire", Icon: MoonCrescent },
  { href: "/profile", label: "Profile", Icon: Star },
];

export function BottomNav() {
  const pathname = usePathname();
  return (
    <nav className="fixed inset-x-0 bottom-0 z-40 border-t border-white/8 bg-midnight-950/90 backdrop-blur">
      <div className="mx-auto grid max-w-3xl grid-cols-3">
        {items.map(({ href, label, Icon }) => {
          const active = pathname === href || pathname.startsWith(`${href}/`);
          return (
            <Link
              key={href}
              href={href}
              aria-current={active ? "page" : undefined}
              className={cn(
                "flex flex-col items-center gap-0.5 py-2.5 text-xs font-semibold transition-colors",
                active ? "text-gold-400" : "text-moon-500 hover:text-moon-300",
              )}
            >
              <Icon size={22} />
              {label}
            </Link>
          );
        })}
      </div>
    </nav>
  );
}
