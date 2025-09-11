# Headless WordPress Admin

Administración headless para WordPress con interfaz moderna

## Características

- ✅ Arquitectura orientada a objetos con PSR-4 autoloading
- ✅ Gestión de dependencias con Composer y pnpm
- ✅ Pipeline de assets con Vite
- ✅ Frontend moderno con Svelte y Tailwind CSS v4
- ✅ Calidad de código con PHPStan y PHPCS
- ✅ CI/CD con GitHub Actions

## Requisitos

- PHP 8.0+
- WordPress 6.0+
- Node.js 18+
- Composer
- pnpm

## Instalación para desarrollo

1. Clona el repositorio:
   ```bash
   git clone <repository-url> headless-wp-admin
   cd headless-wp-admin
   ```

2. Instala las dependencias PHP:
   ```bash
   composer install
   ```

3. Instala las dependencias Node.js:
   ```bash
   pnpm install
   ```

4. Inicia el servidor de desarrollo:
   ```bash
   pnpm run dev
   ```

5. Para compilar para producción:
   ```bash
   pnpm run build
   ```

## Scripts disponibles

### PHP
- `composer run phpstan` - Análisis estático con PHPStan
- `composer run phpcs` - Revisión de estándares de código
- `composer run phpcbf` - Corrección automática de estándares
- `composer run test` - Ejecuta todas las pruebas de calidad

### Node.js
- `pnpm run dev` - Servidor de desarrollo
- `pnpm run build` - Compilación para producción
- `pnpm run preview` - Preview de la build
- `pnpm run check` - Verificación de tipos TypeScript

## Estructura del proyecto

```
headless-wp-admin/
├── src/                    # Código PHP
│   ├── Admin/             # Clases para el admin
│   ├── Frontend/          # Clases para el frontend
│   ├── Core/              # Clases principales
│   └── Utils/             # Utilidades
├── assets/                # Archivos fuente
│   ├── js/               # JavaScript
│   ├── css/              # CSS
│   ├── svelte/           # Componentes Svelte
│   └── images/           # Imágenes
├── public/               # Archivos compilados
├── tests/                # Pruebas
├── languages/            # Traducciones
└── vendor/               # Dependencias PHP
```

## Desarrollo

El plugin utiliza una arquitectura moderna con las siguientes tecnologías:

- **Backend**: PHP 8.0+ con arquitectura orientada a objetos y PSR-4
- **Frontend**: Svelte con Tailwind CSS v4
- **Build**: Vite para compilación ultrarrápida
- **Calidad**: PHPStan nivel 8 y PHPCS con estándares de WordPress

## Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

GPL v2 or later

## Autor

Benito Anagua - [benito.anagua@gmail.com](mailto:benito.anagua@gmail.com)
