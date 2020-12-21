<?php

/**
 * AppBear bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://appbear.io
 * @since             0.0.2
 * @package           App_Bear
 *
 * @wordpress-plugin
 * Plugin Name:       AppBear (BETA)
 * Plugin URI:        https://appbear.io
 * Description:       Convert your WordPress site into a Native Mobile App. No coding required. Your app syncs with your site automatically. Increase engagement, loyalty and monetize better on mobile!
 * Version:           0.0.10
 * Author:            AppBear Team
 * Author URI:        https://appbear.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       appbear
 * Domain Path:       /languages
 */


 /*
|---------------------------------------------------------------------------------------------------
| AppBear Plugin Front Controller
|---------------------------------------------------------------------------------------------------
*/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Load Plugin Constants
 */
require_once plugin_dir_path( __FILE__ ) . '/constants.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-appbear-activator.php
 */
function appbear_activate_hook() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-appbear-activator.php';
	App_Bear_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-appbear-deactivator.php
 */
function appbear_deactivate_hook() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-appbear-deactivator.php';
	App_Bear_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'appbear_activate_hook' );
register_deactivation_hook( __FILE__, 'appbear_deactivate_hook' );

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

if ( class_exists( 'AppbearLoader148', false ) && defined('APPBEAR_DID_INIT') === false ) {
  $loader = new AppbearLoader148( APPBEAR_VERSION, APPBEAR_PRIORITY );
  $loader->init();
}
