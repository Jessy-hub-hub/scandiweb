import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import svgr from 'vite-plugin-svgr';

export default defineConfig(({ command }) => ({
  // Correct base path for GitHub Pages
  base: command === 'build' ? '/scandiweb/' : '/',
  plugins: [react(), svgr()],
  server: {
    port: 3000,
    proxy: {
      '/graphql': {
        target: 'https://rugurujane.xyz/backend',
        changeOrigin: true,
      },
    },
  },
  build: {
    outDir: 'docs' // GitHub Pages serves from "docs"
  }
}));
