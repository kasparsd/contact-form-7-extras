<?php

/**
 * Integration base.
 */
abstract class Cf7_Extras_Integration {
	/**
	 * HTML input field prefix used in controls settings.
	 *
	 * @var string
	 */
	const FIELD_PREFIX = 'extra';

	/**
	 * Initialize the integration.
	 */
	abstract public function init();

	/**
	 * Get the settings instance for a form.
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return Cf7_Extras_Form_Settings
	 */
	public function get_settings( $form_id ) {
		return Cf7_Extras_Form_Settings::from_form_id( $form_id );
	}

	/**
	 * Get the form field name for a field ID.
	 *
	 * @param string $field_id Field ID.
	 *
	 * @return string
	 */
	public function get_field_name( $field_id ) {
		return sprintf( '%s[%s]', self::FIELD_PREFIX, $field_id );
	}
}
