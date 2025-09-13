<?php

/**
 * Status Indicator Component
 */

namespace HeadlessWPAdmin\Views\Components;

use HeadlessWPAdmin\Core\TemplateSystem\Component;

class StatusIndicator extends Component
{

    /**
     * Active status
     *
     * @var bool
     */
    private $active;

    /**
     * Label text
     *
     * @var string
     */
    private $label;

    /**
     * Constructor
     *
     * @param bool $active
     * @param string $label
     */
    public function __construct(bool $active, string $label = '')
    {
        $this->active = $active;
        $this->label = $label;

        // Base Tailwind 4 classes
        $this->addClass('flex items-center space-x-2');
    }

    /**
     * Render the component
     *
     * @param array<string, mixed> $context
     * @return string
     */
    public function render(array $context = []): string
    {
        $statusClass = $this->active
            ? 'bg-green-500 animate-pulse'
            : 'bg-gray-400';

        $labelClass = $this->active
            ? 'text-green-700'
            : 'text-gray-600';

        ob_start();
?>
        <div class="<?php echo $this->getClassString(); ?>">
            <div class="relative">
                <div class="w-3 h-3 rounded-full <?php echo $statusClass; ?> shadow-sm"></div>
                <?php if ($this->active): ?>
                    <div class="absolute -inset-1 w-5 h-5 bg-green-400/20 rounded-full"></div>
                <?php endif; ?>
            </div>
            <?php if ($this->label): ?>
                <span class="text-sm font-medium <?php echo $labelClass; ?>">
                    <?php echo $this->esc($this->label); ?>
                </span>
            <?php endif; ?>
        </div>
<?php
        $output = ob_get_clean();
        return $output === false ? '' : $output;
    }
}
