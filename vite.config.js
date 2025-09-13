//vite.config.js
import { defineConfig } from "vite";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import tailwindcss from "@tailwindcss/vite";
import { resolve } from "path";
import { generateMaterialTheme } from "./assets/js/themes/generate-material-theme.js";

const themeConfig = {
  outputDir: resolve(process.cwd(), "assets/css"),
  seedColor: "#21759b",
  variant: "CONTENT",
  contrastLevel: 1,
};

generateMaterialTheme(themeConfig);

export default defineConfig({
  plugins: [svelte(), tailwindcss()],
  build: {
    manifest: true,
    outDir: "public",
    rollupOptions: {
      input: [
        "assets/css/app.css",
        "assets/js/admin/main.js",
        "assets/js/frontend/main.js",
      ],
      output: {
        entryFileNames: "assets/[name].js",
        chunkFileNames: "assets/[name].js",
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith(".css")) {
            return "assets/app.css";
          }
          return "assets/[name].[hash][extname]";
        },
      },
    },
  },
  server: {
    port: 3000,
    host: true,
  },
});
