<?php

/**
 * Template Renderer for Headless WordPress Admin
 * Handles template location, rendering, and component system
 */

namespace HeadlessWPAdmin\Core\TemplateSystem;

use HeadlessWPAdmin\Core\TemplateSystem\RenderStrategy\PhpRenderStrategy;
use HeadlessWPAdmin\Core\TemplateSystem\RenderStrategy\RenderStrategyInterface;
use Exception;

class TemplateRenderer
{

    /**
     * Base path for templates
     *
     * @var string
     */
    private $basePath;

    /**
     * Render strategy
     *
     * @var RenderStrategyInterface
     */
    private $renderStrategy;

    /**
     * Component registry
     *
     * @var array<string, Component>
     */
    private $componentRegistry = [];

    /**
     * Constructor
     *
     * @param string $basePath
     * @param RenderStrategyInterface|null $strategy
     */
    public function __construct(string $basePath, ?RenderStrategyInterface $strategy = null)
    {
        $this->basePath = rtrim($basePath, '/');
        $this->renderStrategy = $strategy ?: new PhpRenderStrategy();
    }

    /**
     * Render a template
     *
     * @param string $template
     * @param array<string, mixed> $context
     * @return string
     * @throws Exception
     */
    public function render(string $template, array $context = []): string
    {
        $templatePath = $this->locateTemplate($template);
        return $this->renderStrategy->render($templatePath, $context);
    }

    /**
     * Render a component
     *
     * @param string $name
     * @param array<string, mixed> $props
     * @return string
     */
    public function component(string $name, array $props = []): string
    {
        if (isset($this->componentRegistry[$name])) {
            $component = $this->componentRegistry[$name];
            return $component->render($props);
        }

        // Fallback to PHP template
        try {
            return $this->render("components/{$name}", $props);
        } catch (Exception $e) {
            error_log("Component not found: {$name}");
            return '';
        }
    }

    /**
     * Register a component
     *
     * @param string $name
     * @param Component $component
     */
    public function registerComponent(string $name, Component $component): void
    {
        $this->componentRegistry[$name] = $component;
    }

    /**
     * Locate template file
     *
     * @param string $template
     * @return string
     * @throws Exception
     */
    private function locateTemplate(string $template): string
    {
        // 1. Check child theme
        $themePath = get_stylesheet_directory() . "/headless-wp-admin/{$template}.php";
        if (file_exists($themePath)) {
            return $themePath;
        }

        // 2. Check parent theme
        $parentPath = get_template_directory() . "/headless-wp-admin/{$template}.php";
        if (file_exists($parentPath)) {
            return $parentPath;
        }

        // 3. Check plugin views
        $pluginPath = "{$this->basePath}/Views/{$template}.php";
        if (file_exists($pluginPath)) {
            return $pluginPath;
        }

        throw new Exception("Template not found: {$template}");
    }

    /**
     * Get base path
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }
}
