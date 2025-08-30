import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Laravel + Vite integration with automatic page reloads
export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  server: {
    // These help HMR when using XAMPP/Apache on Windows
    host: 'localhost',
    port: 5173,
    hmr: {
      host: 'localhost',
    },
  },
});

