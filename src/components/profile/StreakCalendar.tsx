import { Card } from "@/components/ui/Card";
import { localDateString } from "@/lib/utils";
import { cn } from "@/lib/utils";

const WEEKDAYS = ["S", "M", "T", "W", "T", "F", "S"];

/** Last ~5 weeks of ritual days, aligned to weekday columns. */
export function StreakCalendar({ history }: { history: string[] }) {
  const ritualDays = new Set(history);
  const today = new Date();
  const todayStr = localDateString(today);

  // Start 34 days back, padded to the previous Sunday.
  const start = new Date(today);
  start.setDate(start.getDate() - 34);
  start.setDate(start.getDate() - start.getDay());

  const cells: { date: string; future: boolean }[] = [];
  const cursor = new Date(start);
  while (cursor.getTime() <= today.getTime() || cursor.getDay() !== 0) {
    cells.push({
      date: localDateString(cursor),
      future: cursor.getTime() > today.getTime(),
    });
    cursor.setDate(cursor.getDate() + 1);
    if (cells.length > 49) break; // safety
  }

  return (
    <Card className="p-5">
      <h2 className="font-display mb-3 text-xl font-semibold">
        Ritual calendar
      </h2>
      <div className="grid grid-cols-7 gap-2 text-center">
        {WEEKDAYS.map((d, i) => (
          <span key={`${d}-${i}`} className="text-xs text-moon-500">
            {d}
          </span>
        ))}
        {cells.map(({ date, future }) => {
          const done = ritualDays.has(date);
          const isToday = date === todayStr;
          return (
            <span
              key={date}
              title={date}
              className={cn(
                "mx-auto flex h-8 w-8 items-center justify-center rounded-full text-[10px]",
                future && "invisible",
                done
                  ? "bg-gold-400 font-bold text-midnight-950"
                  : "bg-midnight-800 text-moon-500",
                isToday && "ring-2 ring-amethyst-400",
              )}
            >
              {date.slice(8)}
            </span>
          );
        })}
      </div>
      <p className="mt-3 text-xs text-moon-500">
        Gold days are completed rituals. Keep the chain unbroken.
      </p>
    </Card>
  );
}
