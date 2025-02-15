import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import svgr from 'vite-plugin-svgr';

export default defineConfig(({ command }) => ({
  // When building for production (GitHub Pages), use the subdirectory as the base
  base: command === 'build' ? '/scandiweb-project/' : '/',
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
}));
