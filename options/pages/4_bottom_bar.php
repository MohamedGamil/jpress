<?php

/**
 * Bottom Bar
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Bottom Bar Page
$settings->open_tab_item('bottombar');
$jpress_bottombar_styling = $settings->add_section( array(
  'name' => __( 'Bottom Bar Styling', 'jpress' ),
  'id' => 'section-bottombar-styling',
  'options' => array( 'toggle' => true )
));

$jpress_bottombar_styling->open_mixed_field(array('name' => __('Bottom bar background color', 'jpress' )));
$jpress_bottombar_styling->add_field(array(
  'id' => 'styling-themeMode_light-bottomBarBackgroundColor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#BCBCBC',
));

$jpress_bottombar_styling->add_field(array(
  'id' => 'styling-themeMode_dark-bottomBarBackgroundColor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#838483',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));
$jpress_bottombar_styling->close_mixed_field();

$jpress_bottombar_styling->open_mixed_field(array('name' => __('InActive tab text color', 'jpress' )));
$jpress_bottombar_styling->add_field(array(
  'id' => 'styling-themeMode_light-bottomBarInActiveColor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#8A8A8A',
));

$jpress_bottombar_styling->add_field(array(
  'id' => 'styling-themeMode_dark-bottomBarInActiveColor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#C3C3C3',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));
$jpress_bottombar_styling->close_mixed_field();

$jpress_bottombar_styling->open_mixed_field(array('name' => __('Active tab text color', 'jpress' )));
$jpress_bottombar_styling->add_field(array(
  'id' => 'styling-themeMode_light-bottomBarActiveColor',
  //'name' => __( 'Light Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#0088ff',
));

$jpress_bottombar_styling->add_field(array(
  'id' => 'styling-themeMode_dark-bottomBarActiveColor',
  'name' => __( 'Dark Mode', 'jpress' ),
  'type' => 'colorpicker',
  'default' => '#0088ff',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  )
));
$jpress_bottombar_styling->close_mixed_field();

$jpress_bottombar_tabs = $settings->add_section( array(
  'name' => __( 'Bottom Bar Tabs', 'jpress' ),
  'id' => 'section-bottombar-tabs',
  'options' => array(
    'toggle' => true,
    'show_if' => array('menu_type', '!=', 'sidemenu'),
  ),
));


$tabs = $jpress_bottombar_tabs->add_group( array(
  'name' => __('Tabs', 'jpress'),
  'id' => 'bottombar_tabs',
  'options' => array(
  'add_item_text' => __('New Tab', 'jpress'),
    'show_if' => array('menu_type', '!=', 'sidemenu'),
  ),
  'controls' => array(
  'name' =>  __('Tab', 'jpress').' #',
  'readonly_name' => true,
  'images' => false,
  ),
));

$tabs->add_field(array(
  'name' => __( 'Tab Type', 'jpress' ),
  'id' => 'type',
  'type' => 'radio',
  'default' => 'NavigationType.category',
  'items' => array(
    'NavigationType.main' => __( 'Main Page', 'jpress' ),
    'NavigationType.category' => __( 'Category', 'jpress' ),
    'NavigationType.page' => __( 'Page', 'jpress' ),
  )
));

$tabs->open_mixed_field(array('name' => __('Tab Icon', 'jpress' )));

$tabs->add_field(array(
  'name' => __( 'Enable', 'jpress' ),
  'id' => 'bottom_bar_icon_enable',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$tabs->add_field( array(
  'name' => __('Tab icon', 'jpress'),
  'id' => 'icon',
  'type' => 'icon_selector',
  'default' => '0xe800',
  'items' => array_merge( JPressItems::icon_fonts() ),
  'options' => array(
    'wrap_height' => '220px',
    'size' => '36px',
    'hide_search' => false,
    'hide_buttons' => true,
    // 'show_if' => array('bottom_bar_icon_enable', '=', 'true'),
    'show_if' => array('bottom_bar_enable_tabs', '=', 'true'),
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

$tabs->open_mixed_field(array('name' => __('Title', 'jpress' )));
$tabs->add_field(array(
  'id' => 'title_enable',
  'name' => __( 'Enable', 'jpress' ),
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
  'on_value' => 'true',
  'off_value' => 'false',
    'show_if' => array('menu_type', '!=', 'sidemenu'),
  )
));
$tabs->add_field(array(
  'id' => 'cutomized_title',
  'name' => __( 'Enable Customized Title', 'jpress' ),
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
  'on_value' => 'true',
  'off_value' => 'false',
    'show_if' => array('title_enable', '=', 'true'),
  )
));
$tabs->add_field(array(
  'id' => 'title',
  'type' => 'text',
  'name' => __( 'Title', 'jpress' ),
  'grid' => '5-of-6',
  'options' => array(
    'show_if' => array('cutomized_title', '=', 'true'),
  ),
));
$tabs->close_mixed_field();
$settings->close_tab_item('bottombar');
