<?php

/**
 * Color Picker Form Field Component
 */

namespace HeadlessWPAdmin\Views\Components;

use HeadlessWPAdmin\Core\TemplateSystem\Component;

class FormFieldColor extends Component
{

    /**
     * Field name
     *
     * @var string
     */
    private $name;

    /**
     * Field value
     *
     * @var string
     */
    private $value;

    /**
     * Field label
     *
     * @var string
     */
    private $label;

    /**
     * Field description
     *
     * @var string
     */
    private $description;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $value
     * @param string $label
     * @param string $description
     */
    public function __construct(string $name, string $value = '', string $label = '', string $description = '')
    {
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
        $this->description = $description;

        // Base Tailwind classes
        $this->addClass('form-field color-field space-y-2');
    }

    /**
     * Render the component
     *
     * @param array<string, mixed> $context
     * @return string
     */
    public function render(array $context = []): string
    {
        ob_start();
?>
        <div class="<?php echo $this->getClassString(); ?>">
            <?php if (!empty($this->label)): ?>
                <label for="<?php echo $this->escAttr($this->name); ?>" class="block text-sm font-medium text-gray-900">
                    <?php echo $this->esc($this->label); ?>
                </label>
            <?php endif; ?>

            <div class="flex items-center space-x-3">
                <input
                    type="text"
                    name="<?php echo $this->escAttr($this->name); ?>"
                    id="<?php echo $this->escAttr($this->name); ?>"
                    value="<?php echo $this->escAttr($this->value); ?>"
                    class="color-picker w-16 h-8 rounded border border-gray-300">
                <div class="w-8 h-8 rounded border border-gray-300" style="background-color: <?php echo $this->escAttr($this->value); ?>"></div>
            </div>

            <?php if (!empty($this->description)): ?>
                <p class="text-xs text-gray-500"><?php echo $this->esc($this->description); ?></p>
            <?php endif; ?>
        </div>
<?php
        $output = ob_get_clean();
        return $output === false ? '' : $output;
    }
}
