import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import svgr from "vite-plugin-svgr";

export default defineConfig({
  base: "/scandiweb/", // Prepend "/scandiweb/" to all built asset paths
  plugins: [react(), svgr()],
  server: {
    port: 3000,
    proxy: {
      "/graphql": {
        target: "https://rugurujane.xyz/backend",
        changeOrigin: true,
      },
    },
  },
  build: {
    outDir: "docs", // GitHub Pages serves from the "docs" folder
  },
});
