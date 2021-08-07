<?php

/**
 * Side Menu
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Side-Menu Page
$settings->open_tab_item('sidemenu');
$jpress_sidemenu_styling = $settings->add_section( array(
  'name' => __( 'Side Menu Styling', 'jpress' ),
  'id' => 'section-sidemenu',
  'options' => array( 'toggle' => true )
));

$jpress_sidemenu_styling->open_mixed_field(array('name' => __('Background color', 'jpress' )));
$jpress_sidemenu_styling->add_field(array(
  'id' => 'styling-themeMode_light-background',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#FFFFFF',
));

$jpress_sidemenu_styling->add_field(array(
  'id' => 'styling-themeMode_dark-background',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#333739',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));
$jpress_sidemenu_styling->close_mixed_field();

$jpress_sidemenu_styling->open_mixed_field(array('name' => __('Icon/Text color', 'jpress' )));
$jpress_sidemenu_styling->add_field(array(
  'id' => 'styling-themeMode_light-sideMenuIconsTextColor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#333739',
));

$jpress_sidemenu_styling->add_field(array(
  'id' => 'styling-themeMode_dark-sideMenuIconsTextColor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#FFFFFF',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));
$jpress_sidemenu_styling->close_mixed_field();


$sidemenu_items = $settings->add_section( array(
  'name' => __( 'Side Menu Items', 'jpress' ),
  'id' => 'section-sidemenu-items',
  'options' => array( 'toggle' => true )
));

$tabs = $sidemenu_items->add_group( array(
  'name' => __('Menu Items', 'jpress'),
  'id' => 'navigators',
  'options' => array(
    'add_item_text' => __('New Tab', 'jpress'),
  ),
  'controls' => array(
    'name' =>  __('Menu Item', 'jpress').' #',
    'position' => 'left',
    'readonly_name' => true,
    'images' => false,
  ),
));

$tabs->add_field(array(
  'name' => __( 'Link Type', 'jpress' ),
  'id' => 'type',
  'type' => 'radio',
  'default' => 'NavigationType.category',
  'items' => array(
    'NavigationType.main' => __( 'Main Page', 'jpress' ),
    'NavigationType.category' => __( 'Category', 'jpress' ),
    'NavigationType.page' => __( 'Page', 'jpress' ),
  )
));
$tabs->open_mixed_field(array('name' => __('Link Icon', 'jpress' )));
$tabs->add_field(array(
  'name' => __( 'Enable', 'jpress' ),
  'id' => 'side_menu_tab_icon',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$tabs->add_field( array(
  'name' => __('Tab icon', 'jpress'),
  'id' => 'icon',
  'type' => 'icon_selector',
  'default' => '0xe9f5',
  'items' => array_merge( JPressItems::icon_fonts() ),
  'options' => array(
  'wrap_height' => '220px',
  'size' => '36px',
  'hide_search' => false,
  'hide_buttons' => true,
    'show_if' => array('side_menu_tab_icon', '=', 'true'),
  ),
));
$tabs->close_mixed_field();

$tabs->add_field(array(
  'name' => __( 'Main Pages', 'jpress' ),
  'id' => 'main',
  'type' => 'select',
  'default' => 'MainPage.home',
  'items' => array(
    'MainPage.home' => __( 'Home', 'jpress' ),
    'MainPage.sections' => __( 'Sections', 'jpress' ),
    'MainPage.favourites' => __( 'Favorites', 'jpress' ),
    'MainPage.settings' => __( 'Settings', 'jpress' ),
    'MainPage.contactUs' => __( 'Contact us', 'jpress' ),
  ),
  'options' => array(
    'show_if' => array('type', '=', 'NavigationType.main'),
  ),
  'attributes' => array( 'required' => true ),
));
$tabs->add_field(array(
  'name' => __( 'Categories', 'jpress' ),
  'id' => 'category',
  'type' => 'select',
  'items' => JPressItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('type', '=', 'NavigationType.category'),
  ),
  'attributes' => array( 'required' => true ),
));
$tabs->add_field(array(
  'name' => __( 'Pages', 'jpress' ),
  'id' => 'page',
  'type' => 'select',
  'items' => JPressItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('type', '=', 'NavigationType.page'),
  ),
  'attributes' => array( 'required' => true ),
));

$tabs->open_mixed_field(array('name' => __('Customized Title', 'jpress' )));
$tabs->add_field(array(
  'name' => __( 'Enable', 'jpress' ),
  'id' => 'cutomized_title',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$tabs->add_field(array(
  'name' => __( 'New Title', 'jpress' ),
  'id' => 'title',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('cutomized_title', '=', 'true'),
  ),
));
$tabs->close_mixed_field();
$settings->close_tab_item('sidemenu');
