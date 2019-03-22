/* eslint camelcase: warn */

( function( $ ) {
	if ( ! window.cf7_extras ) {
		return;
	}

	function trackAnalyticsEvent( eventCategory, eventAction, eventTitle ) {

		// Universal Google Analytics is available.
		if ( 'function' === typeof ga ) {
			ga( 'send', 'event', eventCategory, eventAction, eventTitle );
		}

		// Classic Google Analytics is available.
		if ( 'object' === typeof _gaq && 'function' === typeof _gaq.push ) {
			_gaq.push( [ '_trackEvent', eventCategory, eventAction, eventTitle ] );
		}

		// GA via Google Tag Manager.
		if ( 'object' === typeof dataLayer && 'function' === typeof dataLayer.push ) {
			dataLayer.push( {
				eventCategory: eventCategory,
				eventAction: eventAction,
				eventLabel: eventTitle
			} );
		}

		// Matomo (formerly Piwik) is available.
		if ( 'undefined' !== typeof _paq && 'function' === typeof _paq.push ) {
			_paq.push( [ 'trackEvent', eventCategory, eventAction, eventTitle ] );
		}

		// Facebook Pixel contact event.
		if ( 'function' === typeof fbq ) {
			fbq( 'track', 'Contact', {
				content_category: eventAction,
				content_name: eventTitle
			} );
		}
	};

	function formEventEnabled( formId, eventName ) {
		formId = parseInt( formId );

		if ( ! formId || ! window.cf7_extras.events[ eventName ] ) {
			return false;
		}

		if ( -1 !== window.cf7_extras.events[ eventName ].indexOf( formId ) ) {
			return true;
		}

		return false;
	};

	function getFormConfig( formId ) {
		formId = parseInt( formId );

		if ( window.cf7_extras.forms && window.cf7_extras.forms[ formId ] ) {
			return window.cf7_extras.forms[ formId ];
		}

		return false;
	};

	$( document ).on( 'wpcf7:mailsent', function( event, form ) {
		var formConfig;

		if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
			formConfig = getFormConfig( form.contactFormId );
			trackAnalyticsEvent( 'Contact Form', 'Sent', formConfig.title );
		}
	} );

	$( document ).on( 'wpcf7:mailfailed', function( event, form ) {
		var formConfig;

		if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
			formConfig = getFormConfig( form.contactFormId );
			trackAnalyticsEvent( 'Contact Form', 'Error', formConfig.title );
		}
	} );

	$( document ).on( 'wpcf7:spam', function( event, form ) {
		var formConfig;

		if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
			formConfig = getFormConfig( form.contactFormId );
			trackAnalyticsEvent( 'Contact Form', 'Spam', formConfig.title );
		}
	} );

	$( document ).on( 'wpcf7:submit', function( event, form ) {
		var formConfig;

		if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
			formConfig = getFormConfig( form.contactFormId );
			trackAnalyticsEvent( 'Contact Form', 'Submit', formConfig.title );
		}

		if ( form.contactFormId && 'mail_sent' === form.status && formEventEnabled( form.contactFormId, 'redirect-success' ) ) {
			formConfig = getFormConfig( form.contactFormId );

			if ( formConfig.redirect_url ) {
				window.location = formConfig.redirect_url;
			}
		}
	} );
}( jQuery ) );
