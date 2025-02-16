import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import svgr from 'vite-plugin-svgr';

export default defineConfig(({ command }) => ({
  // Set the base path to match your GitHub Pages repository name.
  base: '/scandiweb/',
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
    // If you're deploying to GitHub Pages, you might use "docs" or "dist"
    // as your output directory. Make sure your GitHub Pages settings match.
    outDir: 'docs'
  }
}));
