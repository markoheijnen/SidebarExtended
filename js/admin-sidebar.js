jQuery(document).ready(function($) {
	$(document).on("change", ".sidebar-columns", function(evt){
		evt.preventDefault();

		$(this).closest('div.widgets-holder-wrap').find('img.ajax-feedback').css('visibility', 'visible');

		var data = {
			action: 'rockstars_sidebar_columns',
			action_nonce: $('#_wpnonce_widgets').val(),

			sidebar: $(this).closest('.widgets-sortables').attr('id'),
			amount_columns: $( 'option:selected', this).val()
		};

		jQuery.post(ajaxurl, data, function(response) {
			$('img.ajax-feedback').css('visibility', 'hidden');
		});
	});
});