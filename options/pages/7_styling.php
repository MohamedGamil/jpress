<?php

/**
 * Styling Tab
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Styling Page
$settings->open_tab_item('styling');

$settings->open_mixed_field(array('name' => __('Shadow Color', 'jpress' )));
$settings->add_field(array(
  'id' => 'styling-themeMode_light-shadowColor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => 'rgba(0,0,0,0.15)',
  'options' => array(
  'format' => 'rgba',
  'show_default_button' => true,
  ),
));

$settings->add_field(array(
  'id' => 'styling-themeMode_dark-shadowColor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => 'rgba(0,0,0,0.15)',
  'options' => array(
  'format' => 'rgba',
  'show_default_button' => true,
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$settings->close_mixed_field();

$settings->open_mixed_field(array('name' => __('Dividers Color', 'jpress' )));
$settings->add_field(array(
  'id' => 'styling-themeMode_light-dividerColor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => 'rgba(0,0,0,0.05)',
  'options' => array(
  'format' => 'rgba',
  'show_default_button' => true,
  ),
));

$settings->add_field(array(
  'id' => 'styling-themeMode_dark-dividerColor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => 'rgba(255,255,255,0.13)',
  'options' => array(
  'format' => 'rgba',
  'show_default_button' => true,
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$settings->close_mixed_field();

$settings->open_mixed_field(array('name' => __('Inputs Background Color', 'jpress' ),'desc' => __( 'All inputs background color on search, sort by select and indicator.', 'jpress' ),));
$settings->add_field(array(
  'id' => 'styling-themeMode_light-inputsbackgroundcolor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => 'rgba(0,0,0,0.04)',
  'options' => array(
  'format' => 'rgba',
  'show_default_button' => true,
  ),
));

$settings->add_field(array(
  'id' => 'styling-themeMode_dark-inputsbackgroundcolor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => 'rgba(255,255,255,0.07)',
  'options' => array(
  'format' => 'rgba',
  'show_default_button' => true,
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$settings->close_mixed_field();

$settings->open_mixed_field(array('name' => __('Buttons Background color', 'jpress' )));
$settings->add_field(array(
  'id' => 'styling-themeMode_light-buttonsbackgroudcolor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#0088FF',
  'options' => array(
  'format' => 'hex',
  'show_default_button' => true,
  ),
));
$settings->add_field(array(
  'id' => 'styling-themeMode_dark-buttonsbackgroudcolor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#0088FF',
  'options' => array(
  'format' => 'hex',
  'show_default_button' => true,
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$settings->close_mixed_field();

$settings->open_mixed_field(array('name' => __('Buttons Text color', 'jpress' )));
$settings->add_field(array(
  'id' => 'styling-themeMode_light-buttonTextColor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#FFFFFF',
  'options' => array(
  'format' => 'hex',
  'show_default_button' => true,
  ),
));
$settings->add_field(array(
  'id' => 'styling-themeMode_dark-buttonTextColor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#FFFFFF',
  'options' => array(
  'format' => 'hex',
  'show_default_button' => true,
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$settings->close_mixed_field();
$settings->open_mixed_field(array('name' => __('Success Message Background color', 'jpress' )));
$settings->add_field(array(
  'id' => 'styling-themeMode_light-successcolor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#006900',
  'options' => array(
  'format' => 'hex',
  'show_default_button' => true,
  ),
));
$settings->add_field(array(
  'id' => 'styling-themeMode_dark-successcolor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#006900',
  'options' => array(
  'format' => 'hex',
  'show_default_button' => true,
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$settings->close_mixed_field();

$settings->open_mixed_field(array('name' => __('Error Message Background color', 'jpress' )));
$settings->add_field(array(
  'id' => 'styling-themeMode_light-errorcolor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#FF0000',
  'options' => array(
  'format' => 'hex',
  'show_default_button' => true,
  ),
));
$settings->add_field(array(
  'id' => 'styling-themeMode_dark-errorcolor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#FF0000',
  'options' => array(
  'format' => 'hex',
  'show_default_button' => true,
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$settings->close_mixed_field();
$settings->close_tab_item('styling');
