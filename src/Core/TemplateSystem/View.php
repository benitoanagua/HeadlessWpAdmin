<?php

/**
 * Base View Class
 */

namespace HeadlessWPAdmin\Core\TemplateSystem;

abstract class View
{

    /**
     * Template renderer
     *
     * @var TemplateRenderer
     */
    protected $renderer;

    /**
     * Context data
     *
     * @var array<string, mixed>
     */
    protected $context = [];

    /**
     * Constructor
     *
     * @param TemplateRenderer $renderer
     */
    public function __construct(TemplateRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Get template path
     *
     * @return string
     */
    abstract protected function getTemplate(): string;

    /**
     * Render the view
     *
     * @return string
     */
    public function render(): string
    {
        return $this->renderer->render($this->getTemplate(), $this->context);
    }

    /**
     * Set context data
     *
     * @param array<string, mixed> $context
     * @return self
     */
    public function setContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Add context data
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function addContext(string $key, $value): self
    {
        $this->context[$key] = $value;
        return $this;
    }
}
