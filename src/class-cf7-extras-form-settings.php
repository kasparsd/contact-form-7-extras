<?php

/**
 * Form settings.
 */
class Cf7_Extras_Form_Settings {

	/**
	 * Default settings.
	 *
	 * @var array
	 */
	private $defaults = array(
		'disable-css' => false,
		'disable-ajax' => false,
		'html5-disable' => false,
		'html5-fallback' => false,
		'disable-autop' => false,
		'enable-shortcodes' => false,
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

	/**
	 * Setup settings for a form.
	 *
	 * @param WPCF7_ContactForm $form Form instance.
	 */
	public function __construct( $form ) {
		$this->form = $form;
	}

	/**
	 * Get settings for a form by ID.
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return self
	 */
	public static function from_form_id( $form_id ) {
		return new self( wpcf7_contact_form( $form_id ) );
	}

	/**
	 * Get the form ID.
	 *
	 * @return int
	 */
	public function form_id() {
		return $this->form->id();
	}

	/**
	 * Get a setting value.
	 *
	 * @param string $key Setting key.
	 *
	 * @return mixed|null Return null if the setting is not found.
	 */
	public function get( $key ) {
		$settings = $this->all();

		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}

		return null;
	}

	/**
	 * Get all settings.
	 *
	 * @return array
	 */
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
