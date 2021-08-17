<?php

/*
|---------------------------------------------------------------------------------------------------
| JPress Plugin Configuration Constants
|---------------------------------------------------------------------------------------------------
*/


/**
 * Currently plugin version & priority.
 *
 * Use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'JPRESS_VERSION', '1.0.4' );
define( 'JPRESS_PRIORITY', 953 );


/**
 * JPress Store URL.
 */
define( 'JPRESS_STORE_URL', 'http://appstage.tielabs.com' );


/**
 * JPress Copyrights URL.
 */
define( 'JPRESS_COPYRIGHTS_URL', 'https://jpress.dedulab.com' );


/**
 * JPress Item ID & Name.
 */
define( 'JPRESS_ITEM_ID', 1044 );
define( 'JPRESS_ITEM_NAME', 'JPress' );


/**
 * JPress Plugin Slug.
 */
define( 'JPRESS_SLUG',  'jpress' );


/**
 * JPress Deeplinking Scheme.
 */
define( 'JPRESS_DEEPLINKING_SCHEME',  'jpress' );


/**
 * JPress Root Directory.
 */
define( 'JPRESS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );


/**
 * JPress Includes Directory.
 */
define( 'JPRESS_INCLUDES_DIR', JPRESS_DIR . 'includes' . DIRECTORY_SEPARATOR );


/**
 * JPress Options Directory.
 */
define( 'JPRESS_OPTIONS_DIR', JPRESS_DIR . 'options' . DIRECTORY_SEPARATOR );


/**
 * JPress Plugin Front Controller File (Main Plugin File)
 */
define( 'JPRESS_FC_FILE', JPRESS_DIR . 'jpress.php' );


/**
 * JPress FontAwesome Version.
 */
defined('JPRESS_FONTAWESOME_VERSION') OR define('JPRESS_FONTAWESOME_VERSION', '5.15.1');


/**
 * JPress Options Key Name
 */
define( 'JPRESS_PRIMARY_OPTIONS', 'jpress-settings' );


/**
 * JPress Download App Key Name
 */
define( 'JPRESS_APP_BIN_PAGE_KEY', 'jpress-app-bins' );


/**
 * Deeplinking Option Key Name
 */
define( 'JPRESS_DEEPLINKING_OPTION', 'jpress_deeplinking_configuration' );


/**
 * License Option Key Name
 */
define( 'JPRESS_LICENSE_KEY_OPTION', 'jpress_license_activation_key' );


/**
 * License Status Option Key Name
 */
define( 'JPRESS_LICENSE_STATUS_KEY_OPTION', 'jpress_activation_status' );


/**
 * Enable / Disable Debugging Helpers
 */
define( 'JPRESS_ENABLE_DEBUG_HELPERS', true );


/**
 * Enable / Disable Displaying Connect to JPress even if the license is active
 */
define( 'JPRESS_ENABLE_CONNECT_PAGE_IF_ACTIVE', false );


/**
 * Enable / Disable Plugin Automatic Updates
 */
define( 'JPRESS_ENABLE_AUTO_UPDATE', false );


/**
 * Enable / Disable License Debugging Mode
 */
define( 'JPRESS_ENABLE_LICENSE_DEBUG_MODE', false );


/**
 * Enable / Disable License Debugging Mode
 */
define( 'JPRESS_OPTIONS_KEY', 'jpress_options' );
