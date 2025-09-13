<?php

/**
 * Checkbox Form Field Component
 */

namespace HeadlessWPAdmin\Views\Components;

use HeadlessWPAdmin\Core\TemplateSystem\Component;

class FormFieldCheckbox extends Component
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
     * Checked state
     *
     * @var bool
     */
    private $checked;

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
     * @param bool $checked
     * @param string $label
     * @param string $description
     */
    public function __construct(string $name, string $value = '1', bool $checked = false, string $label = '', string $description = '')
    {
        $this->name = $name;
        $this->value = $value;
        $this->checked = $checked;
        $this->label = $label;
        $this->description = $description;

        // Base Tailwind classes
        $this->addClass('form-field checkbox-field space-y-2');
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
            <label class="flex items-center space-x-3 cursor-pointer group">
                <div class="relative">
                    <input
                        type="checkbox"
                        name="<?php echo $this->escAttr($this->name); ?>"
                        value="<?php echo $this->escAttr($this->value); ?>"
                        <?php echo $this->checked ? 'checked' : ''; ?>
                        class="sr-only peer">
                    <div class="w-5 h-5 bg-white border-2 border-gray-300 rounded-md 
                               peer-checked:bg-blue-600 peer-checked:border-blue-600 
                               peer-focus:ring-2 peer-focus:ring-blue-500/20
                               group-hover:border-blue-400 transition-all duration-200">
                        <svg class="w-3 h-3 text-white absolute top-0.5 left-0.5 opacity-0 
                                   peer-checked:opacity-100 transition-opacity duration-200"
                            viewBox="0 0 12 12" fill="currentColor">
                            <path d="M10.28 2.28L5 7.56l-2.28-2.28a.75.75 0 00-1.06 1.06L4.44 9.12a.75.75 0 001.06 0l6.72-6.72a.75.75 0 00-1.06-1.06z" />
                        </svg>
                    </div>
                </div>

                <?php if (!empty($this->label)): ?>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-900 group-hover:text-gray-700">
                            <?php echo $this->esc($this->label); ?>
                        </span>
                        <?php if (!empty($this->description)): ?>
                            <p class="text-xs text-gray-500 mt-1"><?php echo $this->esc($this->description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </label>
        </div>
<?php
        $output = ob_get_clean();
        return $output === false ? '' : $output;
    }
}
