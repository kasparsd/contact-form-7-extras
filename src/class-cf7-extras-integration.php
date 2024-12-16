<?php

abstract class Cf7_Extras_Integration {
	/**
	 * HTML input field prefix used in controls settings.
	 *
	 * @var string
	 */
	const FIELD_PREFIX = 'extra';

	abstract public function init();

	public function get_settings( $form_id ) {
		return Cf7_Extras_Form_Settings::from_form_id( $form_id );
	}

	public function get_field_name( $field_id ) {
		return sprintf( '%s[%s]', self::FIELD_PREFIX, $field_id );
	}
}
