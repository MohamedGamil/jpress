<?php

/*
|---------------------------------------------------------------------------------------------------
| AppBear Plugin Configuration Constants
|---------------------------------------------------------------------------------------------------
*/


/**
 * Currently plugin version & priority.
 *
 * Use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'APPBEAR_VERSION', '0.2.7' );
define( 'APPBEAR_PRIORITY', 952 );


/**
 * AppBear Store URL.
 */
define( 'APPBEAR_STORE_URL', 'http://appstage.tielabs.com' );


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
 * AppBear Deeplinking Scheme.
 */
define( 'APPBEAR_DEEPLINKING_SCHEME',  'appbear' );


/**
 * AppBear Root Directory.
 */
define( 'APPBEAR_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );


/**
 * AppBear Includes Directory.
 */
define( 'APPBEAR_INCLUDES_DIR', APPBEAR_DIR . 'includes' . DIRECTORY_SEPARATOR );


/**
 * AppBear Options Directory.
 */
define( 'APPBEAR_OPTIONS_DIR', APPBEAR_DIR . 'options' . DIRECTORY_SEPARATOR );


/**
 * AppBear Plugin Front Controller File (Main Plugin File)
 */
define( 'APPBEAR_FC_FILE', APPBEAR_DIR . 'appbear.php' );


/**
 * AppBear FontAwesome Version.
 */
defined('APPBEAR_FONTAWESOME_VERSION') OR define('APPBEAR_FONTAWESOME_VERSION', '4.x');


/**
 * AppBear Options Key Name
 */
define( 'APPBEAR_PRIMARY_OPTIONS', 'appbear-settings' );


/**
 * Deeplinking Option Key Name
 */
define( 'APPBEAR_DEEPLINKING_OPTION', 'appbear_deeplinking_configuration' );


/**
 * License Option Key Name
 */
define( 'APPBEAR_LICENSE_KEY_OPTION', 'appbear_license_activation_key' );


/**
 * License Status Option Key Name
 */
define( 'APPBEAR_LICENSE_STATUS_KEY_OPTION', 'appbear_activation_status' );


/**
 * Enable / Disable Debugging Helpers
 */
define( 'APPBEAR_ENABLE_DEBUG_HELPERS', true );


/**
 * Enable / Disable Displaying Connect to AppBear even if the license is active
 */
define( 'APPBEAR_ENABLE_CONNECT_PAGE_IF_ACTIVE', false );


/**
 * Enable / Disable Plugin Automatic Updates
 */
define( 'APPBEAR_ENABLE_AUTO_UPDATE', true );


/**
 * Enable / Disable License Debugging Mode
 */
define( 'APPBEAR_ENABLE_LICENSE_DEBUG_MODE', true );
