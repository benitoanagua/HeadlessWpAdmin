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
        admin: "assets/js/admin.js",
        frontend: "assets/js/frontend.js",
      },
      output: {
        entryFileNames: "js/[name].js",
        chunkFileNames: "js/[name]-[hash].js",
        assetFileNames: (assetInfo) => {
          const info = assetInfo.name.split(".");
          const ext = info[info.length - 1];
          if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(ext)) {
            return `images/[name].[ext]`;
          }
          if (/css/i.test(ext)) {
            return `css/[name].[ext]`;
          }
          return `[name].[ext]`;
        },
      },
    },
  },

  server: {
    port: 3000,
    host: true,
  },

  publicDir: "static",
});
