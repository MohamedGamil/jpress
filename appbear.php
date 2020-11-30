<?php
/**
 * Plugin Name: AppBear (BETA)
 * Plugin URI: https://appbear.com
 * Description: Convert your WordPress site into a Native Mobile App. No coding required. Your app syncs with your site automatically. Increase engagement, loyalty and monetize better on mobile!
 * Version: 0.0.1
 * Author: AppBear Team
 * Author URI: https://appbear.com
 * Text Domain: appbear
 * Domain Path: /languages/
 */

/*
|---------------------------------------------------------------------------------------------------
| Appbear Framework
|---------------------------------------------------------------------------------------------------
*/

if( ! class_exists( 'AppbearLoader148', false ) ){
    include dirname( __FILE__ ) . '/loader.php';
    $loader = new AppbearLoader148( '1.4.8', 952 );
    $loader->init();
}
