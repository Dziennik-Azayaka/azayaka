import { fileURLToPath, URL } from "node:url";
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import vueDevTools from "vite-plugin-vue-devtools";
import laravel from 'laravel-vite-plugin';
import path from "path";

// https://vite.dev/config/
export default defineConfig({
    plugins: [vue(), vueDevTools(), laravel({ refresh: true, input: ['./src/main.ts'], hotFile: path.resolve(__dirname, "../../public/hot-authentication") })],
    envDir: '../..',
    resolve: {
        alias: [
            {
                find: "@",
                replacement: fileURLToPath(new URL("./src", import.meta.url))
            },
            {
                find: "#ui",
                replacement: fileURLToPath(new URL("../../frontend-packages/ui/src", import.meta.url))
            },
            {
                find: "#i18n",
                replacement: fileURLToPath(new URL("../../frontend-packages/i18n/src", import.meta.url))
            }
        ],
    },
    base: "/authentication",
    server: {
        port: 5001,
        host: 'localhost',
    },
    build: {
      outDir: path.resolve(__dirname, '../../public/build-authentication'),
      emptyOutDir: true,
      manifest: true,
    },
});
