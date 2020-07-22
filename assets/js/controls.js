/* eslint camelcase: warn */

( function( $ ) {

	var jQueryEvent, formEventCallback;

	var formEventCallbacks = {
		wpcf7mailsent: function( form ) {
			var formConfig;

			if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
				formConfig = getFormConfig( form.contactFormId );
				trackAnalyticsEvent( 'Contact Form', 'Sent', formConfig.title );
			}
		},
		wpcf7mailfailed: function( form ) {
			var formConfig;

			if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
				formConfig = getFormConfig( form.contactFormId );
				trackAnalyticsEvent( 'Contact Form', 'Error', formConfig.title );
			}
		},
		wpcf7spam: function( form ) {
			var formConfig;

			if ( form.contactFormId && formEventEnabled( form.contactFormId, 'track-ga' ) ) {
				formConfig = getFormConfig( form.contactFormId );
				trackAnalyticsEvent( 'Contact Form', 'Spam', formConfig.title );
			}
		},
		wpcf7submit: function( form ) {
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
		}
	};

	var jQueryEvents = {
		'wpcf7:mailsent': function( event, form ) {
			formCallbacks.wpcf7mailsent( form );
		},
		'wpcf7:mailfailed': function( event, form ) {
			formCallbacks.wpcf7mailfailed( form );
		},
		'wpcf7:spam': function( event, form ) {
			formCallbacks.wpcf7spam( form );
		},
		'wpcf7:submit': function( event, form ) {
			formCallbacks.wpcf7submit( form );
		}
	};

	function trackAnalyticsEvent( eventCategory, eventAction, eventTitle ) {

		// Helper method required for the event to be registered by gtag.js.
		var dataLayerPush = function() {
			if ( 'object' === typeof window.dataLayer && 'function' === typeof window.dataLayer.push ) {
				window.dataLayer.push( arguments );
			}
		};

		// GA via Google Tag Manager or Global Site Tag (gtag.js).
		dataLayerPush(
			'event',
			eventAction,
			{
				'event_category': eventCategory,
				'event_label': eventTitle
			}
		);

		// Universal Google Analytics is available.
		if ( 'function' === typeof ga ) {
			ga( 'send', 'event', eventCategory, eventAction, eventTitle );
		}

		// Classic Google Analytics is available.
		if ( 'object' === typeof _gaq && 'function' === typeof _gaq.push ) {
			_gaq.push( [ '_trackEvent', eventCategory, eventAction, eventTitle ] );
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

	// We need the event config for each form to do anything.
	if ( ! window.cf7_extras ) {
		return;
	}

	// Register the new JS events in CF7 version 5.2 and above.
	if ( 'function' === typeof document.addEventListener ) {
		for ( formEventCallback in formEventCallbacks ) {
			document.addEventListener( formEventCallback, function( event ) {
				if ( event.type in formEventCallbacks ) {
					formEventCallbacks[ event.type ].call( event, event.detail );
				}
			} );
		}

	// Register the legacy jQuery events pre CF7 version 5.2.
	} else if ( 'function' === typeof $ ) {
		for ( jQueryEvent in jQueryEvents ) {
			$( document ).on( jQueryEvent, jQueryEvents[ jQueryEvent ] );
		}
	}

}( jQuery ) );
