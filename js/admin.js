jQuery(document).ready(function($){

	$('input[data-toggle-on]').each(function(){
		var target = $(this).data('toggle-on');

		$(target).toggle( ! $(this).is(':checked') );

		$(this).on('change', function() {
			$(target).toggle( ! $(this).is(':checked') );
		});
	});

});