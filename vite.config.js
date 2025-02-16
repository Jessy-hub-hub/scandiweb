// vite.config.js
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import svgr from 'vite-plugin-svgr';

export default defineConfig(({ command }) => ({
  // Use a relative base so that asset paths are correct.
  base: './',
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
    outDir: 'docs'
  }
}));
