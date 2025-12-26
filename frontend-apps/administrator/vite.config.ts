import vue from "@vitejs/plugin-vue";
import laravel from 'laravel-vite-plugin';
import { URL, fileURLToPath } from "node:url";
import path from "path";
import { defineConfig } from "vite";
import vueDevTools from "vite-plugin-vue-devtools";

// https://vite.dev/config/
export default defineConfig({
    plugins: [
        vue(),
        vueDevTools(),
        laravel({
            refresh: true,
            input: ["./src/main.ts"],
            hotFile: path.resolve(__dirname, "../../public/hot-administrator"),
            buildDirectory: "build-administrator"
        }),
    ],
    envDir: "../..",
    resolve: {
        alias: [
            {
                find: "@",
                replacement: fileURLToPath(new URL("./src", import.meta.url)),
            },
            {
                find: "#ui",
                replacement: fileURLToPath(new URL("../../frontend-packages/ui/src", import.meta.url)),
            },
            {
                find: "#i18n",
                replacement: fileURLToPath(new URL("../../frontend-packages/i18n/src", import.meta.url)),
            },
        ],
    },
    server: {
        port: 5002,
        host: "localhost",
    },
    build: {
        outDir: path.resolve(__dirname, "../../public/build-administrator"),
        emptyOutDir: true,
        manifest: true,
    },
});
