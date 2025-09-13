<?php

/**
 * Base Component Class
 */

namespace HeadlessWPAdmin\Core\TemplateSystem;

abstract class Component
{

    /**
     * CSS classes
     *
     * @var array<string>
     */
    protected $classes = [];

    /**
     * CSS styles
     *
     * @var array<string, string>
     */
    protected $styles = [];

    /**
     * Render the component
     *
     * @param array<string, mixed> $context
     * @return string
     */
    abstract public function render(array $context = []): string;

    /**
     * Add CSS class
     *
     * @param string $class
     * @return self
     */
    public function addClass(string $class): self
    {
        $this->classes[] = $class;
        return $this;
    }

    /**
     * Add CSS style
     *
     * @param string $property
     * @param string $value
     * @return self
     */
    public function addStyle(string $property, string $value): self
    {
        $this->styles[$property] = $value;
        return $this;
    }

    /**
     * Get CSS classes as string
     *
     * @return string
     */
    protected function getClassString(): string
    {
        return implode(' ', array_unique($this->classes));
    }

    /**
     * Get CSS styles as string
     *
     * @return string
     */
    protected function getStyleString(): string
    {
        $styles = [];
        foreach ($this->styles as $property => $value) {
            $styles[] = "{$property}: {$value}";
        }
        return implode('; ', $styles);
    }

    /**
     * Escape HTML output
     *
     * @param string $text
     * @return string
     */
    protected function esc(string $text): string
    {
        return esc_html($text);
    }

    /**
     * Escape HTML attribute
     *
     * @param string $text
     * @return string
     */
    protected function escAttr(string $text): string
    {
        return esc_attr($text);
    }

    /**
     * Escape URL
     *
     * @param string $url
     * @return string
     */
    protected function escUrl(string $url): string
    {
        return esc_url($url);
    }
}
