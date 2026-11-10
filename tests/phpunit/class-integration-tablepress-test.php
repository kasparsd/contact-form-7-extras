<?php

/**
 * Test the main plugin class.
 */
class Integration_TablePress_Test extends WP_UnitTestCase {

	/**
	 * Can merge form data with table data.
	 */
	public function test_can_merge_new_fields() {
		$integration = new Cf7_Extras_Integration_TablePress();

		$table = array(
			'data' => array(
				array(
					'',
					'header cell',
				),
				array(
					'first_name',
					'',
				),
			),
		);

		$form_data = array(
			'first_name' => 'John ',
			'age' => '25',
			'food_preferences' => array(
				'pizza',
				'pasta',
			),
		);

		$this->assertEquals(
			array(
				array( '', 'header cell', 'first_name', 'age', 'food_preferences' ),
				array( 'first_name', '', '', '', '' ),
				array( '', '', 'John ', '25', 'pizza, pasta' ),
			),
			$integration->get_data_for_table( $table, $form_data )['data']
		);
	}

	/**
	 * Shortcode brackets in stored values are neutralized so TablePress
	 * does not evaluate them when rendering the table.
	 */
	public function test_escapes_shortcodes_in_values() {
		$integration = new Cf7_Extras_Integration_TablePress();

		$table = array(
			'data' => array(
				array( '' ),
			),
		);

		$form_data = array(
			'leak' => '[table id=3 /]',
			'tags' => array( '[my-shortcode]', 'plain' ),
		);

		$this->assertEquals(
			array(
				array( '', 'leak', 'tags' ),
				array( '', '&#91;table id=3 /&#93;', '&#91;my-shortcode&#93;, plain' ),
			),
			$integration->get_data_for_table( $table, $form_data )['data']
		);
	}

	/**
	 * Posted data is restricted to the form's declared form-tag fields and
	 * the internal cf7_* keys so arbitrary posted keys are not stored.
	 */
	public function test_restricts_form_data_to_declared_fields() {
		$integration = new Cf7_Extras_Integration_TablePress();

		$form_id = wp_insert_post(
			array(
				'post_title'  => 'Restrict Form Data Test',
				'post_type'   => 'wpcf7_contact_form',
				'post_status' => 'publish',
			)
		);
		update_post_meta( $form_id, '_form', "[text your-name]\n[email your-email]\n[submit \"Send\"]\n" );
		$contact_form = wpcf7_contact_form( $form_id );

		$form_data = array(
			'your-name' => 'John',
			'your-email' => 'john@example.com',
			'leak' => '[table id=3 /]',
		);

		$this->assertEquals(
			array(
				'your-name' => 'John',
				'your-email' => 'john@example.com',
			),
			$integration->restrict_form_data( $form_data, $contact_form )
		);
	}
}
