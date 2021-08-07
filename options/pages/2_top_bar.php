<?php

/**
 * Top Bar
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Top Bar Page
$settings->open_tab_item('topbar');
$settings->add_field(array(
  'name' => __('Logo (Light)', 'jpress' ),
  'id' => 'logo-light',
  'type' => 'file',
  'default' => JPRESS_URL . 'assets/img/jpress-logo-light.svg',
));
$settings->add_field(array(
  'name' => __('Logo (Dark)', 'jpress' ),
  'id' => 'logo-dark',
  'type' => 'file',
  'default' => JPRESS_URL . 'assets/img/jpress-logo.svg',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$settings->add_field(array(
  'name' => __( 'Logo Postition', 'jpress' ),
  'id' => 'appbar-position',
  'type' => 'radio',
  'default' => 'LogoPosition.start',
  'items' => array(
    'LogoPosition.start' => __( 'Start', 'jpress' ),
    'LogoPosition.center' => __( 'Center', 'jpress' ),
  )
));
$settings->add_field( array(
  'name' => __('Side menu icon', 'jpress'),
  'id' => 'sidenavbar-icon',
  'type' => 'icon_selector',
  'default' => '0xe808',
  'items' => array_merge( JPressItems::icon_fonts() ),
  'options' => array(
    'wrap_height' => '220px',
    'size' => '36px',
    'hide_search' => false,
    'hide_buttons' => true,
    'show_if' => array('menu_type', '!=', 'bottombar')
  ),
));
$settings->open_mixed_field(array('name' => __('Show search button', 'jpress' )));
$settings->add_field(array(
  'name' => __( 'Enabled', 'jpress' ),
  'id' => 'topbar_search_button',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$settings->add_field( array(
  'name' => __('Search icon', 'jpress'),
  'id' => 'appbar-searchicon',
  'type' => 'icon_selector',
  'default' => '0xe820',
  'items' => array_merge( JPressItems::icon_fonts() ),
  'options' => array(
    'wrap_height' => '220px',
    'size' => '36px',
    'hide_search' => false,
    'hide_buttons' => true,
    'show_if' => array('topbar_search_button', '=', 'true')
  ),
));
$settings->close_mixed_field();

$settings->open_mixed_field(array('name' => __('Background color', 'jpress' )));
$settings->add_field(array(
  'id' => 'styling-themeMode_light-appBarBackgroundColor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#FFFFFF',
));

$settings->add_field(array(
  'id' => 'styling-themeMode_dark-appBarBackgroundColor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#333739',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));
$settings->close_mixed_field();

$settings->open_mixed_field(array('name' => __('Icons/Text colors', 'jpress' )));
$settings->add_field(array(
  'id' => 'styling-themeMode_light-appBarColor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#333739',
));

$settings->add_field(array(
  'id' => 'styling-themeMode_dark-appBarColor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#FFFFFF',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));
$settings->close_mixed_field();
$settings->close_tab_item('topbar');
