<?php

/**
 * Render Strategy Interface
 */

namespace HeadlessWPAdmin\Core\TemplateSystem\RenderStrategy;

interface RenderStrategyInterface
{

    /**
     * Render a template
     *
     * @param string $templatePath
     * @param array<string, mixed> $context
     * @return string
     */
    public function render(string $templatePath, array $context = []): string;
}
