<?php
namespace PapierMache\Controls;

if (class_exists('WP_Customize_Control')) {
    /**
    * Class to create a custom multiselect dropdown control
    */
    class Multiple_Checkbox_Custom_Control extends \WP_Customize_Control
    {
        /**
        * Render the content on the theme customizer page
        */
        public $type = 'multiple-checkbox';

        public function enqueue()
        {
			wp_enqueue_script('ppm_multiple-checkbox', plugin_dir_url(__FILE__)."multiple-checkbox.js", array(), PPM_V);
			wp_enqueue_style('ppm_multiple-checkbox', plugin_dir_url(__FILE__)."multiple-checkbox.css", array(), PPM_V);
        }

        public function render_content()
        {
            if (empty($this->choices)) {
                return;
            } ?>

        <?php if (!empty($this->label)) : ?>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
        <?php endif; ?>

        <?php if (!empty($this->description)) : ?>
            <span class="description customize-control-description"><?php echo $this->description; ?></span>
        <?php endif; ?>

        <?php $multi_values = !is_array($this->value()) ? explode(',', $this->value()) : $this->value(); ?>

        <ul>
            <?php foreach ($this->choices as $value => $label) : ?>

                <li>
                    <label>
                        <input type="checkbox" value="<?php echo esc_attr($value); ?>" <?php checked(in_array($value, $multi_values)); ?> />
                        <?php echo esc_html($label); ?>
                    </label>
                </li>

            <?php endforeach; ?>
        </ul>

        <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr(implode(',', $multi_values)); ?>" />
    <?php
        }

		public static function sanitize($values)
		{
			$multi_values = !is_array($values) ? explode(',', $values) : $values;

			return !empty($multi_values) ? array_map('sanitize_text_field', $multi_values) : false;
		}
    }
}
