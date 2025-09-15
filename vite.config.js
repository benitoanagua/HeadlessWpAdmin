import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [tailwindcss()],
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
    cssCodeSplit: false,
  },
  publicDir: false,
  server: {
    port: 3000,
    host: true,
  },
});
