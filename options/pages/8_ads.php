<?php

/**
 * Advertisement / Admob Tab
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Advertisement / Admob Page
$settings->open_tab_item('advertisement');

$admob = $settings->add_section( array(
  'name' => __( 'Advertisements Settings', 'jpress' ),
  'id' => 'section-advertisement-admob',
  'options' => array( 'toggle' => true )
));

$admob->open_mixed_field(array('name' => __('Admob Banner', 'jpress' )));
$admob->add_field(array(
  'name' => __( 'Enabled', 'jpress' ),
  'id' => 'local-admob_banner',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$admob->add_field(array(
  'name' => __( 'Android ID', 'jpress' ),
  'id' => 'advertisement_android_banner_id_text',
  'type' => 'text',
  'options'	=>	array(
    'show_if' => array('local-admob_banner', '=', 'true')
  ),
));

$admob->add_field(array(
  'name' => __( 'iOS ID', 'jpress' ),
  'id' => 'advertisement_ios_banner_id_text',
  'type' => 'text',
  'options'	=>	array(
    'show_if' => array('local-admob_banner', '=', 'true')
  ),
));
$admob->close_mixed_field();

$admob->open_mixed_field(array('name' => __('Admob Interstitial', 'jpress' )));
$admob->add_field(array(
  'name' => __( 'Enable', 'jpress' ),
  'id' => 'local-advertisement_admob_interstatial',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$admob->add_field(array(
  'name' => __( 'Android ID', 'jpress' ),
  'id' => 'advertisement_android_interstatial_id_text',
  'type' => 'text',
  'options'	=>	array(
    'show_if' => array('local-advertisement_admob_interstatial', '=', 'true')
  ),
));

$admob->add_field(array(
  'name' => __( 'iOS ID', 'jpress' ),
  'id' => 'advertisement_ios_interstatial_id_text',
  'type' => 'text',
  'options'	=>	array(
    'show_if' => array('local-advertisement_admob_interstatial', '=', 'true')
  ),
));
$admob->close_mixed_field();

$admob = $settings->add_section( array(
  'name' => __( 'Single Post Ads Settings', 'jpress' ),
  'id' => 'ads-section-archives-single',
  'options' => array( 'toggle' => true )
));

$admob->open_mixed_field(array(
  'name' => __('Interstitial Ad', 'jpress' ),
  'options' => array( 'show_if' => array('local-advertisement_admob_interstatial', '=', 'true') ),
));
$admob->add_field(array(
  'name' => __( 'Enable Interstitial Before Post View', 'jpress' ),
  'id' => 'local_ads_interstatial_before_post',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));
$admob->add_field(array(
  'name' => __( 'Show Interstitial Ad Every', 'jpress' ),
  'id' => 'local_ads_interstatial_before_post_offset',
  'type' => 'number',
  'default' => '1',
  'options' => array(
    'unit' => 'Post Views',
    'show_if' => array('local_ads_interstatial_before_post', '=', 'true'),
  ),
  'attributes' => array(
    'min' => 1,
    'max' => 99,
  ),
));
$admob->close_mixed_field();

$admob->add_field(array(
  'name' => __( 'Enable Ads Inside Post Content', 'jpress' ),
  'id' => 'local_ads_in_post',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$admob->add_field(array(
  'name' => __( 'Show In-Post Ad After', 'jpress' ),
  'id' => 'local_ads_in_post_paragraph_offset',
  'type' => 'select',
  'default' => '2',
  'items' => array(
    '1' => __( '1 Paragraph', 'jpress' ),
    '2' => __( '2 Paragraphs', 'jpress' ),
    '3' => __( '3 Paragraphs', 'jpress' ),
    '4' => __( '4 Paragraphs', 'jpress' ),
    '5' => __( '5 Paragraphs', 'jpress' ),
    '6' => __( '6 Paragraphs', 'jpress' ),
  ),
  'options' => array('show_if' => array('local_ads_in_post', '=', 'true'))
));

$admob->add_field( array(
  'id' => 'local_ads_in_post_type',
  'name' => __( 'In-Post Ad Type', 'jpress' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.adMob',
  'items' => array(
    'PostLayout.adMob' => JPRESS_URL . 'options/img/blocks/ad.png',
    'PostLayout.htmlAd' => JPRESS_URL . 'options/img/blocks/adHtml.png',
    'PostLayout.imageAd' => JPRESS_URL . 'options/img/blocks/aimg.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('local_ads_in_post', '=', 'true'),
    'show_items_if' => array(
      'PostLayout.adMob' => array('local-admob_banner', '=', 'true'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'In-Post AdMob Banner Size', 'jpress' ),
  'id' => 'in_post_admob_banner_size',
  'type' => 'select',
  'default' => 'banner',
  'items' => array(
    'banner' => __( 'Banner', 'jpress' ),
    'leaderboard' => __( 'Leaderboard', 'jpress' ),
    'smart_banner' => __( 'Smart Banner', 'jpress' ),
    'Medium_banner' => __( 'Medium Banner', 'jpress' ),
    'large_banner' => __( 'Large Banner', 'jpress' ),
    'full_banner' => __( 'Full Banner', 'jpress' ),
  ),
  'options' => array(
    'show_if' => array(
      array('local-admob_banner', '=', 'true'),
      array('local_ads_in_post', '=', 'true'),
      array('local_ads_in_post_type', '=', 'PostLayout.adMob'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'In-Post Ad HTML Code', 'jpress' ),
  'id' => 'in_post_ad_section_html',
  'type' => 'textarea',
  'desc' => __( 'Add your ad spcial HTML markup', 'jpress' ),
  'grid' => '5-of-6',
  'default' => '<p>HTML Content goes here.</p>',
  'options' => array(
    'desc_tooltip' => true,
    'show_if' => array(
      array('local_ads_in_post', '=', 'true'),
      array('local_ads_in_post_type', '=', 'PostLayout.htmlAd'),
    ),
  ),
));

$admob->open_mixed_field(array(
  'name' =>  __('In-Post Image Ad Options', 'jpress' ),
  'options' => array(
    'show_if' => array(
      array('local_ads_in_post', '=', 'true'),
      array('local_ads_in_post_type', '=', 'PostLayout.imageAd'),
    ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link Type', 'jpress' ),
  'id' => 'in_post_ad_image_link_type',
  'type' => 'radio',
  'default' => 'NavigationType.url',
  'items' => array(
    'NavigationType.url' => __( 'Full URL', 'jpress' ),
    'NavigationType.main' => __( 'Main Page', 'jpress' ),
    'NavigationType.category' => __( 'Category', 'jpress' ),
    'NavigationType.page' => __( 'Page', 'jpress' ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link URL', 'jpress' ),
  'id' => 'in_post_ad_image_link_url',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('in_post_ad_image_link_type', '=', 'NavigationType.url'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Main Pages', 'jpress' ),
  'id' => 'in_post_ad_image_link_main',
  'type' => 'select',
  'default' => 'MainPage.home',
  'attributes' => array( 'required' => true ),
  'items' => array(
    'MainPage.home' => __( 'Home', 'jpress' ),
    'MainPage.sections' => __( 'Sections', 'jpress' ),
    'MainPage.favourites' => __( 'Favorites', 'jpress' ),
    'MainPage.settings' => __( 'Settings', 'jpress' ),
    'MainPage.contactUs' => __( 'Contact us', 'jpress' ),
  ),
  'options' => array(
    'show_if' => array('in_post_ad_image_link_type', '=', 'NavigationType.main'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Categories', 'jpress' ),
  'id' => 'in_post_ad_image_link_category',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => JPressItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('in_post_ad_image_link_type', '=', 'NavigationType.category'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Pages', 'jpress' ),
  'id' => 'in_post_ad_image_link_page',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => JPressItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('in_post_ad_image_link_type', '=', 'NavigationType.page'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Ad Image', 'jpress' ),
  'id' => 'in_post_ad_image_file',
  'type' => 'file',
));
$admob->close_mixed_field();

$admob->add_field(array(
  'name' => __( 'Enable Ads After Post', 'jpress' ),
  'id' => 'local_ads_after_post',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$admob->add_field( array(
  'id' => 'local_ads_after_post_type',
  'name' => __( 'After Post Ad Type', 'jpress' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.adMob',
  'items' => array(
    'PostLayout.adMob' => JPRESS_URL . 'options/img/blocks/ad.png',
    'PostLayout.htmlAd' => JPRESS_URL . 'options/img/blocks/adHtml.png',
    'PostLayout.imageAd' => JPRESS_URL . 'options/img/blocks/aimg.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('local_ads_after_post', '=', 'true'),
    'show_items_if' => array(
      'PostLayout.adMob' => array('local-admob_banner', '=', 'true'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'After Post AdMob Banner Size', 'jpress' ),
  'id' => 'after_post_admob_banner_size',
  'type' => 'select',
  'default' => 'banner',
  'items' => array(
    'banner' => __( 'Banner', 'jpress' ),
    'leaderboard' => __( 'Leaderboard', 'jpress' ),
    'smart_banner' => __( 'Smart Banner', 'jpress' ),
    'Medium_banner' => __( 'Medium Banner', 'jpress' ),
    'large_banner' => __( 'Large Banner', 'jpress' ),
    'full_banner' => __( 'Full Banner', 'jpress' ),
  ),
  'options' => array(
    'show_if' => array(
      array('local-admob_banner', '=', 'true'),
      array('local_ads_after_post', '=', 'true'),
      array('local_ads_after_post_type', '=', 'PostLayout.adMob'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'After Post Ad HTML Code', 'jpress' ),
  'id' => 'after_post_ad_section_html',
  'type' => 'textarea',
  'desc' => __( 'Add your ad spcial HTML markup', 'jpress' ),
  'grid' => '5-of-6',
  'default' => '<p>HTML Content goes here.</p>',
  'options' => array(
    'desc_tooltip' => true,
    'show_if' => array(
      array('local_ads_after_post', '=', 'true'),
      array('local_ads_after_post_type', '=', 'PostLayout.htmlAd'),
    ),
  ),
));

$admob->open_mixed_field(array(
  'name' =>  __('After Post Image Ad Options', 'jpress' ),
  'options' => array(
    'show_if' => array(
      array('local_ads_after_post', '=', 'true'),
      array('local_ads_after_post_type', '=', 'PostLayout.imageAd'),
    ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link Type', 'jpress' ),
  'id' => 'after_post_ad_image_link_type',
  'type' => 'radio',
  'default' => 'NavigationType.url',
  'items' => array(
    'NavigationType.url' => __( 'Full URL', 'jpress' ),
    'NavigationType.main' => __( 'Main Page', 'jpress' ),
    'NavigationType.category' => __( 'Category', 'jpress' ),
    'NavigationType.page' => __( 'Page', 'jpress' ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link URL', 'jpress' ),
  'id' => 'after_post_ad_image_link_url',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('after_post_ad_image_link_type', '=', 'NavigationType.url'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Main Pages', 'jpress' ),
  'id' => 'after_post_ad_image_link_main',
  'type' => 'select',
  'default' => 'MainPage.home',
  'attributes' => array( 'required' => true ),
  'items' => array(
    'MainPage.home' => __( 'Home', 'jpress' ),
    'MainPage.sections' => __( 'Sections', 'jpress' ),
    'MainPage.favourites' => __( 'Favorites', 'jpress' ),
    'MainPage.settings' => __( 'Settings', 'jpress' ),
    'MainPage.contactUs' => __( 'Contact us', 'jpress' ),
  ),
  'options' => array(
    'show_if' => array('after_post_ad_image_link_type', '=', 'NavigationType.main'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Categories', 'jpress' ),
  'id' => 'after_post_ad_image_link_category',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => JPressItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('after_post_ad_image_link_type', '=', 'NavigationType.category'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Pages', 'jpress' ),
  'id' => 'after_post_ad_image_link_page',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => JPressItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('after_post_ad_image_link_type', '=', 'NavigationType.page'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Ad Image', 'jpress' ),
  'id' => 'after_post_ad_image_file',
  'type' => 'file',
));
$admob->close_mixed_field();

$admob->add_field(array(
  'name' => __( 'Enable Ads Before Comments', 'jpress' ),
  'id' => 'local_ads_before_comments',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$admob->add_field( array(
  'id' => 'local_ads_before_comments_type',
  'name' => __( 'Before Comments Ad Type', 'jpress' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.adMob',
  'items' => array(
    'PostLayout.adMob' => JPRESS_URL . 'options/img/blocks/ad.png',
    'PostLayout.htmlAd' => JPRESS_URL . 'options/img/blocks/adHtml.png',
    'PostLayout.imageAd' => JPRESS_URL . 'options/img/blocks/aimg.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('local_ads_before_comments', '=', 'true'),
    'show_items_if' => array(
      'PostLayout.adMob' => array('local-admob_banner', '=', 'true'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'Before Comments AdMob Banner Size', 'jpress' ),
  'id' => 'before_comments_admob_banner_size',
  'type' => 'select',
  'default' => 'banner',
  'items' => array(
    'banner' => __( 'Banner', 'jpress' ),
    'leaderboard' => __( 'Leaderboard', 'jpress' ),
    'smart_banner' => __( 'Smart Banner', 'jpress' ),
    'Medium_banner' => __( 'Medium Banner', 'jpress' ),
    'large_banner' => __( 'Large Banner', 'jpress' ),
    'full_banner' => __( 'Full Banner', 'jpress' ),
  ),
  'options' => array(
    'show_if' => array(
      array('local-admob_banner', '=', 'true'),
      array('local_ads_before_comments', '=', 'true'),
      array('local_ads_before_comments_type', '=', 'PostLayout.adMob'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'Before Comments Ad HTML Code', 'jpress' ),
  'id' => 'before_comments_ad_section_html',
  'type' => 'textarea',
  'desc' => __( 'Add your ad spcial HTML markup', 'jpress' ),
  'grid' => '5-of-6',
  'default' => '<p>HTML Content goes here.</p>',
  'options' => array(
    'desc_tooltip' => true,
    'show_if' => array(
      array('local_ads_before_comments', '=', 'true'),
      array('local_ads_before_comments_type', '=', 'PostLayout.htmlAd'),
    ),
  ),
));

$admob->open_mixed_field(array(
  'name' =>  __('Before Comments Image Ad Options', 'jpress' ),
  'options' => array(
    'show_if' => array(
      array('local_ads_before_comments', '=', 'true'),
      array('local_ads_before_comments_type', '=', 'PostLayout.imageAd'),
    ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link Type', 'jpress' ),
  'id' => 'before_comments_ad_image_link_type',
  'type' => 'radio',
  'default' => 'NavigationType.url',
  'items' => array(
    'NavigationType.url' => __( 'Full URL', 'jpress' ),
    'NavigationType.main' => __( 'Main Page', 'jpress' ),
    'NavigationType.category' => __( 'Category', 'jpress' ),
    'NavigationType.page' => __( 'Page', 'jpress' ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link URL', 'jpress' ),
  'id' => 'before_comments_ad_image_link_url',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('before_comments_ad_image_link_type', '=', 'NavigationType.url'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Main Pages', 'jpress' ),
  'id' => 'before_comments_ad_image_link_main',
  'type' => 'select',
  'default' => 'MainPage.home',
  'attributes' => array( 'required' => true ),
  'items' => array(
    'MainPage.home' => __( 'Home', 'jpress' ),
    'MainPage.sections' => __( 'Sections', 'jpress' ),
    'MainPage.favourites' => __( 'Favorites', 'jpress' ),
    'MainPage.settings' => __( 'Settings', 'jpress' ),
    'MainPage.contactUs' => __( 'Contact us', 'jpress' ),
  ),
  'options' => array(
    'show_if' => array('before_comments_ad_image_link_type', '=', 'NavigationType.main'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Categories', 'jpress' ),
  'id' => 'before_comments_ad_image_link_category',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => JPressItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('before_comments_ad_image_link_type', '=', 'NavigationType.category'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Pages', 'jpress' ),
  'id' => 'before_comments_ad_image_link_page',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => JPressItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('before_comments_ad_image_link_type', '=', 'NavigationType.page'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Ad Image', 'jpress' ),
  'id' => 'before_comments_ad_image_file',
  'type' => 'file',
));
$admob->close_mixed_field();

$admob = $settings->add_section( array(
  'name' => __( 'Single Category Page Ads Settings', 'jpress' ),
  'id' => 'ads-section-archives-category',
  'options' => array( 'toggle' => true )
));

$admob->open_mixed_field(array(
  'name' =>  __('Enable Single Category Ads', 'jpress' ),
  'options' => array(
    'show_if' => array(
      // array('local_ads_single_cat', '=', 'true'),
      // array('local_ads_single_cat_type', '=', 'PostLayout.imageAd'),
    ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Enable Ads', 'jpress' ),
  'id' => 'local_ads_single_cat',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$admob->add_field(array(
  'name' => __( 'Show Ad Every', 'jpress' ),
  'id' => 'local_ads_single_cat_offset',
  'type' => 'number',
  'default' => '1',
  'options' => array(
    // 'show_unit' => false,
    'unit' => 'Posts',
    'show_if' => array('local_ads_single_cat', '=', 'true'),
  ),
  'attributes' => array(
    'min' => 1,
    'max' => 99,
  ),
));
$admob->close_mixed_field();

$admob->add_field( array(
  'id' => 'local_ads_single_cat_type',
  'name' => __( 'Ad Type', 'jpress' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.adMob',
  'items' => array(
    'PostLayout.adMob' => JPRESS_URL . 'options/img/blocks/ad.png',
    'PostLayout.htmlAd' => JPRESS_URL . 'options/img/blocks/adHtml.png',
    'PostLayout.imageAd' => JPRESS_URL . 'options/img/blocks/aimg.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('local_ads_single_cat', '=', 'true'),
    'show_items_if' => array(
      'PostLayout.adMob' => array('local-admob_banner', '=', 'true'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'AdMob Banner Size', 'jpress' ),
  'id' => 'single_cat_admob_banner_size',
  'type' => 'select',
  'default' => 'banner',
  'items' => array(
    'banner' => __( 'Banner', 'jpress' ),
    'leaderboard' => __( 'Leaderboard', 'jpress' ),
    'smart_banner' => __( 'Smart Banner', 'jpress' ),
    'Medium_banner' => __( 'Medium Banner', 'jpress' ),
    'large_banner' => __( 'Large Banner', 'jpress' ),
    'full_banner' => __( 'Full Banner', 'jpress' ),
  ),
  'options' => array(
    'show_if' => array(
      array('local-admob_banner', '=', 'true'),
      array('local_ads_single_cat', '=', 'true'),
      array('local_ads_single_cat_type', '=', 'PostLayout.adMob'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'Ad HTML Code', 'jpress' ),
  'id' => 'single_cat_ad_section_html',
  'type' => 'textarea',
  'desc' => __( 'Add your ad spcial HTML markup', 'jpress' ),
  'grid' => '5-of-6',
  'default' => '<p>HTML Content goes here.</p>',
  'options' => array(
    'desc_tooltip' => true,
    'show_if' => array(
      array('local_ads_single_cat', '=', 'true'),
      array('local_ads_single_cat_type', '=', 'PostLayout.htmlAd'),
    ),
  ),
));

$admob->open_mixed_field(array(
  'name' =>  __(' Image Ad Options', 'jpress' ),
  'options' => array(
    'show_if' => array(
      array('local_ads_single_cat', '=', 'true'),
      array('local_ads_single_cat_type', '=', 'PostLayout.imageAd'),
    ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link Type', 'jpress' ),
  'id' => 'single_cat_ad_image_link_type',
  'type' => 'radio',
  'default' => 'NavigationType.url',
  'items' => array(
    'NavigationType.url' => __( 'Full URL', 'jpress' ),
    'NavigationType.main' => __( 'Main Page', 'jpress' ),
    'NavigationType.category' => __( 'Category', 'jpress' ),
    'NavigationType.page' => __( 'Page', 'jpress' ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link URL', 'jpress' ),
  'id' => 'single_cat_ad_image_link_url',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('single_cat_ad_image_link_type', '=', 'NavigationType.url'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Main Pages', 'jpress' ),
  'id' => 'single_cat_ad_image_link_main',
  'type' => 'select',
  'default' => 'MainPage.home',
  'attributes' => array( 'required' => true ),
  'items' => array(
    'MainPage.home' => __( 'Home', 'jpress' ),
    'MainPage.sections' => __( 'Sections', 'jpress' ),
    'MainPage.favourites' => __( 'Favorites', 'jpress' ),
    'MainPage.settings' => __( 'Settings', 'jpress' ),
    'MainPage.contactUs' => __( 'Contact us', 'jpress' ),
  ),
  'options' => array(
    'show_if' => array('single_cat_ad_image_link_type', '=', 'NavigationType.main'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Categories', 'jpress' ),
  'id' => 'single_cat_ad_image_link_category',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => JPressItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('single_cat_ad_image_link_type', '=', 'NavigationType.category'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Pages', 'jpress' ),
  'id' => 'single_cat_ad_image_link_page',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => JPressItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('single_cat_ad_image_link_type', '=', 'NavigationType.page'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Ad Image', 'jpress' ),
  'id' => 'single_cat_ad_image_file',
  'type' => 'file',
));
$admob->close_mixed_field();

$settings->close_tab_item('advertisement');
