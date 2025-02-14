import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import svgr from 'vite-plugin-svgr';

export default defineConfig(({ command }) => ({
  // Set the base path only during production builds (e.g., GitHub Pages)
  base: command === 'build' ? '/scandiweb-project/' : '/',
  plugins: [react(), svgr()],
  server: {
    port: 3000,
    proxy: {
      '/graphql': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
}));
