<?php

/**
 * Archives
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Archives Page
$settings->open_tab_item('archives');

$archives_single = $settings->add_section( array(
  'name' => __( 'Single Post Settings', 'textdomain' ),
  'id' => 'section-archives-single',
  'options' => array( 'toggle' => true )
));

$archives_single->open_mixed_field(array('name' => __('Advanced Settings', 'textdomain' )));
$archives_single->add_field(array(
  'name' => __( 'Author', 'textdomain' ),
  'id' => 'archives-single-options-author',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_single->add_field(array(
  'name' => __( 'Catgeory', 'textdomain' ),
  'id' => 'archives-single-options-category',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_single->add_field(array(
  'name' => __( 'tags', 'textdomain' ),
  'id' => 'archives-single-options-tags',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_single->add_field(array(
  'name' => __( 'Read Time', 'textdomain' ),
  'id' => 'archives-single-options-readtime',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_single->add_field(array(
  'name' => __( 'Created Date', 'textdomain' ),
  'id' => 'archives-single-options-date',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_single->add_field(array(
  'name' => __( 'Favorite', 'textdomain' ),
  'id' => 'archives-single-options-save',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_single->add_field(array(
  'name' => __( 'Share', 'textdomain' ),
  'id' => 'archives-single-options-share',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_single->add_field(array(
  'name' => __( 'Text to Speech', 'textdomain' ),
  'id' => 'archives-single-options-tts',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_single->close_mixed_field();

$archives_categories = $settings->add_section( array(
  'name' => __( 'Categories List Page Settings', 'textdomain' ),
  'id' => 'section-archives-categories',
  'options' => array( 'toggle' => true )
));
$archives_categories->add_field( array(
  'id' => 'archives-categories-postlayout',
  'name' => __( 'Categories Page Layout', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'CategoriesLayout.cat1',
  'items' => array(
    'CategoriesLayout.cat1' => APPBEAR_URL . 'options/img/categories/cat_1.png',
    'CategoriesLayout.cat2' => APPBEAR_URL . 'options/img/categories/cat_2.png',
    'CategoriesLayout.cat3' => APPBEAR_URL . 'options/img/categories/cat_3.png',
    'CategoriesLayout.cat4' => APPBEAR_URL . 'options/img/categories/cat_4.png',
    'CategoriesLayout.cat5' => APPBEAR_URL . 'options/img/categories/cat_5.png',
  ),
  'options' => array(
    'width' => '155px',
  ),
));

$archives_category = $settings->add_section( array(
  'name' => __( 'Single Category Page Settings', 'textdomain' ),
  'id' => 'section-archives-category',
  'options' => array( 'toggle' => true )
));
$archives_category->add_field( array(
  'id' => 'archives-category-postlayout',
  'name' => __( 'Single Category Posts Layout', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.startThumbPost',
  'items' => array(
    'PostLayout.cardPost' => APPBEAR_URL . 'options/img/blocks/cardPost.png',
    'PostLayout.endThumbPost' => APPBEAR_URL . 'options/img/blocks/endThumbPost.png',
    'PostLayout.featuredMetaPost' => APPBEAR_URL . 'options/img/blocks/featuredMetaPost.png',
    'PostLayout.featuredPost' => APPBEAR_URL . 'options/img/blocks/featuredPost.png',
    'PostLayout.gridPost' => APPBEAR_URL . 'options/img/blocks/gridPost.png',
    'PostLayout.imagePost' => APPBEAR_URL . 'options/img/blocks/imagePost.png',
    'PostLayout.minimalPost' => APPBEAR_URL . 'options/img/blocks/minimalPost.png',
    'PostLayout.simplePost' => APPBEAR_URL . 'options/img/blocks/simplePost.png',
    'PostLayout.startThumbPost' => APPBEAR_URL . 'options/img/blocks/startThumbPost.png',
    'PostLayout.startThumbPostCompact' => APPBEAR_URL . 'options/img/blocks/startThumbPostCompact.png',
  ),
  'options' => array(
  'width' => '155px',
  ),
));
$archives_category->add_field(array(
  'name' => __( 'Sort Order', 'textdomain' ),
  'id' => 'local-archives-category-sort',
  'type' => 'select',
  'default' => 'latest',
  'items' => array(
    'latest' => __( 'Recent Posts', 'textdomain' ),
    // 'rand' => __( 'Random Posts', 'textdomain' ),
    'modified' => __( 'Last Modified Posts', 'textdomain' ),
    'comment_count' => __( 'Most Commented posts', 'textdomain' ),
    'title' => __( 'Alphabetically', 'textdomain' ),
  )
));
$archives_category->add_field(array(
  'name' => __( 'Number of posts to show', 'textdomain' ),
  'id' => 'local-archives-category-count',
  'type' => 'select',
  'default' => '10',
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
  )
));
$archives_category->open_mixed_field(array('name' => __('Advanced Settings', 'textdomain' )));
$archives_category->add_field(array(
  'name' => __( 'Read Time', 'textdomain' ),
  'id' => 'archives-category-options-readtime',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_category->add_field(array(
  'name' => __( 'Created Date', 'textdomain' ),
  'id' => 'archives-category-options-date',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_category->add_field(array(
  'name' => __( 'Favorite', 'textdomain' ),
  'id' => 'archives-category-options-save',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_category->add_field(array(
  'name' => __( 'Share', 'textdomain' ),
  'id' => 'archives-category-options-share',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_category->close_mixed_field();

$archives_search = $settings->add_section( array(
  'name' => __( 'Search Page Settings', 'textdomain' ),
  'id' => 'section-archives-search',
  'options' => array( 'toggle' => true )
));
$archives_search->add_field( array(
  'id' => 'archives-search-postlayout',
  'name' => __( 'Search Page Posts Layout', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.startThumbPost',
  'items' => array(
  'PostLayout.cardPost' => APPBEAR_URL . 'options/img/blocks/cardPost.png',
  'PostLayout.endThumbPost' => APPBEAR_URL . 'options/img/blocks/endThumbPost.png',
  'PostLayout.featuredMetaPost' => APPBEAR_URL . 'options/img/blocks/featuredMetaPost.png',
  'PostLayout.featuredPost' => APPBEAR_URL . 'options/img/blocks/featuredPost.png',
  'PostLayout.gridPost' => APPBEAR_URL . 'options/img/blocks/gridPost.png',
  'PostLayout.imagePost' => APPBEAR_URL . 'options/img/blocks/imagePost.png',
  'PostLayout.minimalPost' => APPBEAR_URL . 'options/img/blocks/minimalPost.png',
  'PostLayout.simplePost' => APPBEAR_URL . 'options/img/blocks/simplePost.png',
  'PostLayout.startThumbPost' => APPBEAR_URL . 'options/img/blocks/startThumbPost.png',
  'PostLayout.startThumbPostCompact' => APPBEAR_URL . 'options/img/blocks/startThumbPostCompact.png',
  ),
  'options' => array(
  'width' => '155px',
  ),
));
$archives_search->add_field(array(
  'name' => __( 'Sort Order', 'textdomain' ),
  'id' => 'local-archives-search-sort',
  'type' => 'select',
  'default' => 'latest',
  'items' => array(
    'latest' => __( 'Recent Posts', 'textdomain' ),
    // 'rand' => __( 'Random Posts', 'textdomain' ),
    'modified' => __( 'Last Modified Posts', 'textdomain' ),
    'comment_count' => __( 'Most Commented posts', 'textdomain' ),
    'title' => __( 'Alphabetically', 'textdomain' ),
  )
));
$archives_search->add_field(array(
  'name' => __( 'Number of posts to show', 'textdomain' ),
  'id' => 'local-archives-search-count',
  'type' => 'select',
  'default' => '10',
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
  )
));
$archives_search->open_mixed_field(array('name' => __('Advanced Settings', 'textdomain' )));
$archives_search->add_field(array(
  'name' => __( 'Catgeory', 'textdomain' ),
  'id' => 'archives-search-options-category',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_search->add_field(array(
  'name' => __( 'Read Time', 'textdomain' ),
  'id' => 'archives-search-options-readtime',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_search->add_field(array(
  'name' => __( 'Created Date', 'textdomain' ),
  'id' => 'archives-search-options-date',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_search->add_field(array(
  'name' => __( 'Favorite', 'textdomain' ),
  'id' => 'archives-search-options-save',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_search->add_field(array(
  'name' => __( 'Share', 'textdomain' ),
  'id' => 'archives-search-options-share',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_search->close_mixed_field();

$archives_favorites = $settings->add_section( array(
  'name' => __( 'Favorites Page Settings', 'textdomain' ),
  'id' => 'section-archives-favorites',
  'options' => array( 'toggle' => true )
));
$archives_favorites->add_field( array(
  'id' => 'archives-favorites-postlayout',
  'name' => __( 'Favorites Page Posts Layout', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.startThumbPost',
  'items' => array(
  'PostLayout.cardPost' => APPBEAR_URL . 'options/img/blocks/cardPost.png',
  'PostLayout.endThumbPost' => APPBEAR_URL . 'options/img/blocks/endThumbPost.png',
  'PostLayout.featuredMetaPost' => APPBEAR_URL . 'options/img/blocks/featuredMetaPost.png',
  'PostLayout.featuredPost' => APPBEAR_URL . 'options/img/blocks/featuredPost.png',
  'PostLayout.gridPost' => APPBEAR_URL . 'options/img/blocks/gridPost.png',
  'PostLayout.imagePost' => APPBEAR_URL . 'options/img/blocks/imagePost.png',
  'PostLayout.minimalPost' => APPBEAR_URL . 'options/img/blocks/minimalPost.png',
  'PostLayout.simplePost' => APPBEAR_URL . 'options/img/blocks/simplePost.png',
  'PostLayout.startThumbPost' => APPBEAR_URL . 'options/img/blocks/startThumbPost.png',
  'PostLayout.startThumbPostCompact' => APPBEAR_URL . 'options/img/blocks/startThumbPostCompact.png',
  ),
  'options' => array(
  'width' => '155px',
  ),
));
$archives_favorites->add_field(array(
  'name' => __( 'Sort Order', 'textdomain' ),
  'id' => 'local-archives-favorites-sort',
  'type' => 'select',
  'default' => 'latest',
  'items' => array(
    'latest' => __( 'Recent Posts', 'textdomain' ),
    // 'rand' => __( 'Random Posts', 'textdomain' ),
    'modified' => __( 'Last Modified Posts', 'textdomain' ),
    'comment_count' => __( 'Most Commented posts', 'textdomain' ),
    'title' => __( 'Alphabetically', 'textdomain' ),
  )
));
$archives_favorites->add_field(array(
  'name' => __( 'Number of posts to show', 'textdomain' ),
  'id' => 'local-archives-favorites-count',
  'type' => 'select',
  'default' => '10',
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
  )
));
$archives_favorites->open_mixed_field(array('name' => __('Advanced Settings', 'textdomain' )));
$archives_favorites->add_field(array(
  'name' => __( 'Catgeory', 'textdomain' ),
  'id' => 'archives-favorites-options-category',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_favorites->add_field(array(
  'name' => __( 'Read Time', 'textdomain' ),
  'id' => 'archives-favorites-options-readtime',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_favorites->add_field(array(
  'name' => __( 'Created Date', 'textdomain' ),
  'id' => 'archives-favorites-options-date',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_favorites->add_field(array(
  'name' => __( 'Favorite', 'textdomain' ),
  'id' => 'archives-favorites-options-save',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_favorites->add_field(array(
  'name' => __( 'Share', 'textdomain' ),
  'id' => 'archives-favorites-options-share',
  'type' => 'switcher',
  'default'	=>	'true',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));
$archives_favorites->close_mixed_field();

$settings->close_tab_item('archives');
