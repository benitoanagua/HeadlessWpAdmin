<?php

/**
 * Text Input Form Field Component
 */

namespace HeadlessWPAdmin\Views\Components;

use HeadlessWPAdmin\Core\TemplateSystem\Component;

class FormFieldText extends Component
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
     * Field type
     *
     * @var string
     */
    private $type;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $value
     * @param string $label
     * @param string $description
     * @param string $type
     */
    public function __construct(string $name, string $value = '', string $label = '', string $description = '', string $type = 'text')
    {
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
        $this->description = $description;
        $this->type = $type;

        // Base Tailwind classes
        $this->addClass('form-field text-field space-y-2');
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

            <input
                type="<?php echo $this->escAttr($this->type); ?>"
                name="<?php echo $this->escAttr($this->name); ?>"
                id="<?php echo $this->escAttr($this->name); ?>"
                value="<?php echo $this->escAttr($this->value); ?>"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">

            <?php if (!empty($this->description)): ?>
                <p class="text-xs text-gray-500"><?php echo $this->esc($this->description); ?></p>
            <?php endif; ?>
        </div>
<?php
        $output = ob_get_clean();
        return $output === false ? '' : $output;
    }
}
