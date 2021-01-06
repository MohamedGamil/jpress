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
$appbear_sidemenu_styling = $settings->add_section( array(
  'name' => __( 'Side Menu Styling', 'textdomain' ),
  'id' => 'section-sidemenu',
  'options' => array( 'toggle' => true )
));

$appbear_sidemenu_styling->open_mixed_field(array('name' => __('Background color', 'textdomain' )));
$appbear_sidemenu_styling->add_field(array(
  'id' => 'styling-themeMode_light-background',
  //'name' => __( 'Light Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#FFFFFF',
));

$appbear_sidemenu_styling->add_field(array(
  'id' => 'styling-themeMode_dark-background',
  'name' => __( 'Dark Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#333739',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));
$appbear_sidemenu_styling->close_mixed_field();

$appbear_sidemenu_styling->open_mixed_field(array('name' => __('Icon/Text color', 'textdomain' )));
$appbear_sidemenu_styling->add_field(array(
  'id' => 'styling-themeMode_light-sideMenuIconsTextColor',
  //'name' => __( 'Light Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#333739',
));

$appbear_sidemenu_styling->add_field(array(
  'id' => 'styling-themeMode_dark-sideMenuIconsTextColor',
  'name' => __( 'Dark Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#FFFFFF',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));
$appbear_sidemenu_styling->close_mixed_field();


$sidemenu_items = $settings->add_section( array(
  'name' => __( 'Side Menu Items', 'textdomain' ),
  'id' => 'section-sidemenu-items',
  'options' => array( 'toggle' => true )
));

$tabs = $sidemenu_items->add_group( array(
  'name' => __('Menu Items', 'textdomain'),
  'id' => 'navigators',
  'options' => array(
    'add_item_text' => __('New Tab', 'textdomain'),
  ),
  'controls' => array(
    'name' =>  __('Menu Item', 'textdomain').' #',
    'position' => 'left',
    'readonly_name' => true,
    'images' => false,
  ),
));

$tabs->add_field(array(
  'name' => __( 'Link Type', 'textdomain' ),
  'id' => 'type',
  'type' => 'radio',
  'default' => 'NavigationType.category',
  'items' => array(
    'NavigationType.main' => __( 'Main Page', 'textdomain' ),
    'NavigationType.category' => __( 'Category', 'textdomain' ),
    'NavigationType.page' => __( 'Page', 'textdomain' ),
  )
));
$tabs->open_mixed_field(array('name' => __('Link Icon', 'textdomain' )));
$tabs->add_field(array(
  'name' => __( 'Enable', 'textdomain' ),
  'id' => 'side_menu_tab_icon',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$tabs->add_field( array(
  'name' => __('Tab icon', 'textdomain'),
  'id' => 'icon',
  'type' => 'icon_selector',
  'default' => '0xe9f5',
  'items' => array_merge( AppbearItems::icon_fonts() ),
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
  'name' => __( 'Main Pages', 'textdomain' ),
  'id' => 'main',
  'type' => 'select',
  'default' => 'MainPage.home',
  'items' => array(
    'MainPage.home' => __( 'Home', 'textdomain' ),
    'MainPage.sections' => __( 'Sections', 'textdomain' ),
    'MainPage.favourites' => __( 'Favorites', 'textdomain' ),
    'MainPage.settings' => __( 'Settings', 'textdomain' ),
    'MainPage.contactUs' => __( 'Contact us', 'textdomain' ),
  ),
  'options' => array(
    'show_if' => array('type', '=', 'NavigationType.main'),
  ),
  'attributes' => array( 'required' => true ),
));
$tabs->add_field(array(
  'name' => __( 'Categories', 'textdomain' ),
  'id' => 'category',
  'type' => 'select',
  'items' => AppbearItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('type', '=', 'NavigationType.category'),
  ),
  'attributes' => array( 'required' => true ),
));
$tabs->add_field(array(
  'name' => __( 'Pages', 'textdomain' ),
  'id' => 'page',
  'type' => 'select',
  'items' => AppbearItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('type', '=', 'NavigationType.page'),
  ),
  'attributes' => array( 'required' => true ),
));

$tabs->open_mixed_field(array('name' => __('Customized Title', 'textdomain' )));
$tabs->add_field(array(
  'name' => __( 'Enable', 'textdomain' ),
  'id' => 'cutomized_title',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$tabs->add_field(array(
  'name' => __( 'New Title', 'textdomain' ),
  'id' => 'title',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('cutomized_title', '=', 'true'),
  ),
));
$tabs->close_mixed_field();
$settings->close_tab_item('sidemenu');
