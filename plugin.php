<?php
/**
 * Plugin Name: Contact Form 7 Controls
 * Description: Add simple controls for the advanced functionality of Contact Form 7.
 * Tags: contact form, contact form 7, cf7, admin, backend, google analytics, ga, forms, form, track
 * Plugin URI: https://preseto.com/plugins/contact-form-7-controls
 * Author: Kaspars Dambis
 * Author URI: https://preseto.com
 * Version: 0.6.2
 * License: GPL2
 * Text Domain: contact-form-7-extras
 */

include_once dirname( __FILE__ ) . '/src/class-cf7-extras.php';

$plugin = Cf7_Extras::instance();
$plugin->set_plugin_dir( dirname( __FILE__ ) );
$plugin->init();
