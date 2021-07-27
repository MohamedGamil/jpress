<?php

/**
 * Settings Page
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Settings Page
$settings->open_tab_item('settings');

$section_header_social = $settings->add_section( array(
  'name' => __( 'Social', 'textdomain' ),
  'id' => 'local-section_social_links',
  'desc' => __( 'Add social networks links to your application', 'textdomain' ),
  'options' => array( 'toggle' => true )
));

$section_header_social->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'social_enabled',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));

$socialLinks = $section_header_social->add_group( array(
  'id' => 'social',
  'name' => __('Social', 'textdomain'),
  'controls' => array(
    'name' =>  __('Social Link', 'textdomain').' #',
    'readonly_name' => false,
    'images' => false,
  ),
  'options' => array(
  'add_item_text' => __('New Social Link', 'textdomain'),
    'show_if' => array('social_enabled', '=', 'true'),
  ),
));

$socialLinks->open_mixed_field(array('name' => __('Title', 'textdomain' )));
$socialLinks->add_field(array(
  'name' => __( 'Enable', 'textdomain' ),
  'id' => 'social_link_title',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$socialLinks->add_field(array(
  'id' => 'title',
  'name' => __('Social Link Title', 'textdomain'),
  'type' => 'text',
  'grid' => '3-of-6',
  'options' => array(
    'show_if' => array( 'social_link_title', '=', 'true' ),
  ),
));
$socialLinks->close_mixed_field();

$socialLinks->add_field( array(
  'name' => __('Icon', 'textdomain'),
  'id' => 'icon',
  'type' => 'icon_selector',
  'default' => '0xe95d',
  'items' => AppbearItems::icon_fonts(),
  'only' => AppbearItems::SOCIAL_ICONS_SUBSET,
  'options' => array(
    'wrap_height' => '220px',
    'size' => '36px',
    'hide_search' => false,
    'hide_buttons' => true,
  ),
));

$socialLinks->add_field(array(
  'id' => 'url',
  'name' => __('URL', 'textdomain'),
  'type' => 'text',
  'grid' => '3-of-6'
));

$settings->add_field(array(
  'name' => __( 'Text size option', 'textdomain' ),
  'id' => 'settingspage-textSize',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
  'desc' => __( 'Give your vistiors the ability to change the text size of the application', 'textdomain' ),
));

$settings->add_field(array(
  'name' => __( 'Switch between Dark/Light modes', 'textdomain' ),
  'id' => 'settingspage-darkMode',
  'type' => 'switcher',
  'default'	=>	'false',
  'desc' => __( 'Give your vistiors the ability to switch between Dark/Light modes', 'textdomain' ),
  'options' => array(
  'on_value' => 'true',
  'off_value' => 'false',
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));

$settings->add_field(array(
  'name' => __( 'Rate application', 'textdomain' ),
  'id' => 'settingspage-rateApp',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
  'desc' => __( 'Show rate appliction button on the settings page', 'textdomain' ),
));

$settings->add_field(array(
  'name' => __( 'Share application', 'textdomain' ),
  'id' => 'local-settingspage-share',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
  'desc' => __( 'Show share appliction button on the settings page', 'textdomain' ),
));

$settings->open_mixed_field(array('name' => __('Share info', 'textdomain' ),'options'	=>	array('show_if' => array('local-settingspage-share', '=', 'true'))));

$settings->add_field(
array(
  'name' => __( 'Headline', 'textdomain' ),
  'id' => 'settingspage-shareApp-title',
'type' => 'text'
));

$settings->add_field(array(
  'name' => __( 'Image', 'textdomain' ),
  'id' => 'settingspage-shareApp-image',
  'type' => 'file',
  'desc' => __( 'The image that will be shared with the application link', 'textdomain' ),
));

$settings->add_field(
array(
  'name' => __( 'Android Link', 'textdomain' ),
  'id' => 'settingspage-shareApp-android',
'type' => 'text'
));

$settings->add_field(
array(
  'name' => __( 'iOS Link', 'textdomain' ),
  'id' => 'settingspage-shareApp-ios',
'type' => 'text'
));

$settings->close_mixed_field();

$settings->open_mixed_field(array('name' => __('About us', 'textdomain' )));

$settings->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'local-settingspage-aboutus',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));

$settings->add_field( array(
  'id' => 'settingspage-aboutUs',
  'name' => __( 'About us page',   'textdomain' ),
  'type' => 'select',
  'items' => AppbearItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options'	=>	array(
    'show_if' => array('local-settingspage-aboutus', '=', 'true')
  ),
));

$settings->close_mixed_field();

$settings->add_field( array(
  'id' => 'settingspage-privacyPolicy',
  'name' => __( 'Privacy page',   'textdomain' ),
  'type' => 'select',
  'default' => get_option( 'wp_page_for_privacy_policy' ),
  'items' => AppbearItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
));

//  . ' ' . get_option( 'wp_page_for_privacy_policy' )
$settings->add_field( array(
  'id' => 'settingspage-termsAndConditions',
  'name' => __( 'Terms and conditions page',   'textdomain' ),
  'type' => 'select',
  'default' => get_option( 'wp_page_for_privacy_policy' ),
  'items' => AppbearItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
));

$settings->open_mixed_field(array('name' => __('Contact us', 'textdomain' )));

$settings->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'settingspage-contactus',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));

$settings->add_field(array(
  'name' => __( 'Email/s', 'textdomain' ),
  'id' => 'local-settingspage-contactus',
  'type' => 'textarea',
  'desc' => __( 'Those emails will be the emails which will receive the contact us messages from the applications.', 'textdomain' ),
  'grid' => '5-of-6',
  'default' => get_bloginfo( 'admin_email' ),
  'options' => array(
    'desc_tooltip' => true,
    'show_if' => array('settingspage-contactus', '=', 'true')
  )
));

$settings->close_mixed_field();

$settings->add_field(array(
  'name' => __( 'About application', 'textdomain' ),
  'id' => 'local-settingspage-aboutapp',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
  'desc' => __( 'Show about appliction page onn the settings page, which will be needed if you need to activate the development mode too', 'textdomain' ),
));
$settings->open_mixed_field(array('name' => __('About Info', 'textdomain' ),'options'	=>	array('show_if' => array('local-settingspage-aboutapp', '=', 'true'))));

$settings->add_field(array(
  'name' => __('Logo (Light)', 'textdomain' ),
  'id' => 'settingspage-aboutapp-logo-light',
  'type' => 'file',
  'default' => JPRESS_URL .'img/jannah-logo-light.png',
));

$settings->add_field(array(
  'name' => __('Logo (Dark)', 'textdomain' ),
  'id' => 'settingspage-aboutapp-logo-dark',
  'type' => 'file',
  'default' => JPRESS_URL .'img/jannah-logo-dark.png',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));

$settings->add_field(
array(
  'name' => __( 'Title', 'textdomain' ),
  'id' => 'settingspage-aboutapp-title',
  'type' => 'text',
  'default' => get_bloginfo( 'name' ),
));

$settings->add_field(
array(
  'name' => __( 'Description', 'textdomain' ),
  'id' => 'settingspage-aboutapp-content',
  'type' => 'textarea',
  'default' => get_bloginfo( 'description' ),
));

$settings->close_mixed_field();
$settings->add_field(array(
  'name' => __( 'Enable Demos', 'textdomain' ),
  'id' => 'settingspage-demos',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$settings->open_mixed_field( array(
  'name' => __('Development Mode', 'textdomain' ),
  'desc' => __( 'The development mode allows you to only save changes to your mobile application and after you see the result, you can deactivate it and publish the changes to all your visitors.', 'textdomain' ),
  'options'	=>	array('show_if' => array('local-settingspage-aboutapp', '=', 'true'))
));

$settings->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'settingspage-devmode',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));

$settings->close_mixed_field();
$settings->close_tab_item('settings');
