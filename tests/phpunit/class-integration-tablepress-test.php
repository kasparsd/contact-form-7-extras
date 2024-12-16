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
}
