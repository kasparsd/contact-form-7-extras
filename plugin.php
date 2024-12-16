<?php
/**
 * Plugin Name: Controls for Contact Form 7
 * Description: Use the "Customize" tab in each form settings for advanced controls. Subscribe to the <a href="https://formcontrols.com/pro" target="_blank">🚀 PRO version</a> for advanced analytics and tracking features.
 * Plugin URI: https://formcontrols.com
 * Author: Kaspars Dambis
 * Author URI: https://formcontrols.com
 * Version: 0.8.2
 * License: GPL2
 * Text Domain: contact-form-7-extras
 */

include_once __DIR__ . '/src/class-cf7-extras.php';

$plugin = Cf7_Extras::instance();
$plugin->set_plugin_dir( __DIR__ );
$plugin->init();
