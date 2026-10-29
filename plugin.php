<?php
/**
 * Plugin Name: Controls for Contact Form 7
 * Description: Use the "Controls" tab of each form settings for advanced controls. Subscribe to the <a href="https://formcontrols.com/pro" target="_blank">🚀 PRO version</a> for advanced analytics and tracking features.
 * Plugin URI: https://formcontrols.com
 * Author: Kaspars Dambis
 * Author URI: https://formcontrols.com
 * Version: 0.10.1
 * License: GPL2
 * Text Domain: contact-form-7-extras
 */

$plugin_files = array(
	'class-cf7-extras-form-settings.php',
	'class-cf7-extras-integration.php',
	'class-cf7-extras-integration-tablepress.php',
	'class-cf7-extras.php',
);

foreach ( $plugin_files as $file ) {
	require_once __DIR__ . '/php/' . $file;
}

$plugin = Cf7_Extras::instance();
$plugin->set_plugin_dir( __DIR__ );

add_action( 'plugins_loaded', array( $plugin, 'init' ) );
