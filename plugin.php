<?php
/**
 * Plugin Name: Controls for Contact Form 7
 * Description: Simple controls for the advanced functionality of Contact Form 7.
 * Plugin URI: https://formcontrols.com
 * Author: Kaspars Dambis
 * Author URI: https://formcontrols.com
 * Version: 0.7.2
 * License: GPL2
 * Text Domain: contact-form-7-extras
 */

include_once dirname( __FILE__ ) . '/src/class-cf7-extras.php';

$plugin = Cf7_Extras::instance();
$plugin->set_plugin_dir( dirname( __FILE__ ) );
$plugin->init();
