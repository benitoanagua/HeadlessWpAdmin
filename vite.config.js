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
    outDir: "public",
    rollupOptions: {
      input: {
        main: "assets/js/main.js",
      },
      output: {
        entryFileNames: "assets/[name].js",
        assetFileNames: "assets/[name].[ext]",
        chunkFileNames: "assets/[name].js",
      },
    },
    cssCodeSplit: false, // fuerza a que todo el CSS se bundle en un solo archivo
  },
  publicDir: false,
  server: {
    port: 3000,
    host: true,
  },
});
