<?php
namespace PapierMache\Controls;

if (! class_exists('WP_Customize_Control')) {
    return null;
}

/**
 * Class to create a custom date picker
 */
class Palette_Custom_Control extends \WP_Customize_Control
{
    public $type = 'palette';

    /**
    * Enqueue the styles and scripts
    */
    public function enqueue()
    {
		wp_enqueue_script('ppm_palette', plugin_dir_url(__FILE__)."palette.js", array(), PPM_V);
		wp_enqueue_style('ppm_palette', plugin_dir_url(__FILE__)."palette.css", array(), PPM_V);
    }

    /**
    * Render the content on the theme customizer page
    */
    public function render_content()
    {
        ?>
        <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
        <div class='customize-palette-control' data-colors='<?php echo $this->value(); ?>' data-name='<?php echo $this->id; ?>' data-id='<?php echo $this->id; ?>'>
            <input type='color' class='color_picker'> <span class="action add_color dashicons dashicons-plus" title='<?php _e('Add color', 'papier-mache'); ?>'></span> <span class="action change_color dashicons dashicons-update" title='<?php _e('Change color', 'papier-mache'); ?>'></span> <span class="action reset dashicons dashicons-backup" title='<?php _e('Reset palette', 'papier-mache'); ?>'></span>
            <div class='palette_colors'></div>
            <input type='hidden' <?php $this->link(); ?>>
        </div>
        <?php
    }
}
