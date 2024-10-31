<?php
namespace PapierMache\Controls;

if (class_exists('WP_Customize_Control')) {
	/**
	 * Class to create a custom multiselect dropdown control
	 */
	class Multiple_Select_Custom_Control extends \WP_Customize_Control
	{
		/**
		 * Render the content on the theme customizer page
		 */
		public $type              = 'multiple-select';
		public $all_options_text  = false;
		public $all_options_value = 0;
		public $rows              = 5;

		public function enqueue()
		{
			wp_enqueue_script('ppm_multiple-select', plugin_dir_url(__FILE__) . 'multiple-select.js', [], PPM_V);
			wp_enqueue_style('ppm_multiple-select', plugin_dir_url(__FILE__) . 'multiple-select.css', [], PPM_V);
		}

		public function render_content()
		{
			if (empty($this->choices)) {
				return;
			}

			$values = $this->value();
			if (!is_array($values)) {
				$values = explode(',', $this->value());
			} ?>
			<div class="multiple_select_control">
				<?php if (!empty($this->label)) : ?>
					<span class="customize-control-title"><?php echo $this->label; ?></span>
				<?php endif; ?>

				<?php if (!empty($this->description)) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>

				<?php if (!empty($this->all_options_value)) : ?>
					<?php echo $this->all_options_text; ?>
					<input type='checkbox' data-value="<?php echo $this->all_options_value; ?>" class="all_options" <?php echo checked(in_array($this->all_options_value, $values)); ?>>
				<?php endif; ?>

				<select multiple="multiple" <?php if (in_array($this->all_options_value, $values)) : ?>disabled<?php endif; ?> size="<?php echo $this->rows; ?>">
					<?php foreach ($this->choices as $key => $label) : ?>
						<option value="<?php echo $key; ?>" <?php echo selected(in_array($key, $values)); ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>

				<input type="hidden" <?php $this->link(); ?> value="<?php echo implode(',', $values); ?>" />
			</div>

			<?php
		}

		public static function sanitize($values)
		{
			$multi_values = !is_array($values) ? explode(',', $values) : $values;

			return !empty($multi_values) ? array_map('sanitize_text_field', $multi_values) : false;
		}
	}
}
