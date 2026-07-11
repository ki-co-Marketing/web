"use client";

import Link from "next/link";
import { Heart } from "@/components/icons";
import { Button, buttonStyles } from "@/components/ui/Button";
import { Modal } from "@/components/ui/Modal";
import { GAME_CONFIG } from "@/lib/game";

export function HeartsOutModal({
  open,
  courseSlug,
  onRestart,
}: {
  open: boolean;
  courseSlug: string;
  onRestart: () => void;
}) {
  return (
    <Modal open={open} labelledBy="hearts-out-title">
      <div className="text-center">
        <Heart filled={false} size={44} className="mx-auto text-danger-400" />
        <h2
          id="hearts-out-title"
          className="font-display mt-3 text-2xl font-semibold"
        >
          Your energy is spent, seeker
        </h2>
        <p className="mt-2 text-sm text-moon-300">
          Hearts return with rest — one every{" "}
          {GAME_CONFIG.HEART_REGEN_MINUTES} minutes. Or begin this ritual anew
          with a full vessel; your progress in this lesson starts over.
        </p>
        <div className="mt-5 grid gap-2">
          <Button
            variant="primary"
            size="lg"
            onClick={onRestart}
            data-testid="hearts-out-restart"
          >
            Begin the ritual anew
          </Button>
          <Link
            href={`/learn/${courseSlug}`}
            data-testid="hearts-out-exit"
            className={buttonStyles("ghost", "md")}
          >
            Rest &amp; return to the path
          </Link>
        </div>
      </div>
    </Modal>
  );
}
