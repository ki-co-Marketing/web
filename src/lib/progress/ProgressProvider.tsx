"use client";

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useReducer,
  type ReactNode,
} from "react";
import {
  GAME_CONFIG,
  addHearts,
  advanceStreak,
  appendXpEvents,
  computeHearts,
  computeLessonXp,
  isPerfect,
  loseHeart,
  xpEventsForLesson,
  type LessonResult,
} from "@/lib/game";
import { LocalStorageProgressStore } from "./local-storage-store";
import { MemoryProgressStore } from "./memory-store";
import {
  defaultSnapshot,
  type ProgressSnapshot,
  type ProgressStore,
} from "./types";

interface State {
  snapshot: ProgressSnapshot | null;
  hydrated: boolean;
}

type Action =
  | { type: "HYDRATE"; snapshot: ProgressSnapshot }
  | { type: "HEART_LOST"; at: string }
  | { type: "HEARTS_REFILLED"; at: string }
  | { type: "REFRESH_HEARTS"; at: string }
  | { type: "LESSON_COMPLETED"; result: LessonResult; at: string }
  | { type: "SWITCH_COURSE"; courseSlug: string }
  | { type: "RESET"; at: string };

function reducer(state: State, action: Action): State {
  if (action.type === "HYDRATE") {
    return { snapshot: action.snapshot, hydrated: true };
  }
  const s = state.snapshot;
  if (!s) return state;

  switch (action.type) {
    case "HEART_LOST": {
      const now = new Date(action.at);
      return { ...state, snapshot: { ...s, hearts: loseHeart(s.hearts, now) } };
    }
    case "HEARTS_REFILLED": {
      const now = new Date(action.at);
      return {
        ...state,
        snapshot: {
          ...s,
          hearts: { count: GAME_CONFIG.HEARTS_MAX, updatedAt: now.toISOString() },
        },
      };
    }
    case "REFRESH_HEARTS": {
      const now = new Date(action.at);
      const hearts = computeHearts(s.hearts, now);
      if (hearts === s.hearts) return state;
      return { ...state, snapshot: { ...s, hearts } };
    }
    case "LESSON_COMPLETED": {
      const now = new Date(action.at);
      const breakdown = computeLessonXp(action.result);
      const events = xpEventsForLesson(action.result, breakdown, now);
      const prev = s.lessons[action.result.lessonId];
      const pct =
        action.result.totalExercises > 0
          ? action.result.correctFirstTry / action.result.totalExercises
          : 1;
      const hearts = isPerfect(action.result)
        ? addHearts(s.hearts, GAME_CONFIG.PERFECT_LESSON_HEART_BONUS, now)
        : computeHearts(s.hearts, now);
      return {
        ...state,
        snapshot: {
          ...s,
          xpTotal: s.xpTotal + breakdown.total,
          xpEvents: appendXpEvents(s.xpEvents, events),
          hearts,
          streak: advanceStreak(s.streak, now),
          lessons: {
            ...s.lessons,
            [action.result.lessonId]: {
              completedAt: action.at,
              timesCompleted: (prev?.timesCompleted ?? 0) + 1,
              bestPct: Math.max(prev?.bestPct ?? 0, pct),
            },
          },
        },
      };
    }
    case "SWITCH_COURSE": {
      if (s.activeCourseSlug === action.courseSlug) return state;
      return {
        ...state,
        snapshot: { ...s, activeCourseSlug: action.courseSlug },
      };
    }
    case "RESET": {
      return { ...state, snapshot: defaultSnapshot(new Date(action.at)) };
    }
    default:
      return state;
  }
}

export interface ProgressActions {
  loseHeart(): void;
  refillHearts(): void;
  completeLesson(result: LessonResult): void;
  switchCourse(courseSlug: string): void;
  resetProgress(): void;
}

interface ProgressContextValue {
  snapshot: ProgressSnapshot | null;
  hydrated: boolean;
  actions: ProgressActions;
}

const ProgressContext = createContext<ProgressContextValue | null>(null);

export function ProgressProvider({
  children,
  store: storeOverride,
}: {
  children: ReactNode;
  /** Test seam — defaults to localStorage in the browser. */
  store?: ProgressStore;
}) {
  const store = useMemo<ProgressStore>(() => {
    if (storeOverride) return storeOverride;
    if (typeof window === "undefined") return new MemoryProgressStore();
    return new LocalStorageProgressStore(window.localStorage);
  }, [storeOverride]);

  const [state, dispatch] = useReducer(reducer, {
    snapshot: null,
    hydrated: false,
  });

  // Hydrate once from the store; a missing/corrupt snapshot starts fresh.
  useEffect(() => {
    let cancelled = false;
    store.load().then((loaded) => {
      if (cancelled) return;
      dispatch({
        type: "HYDRATE",
        snapshot: loaded ?? defaultSnapshot(new Date()),
      });
    });
    return () => {
      cancelled = true;
    };
  }, [store]);

  // Persist every post-hydration change.
  useEffect(() => {
    if (state.hydrated && state.snapshot) {
      void store.save(state.snapshot);
    }
  }, [state.hydrated, state.snapshot, store]);

  // Materialize lazy heart regeneration once a minute.
  useEffect(() => {
    if (!state.hydrated) return;
    const tick = () =>
      dispatch({ type: "REFRESH_HEARTS", at: new Date().toISOString() });
    tick();
    const id = setInterval(tick, 60_000);
    return () => clearInterval(id);
  }, [state.hydrated]);

  const loseHeartAction = useCallback(
    () => dispatch({ type: "HEART_LOST", at: new Date().toISOString() }),
    [],
  );
  const refillHearts = useCallback(
    () => dispatch({ type: "HEARTS_REFILLED", at: new Date().toISOString() }),
    [],
  );
  const completeLesson = useCallback(
    (result: LessonResult) =>
      dispatch({
        type: "LESSON_COMPLETED",
        result,
        at: new Date().toISOString(),
      }),
    [],
  );
  const switchCourse = useCallback(
    (courseSlug: string) => dispatch({ type: "SWITCH_COURSE", courseSlug }),
    [],
  );
  const resetProgress = useCallback(
    () => dispatch({ type: "RESET", at: new Date().toISOString() }),
    [],
  );

  const value = useMemo<ProgressContextValue>(
    () => ({
      snapshot: state.snapshot,
      hydrated: state.hydrated,
      actions: {
        loseHeart: loseHeartAction,
        refillHearts,
        completeLesson,
        switchCourse,
        resetProgress,
      },
    }),
    [
      state.snapshot,
      state.hydrated,
      loseHeartAction,
      refillHearts,
      completeLesson,
      switchCourse,
      resetProgress,
    ],
  );

  return (
    <ProgressContext.Provider value={value}>
      {children}
    </ProgressContext.Provider>
  );
}

export function useProgress(): ProgressContextValue {
  const ctx = useContext(ProgressContext);
  if (!ctx) {
    throw new Error("useProgress must be used within <ProgressProvider>");
  }
  return ctx;
}
