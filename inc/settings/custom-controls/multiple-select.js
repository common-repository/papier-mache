jQuery(document).ready(function() {

	function setMultiOptions( parent ) {
		select_values = jQuery( parent ).find( 'option:selected' ).map(
			function() {
				return this.value;
			}
		).get();

		if(jQuery(parent).find('input[type=checkbox]').is(':checked')) {
			select_values.unshift(jQuery(parent).find('input[type=checkbox]').attr('data-value'));
		}

		console.log(parent);
		console.log(select_values);
		jQuery( parent ).find( 'input[type=hidden]' ).val( select_values.join( ',' ) ).trigger( 'change' );
	}

	jQuery("body").on(
		'change',
		'.customize-control-multiple-select input[type="checkbox"]',
		function(e) {
			var el = e.target;

			var status = jQuery(this).is(':checked');
			if(status) {
				jQuery(this).parent().find('select').attr('disabled', 'disabled');
			} else {
				jQuery(this).parent().find('select').removeAttr('disabled');
			}

			//Set elements
			parent = el.closest('.multiple_select_control');
			setMultiOptions(parent);
		}
	);

	jQuery("body").on(
		'mousedown',
		'.customize-control-multiple-select select option',
		function(e) {
			var el = e.target;

			e.preventDefault();

			// toggle selection
			if(el.hasAttribute('selected')) el.removeAttribute('selected');
			else el.setAttribute('selected', '');

			// hack to correct buggy behavior
			// var select = el.parentNode.cloneNode(true);
			// el.parentNode.parentNode.replaceChild(select, el.parentNode);

			//Set elements
			parent = el.closest('.multiple_select_control');
			setMultiOptions(parent);
		}
	);

}); // jQuery( document ).ready
