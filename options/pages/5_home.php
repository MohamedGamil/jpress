<?php

/**
 * Home Page
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Home Page
$settings->open_tab_item('homepage');

$tabs = $settings->add_section( array(
  'name' => __( 'Home Page tabs', 'textdomain' ),
  'id' => 'section-homepage-tabs',
  'options' => array( 'toggle' => true )
));

$tabs->add_field(array(
  'name' => __( 'Enable Tabs', 'textdomain' ),
  'id' => 'tabsbar_categories_tab',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));

$tabs->open_mixed_field(
  array(
    'name' =>  __('Customize Home Page Title in tabs', 'textdomain' ),
    'options' => array(
      'show_if' => array('tabsbar_categories_tab', '=', 'true')
    ),
  )
);
$tabs->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'local-hompage_title',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));
$tabs->add_field(array(
  'id' => 'homepage-sections-title',
  'name' => __( 'Title', 'textdomain' ),
  'type' => 'text',
  'grid' => '5-of-6',
  'default' => __( 'Home', 'textdomain' ),
  'options' => array(
    'show_if' => array('local-hompage_title', '=', 'true')
  )
));
$tabs->close_mixed_field();

$tabs->add_field( array(
  'name' => __( 'Layout', 'textdomain' ),
  'id' => 'tabs-tabslayout',
  'type' => 'image_selector',
  'default' => 'TabsLayout.tab1',
  'items' => array(
    'TabsLayout.tab1' => JPRESS_URL . 'options/img/topbar_tabs/tab_1.png',
    'TabsLayout.tab2' => JPRESS_URL . 'options/img/topbar_tabs/tab_2.png',
    'TabsLayout.tab3' => JPRESS_URL . 'options/img/topbar_tabs/tab_3.png',
    'TabsLayout.tab4' => JPRESS_URL . 'options/img/topbar_tabs/tab_4.png',
    'TabsLayout.tab5' => JPRESS_URL . 'options/img/topbar_tabs/tab_5.png',
    'TabsLayout.tab6' => JPRESS_URL . 'options/img/topbar_tabs/tab_6.png'
  ),
  'options' => array(
    'width' => '200px',
    'show_if' => array('tabsbar_categories_tab', '=', 'true'),
  ),
));

$tabs->open_mixed_field(array('name' => __('Background color', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
$tabs->add_field(array(
  'id' => 'styling-themeMode_light-tabbarbackgroundcolor',
  //'name' => __( 'Light Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#FFFFFF',
));

$tabs->add_field(array(
  'id' => 'styling-themeMode_dark-tabbarbackgroundcolor',
  'name' => __( 'Dark Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#333739',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$tabs->close_mixed_field();

$tabs->open_mixed_field(array('name' => __('InActive Tab Text color', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
$tabs->add_field(array(
  'id' => 'styling-themeMode_light-tabbartextcolor',
  //'name' => __( 'Light Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#7F7F7F',
));

$tabs->add_field(array(
  'id' => 'styling-themeMode_dark-tabbartextcolor',
  'name' => __( 'Dark Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#8A8A89',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$tabs->close_mixed_field();

$tabs->open_mixed_field(array('name' => __('Active Tab Text color', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
$tabs->add_field(array(
  'id' => 'styling-themeMode_light-tabbaractivetextcolor',
  //'name' => __( 'Light Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#333739',
));

$tabs->add_field(array(
  'id' => 'styling-themeMode_dark-tabbaractivetextcolor',
  'name' => __( 'Dark Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#FFFFFF',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$tabs->close_mixed_field();

$tabs->open_mixed_field(array('name' => __('Indicator color', 'textdomain' ),'desc' => __('The line under/outline/background the active tab', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
$tabs->add_field(array(
  'id' => 'styling-themeMode_light-tabbarindicatorcolor',
  //'name' => __( 'Light Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#0088FF',
));

$tabs->add_field(array(
  'id' => 'styling-themeMode_dark-tabbarindicatorcolor',
  'name' => __( 'Dark Mode', 'textdomain' ),
  'type' => 'colorpicker',
  'default' => '#0088FF',
  'options' => array(
    'show_if' => array('switch_theme_mode', '=', 'true'),
  ),
));
$tabs->close_mixed_field();

$homepage_tabs = $tabs->add_group( array(
  'name' => __('Tabs', 'textdomain'),
  'id' => 'tabsbaritems',
  'options' => array(
  'add_item_text' => __('New Tab', 'textdomain'),
    'show_if' => array('tabsbar_categories_tab', '=', 'true')
  ),
  'controls' => array(
    'name' =>  __('Tabs Item', 'textdomain').' #',
    'position' => 'top',
    'readonly_name' => true,
    'images' => false,
  ),
));
$homepage_tabs->add_field(array(
  'name' => __( 'Category', 'textdomain' ),
  'id' => 'categories',
  'type' => 'select',
  'items' => JPressItems::terms( 'category' ),
  'options' => array(
  'multiple' => true,
  'search' => true,
    'show_if' => array('local-tabs-tab_type', '=', 'category'),
  ),
));

$homepage_tabs->open_mixed_field(array('name' => __('Customized Title', 'textdomain' )));
$homepage_tabs->add_field(array(
  'name' => __( 'Enable', 'textdomain' ),
  'id' => 'customized-title',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$homepage_tabs->add_field(array(
  'name' => __( 'New Title', 'textdomain' ),
  'id' => 'title',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('customized-title', '=', 'true'),
  ),
));
$homepage_tabs->close_mixed_field();

$homepage_tabs->open_mixed_field(array('name' => __('Exclude Posts', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
$homepage_tabs->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'local-tabs-exclude_posts',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$homepage_tabs->add_field(array(
  'id' => 'tabs-exclude_posts',
  'name' => __( 'Posts ID/IDs', 'textdomain' ),
  'type' => 'text',
  'grid' => '5-of-6',
  'desc' => __( 'Enter a post ID, or IDs separated by comma', 'textdomain' ),
  'options' => array(
    'show_if' => array('local-tabs-exclude_posts', '=', 'true')
  )
));
$homepage_tabs->close_mixed_field();
$homepage_tabs->open_mixed_field(array('name' => __('Offset', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
$homepage_tabs->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'local-tabs-offset_posts',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$homepage_tabs->add_field(array(
  'id' => 'tabs-offset_posts',
  'name' => __( 'Offset Count', 'textdomain' ),
  'type' => 'number',
  'grid' => '5-of-6',
  'desc' => __( 'Number of posts to pass over', 'textdomain' ),
  'options' => array(
    'show_unit' => false,
    'show_if' => array('local-tabs-offset_posts', '=', 'true')
  )
));
$homepage_tabs->close_mixed_field();
$homepage_tabs->add_field(array(
  'name' => __( 'Number of posts to show', 'textdomain' ),
  'id' => 'tabs-count',
  'type' => 'select',
  'default' => '3',
  'items' => array(
    '1' => __( '1 Post', 'textdomain' ),
    '2' => __( '2 Posts', 'textdomain' ),
    '3' => __( '3 Posts', 'textdomain' ),
    '4' => __( '4 Posts', 'textdomain' ),
    '5' => __( '5 Posts', 'textdomain' ),
    '6' => __( '6 Posts', 'textdomain' ),
    '7' => __( '7 Posts', 'textdomain' ),
    '8' => __( '8 Posts', 'textdomain' ),
    '9' => __( '9 Posts', 'textdomain' ),
    '10' => __( '10 Posts', 'textdomain' ),
  ),
  'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))
));
$homepage_tabs->add_field(array(
  'name' => __( 'Sort Order', 'textdomain' ),
  'id' => 'tabs-sort',
  'type' => 'select',
  'default' => 'latest',
  'items' => array(
    'latest' => __( 'Recent Posts', 'textdomain' ),
    // 'rand' => __( 'Random Posts', 'textdomain' ),
    'modified' => __( 'Last Modified Posts', 'textdomain' ),
    'comment_count' => __( 'Most Commented posts', 'textdomain' ),
    'title' => __( 'Alphabetically', 'textdomain' ),
  ),
  'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))
));
$homepage_tabs->add_field( array(
  'id' => 'tabs-postlayout',
  'name' => __( 'Posts Layout', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.startThumbPost',
  'items' => array(
    'PostLayout.cardPost' => JPRESS_URL . 'options/img/blocks/cardPost.png',
    'PostLayout.endThumbPost' => JPRESS_URL . 'options/img/blocks/endThumbPost.png',
    'PostLayout.featuredMetaPost' => JPRESS_URL . 'options/img/blocks/featuredMetaPost.png',
    'PostLayout.featuredPost' => JPRESS_URL . 'options/img/blocks/featuredPost.png',
    'PostLayout.gridPost' => JPRESS_URL . 'options/img/blocks/gridPost.png',
    'PostLayout.imagePost' => JPRESS_URL . 'options/img/blocks/imagePost.png',
    'PostLayout.minimalPost' => JPRESS_URL . 'options/img/blocks/minimalPost.png',
    'PostLayout.relatedPost' => JPRESS_URL . 'options/img/blocks/relatedPost.png',
    'PostLayout.simplePost' => JPRESS_URL . 'options/img/blocks/simplePost.png',
    'PostLayout.startThumbPost' => JPRESS_URL . 'options/img/blocks/startThumbPost.png',
    'PostLayout.startThumbPostCompact' => JPRESS_URL . 'options/img/blocks/startThumbPostCompact.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('tabsbar_categories_tab', '=', 'true')
  ),
));
$homepage_tabs->add_field(array(
  'name' => __( 'Is first post "Featured"?', 'textdomain' ),
  'id' => 'local-tabs-firstfeatured',
  'type' => 'switcher',
  'default'	=>	'false',
  'desc' => __( 'Enable this to make the first post of this section with different post layout', 'textdomain' ),
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false',
    'show_if' => array('tabsbar_categories_tab', '=', 'true')
  ),
));
$homepage_tabs->add_field( array(
  'id' => 'tabs-firstfeatured',
  'name' => __( 'Featured Post Layout', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.featuredPost',
  'items' => array(
    'PostLayout.cardPost' => JPRESS_URL . 'options/img/blocks/cardPost.png',
    'PostLayout.featuredMetaPost' => JPRESS_URL . 'options/img/blocks/featuredMetaPost.png',
    'PostLayout.featuredPost' => JPRESS_URL . 'options/img/blocks/featuredPost.png',
    'PostLayout.imagePost' => JPRESS_URL . 'options/img/blocks/imagePost.png',
    'PostLayout.simplePost' => JPRESS_URL . 'options/img/blocks/simplePost.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('local-tabs-firstfeatured', '=', 'true')
  ),
));
$homepage_tabs->open_mixed_field(array('name' => __('Advanced Settings', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
$homepage_tabs->add_field(array(
  'name' => __( 'Catgeory', 'textdomain' ),
  'id' => 'tabs-options-category',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$homepage_tabs->add_field(array(
  'name' => __( 'Read Time', 'textdomain' ),
  'id' => 'tabs-options-readtime',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$homepage_tabs->add_field(array(
  'name' => __( 'Created Date', 'textdomain' ),
  'id' => 'tabs-options-date',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$homepage_tabs->add_field(array(
  'name' => __( 'Favorite', 'textdomain' ),
  'id' => 'tabs-options-save',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$homepage_tabs->add_field(array(
  'name' => __( 'Share', 'textdomain' ),
  'id' => 'tabs-options-share',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$homepage_tabs->close_mixed_field();

$homepage = $settings->add_section( array(
  'name' => __( 'Home Page Sections', 'textdomain' ),
  'id' => 'section-homepage-builder',
  'options' => array( 'toggle' => true )
));

$section = $homepage->add_group( array(
  'name' => __( 'Home Page Sections', 'textdomain' ),
  'id' => 'sections',
  'options' => array(
    'add_item_text' => __('New Section', 'textdomain'),
  ),
  'controls' => array(
    'name' =>  __('Section', 'textdomain').' #',
    'readonly_name' => false,
    'images' => true,
    'position' => 'left',
    'default_image' => JPRESS_URL . 'assets/img/transparent.png',
    'image_field_id' => 'postlayout',
    'height' => '190px',
  ),
));

$section->add_field( array(
  'id' => 'postlayout',
  'name' => __( 'Posts Layout', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.startThumbPost',
  'items' => array(
    'PostLayout.cardPost' => JPRESS_URL . 'options/img/blocks/cardPost.png',
    'PostLayout.endThumbPost' => JPRESS_URL . 'options/img/blocks/endThumbPost.png',
    'PostLayout.featuredMetaPost' => JPRESS_URL . 'options/img/blocks/featuredMetaPost.png',
    'PostLayout.featuredPost' => JPRESS_URL . 'options/img/blocks/featuredPost.png',
    'PostLayout.gridPost' => JPRESS_URL . 'options/img/blocks/gridPost.png',
    'PostLayout.imagePost' => JPRESS_URL . 'options/img/blocks/imagePost.png',
    'PostLayout.minimalPost' => JPRESS_URL . 'options/img/blocks/minimalPost.png',
    'PostLayout.relatedPost' => JPRESS_URL . 'options/img/blocks/relatedPost.png',
    'PostLayout.simplePost' => JPRESS_URL . 'options/img/blocks/simplePost.png',
    'PostLayout.startThumbPost' => JPRESS_URL . 'options/img/blocks/startThumbPost.png',
    'PostLayout.startThumbPostCompact' => JPRESS_URL . 'options/img/blocks/startThumbPostCompact.png',
    'PostLayout.adMob' => JPRESS_URL . 'options/img/blocks/ad.png',
    'PostLayout.htmlAd' => JPRESS_URL . 'options/img/blocks/adHtml.png',
    'PostLayout.imageAd' => JPRESS_URL . 'options/img/blocks/aimg.png',
  ),
  'options' => array(
    'width' => '155px',
  ),
));

$section->add_field(array(
  'name' => __( 'AdMob Banner Size', 'textdomain' ),
  'id' => 'admob_banner_size',
  'type' => 'select',
  'default' => 'banner',
  'items' => array(
    'banner' => __( 'Banner', 'textdomain' ),
    'leaderboard' => __( 'Leaderboard', 'textdomain' ),
    'smart_banner' => __( 'Smart Banner', 'textdomain' ),
    'Medium_banner' => __( 'Medium Banner', 'textdomain' ),
    'large_banner' => __( 'Large Banner', 'textdomain' ),
    'full_banner' => __( 'Full Banner', 'textdomain' ),
  ),
  'options' => array(
    'show_if' => array('postlayout', '=', 'PostLayout.adMob'),
  ),
));
$section->add_field(array(
  'name' => __( 'Ad HTML Code', 'textdomain' ),
  'id' => 'ad_section_html',
  'type' => 'textarea',
  'desc' => __( 'Add your ad spcial HTML markup', 'textdomain' ),
  'grid' => '5-of-6',
  'default' => '<p>HTML Content goes here.</p>',
  'options' => array(
    'desc_tooltip' => true,
    'show_if' => array('postlayout', '=', 'PostLayout.htmlAd'),
  )
));

$section->open_mixed_field(array(
  'name' =>  __('Image Ad Options', 'textdomain' ),
  'options' => array(
    'show_if' => array('postlayout', '=', 'PostLayout.imageAd'),
  ),
));
$section->add_field(array(
  'name' => __( 'Link Type', 'textdomain' ),
  'id' => 'ad_image_link_type',
  'type' => 'radio',
  'default' => 'NavigationType.url',
  'items' => array(
    'NavigationType.url' => __( 'Full URL', 'textdomain' ),
    'NavigationType.main' => __( 'Main Page', 'textdomain' ),
    'NavigationType.category' => __( 'Category', 'textdomain' ),
    'NavigationType.page' => __( 'Page', 'textdomain' ),
  ),
));
$section->add_field(array(
  'name' => __( 'Link URL', 'textdomain' ),
  'id' => 'ad_image_link_url',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('ad_image_link_type', '=', 'NavigationType.url'),
  ),
));
$section->add_field(array(
  'name' => __( 'Main Pages', 'textdomain' ),
  'id' => 'ad_image_link_main',
  'type' => 'select',
  'default' => 'MainPage.home',
  'attributes' => array( 'required' => true ),
  'items' => array(
    'MainPage.home' => __( 'Home', 'textdomain' ),
    'MainPage.sections' => __( 'Sections', 'textdomain' ),
    'MainPage.favourites' => __( 'Favorites', 'textdomain' ),
    'MainPage.settings' => __( 'Settings', 'textdomain' ),
    'MainPage.contactUs' => __( 'Contact us', 'textdomain' ),
  ),
  'options' => array(
    'show_if' => array('ad_image_link_type', '=', 'NavigationType.main'),
  ),
));
$section->add_field(array(
  'name' => __( 'Categories', 'textdomain' ),
  'id' => 'ad_image_link_category',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => JPressItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('ad_image_link_type', '=', 'NavigationType.category'),
  ),
));
$section->add_field(array(
  'name' => __( 'Pages', 'textdomain' ),
  'id' => 'ad_image_link_page',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => JPressItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('ad_image_link_type', '=', 'NavigationType.page'),
  ),
));
$section->add_field(array(
  'name' => __( 'Ad Image', 'textdomain' ),
  'id' => 'ad_image_file',
  'type' => 'file',
));
$section->close_mixed_field();


$section->open_mixed_field(array(
  'name' =>  __('Section Title', 'textdomain' ),
  'options' => array(
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
));
$section->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'local-section_title',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false',
  )
));
$section->add_field(array(
  'id' => 'title',
  'name' => __( 'Title', 'textdomain' ),
  'type' => 'text',
  'grid' => '5-of-6',
  'desc' => __( 'If you don\'t need this section to have title, then switch it off', 'textdomain' ),
  'options' => array(
    'show_if' => array('local-section_title', '=', 'true')
  )
));
$section->close_mixed_field();

$section->add_field(array(
  'name' => __( "Show 'See All' Button", 'textdomain' ),
  'id' => 'local-enable_see_all',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false',
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
));
$section->add_field(array(
  'name' => __( "Show 'Load more' Button", 'textdomain' ),
  'id' => 'local-enable_load_more',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false',
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  )
));

$section->open_mixed_field(array(
  'name' =>  __('Show posts by', 'textdomain' ),
  'options' => array(
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
));
$section->add_field(array(
  'name' => __( 'Taxonomy type', 'textdomain' ),
  'id' => 'showposts',
  'type' => 'radio',
  'default' => 'categories',
  'items' => array(
    'categories' => __( 'Categories', 'textdomain' ),
    'tags' => __( 'Tags', 'textdomain' ),
  ),
  'options' => array(
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
));
$section->add_field( array(
  'id' => 'categories',
  'name' => __( 'Categories', 'textdomain' ),
  'type' => 'checkbox',
  // 'default' => '$all$',
  'items' => JPressItems::terms( 'category' ),
  'desc' => __( 'Select all categories you need to show thier posts in that section', 'textdomain' ),
  'options' => array(
    'show_if' => array('showposts', '=', 'categories')
  )
));
$section->add_field(array(
  'id' => 'tags',
  'name' => __( 'Tags', 'textdomain' ),
  'type' => 'checkbox',
  // 'default' => '$all$',
  'items' => JPressItems::terms( 'post_tag' ),
  'desc' => __( 'Select all tags you need to show thier posts in that section', 'textdomain' ),
  'options' => array(
    'show_if' => array('showposts', '=', 'tags')
  )
));
$section->close_mixed_field();

$section->close_mixed_field();
$section->open_mixed_field(array(
  'name' => __('Exclude Posts', 'textdomain' ),
  'options' => array(
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
));
$section->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'local-enable_exclude_posts',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false',
  )
));
$section->add_field(array(
  'id' => 'local-exclude_posts',
  'name' => __( 'Posts ID/IDs', 'textdomain' ),
  'type' => 'text',
  'grid' => '5-of-6',
  'desc' => __( 'Enter a post ID, or IDs separated by comma', 'textdomain' ),
  'options' => array(
    'show_if' => array('local-enable_exclude_posts', '=', 'true')
  )
));
$section->close_mixed_field();
$section->open_mixed_field(array(
  'name' => __('Offset', 'textdomain' ),
  'options' => array(
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
));
$section->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'local-enable_offset_posts',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false',
  ),
));
$section->add_field(array(
  'id' => 'local-offset_posts',
  'name' => __( 'Count', 'textdomain' ),
  'type' => 'number',
  'grid' => '5-of-6',
  'desc' => __( 'Number of posts to pass over', 'textdomain' ),
  'options' => array(
    'show_unit' => false,
    'show_if' => array('local-enable_offset_posts', '=', 'true')
  )
));
$section->close_mixed_field();
$section->add_field(array(
  'name' => __( 'Sort Order', 'textdomain' ),
  'id' => 'local-sort',
  'type' => 'select',
  'default' => 'latest',
  'items' => array(
    'latest' => __( 'Recent Posts', 'textdomain' ),
    // 'rand' => __( 'Random Posts', 'textdomain' ),
    'modified' => __( 'Last Modified Posts', 'textdomain' ),
    'comment_count' => __( 'Most Commented posts', 'textdomain' ),
    'title' => __( 'Alphabetically', 'textdomain' ),
  ),
  'options' => array(
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
));
$section->add_field(array(
  'name' => __( 'Number of posts to show', 'textdomain' ),
  'id' => 'local-count',
  'type' => 'select',
  'default' => '3',
  'items' => array(
    '1' => __( '1 Post', 'textdomain' ),
    '2' => __( '2 Posts', 'textdomain' ),
    '3' => __( '3 Posts', 'textdomain' ),
    '4' => __( '4 Posts', 'textdomain' ),
    '5' => __( '5 Posts', 'textdomain' ),
    '6' => __( '6 Posts', 'textdomain' ),
    '7' => __( '7 Posts', 'textdomain' ),
    '8' => __( '8 Posts', 'textdomain' ),
    '9' => __( '9 Posts', 'textdomain' ),
    '10' => __( '10 Posts', 'textdomain' ),
  ),
  'options' => array(
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
));

$section->open_mixed_field(array(
  'name' =>  __('Featured post', 'textdomain' ),
  'options' => array(
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
));
$section->add_field(array(
  'name' => __( 'Is first post "Featured"?', 'textdomain' ),
  'id' => 'local-firstfeatured',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false',
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
  'desc' => __( 'Enable this to make the first post of this section with different post layout', 'textdomain' ),
));
$section->add_field(array(
  'name' => __( 'First Post Layout', 'textdomain' ),
  'id' => 'firstFeatured',
  'type' => 'image_selector',
  'default' => 'PostLayout.featuredPost',
  'items' => array(
    'PostLayout.cardPost' => JPRESS_URL . 'options/img/blocks/cardPost.png',
    'PostLayout.featuredMetaPost' => JPRESS_URL . 'options/img/blocks/featuredMetaPost.png',
    'PostLayout.featuredPost' => JPRESS_URL . 'options/img/blocks/featuredPost.png',
    'PostLayout.imagePost' => JPRESS_URL . 'options/img/blocks/imagePost.png',
    'PostLayout.simplePost' => JPRESS_URL . 'options/img/blocks/simplePost.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('local-firstfeatured', '=', 'true')
  )
));
$section->close_mixed_field();

$section->add_field(array(
  'name' => __( 'Add separator after the block?', 'textdomain' ),
  'id' => 'separator',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false',
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  )
));
$section->open_mixed_field(array(
  'name' => __('Advanced Settings', 'textdomain' ),
  'options' => array(
    'show_if' => array('postlayout', 'not in', ['PostLayout.adMob', 'PostLayout.htmlAd', 'PostLayout.imageAd']),
  ),
));
$section->add_field(array(
  'name' => __( 'Catgeory', 'textdomain' ),
  'id' => 'options-category',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$section->add_field(array(
  'name' => __( 'Read Time', 'textdomain' ),
  'id' => 'options-readtime',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$section->add_field(array(
  'name' => __( 'Created Date', 'textdomain' ),
  'id' => 'options-date',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$section->add_field(array(
  'name' => __( 'Favorite', 'textdomain' ),
  'id' => 'options-save',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$section->add_field(array(
  'name' => __( 'Share', 'textdomain' ),
  'id' => 'options-share',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$section->close_mixed_field();

$settings->close_tab_item('homepage');
