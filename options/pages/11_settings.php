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
  'name' => __( 'Social', 'jpress' ),
  'id' => 'local-section_social_links',
  'desc' => __( 'Add social networks links to your application', 'jpress' ),
  'options' => array( 'toggle' => true )
));

$section_header_social->add_field(array(
  'name' => __( 'Enabled', 'jpress' ),
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
  'name' => __('Social', 'jpress'),
  'controls' => array(
    'name' =>  __('Social Link', 'jpress').' #',
    'readonly_name' => false,
    'images' => false,
  ),
  'options' => array(
  'add_item_text' => __('New Social Link', 'jpress'),
    'show_if' => array('social_enabled', '=', 'true'),
  ),
));

$socialLinks->open_mixed_field(array('name' => __('Title', 'jpress' )));
$socialLinks->add_field(array(
  'name' => __( 'Enable', 'jpress' ),
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
  'name' => __('Social Link Title', 'jpress'),
  'type' => 'text',
  'grid' => '3-of-6',
  'options' => array(
    'show_if' => array( 'social_link_title', '=', 'true' ),
  ),
));
$socialLinks->close_mixed_field();

$socialLinks->add_field( array(
  'name' => __('Icon', 'jpress'),
  'id' => 'icon',
  'type' => 'icon_selector',
  'default' => '0xe95d',
  'items' => JPressItems::icon_fonts(),
  'only' => JPressItems::SOCIAL_ICONS_SUBSET,
  'options' => array(
    'wrap_height' => '220px',
    'size' => '36px',
    'hide_search' => false,
    'hide_buttons' => true,
  ),
));

$socialLinks->add_field(array(
  'id' => 'url',
  'name' => __('URL', 'jpress'),
  'type' => 'text',
  'grid' => '3-of-6'
));

$settings->add_field(array(
  'name' => __( 'Text size option', 'jpress' ),
  'id' => 'settingspage-textSize',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
  'desc' => __( 'Give your vistiors the ability to change the text size of the application', 'jpress' ),
));

$settings->add_field(array(
  'name' => __( 'Switch between Dark/Light modes', 'jpress' ),
  'id' => 'settingspage-darkMode',
  'type' => 'switcher',
  'default'	=>	'false',
  'desc' => __( 'Give your vistiors the ability to switch between Dark/Light modes', 'jpress' ),
  'options' => array(
  'on_value' => 'true',
  'off_value' => 'false',
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));

$settings->add_field(array(
  'name' => __( 'Rate application', 'jpress' ),
  'id' => 'settingspage-rateApp',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
  'desc' => __( 'Show rate appliction button on the settings page', 'jpress' ),
));

$settings->add_field(array(
  'name' => __( 'Share application', 'jpress' ),
  'id' => 'local-settingspage-share',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
  'desc' => __( 'Show share appliction button on the settings page', 'jpress' ),
));

$settings->open_mixed_field(array('name' => __('Share info', 'jpress' ),'options'	=>	array('show_if' => array('local-settingspage-share', '=', 'true'))));

$settings->add_field(
array(
  'name' => __( 'Headline', 'jpress' ),
  'id' => 'settingspage-shareApp-title',
'type' => 'text'
));

$settings->add_field(array(
  'name' => __( 'Image', 'jpress' ),
  'id' => 'settingspage-shareApp-image',
  'type' => 'file',
  'desc' => __( 'The image that will be shared with the application link', 'jpress' ),
));

$settings->add_field(
array(
  'name' => __( 'Android Link', 'jpress' ),
  'id' => 'settingspage-shareApp-android',
'type' => 'text'
));

$settings->add_field(
array(
  'name' => __( 'iOS Link', 'jpress' ),
  'id' => 'settingspage-shareApp-ios',
'type' => 'text'
));

$settings->close_mixed_field();

$settings->open_mixed_field(array('name' => __('About us', 'jpress' )));

$settings->add_field(array(
  'name' => __( 'Enabled', 'jpress' ),
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
  'name' => __( 'About us page',   'jpress' ),
  'type' => 'select',
  'items' => JPressItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options'	=>	array(
    'show_if' => array('local-settingspage-aboutus', '=', 'true')
  ),
));

$settings->close_mixed_field();

$settings->add_field( array(
  'id' => 'settingspage-privacyPolicy',
  'name' => __( 'Privacy page',   'jpress' ),
  'type' => 'select',
  'default' => get_option( 'wp_page_for_privacy_policy' ),
  'items' => JPressItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
));

//  . ' ' . get_option( 'wp_page_for_privacy_policy' )
$settings->add_field( array(
  'id' => 'settingspage-termsAndConditions',
  'name' => __( 'Terms and conditions page',   'jpress' ),
  'type' => 'select',
  'default' => get_option( 'wp_page_for_privacy_policy' ),
  'items' => JPressItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
));

$settings->open_mixed_field(array('name' => __('Contact us', 'jpress' )));

$settings->add_field(array(
  'name' => __( 'Enabled', 'jpress' ),
  'id' => 'settingspage-contactus',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));

$settings->add_field(array(
  'name' => __( 'Email/s', 'jpress' ),
  'id' => 'local-settingspage-contactus',
  'type' => 'textarea',
  'desc' => __( 'Those emails will be the emails which will receive the contact us messages from the applications.', 'jpress' ),
  'grid' => '5-of-6',
  'default' => get_bloginfo( 'admin_email' ),
  'options' => array(
    'desc_tooltip' => true,
    'show_if' => array('settingspage-contactus', '=', 'true')
  )
));

$settings->close_mixed_field();

$settings->add_field(array(
  'name' => __( 'About application', 'jpress' ),
  'id' => 'local-settingspage-aboutapp',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
  'desc' => __( 'Show about appliction page onn the settings page, which will be needed if you need to activate the development mode too', 'jpress' ),
));
$settings->open_mixed_field(array('name' => __('About Info', 'jpress' ),'options'	=>	array('show_if' => array('local-settingspage-aboutapp', '=', 'true'))));

$settings->add_field(array(
  'name' => __('Logo (Light)', 'jpress' ),
  'id' => 'settingspage-aboutapp-logo-light',
  'type' => 'file',
  'default' => JPRESS_URL . 'assets/img/jpress-logo-light.svg',
));

$settings->add_field(array(
  'name' => __('Logo (Dark)', 'jpress' ),
  'id' => 'settingspage-aboutapp-logo-dark',
  'type' => 'file',
  'default' => JPRESS_URL . 'assets/img/jpress-logo.svg',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));

$settings->add_field(
array(
  'name' => __( 'Title', 'jpress' ),
  'id' => 'settingspage-aboutapp-title',
  'type' => 'text',
  'default' => get_bloginfo( 'name' ),
));

$settings->add_field(
array(
  'name' => __( 'Description', 'jpress' ),
  'id' => 'settingspage-aboutapp-content',
  'type' => 'textarea',
  'default' => get_bloginfo( 'description' ),
));

$settings->close_mixed_field();
$settings->add_field(array(
  'name' => __( 'Enable Demos', 'jpress' ),
  'id' => 'settingspage-demos',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$settings->open_mixed_field( array(
  'name' => __('Development Mode', 'jpress' ),
  'desc' => __( 'The development mode allows you to only save changes to your mobile application and after you see the result, you can deactivate it and publish the changes to all your visitors.', 'jpress' ),
  'options'	=>	array('show_if' => array('local-settingspage-aboutapp', '=', 'true'))
));

$settings->add_field(array(
  'name' => __( 'Enabled', 'jpress' ),
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
