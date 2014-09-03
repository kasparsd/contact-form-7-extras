jQuery(document).ready(function($){
	
	if ( $('#extra-disable-ajax').is(":checked") ) {
		$('.extra-field-extra-track-ga-success').hide();
	}

	$('#extra-disable-ajax').on('change', function(){
		if ( $(this).is(":checked") ) {
			$('.extra-field-extra-track-ga-success').hide();
		} else {
			$('.extra-field-extra-track-ga-success').show();
		}
	});

});