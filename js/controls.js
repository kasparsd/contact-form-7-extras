( function( $ ) {
	if ( ! window.cf7_extras ) {
		return;
	}

	var trackAnalyticsEvent = function( eventCategory, eventAction, eventTitle ) {
		if ( 'function' === typeof ga ) { // Universal Google Analytics is available
			ga( 'send', 'event', eventCategory, eventAction, eventTitle );
		} else if ( 'undefined' !== typeof _gaq ) { // Classic Google Analytics is available
			_gaq.push( [ '_trackEvent', eventCategory, eventAction, eventTitle ] );
		}
		if ( 'undefined' !== typeof _paq ) { // Matomo (formerly Piwik) is available
			_paq.push([ 'trackEvent', eventCategory, eventAction, eventTitle ]);
		}
	};

	var formEventEnabled = function( formId, eventName ) {
		formId = parseInt( formId );

		if ( ! formId || ! window.cf7_extras.events[ eventName ] ) {
			return false;
		}

		if ( -1 !== window.cf7_extras.events[ eventName ].indexOf( formId ) ) {
			return true;
		}

		return false;
	};

	var getFormConfig = function( formId ) {
		formId = parseInt( formId );

		if ( window.cf7_extras.forms && window.cf7_extras.forms[ formId ] ) {
			return window.cf7_extras.forms[ formId ];
		}

		return false;
	}

	$( document ).on( 'wpcf7:mailsent', function( event, form ) {
		if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
			var formConfig = getFormConfig( form.contactFormId );
			trackAnalyticsEvent( 'Contact Form', 'Sent', formConfig.title );
		}
	} );

	$( document ).on( 'wpcf7:mailfailed', function( event, form ) {
		if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
			var formConfig = getFormConfig( form.contactFormId );
			trackAnalyticsEvent( 'Contact Form', 'Error', formConfig.title );
		}
	} );

	$( document ).on( 'wpcf7:spam', function( event, form ) {
		if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
			var formConfig = getFormConfig( form.contactFormId );
			trackAnalyticsEvent( 'Contact Form', 'Spam', formConfig.title );
		}
	} );

	$( document ).on( 'wpcf7:submit', function( event, form ) {
		if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
			var formConfig = getFormConfig( form.contactFormId );
			trackAnalyticsEvent( 'Contact Form', 'Submit', formConfig.title );
		}

		if ( form.contactFormId && 'mail_sent' === form.status && formEventEnabled( form.contactFormId, 'redirect-success' ) ) {
			var formConfig = getFormConfig( form.contactFormId );

			if ( formConfig.redirect_url ) {
				window.location = formConfig.redirect_url;
			}
		}
	} );
} )( jQuery );
