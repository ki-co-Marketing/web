import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // Sandbox-friendly: skip sharp-based optimization; card art is pre-sized
  // ~400px thumbnails. Re-enable when deploying to Vercel (Roadmap P1).
  images: { unoptimized: true },
};

export default nextConfig;
