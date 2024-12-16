<?php

/**
 * Test the main plugin class.
 */
class Plugin_Test extends WP_UnitTestCase {

	/**
	 * If the plugin class is available.
	 */
	public function test_plugin_available() {
		$this->assertTrue( class_exists( 'Cf7_Extras' ), 'Plugin is active' );
	}
}
