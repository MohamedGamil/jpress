<?php
/**
 * Plugin Name: appBear
 * Plugin URI: https://appbear.com
 * Description: Convert your WordPress site into a Native Mobile App. No coding required. Your app syncs with your site automatically. Increase engagement, loyalty and monetize better on mobile!
 * Version: 1.0.0
 * Author: appBear Team
 * Author URI: https://appbear.com
 * Text Domain: appbear
 * Domain Path: /languages/
 */

/*
|---------------------------------------------------------------------------------------------------
| Xbox Framework
|---------------------------------------------------------------------------------------------------
*/

if( ! class_exists( 'XboxLoader148', false ) ){
    include dirname( __FILE__ ) . '/loader.php';
    $loader = new XboxLoader148( '1.4.8', 952 );
    $loader->init();
}