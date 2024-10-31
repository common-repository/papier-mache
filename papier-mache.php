<?php
/*
Plugin Name: Papier-mâché
Plugin URI:   https://bitbucket.org/baxtian/papier-mache/src/master/
Description: A rain of confetti / papier-mâché to show you have something to celebrate.
Version:     0.6.2
Author:      Juan Sebastián Echeverry
Author URI:  http://sebaxtian.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: papier-mache
Domain Path: /languages

Copyright 2020 Juan Sebastián Echeverry (baxtian.echeverry@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//Detectar la versión del plugin
$plugin_data    = get_file_data(__FILE__, ['Version' => 'Version'], false);
$plugin_version = $plugin_data['Version'];

define('PPM_V', $plugin_version);

require_once('inc/settings/papier-mache.php');

add_action('wp_enqueue_scripts', 'ppm_enqueue');
add_action('plugins_loaded', 'ppm_plugin_setup');
add_action('admin_init', 'ppm_adminheader');
add_action('wp_footer', 'ppm_footer');
add_action('wp_ajax_papier_mache', 'ppm_config');
add_action('wp_ajax_nopriv_papier_mache', 'ppm_config');

//Función para encolar los scripts
function ppm_enqueue()
{
	wp_register_script('confetti-js', plugin_dir_url(__FILE__) . 'scripts/confetti-js/dist/index.min.js', [], PPM_V);
	wp_register_script('papier-mache', plugin_dir_url(__FILE__) . 'scripts/papier-mache.js', ['confetti-js', 'jquery'], PPM_V);
	wp_localize_script('papier-mache', 'papier_mache', ['ajaxurl' => admin_url('admin-ajax.php')]);

	wp_register_style('papier-mache', plugin_dir_url(__FILE__) . 'styles/papier-mache.css', [], PPM_V);
}

//Función después de activar los plugins
function ppm_plugin_setup()
{
	//Activar el traductor
	load_plugin_textdomain('papier-mache', false, basename(dirname(__FILE__)) . '/languages/');
}

// Función que declara los estilos y scripts para visualizar al
// administrar el sitio
function ppm_adminheader()
{
	wp_register_style('admin-confetti', plugin_dir_url(__FILE__) . 'styles/admin.css', [], PPM_V);
	wp_enqueue_style('admin-confetti');

	//Para el customize
	wp_register_style('palette', plugin_dir_url(__FILE__) . 'styles/palette.css', [], PPM_V);
	wp_register_script('palette', plugin_dir_url(__FILE__) . 'scripts/palette.js', ['wp-color-picker'], PPM_V);
	wp_register_script('ppm-customize', plugin_dir_url(__FILE__) . 'scripts/ppm-customize.js', ['palette', 'customize-controls'], PPM_V);
}

//Función para incluir el canvas en el pie de página
function ppm_footer()
{
	//Detectar si está inhabilitado
	$papier_mache   = get_option('papier_mache');
	$disabled_roles = $papier_mache['disabled_roles'];
	$show_in        = $papier_mache['show_in'];

	$current_user = wp_get_current_user();
	$user_roles   = $current_user->roles;

	//Si no tiene roles, entonces es un usuaro no registrado
	if (count($user_roles) == 0) {
		$user_roles[] = 'cnf_unregistered_visitor';
	}

	//Por defecto está habilitado
	$habilitado = true;

	//¿Está el rol del usuario en la lista de deshabilitados?
	if (is_array($disabled_roles) && count($disabled_roles) > 0) {
		foreach ($disabled_roles as $disabled_role) {
			if (in_array($disabled_role, $user_roles)) {
				$habilitado = false;

				break;
			}
		}
	}

	//Es una página habilitada
	//Si es -1 es en toda la página
	if (in_array('-1', $show_in)) {
		//Está habilitado en todas las páginas
	} elseif (in_array('0', $show_in)) {
		//Solo en home
		if (!is_home() && !is_front_page()) {
			$habilitado = false;
		}
	} else {
		if (!is_array($show_in) || count($show_in) == 0) { //¿Hay alguna página?
			$habilitado = false;
		} elseif (!is_page($show_in)) { //¿Es esta la página?
			$habilitado = false;
		}
	}

	if ($habilitado) {
		echo '<canvas id="papier-mache"></canvas>';

		wp_enqueue_script('papier-mache');
		wp_enqueue_style('papier-mache');
	}
}

//Función para retornar la configuración
function ppm_config()
{
	$answer = [
		'target' => 'papier-mache',
	];

	$papier_mache = get_option('papier_mache');

	if ($max_items = $papier_mache['max_items']) {
		$answer['max'] = $max_items;
	}

	if ($props = array_filter($papier_mache['props'])) {
		$answer['props'] = $props;
	}

	if ($clock = $papier_mache['clock']) {
		$answer['clock'] = $clock;
	}

	if ($size = $papier_mache['size']) {
		$answer['size'] = $size;
	}

	if ($colors = $papier_mache['colors']) {
		$colors = explode(',', $colors);
		foreach ($colors as $hex) {
			list($r, $g, $b)    = sscanf($hex, '#%02x%02x%02x');
			$answer['colors'][] = [$r, $g, $b];
		}
	}

	echo json_encode($answer);
	wp_die();
}
