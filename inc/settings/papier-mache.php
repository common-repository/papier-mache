<?php

add_action('customize_register', 'ppm_customize_options');

function ppm_customize_options($wp_customize)
{
    require_once('custom-controls/multiple-checkbox.php');
    require_once('custom-controls/multiple-select.php');
    require_once('custom-controls/palette.php');

    //Agregar sección de PayPal
    $wp_customize->add_section(
        'papier-mache',
        array(
            'title' => __('Papier-mâché', 'papier-mache'),
        )
    );

    //Declarar el campo para 'max number of confetti'
    $wp_customize->add_setting(
        'papier_mache[max_items]',
        array(
            'type' => 'option', // or 'theme_mod'
            'sanitize_callback' => 'esc_int',
            'capability' => 'activate_plugins'
        )
    );

    //Opción para definir el campo para 'max number of confetti'
    $wp_customize->add_control(
        'ppm_max_items',
        array(
            'label'    => __('Max confetti', 'papier-mache'),
            'section'  => 'papier-mache',
            'settings' => 'papier_mache[max_items]',
            'type'     => 'text',
            'input_attrs' => array('placeholder' => 80 )
        )
    );

    //Declarar el campo para 'max number of confetti'
    $wp_customize->add_setting(
        'papier_mache[props]',
        array(
            'default' => array( ),
            'type' => 'option', // or 'theme_mod'
            'sanitize_callback' => '\PapierMache\Controls\Multiple_Checkbox_Custom_Control::sanitize',
            'capability' => 'activate_plugins'
        )
    );

    //Opción para definir el campo para 'max number of confetti'
    $wp_customize->add_control(
        new \PapierMache\Controls\Multiple_Checkbox_Custom_Control(
            $wp_customize, 'ppm_props', array(
                'label' => __( 'Type of confetti', 'papier-mache' ),
                'section' => 'papier-mache',
                'settings' => 'papier_mache[props]',
                'type'     => 'multiple-checkbox',
                'choices' => array(
                    'square' => __('Squares', 'papier-mache'),
                    'triangle' => __('Triangles', 'papier-mache'),
                    'line' => __('Lines', 'papier-mache'),
                    'circle' => __('Circles', 'papier-mache'),
                )
            )
        )
    );

    //Declarar el campo para 'colores'
    $wp_customize->add_setting(
        'papier_mache[colors]',
        array(
            'type' => 'option', // or 'theme_mod'
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '#a568f6,#e63d87,#00c7e4,#fdd67e',
        )
    );

    //Opción para definir el campo para 'colores'
    $wp_customize->add_control(
        new \PapierMache\Controls\Palette_Custom_Control(
            $wp_customize, 'ppm_colors', array(
                'label' => __('Colors', 'papier-mache'),
                'section' => 'papier-mache',
                'settings' => 'papier_mache[colors]',
                'type' => 'palette',
            )
        )
    );

    //Declarar el campo para 'clock'
    $wp_customize->add_setting(
        'papier_mache[clock]',
        array(
            'type' => 'option', // or 'theme_mod'
            'sanitize_callback' => 'esc_int',
            'capability' => 'activate_plugins'
        )
    );

    //Opción para definir el campo para 'clock'
    $wp_customize->add_control(
        'ppm_clock',
        array(
            'label'    => __('Clock', 'papier-mache'),
            'section'  => 'papier-mache',
            'settings' => 'papier_mache[clock]',
            'type'     => 'text',
            'input_attrs' => array('placeholder' => 25 )
        )
    );

    //Declarar el campo para 'size'
    $wp_customize->add_setting(
        'papier_mache[size]',
        array(
            'type' => 'option', // or 'theme_mod'
            'sanitize_callback' => 'esc_int',
            'capability' => 'activate_plugins'
        )
    );

    //Opción para definir el campo para 'size'
    $wp_customize->add_control(
        'ppm_size',
        array(
            'label'    => __('Prop size', 'papier-mache'),
            'section'  => 'papier-mache',
            'settings' => 'papier_mache[size]',
            'type'     => 'text',
            'input_attrs' => array('placeholder' => 1 )
        )
    );

    //Declarar el campo para perfiles que no podrán vr el confeti
    $wp_customize->add_setting(
        'papier_mache[disabled_roles]',
        array(
            'default' => array( ),
            'type' => 'option', // or 'theme_mod'
            'sanitize_callback' => 'sanitize_multiple_checkbox',
            'capability' => 'activate_plugins'
        )
    );

    //Listado de roles
    global $wp_roles;
    $roles = $wp_roles->get_names();
    $roles['cnf_unregistered_visitor'] = __('Unregistered visitor', 'papier-mache');

    foreach($roles as $key => $item) {
        $roles[$key] = translate_user_role($item);
    }

    //Opción para definir el campo para perfiles que no podrán vr el confeti
    $wp_customize->add_control(
        new \PapierMache\Controls\Multiple_Checkbox_Custom_Control(
            $wp_customize, 'ppm_disabled_roles', array(
                'label' => __( 'Roles that can not see the confetti', 'papier-mache' ),
                'section' => 'papier-mache',
                'settings' => 'papier_mache[disabled_roles]',
                'type'     => 'multiple-checkbox',
                'choices' => $roles,
            )
        )
    );

	//Dönde se mostrará el papel maché
	$wp_customize->add_setting(
		'papier_mache[show_in]',
		array(
			'default' => array( ),
			'type' => 'option', // or 'theme_mod'
			'sanitize_callback' => '\PapierMache\Controls\Multiple_Select_Custom_Control::sanitize',
			'capability' => 'activate_plugins'
		)
	);

	//Páginas
    $pages = array(
        // '-1' => __( '&mdash; All site &mdash;', 'papier-mache' ),
        '0' => __( '&mdash; Homepage &mdash;', 'papier-mache' ),
    );

    $_pages = get_posts(
        array(
            'post_type' => 'page',
            'nopaging' => true,
            'orderby' => 'title',
            'order' => 'ASC',
        )
    );

    if($_pages && count($_pages) > 0) {
        foreach($_pages as $item) {
            $pages[$item->ID] = $item->post_title;
        }
    }

	//Opción para definir el campo para páginas
	$wp_customize->add_control(
		new \PapierMache\Controls\Multiple_Select_Custom_Control(
			$wp_customize,
			'papier_mache[show_in]',
			array(
				'label' => __('Display', 'papier-mache'),
				// 'description' => __('Description', 'papier-mache'),
				'section' => 'papier-mache',
				'settings' => 'papier_mache[show_in]',
				'type'     => 'multiple-select',
				'choices' => $pages,
				'all_options_value' => -1,
				'all_options_text' => __('In all site', 'papier-mache'),
				'rows' => 9
			)
		)
	);

}

function esc_int($number)
{
    if(empty(trim($number))) return '';
    return (int)$number;
}

function sanitize_multiple_checkbox( $values ) {

    $multi_values = !is_array( $values ) ? explode( ',', $values ) : $values;

    return !empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : false;
}
