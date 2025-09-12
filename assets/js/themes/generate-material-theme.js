// assets/js/themes/generate-material-theme.js - VERSIÓN CORREGIDA Y OPTIMIZADA
import { writeFileSync, mkdirSync, existsSync } from "fs";
import { dirname, resolve } from "path";
import { fileURLToPath } from "url";
import {
  argbFromHex,
  hexFromArgb,
  MaterialDynamicColors,
  Hct,
  SchemeTonalSpot,
  SchemeNeutral,
  SchemeVibrant,
  SchemeExpressive,
  SchemeMonochrome,
  SchemeContent,
  SchemeFidelity,
} from "@material/material-color-utilities";

// Obtener __dirname para módulos ES
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Mapeo de variantes a constructores de esquema
const SCHEME_CONSTRUCTORS = {
  TONAL_SPOT: SchemeTonalSpot,
  NEUTRAL: SchemeNeutral,
  VIBRANT: SchemeVibrant,
  EXPRESSIVE: SchemeExpressive,
  MONOCHROME: SchemeMonochrome,
  CONTENT: SchemeContent,
  FIDELITY: SchemeFidelity,
};

export function generateMaterialTheme(config = {}) {
  // Configuración por defecto
  const defaultConfig = {
    outputDir: "assets/css", // Cambiado para que Vite lo procese
    seedColor: "#bb3813",
    variant: "CONTENT",
    contrastLevel: 1,
  };

  // Merge con configuración proporcionada
  const themeConfig = { ...defaultConfig, ...config };

  // Si outputDir es relativo, resolverlo desde el directorio del proyecto
  const outputDir = resolve(themeConfig.outputDir);

  console.log(
    `🎨 Generating theme with seed: ${themeConfig.seedColor}, variant: ${themeConfig.variant}`
  );
  console.log(`📁 Output directory: ${outputDir}`);

  function createScheme(isDark) {
    try {
      const sourceColor = argbFromHex(themeConfig.seedColor);
      const sourceHct = Hct.fromInt(sourceColor);
      const SchemeConstructor =
        SCHEME_CONSTRUCTORS[themeConfig.variant] || SchemeContent;
      return new SchemeConstructor(
        sourceHct,
        isDark,
        themeConfig.contrastLevel
      );
    } catch (error) {
      console.error(
        `❌ Error creating ${isDark ? "dark" : "light"} scheme:`,
        error.message
      );
      throw error;
    }
  }

  function extractColors(scheme) {
    const props = [
      "primary",
      "onPrimary",
      "primaryContainer",
      "onPrimaryContainer",
      "secondary",
      "onSecondary",
      "secondaryContainer",
      "onSecondaryContainer",
      "tertiary",
      "onTertiary",
      "tertiaryContainer",
      "onTertiaryContainer",
      "error",
      "onError",
      "errorContainer",
      "onErrorContainer",
      "background",
      "onBackground",
      "surface",
      "surfaceDim",
      "surfaceBright",
      "surfaceContainerLowest",
      "surfaceContainerLow",
      "surfaceContainer",
      "surfaceContainerHigh",
      "surfaceContainerHighest",
      "onSurface",
      "surfaceVariant",
      "onSurfaceVariant",
      "outline",
      "outlineVariant",
      "shadow",
      "scrim",
      "inverseSurface",
      "inverseOnSurface",
      "inversePrimary",
    ];

    const colors = {};
    for (const prop of props) {
      try {
        const color = MaterialDynamicColors[prop]?.getArgb(scheme);
        colors[prop] = hexFromArgb(color);
      } catch {
        colors[prop] = "#FF00FF";
      }
    }

    return colors;
  }

  try {
    const lightScheme = createScheme(false);
    const darkScheme = createScheme(true);

    const lightColors = extractColors(lightScheme);
    const darkColors = extractColors(darkScheme);

    // Generar CSS con mejor formato y mayor compatibilidad
    const cssContent = `@theme {
${Object.entries(lightColors)
  .map(([k, v]) => `  --color-${k}: ${v};`)
  .join("\n")}
}

[data-theme="dark"] {
${Object.entries(darkColors)
  .map(([k, v]) => `  --color-${k}: ${v};`)
  .join("\n")}
}`;

    // Crear directorio si no existe
    if (!existsSync(outputDir)) {
      mkdirSync(outputDir, { recursive: true });
      console.log(`📁 Created output directory: ${outputDir}`);
    }

    // Escribir archivo CSS
    const outputFile = resolve(outputDir, "material-tokens.css");
    writeFileSync(outputFile, cssContent, "utf8");

    console.log(`✅ Material tokens generated successfully`);
    console.log(`📄 File written to: ${outputFile}`);

    return cssContent;
  } catch (error) {
    console.error("❌ Error generating material theme:", error.message);
    console.error("Stack trace:", error.stack);
    throw error;
  }
}
