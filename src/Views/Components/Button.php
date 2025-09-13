<?php

/**
 * Button Component
 */

namespace HeadlessWPAdmin\Views\Components;

use HeadlessWPAdmin\Core\TemplateSystem\Component;

class Button extends Component
{

    /**
     * Button types
     */
    public const TYPE_PRIMARY = 'primary';
    public const TYPE_SECONDARY = 'secondary';
    public const TYPE_DANGER = 'danger';

    /**
     * Button text
     *
     * @var string
     */
    private $label;

    /**
     * Button type
     *
     * @var string
     */
    private $type;

    /**
     * HTML attributes
     *
     * @var array<string, string|bool>
     */
    private $attributes = [];

    /**
     * Constructor
     *
     * @param string $label
     * @param string $type
     * @param array<string, string|bool> $attributes
     */
    public function __construct(string $label, string $type = self::TYPE_PRIMARY, array $attributes = [])
    {
        $this->label = $label;
        $this->type = $type;
        $this->attributes = $attributes;

        // Base classes
        $this->addClass('px-4 py-2 rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2');

        // Type-specific classes
        switch ($type) {
            case self::TYPE_SECONDARY:
                $this->addClass('bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-500');
                break;
            case self::TYPE_DANGER:
                $this->addClass('bg-red-600 text-white hover:bg-red-700 focus:ring-red-500');
                break;
            case self::TYPE_PRIMARY:
            default:
                $this->addClass('bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500');
                break;
        }
    }

    /**
     * Render the component
     *
     * @param array<string, mixed> $context
     * @return string
     */
    public function render(array $context = []): string
    {
        $attrs = '';
        foreach ($this->attributes as $key => $value) {
            if ($value === true) {
                $attrs .= " {$key}";
            } else {
                $attrs .= " {$key}=\"" . $this->escAttr((string) $value) . "\"";
            }
        }

        ob_start();
?>
        <button type="button" class="<?php echo $this->getClassString(); ?>" <?php echo $attrs; ?>>
            <?php echo $this->esc($this->label); ?>
        </button>
<?php
        $output = ob_get_clean();
        return $output === false ? '' : $output;
    }

    /**
     * Get button type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
