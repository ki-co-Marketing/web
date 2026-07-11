import { MoonCrescent, Pentacle, Sparkles } from "@/components/icons";
import { Card } from "@/components/ui/Card";

const placeholders = [
  { Icon: Pentacle, label: "First Reading" },
  { Icon: MoonCrescent, label: "Full Moon Streak" },
  { Icon: Sparkles, label: "Perfect Ritual" },
];

export function BadgesRow() {
  return (
    <Card className="p-5">
      <h2 className="font-display mb-3 text-xl font-semibold">Sigils</h2>
      <div className="grid grid-cols-3 gap-4">
        {placeholders.map(({ Icon, label }) => (
          <div
            key={label}
            className="flex flex-col items-center gap-2 rounded-xl border border-dashed border-midnight-700 p-4 text-center opacity-50"
          >
            <Icon size={28} className="text-moon-500" />
            <span className="text-xs text-moon-500">{label}</span>
          </div>
        ))}
      </div>
      <p className="mt-3 text-xs text-moon-500">
        Achievement sigils arrive in a future moon cycle.
      </p>
    </Card>
  );
}
