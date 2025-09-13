<?php

/**
 * PHP Render Strategy
 */

namespace HeadlessWPAdmin\Core\TemplateSystem\RenderStrategy;

class PhpRenderStrategy implements RenderStrategyInterface
{

    /**
     * Render PHP template
     *
     * @param string $templatePath
     * @param array<string, mixed> $context
     * @return string
     */
    public function render(string $templatePath, array $context = []): string
    {
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template file not found: {$templatePath}");
        }

        extract($context, EXTR_SKIP);
        ob_start();
        include $templatePath;
        $output = ob_get_clean();

        if ($output === false) {
            return '';
        }

        return $output;
    }
}
