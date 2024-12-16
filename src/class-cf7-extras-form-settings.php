<?php

class Cf7_Extras_Form_Settings {

	private $defaults = array(
		'disable-css' => false,
		'disable-ajax' => false,
		'html5-disable' => false,
		'html5-fallback' => false,
		'disable-autop' => false,
		'redirect-success' => false,
		'track-ga-success' => false,
		'track-ga-submit' => false,
		'track-ga' => false,
		'google-recaptcha-lang' => null,
	);

	/**
	 * Form instance.
	 *
	 * @var WPCF7_ContactForm
	 */
	protected $form;

	public function __construct( $form ) {
		$this->form = $form;
	}

	public static function from_form_id( $form_id ) {
		return new self( wpcf7_contact_form( $form_id ) );
	}

	public function form_id() {
		return $this->form->id();
	}

	public function get( $key ) {
		$settings = $this->all();

		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}

		return null;
	}

	public function all() {
		$settings = get_post_meta( $this->form->id(), 'extras', true );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		$settings = array_merge( $this->defaults, $settings );

		// Convert legacy settings into one.
		if ( ! empty( $settings['track-ga-success'] ) || ! empty( $settings['track-ga-submit'] ) ) {
			$settings['track-ga'] = true;
		}

		return $settings;
	}
}
