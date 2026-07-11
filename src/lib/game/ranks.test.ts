import { describe, expect, it } from "vitest";
import { getRank } from "./ranks";

describe("getRank", () => {
  it("starts as Seeker with 0 progress", () => {
    const rank = getRank(0);
    expect(rank.name).toBe("Seeker");
    expect(rank.next?.name).toBe("Initiate");
    expect(rank.progress).toBe(0);
  });

  it("stays Seeker at 29 XP (the one-perfect-lesson boundary)", () => {
    const rank = getRank(29);
    expect(rank.name).toBe("Seeker");
    expect(rank.progress).toBeCloseTo(29 / 30);
  });

  it("promotes to Initiate exactly at 30 XP", () => {
    expect(getRank(30).name).toBe("Initiate");
  });

  it("holds Oracle at 849 and crowns Luminary at 850", () => {
    expect(getRank(849).name).toBe("Oracle");
    const top = getRank(850);
    expect(top.name).toBe("Luminary");
    expect(top.next).toBeNull();
    expect(top.progress).toBe(1);
  });

  it("clamps negative XP to Seeker", () => {
    expect(getRank(-10).name).toBe("Seeker");
  });
});
