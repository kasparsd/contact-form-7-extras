<?php

class Plugin_Test extends WP_UnitTestCase {
	public function test_plugin() {
		$this->assertTrue( class_exists( 'Cf7_Extras' ) );
	}
}
