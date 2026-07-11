import { BottomNav } from "@/components/shell/BottomNav";
import { StatBar } from "@/components/shell/StatBar";

export default function ShellLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <div className="min-h-dvh pb-24">
      <StatBar />
      <main className="mx-auto max-w-3xl px-4">{children}</main>
      <BottomNav />
    </div>
  );
}
