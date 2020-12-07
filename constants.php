<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/*
|---------------------------------------------------------------------------------------------------
| AppBear Plugin Front Controller
|---------------------------------------------------------------------------------------------------
*/


/**
 * Currently plugin version & priority.
 *
 * Use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'APPBEAR_VERSION', '0.0.2' );
define( 'APPBEAR_PRIORITY', 952 );


/**
 * AppBear Store URL.
 */
define( 'APPBEAR_STORE_URL', 'https://appstage.tielabs.com' );


/**
 * AppBear Copyrights URL.
 */
define( 'APPBEAR_COPYRIGHTS_URL', 'https://appbear.io' );


/**
 * AppBear Item ID & Name.
 */
define( 'APPBEAR_ITEM_ID', 1044 );
define( 'APPBEAR_ITEM_NAME', 'AppBear' );


/**
 * AppBear Plugin Slug.
 */
define( 'APPBEAR_SLUG',  'appbear' );


/**
 * AppBear Root Directory.
 */
define( 'APPBEAR_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );


/**
 * AppBear FontAwesome Version.
 */
defined('APPBEAR_FONTAWESOME_VERSION') OR define('APPBEAR_FONTAWESOME_VERSION', '4.x');


/**
 * Enable / Disable License Debugging Mode
 */
define( 'APPBEAR_ENABLE_LICENSE_DEBUG_MODE', true );
