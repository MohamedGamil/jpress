<?php

/**
 * General Tab
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: General Styling
$settings->open_tab_item('general');

$section_header_1 = $settings->add_section( array(
  'name' => __( 'General Settings', 'jpress' ),
  'id' => 'section-general-settings',
  'options' => array( 'toggle' => true )
));

$section_header_1->add_field(array(
  'name' => __( 'Date format', 'jpress' ),
  'id' => 'time_format',
  'type' => 'radio',
  'default' => 'traditional',
  'items' => array(
    'traditional' => __( 'Traditional', 'jpress' ),
    'modern' => __( 'Time Ago Format', 'jpress' ),
  )
));

$section_header_1->add_field(array(
  'name' => __( 'Switch between dark & light', 'jpress' ),
  'id' => 'switch_theme_mode',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value'  => 'true',
    'off_value' => 'false'
  )
));

$section_header_1->add_field( array(
  'id' => 'thememode',
  'name' => __( 'Default Theme Mode', 'jpress' ),
  'type' => 'select',
  'default' => 'ThemeMode_light',
  'items' => array(
    'ThemeMode_system' => __( 'System', 'jpress' ),
    'ThemeMode_light' => __( 'Light', 'jpress' ),
    'ThemeMode_dark' => __( 'Dark', 'jpress' ),
  ),
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));

$section_header_1->add_field( array(
  'id' => 'menu_type',
  'name' => __( 'Menu Type', 'jpress' ),
  'type' => 'select',
  'default' => 'both',
  'items' => array(
    'bottombar' => __( 'Bottom Bar Only', 'jpress' ),
    'sidemenu' => __( 'Side Menu Only', 'jpress' ),
    'both' => __( 'Bottom Bar & Side Menu', 'jpress' ),
  )
));
$section_header_1->open_mixed_field(array('name' => __('Background color', 'jpress' ),'desc'      => __( 'Application background color.', 'jpress' ),));
$section_header_1->add_field(array(
  'id'        => 'styling-themeMode_light-scaffoldbackgroundcolor',
  'type'      => 'colorpicker',
  'default'   => '#FFFFFF',
    'options' => array(
    'show_name' => array('switch_theme_mode', '=', 'true'),
  )
));

$section_header_1->add_field(array(
  'id'        => 'styling-themeMode_dark-scaffoldbackgroundcolor',
  'name'      => __( 'Dark Mode', 'jpress' ),
  'type'      => 'colorpicker',
  'default'   => '#333739',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));

$section_header_1->close_mixed_field();

$section_header_1->open_mixed_field(array('name' => __('Main Color', 'jpress' ),'desc'      => __( 'The main color of the application.', 'jpress' ),));

$section_header_1->add_field(array(
  'id'        => 'styling-themeMode_light-primary',
  'type'      => 'colorpicker',
  'default'   => '#0088ff',
));

$section_header_1->add_field(array(
  'id'        => 'styling-themeMode_dark-primary',
  'name'      => __( 'Dark Mode', 'jpress' ),
  'type'      => 'colorpicker',
  'default'   => '#0088ff',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));

$section_header_1->close_mixed_field();

$section_header_1->open_mixed_field(array('name' => __('Primary text Color', 'jpress' ),'desc'      => __( 'All text color on application such as post titles, sections titles, posts content, pages content and settings page.', 'jpress' ),));

$section_header_1->add_field(array(
  'id'        => 'styling-themeMode_light-secondary',
  'type'      => 'colorpicker',
  'default'   => '#333739',
));

$section_header_1->add_field(array(
  'id'        => 'styling-themeMode_dark-secondary',
  'name'      => __( 'Dark Mode', 'jpress' ),
  'type'      => 'colorpicker',
  'default'   => '#FFFFFF',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));

$section_header_1->close_mixed_field();

$section_header_1->open_mixed_field(array('name' => __('Meta text color', 'jpress' ),'desc' => __( 'All small text color on application such as meta posts.', 'jpress' ),));
$section_header_1->add_field(array(
  'id' => 'styling-themeMode_light-secondaryvariant',
  'type' => 'colorpicker',
  'default' => '#8A8A89',
));

$section_header_1->add_field(array(
  'id' => 'styling-themeMode_dark-secondaryvariant',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#8A8A89',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));

$section_header_1->close_mixed_field();

$deeplinkingOpts = jpress_get_deeplinking_opts();
$canDeeplinking = isset($deeplinkingOpts->appid_ios) && empty($deeplinkingOpts->appid_ios) === false;
$deeplinkingAttrs = $canDeeplinking === false ? array( 'disabled' => 'disabled' ) : array();
$section_header_1->add_field(array(
  'name' => __( 'Enable Deeplinking Widget', 'jpress' ),
  'desc' => $canDeeplinking ? '' : __('This option can be enabled after activating your license, then saving settings for the first time. Also make sure to follow <a href="https://developer.android.com/training/app-links/verify-site-associations.html#request-verify" target="_blank">Android documentation</a> to ensure that deeplinking works without issues.'),
  'id' => 'is_deeplinking_widget_enabled',
  'type' => 'switcher',
  'default'	=>	$canDeeplinking ? 'true' : 'false',
  'attributes' => $deeplinkingAttrs,
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false',
  )
));

$settings->close_tab_item('general');
