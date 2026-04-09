import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import vueDevTools from 'vite-plugin-vue-devtools';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/main.ts'],
      refresh: true,
    }),
    vue(),
    vueDevTools(),
    tailwindcss(),
  ],
  server: {
    watch: {
      ignored: ['**/storage/framework/views/**'],
    },
  },
  resolve: {
    alias: {
      '@': '/resources/js',
    },
  },
  build: {
    rollupOptions: {
      input: 'resources/js/main.ts',
      output: {
        manualChunks(id) {
          if (id.includes('node_modules')) return 'vendor';

          if (id.endsWith('route.ts')) return 'index';

          const match = id.match(/resources\/js\/modules\/([^/]+)\//);
          if (match) return `module-${match[1]}`;
          
          return 'index';
        },
      },
    },
    chunkSizeWarningLimit: 700
  },
});
