<?php
/**
 * Bootstrap the plugin unit testing environment.
 *
 * Use the WP testing library bundled with wp-env.
 */

// Composer autoloader must be loaded before phpunit will be available.
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

$wp_tests_dir = getenv( 'WP_PHPUNIT__DIR' ); // Configured by wp-phpunit/wp-phpunit.

if ( empty( $wp_tests_dir ) || ! is_dir( $wp_tests_dir ) ) {
	throw new RuntimeException( 'Failed to find the WP_PHPUNIT__DIR directory.' );
}

// Load the wp-tests-config.php from wp-env since it knows about the database.
$wp_env_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( $wp_env_tests_dir ) {
	putenv( sprintf( 'WP_PHPUNIT__TESTS_CONFIG=%s/wp-tests-config.php', $wp_env_tests_dir ) );
}

global $wp_tests_options; // WP testing library uses this to define option values.

$wp_tests_options = [
	'active_plugins' => [
		'contact-form-7-extras/plugin.php',
		'contact-form-7/wp-contact-form-7.php',
	],
];

// Include all helper functions.
require_once $wp_tests_dir . '/includes/functions.php';

// Load WP and start tests.
require_once $wp_tests_dir . '/includes/bootstrap.php';
