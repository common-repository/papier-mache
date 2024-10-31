/*
 * palette.js - rev 1
 * Copyright (c) 2018, Sebastian
 * Liscensed under the MIT License (MIT-LICENSE.txt)
 * http://www.opensource.org/licenses/mit-license.php
 */

/*
 * Created a palette around a textfield
 */

function Palette(element) {
	var me = this;
	var colors = default_colors = ['#a568f6', '#e63d87', '#00c7e4', '#fdd67e'];
	var new_color = jQuery(element).find('input.color_picker');
	var button_add = jQuery(element).find('.add_color');
	var button_change = jQuery(element).find('.change_color');
	var button_reset = jQuery(element).find('.reset');
	var palette = jQuery(element).find('.palette_colors');
	var input = jQuery(element).find('input[type=hidden]');

	//Agregar calse identificadora
	jQuery(element).addClass('palette_js');

	//Obtener los colores
	value = jQuery(element).attr('data-colors');
	//Si no hay colores, usar los colores por defecto
	if(value.length > 0) colors = value.split(",");

	//Ubicar nombre e id en campo oculto
	name = jQuery(element).attr('data-name');
	input.attr('name', name);

	id = jQuery(element).attr('data-id');
	input.attr('id', id);

	input.val('');

	//Actualizar colores de la lista según la paeta
	var updateList = function() {
		var tmp = '';
		palette.find('.color').each(function(item) {
			var color = jQuery(this).attr('data-color');
			if(tmp.length > 0) {
				tmp += ',' + color;
			} else {
				tmp = color;
			}
		});
		input.val(tmp);
		input.trigger('change');
	}

	//Determinar si el widget debe o no tener mnarca de selección según si hay o
	//no colores eleccionados
	var setSelectionStatus = function() {
		//Buscar si hay al menos una seleccionada
		var seleccionadas = palette.find('.color.selected');
		if(seleccionadas.length > 0) {
			jQuery(element).addClass('seleccion');
		} else {
			jQuery(element).removeClass('seleccion');
		}
	}

	//Declarar función para agregar color
	var addColor = function(color) {
		new_item = jQuery('<div class="color" data-color="' + color + '"><span class="delete dashicons dashicons-no"></span></div>')
			.appendTo(palette)
			.css('background-color', color);
		updateList();

		//Vincular evento para eliminar color
		new_item.find('.delete').on('click', function() {
			jQuery(this).parent().remove();
			updateList();

			setSelectionStatus();
		});

		//Vincular elemento para seleccionar color
		new_item.on('click', function() {
			if(jQuery(this).hasClass('selected')) {
				palette.find('.color').removeClass('selected');
			} else {
				new_color.val(jQuery(this).attr('data-color'));
				palette.find('.color').removeClass('selected');
				jQuery(this).toggleClass('selected');
			}

			setSelectionStatus();
		});
	}

	//Declarar función para cambiar color
	var changeColor = function(color) {
		//Buscar color seleccionado Y CAMBIAR DATOS
		palette.find('.selected')
			.attr('data-color', color)
			.css('background-color', color);

		//Actualizar lista
		updateList();
	}

	//Declarar función para cambiar color
	var reset = function() {
		//Buscar color seleccionado Y CAMBIAR DATOS
		palette.find('.color').each(function() {
			jQuery(this).remove();
		});

		//Agregar colores por defecto
		jQuery.each(default_colors, function(index, value) {
			addColor(value);
		});

		//Actualizar lista
		updateList();
	}

	//Agregar colores iniciales a la paleta
	jQuery.each(colors, function(index, value) {
		addColor(value);
	});

	//Vincular evento al botón para agregar color
	button_add.on('click', function() {
		color = new_color.val();
		addColor(color);
	});

	//Vincular evento al botón para cambiar color
	button_change.on('click', function() {
		color = new_color.val();
		changeColor(color);
	});

	//Vincular evento al botón para reiniciar
	button_reset.on('click', function() {
		reset();
	});

};

//Palette
jQuery(document).ready(function() {
	jQuery(window).load(function() {
		jQuery('.customize-palette-control').each( function() {
			var palette = new Palette(this);
		});
	});

});
