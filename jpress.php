<?php

/**
 * JPress bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jpress.dedulab.com
 * @since             0.0.2
 * @package           JPress
 *
 * @wordpress-plugin
 * Plugin Name:       JPress (BETA)
 * Plugin URI:        https://jpress.dedulab.com
 * Description:       Convert your WordPress site into a Native Mobile App. No coding required. Your app syncs with your site automatically. Increase engagement, loyalty and monetize better on mobile!
 * Version:           1.0.4
 * Author:            JPress Team
 * Author URI:        https://dedulab.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jpress
 * Domain Path:       /languages
 */


/*
|---------------------------------------------------------------------------------------------------
| JPress Plugin Front Controller
|---------------------------------------------------------------------------------------------------
*/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Load Plugin Constants & Helper Functions
 */
require_once plugin_dir_path( __FILE__ ) . '/constants.php';
require_once JPRESS_INCLUDES_DIR . 'functions.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jpress-activator.php
 */
function jpress_activate_hook() {
	require_once JPRESS_INCLUDES_DIR . 'class-jpress-activator.php';

	JPress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jpress-deactivator.php
 */
function jpress_deactivate_hook() {
	require_once JPRESS_INCLUDES_DIR . 'class-jpress-deactivator.php';

	JPress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'jpress_activate_hook' );
register_deactivation_hook( __FILE__, 'jpress_deactivate_hook' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.2
 */
require_once plugin_dir_path( __FILE__ ) . '/loader.php';

if ( class_exists( 'JPressLoader', false ) && defined('JPRESS_DID_INIT') === false ) {
  $loader = new JPressLoader( JPRESS_VERSION, JPRESS_PRIORITY );
  $loader->init();
}
