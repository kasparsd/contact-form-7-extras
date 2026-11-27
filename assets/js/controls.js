/* eslint camelcase: warn */

( function() {
	var formEventCallback;

	var formEventCallbacks = {
		wpcf7mailsent: function( form, formConfig ) {
			trackAnalyticsEvent( 'Contact Form', 'Sent', formConfig.title );
		},
		wpcf7mailfailed: function( form, formConfig ) {
			trackAnalyticsEvent( 'Contact Form', 'Error', formConfig.title );
		},
		wpcf7spam: function( form, formConfig ) {
			trackAnalyticsEvent( 'Contact Form', 'Spam', formConfig.title );
		},
		wpcf7submit: function( form, formConfig ) {
			var errorStati = [ 'validation_failed', 'acceptance_missing', 'spam', 'aborted', 'mail_failed' ];

			if ( form.status && -1 === errorStati.indexOf( form.status ) ) {
				trackAnalyticsEvent( 'Contact Form', 'Submit', formConfig.title );
			} else {
				trackAnalyticsEvent( 'Contact Form', form.status, formConfig.title );
			}
		}
	};

	function trackAnalyticsEvent( eventCategory, eventAction, eventTitle ) {

		// Helper method required for the event to be registered by gtag.js.
		var dataLayerPush = function() {
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push( arguments );
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
	}

	function formEventEnabled( formId, eventName ) {
		formId = parseInt( formId );

		if ( ! formId || ! window.cf7_extras.events[ eventName ] ) {
			return false;
		}

		if ( -1 !== window.cf7_extras.events[ eventName ].indexOf( formId ) ) {
			return true;
		}

		return false;
	}

	function getFormConfig( formId ) {
		formId = parseInt( formId );

		if ( window.cf7_extras.forms && window.cf7_extras.forms[ formId ] ) {
			return window.cf7_extras.forms[ formId ];
		}

		return false;
	}

	// We need the event config for each form to do anything.
	if ( ! window.cf7_extras ) {
		return;
	}

	// All form events are documented as JS events since Contact Form 7 version 5.2.
	if ( 'function' === typeof document.addEventListener ) {
		for ( formEventCallback in formEventCallbacks ) {
			document.addEventListener(
				formEventCallback,
				function( event ) {
					var form, formConfig;

					if ( ! ( event.type in formEventCallbacks ) ) {
						return;
					}

					form = event.detail;
					formConfig = getFormConfig( form.contactFormId );

					// Analytics tracking is enabled per form.
					if ( formEventEnabled( form.contactFormId, 'track-ga' ) ) {
						formEventCallbacks[ event.type ].call( event, form, formConfig );
					}

					// Redirect on successful submission is enabled independently of analytics tracking.
					if (
						'wpcf7submit' === event.type &&
						'mail_sent' === form.status &&
						formConfig &&
						formConfig.redirect_url &&
						formEventEnabled( form.contactFormId, 'redirect-success' )
					) {
						window.location = formConfig.redirect_url;
					}
				}
			);
		}
	}
}() );
