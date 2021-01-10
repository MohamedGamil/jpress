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
  'name' => __( 'Advertisements Settings', 'textdomain' ),
  'id' => 'section-advertisement-admob',
  'options' => array( 'toggle' => true )
));

$admob->open_mixed_field(array('name' => __('Admob Banner', 'textdomain' )));
$admob->add_field(array(
  'name' => __( 'Enabled', 'textdomain' ),
  'id' => 'local-admob_banner',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$admob->add_field(array(
  'name' => __( 'Android ID', 'textdomain' ),
  'id' => 'advertisement_android_banner_id_text',
  'type' => 'text',
  'options'	=>	array(
    'show_if' => array('local-admob_banner', '=', 'true')
  ),
));

$admob->add_field(array(
  'name' => __( 'iOS ID', 'textdomain' ),
  'id' => 'advertisement_ios_banner_id_text',
  'type' => 'text',
  'options'	=>	array(
    'show_if' => array('local-admob_banner', '=', 'true')
  ),
));
$admob->close_mixed_field();

$admob->open_mixed_field(array('name' => __('Admob Interstitial', 'textdomain' )));
$admob->add_field(array(
  'name' => __( 'Enable', 'textdomain' ),
  'id' => 'local-advertisement_admob_interstatial',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$admob->add_field(array(
  'name' => __( 'Android ID', 'textdomain' ),
  'id' => 'advertisement_android_interstatial_id_text',
  'type' => 'text',
  'options'	=>	array(
    'show_if' => array('local-advertisement_admob_interstatial', '=', 'true')
  ),
));

$admob->add_field(array(
  'name' => __( 'iOS ID', 'textdomain' ),
  'id' => 'advertisement_ios_interstatial_id_text',
  'type' => 'text',
  'options'	=>	array(
    'show_if' => array('local-advertisement_admob_interstatial', '=', 'true')
  ),
));
$admob->close_mixed_field();

$admob = $settings->add_section( array(
  'name' => __( 'Single Post Ads Settings', 'textdomain' ),
  'id' => 'ads-section-archives-single',
  'options' => array( 'toggle' => true )
));

$admob->add_field(array(
  'name' => __( 'Enable Ads Inside Post Content', 'textdomain' ),
  'id' => 'local_ads_in_post',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$admob->add_field(array(
  'name' => __( 'Show In-Post Ad After', 'textdomain' ),
  'id' => 'local_ads_in_post_paragraph_offset',
  'type' => 'select',
  'default' => '2',
  'items' => array(
    '1' => __( '1 Paragraph', 'textdomain' ),
    '2' => __( '2 Paragraphs', 'textdomain' ),
    '3' => __( '3 Paragraphs', 'textdomain' ),
    '4' => __( '4 Paragraphs', 'textdomain' ),
    '5' => __( '5 Paragraphs', 'textdomain' ),
    '6' => __( '6 Paragraphs', 'textdomain' ),
  ),
  'options' => array('show_if' => array('local_ads_in_post', '=', 'true'))
));

$admob->add_field( array(
  'id' => 'local_ads_in_post_type',
  'name' => __( 'In-Post Ad Type', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.adMob',
  'items' => array(
    'PostLayout.adMob' => APPBEAR_URL . 'options/img/blocks/ad.png',
    'PostLayout.htmlAd' => APPBEAR_URL . 'options/img/blocks/adHtml.png',
    'PostLayout.imageAd' => APPBEAR_URL . 'options/img/blocks/aimg.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('local_ads_in_post', '=', 'true'),
  ),
));

$admob->add_field(array(
  'name' => __( 'In-Post AdMob Banner Size', 'textdomain' ),
  'id' => 'in_post_admob_banner_size',
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
    'show_if' => array(
      array('local_ads_in_post', '=', 'true'),
      array('local_ads_in_post_type', '=', 'PostLayout.adMob'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'In-Post Ad HTML Code', 'textdomain' ),
  'id' => 'in_post_ad_section_html',
  'type' => 'textarea',
  'desc' => __( 'Add your ad spcial HTML markup', 'textdomain' ),
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
  'name' =>  __('In-Post Image Ad Options', 'textdomain' ),
  'options' => array(
    'show_if' => array(
      array('local_ads_in_post', '=', 'true'),
      array('local_ads_in_post_type', '=', 'PostLayout.imageAd'),
    ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link Type', 'textdomain' ),
  'id' => 'in_post_ad_image_link_type',
  'type' => 'radio',
  'default' => 'NavigationType.url',
  'items' => array(
    'NavigationType.url' => __( 'Full URL', 'textdomain' ),
    'NavigationType.main' => __( 'Main Page', 'textdomain' ),
    'NavigationType.category' => __( 'Category', 'textdomain' ),
    'NavigationType.page' => __( 'Page', 'textdomain' ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link URL', 'textdomain' ),
  'id' => 'in_post_ad_image_link_url',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('in_post_ad_image_link_type', '=', 'NavigationType.url'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Main Pages', 'textdomain' ),
  'id' => 'in_post_ad_image_link_main',
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
    'show_if' => array('in_post_ad_image_link_type', '=', 'NavigationType.main'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Categories', 'textdomain' ),
  'id' => 'in_post_ad_image_link_category',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => AppbearItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('in_post_ad_image_link_type', '=', 'NavigationType.category'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Pages', 'textdomain' ),
  'id' => 'in_post_ad_image_link_page',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => AppbearItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('in_post_ad_image_link_type', '=', 'NavigationType.page'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Ad Image', 'textdomain' ),
  'id' => 'in_post_ad_image_file',
  'type' => 'file',
));
$admob->close_mixed_field();

$admob->open_mixed_field(array('name' => __('Interstitial Ad', 'textdomain' )));
$admob->add_field(array(
  'name' => __( 'Enable Interstitial Before Post View', 'textdomain' ),
  'id' => 'local_ads_interstatial_before_post',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));
$admob->add_field(array(
  'name' => __( 'Show Interstitial Ad Every', 'textdomain' ),
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
  'name' => __( 'Enable Ads After Post', 'textdomain' ),
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
  'name' => __( 'After Post Ad Type', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.adMob',
  'items' => array(
    'PostLayout.adMob' => APPBEAR_URL . 'options/img/blocks/ad.png',
    'PostLayout.htmlAd' => APPBEAR_URL . 'options/img/blocks/adHtml.png',
    'PostLayout.imageAd' => APPBEAR_URL . 'options/img/blocks/aimg.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('local_ads_after_post', '=', 'true'),
  ),
));

$admob->add_field(array(
  'name' => __( 'After Post AdMob Banner Size', 'textdomain' ),
  'id' => 'after_post_admob_banner_size',
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
    'show_if' => array(
      array('local_ads_after_post', '=', 'true'),
      array('local_ads_after_post_type', '=', 'PostLayout.adMob'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'After Post Ad HTML Code', 'textdomain' ),
  'id' => 'after_post_ad_section_html',
  'type' => 'textarea',
  'desc' => __( 'Add your ad spcial HTML markup', 'textdomain' ),
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
  'name' =>  __('After Post Image Ad Options', 'textdomain' ),
  'options' => array(
    'show_if' => array(
      array('local_ads_after_post', '=', 'true'),
      array('local_ads_after_post_type', '=', 'PostLayout.imageAd'),
    ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link Type', 'textdomain' ),
  'id' => 'after_post_ad_image_link_type',
  'type' => 'radio',
  'default' => 'NavigationType.url',
  'items' => array(
    'NavigationType.url' => __( 'Full URL', 'textdomain' ),
    'NavigationType.main' => __( 'Main Page', 'textdomain' ),
    'NavigationType.category' => __( 'Category', 'textdomain' ),
    'NavigationType.page' => __( 'Page', 'textdomain' ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link URL', 'textdomain' ),
  'id' => 'after_post_ad_image_link_url',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('after_post_ad_image_link_type', '=', 'NavigationType.url'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Main Pages', 'textdomain' ),
  'id' => 'after_post_ad_image_link_main',
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
    'show_if' => array('after_post_ad_image_link_type', '=', 'NavigationType.main'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Categories', 'textdomain' ),
  'id' => 'after_post_ad_image_link_category',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => AppbearItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('after_post_ad_image_link_type', '=', 'NavigationType.category'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Pages', 'textdomain' ),
  'id' => 'after_post_ad_image_link_page',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => AppbearItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('after_post_ad_image_link_type', '=', 'NavigationType.page'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Ad Image', 'textdomain' ),
  'id' => 'after_post_ad_image_file',
  'type' => 'file',
));
$admob->close_mixed_field();

$admob->add_field(array(
  'name' => __( 'Enable Ads Before Comments', 'textdomain' ),
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
  'name' => __( 'Before Comments Ad Type', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.adMob',
  'items' => array(
    'PostLayout.adMob' => APPBEAR_URL . 'options/img/blocks/ad.png',
    'PostLayout.htmlAd' => APPBEAR_URL . 'options/img/blocks/adHtml.png',
    'PostLayout.imageAd' => APPBEAR_URL . 'options/img/blocks/aimg.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('local_ads_before_comments', '=', 'true'),
  ),
));

$admob->add_field(array(
  'name' => __( 'Before Comments AdMob Banner Size', 'textdomain' ),
  'id' => 'before_comments_admob_banner_size',
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
    'show_if' => array(
      array('local_ads_before_comments', '=', 'true'),
      array('local_ads_before_comments_type', '=', 'PostLayout.adMob'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'Before Comments Ad HTML Code', 'textdomain' ),
  'id' => 'before_comments_ad_section_html',
  'type' => 'textarea',
  'desc' => __( 'Add your ad spcial HTML markup', 'textdomain' ),
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
  'name' =>  __('Before Comments Image Ad Options', 'textdomain' ),
  'options' => array(
    'show_if' => array(
      array('local_ads_before_comments', '=', 'true'),
      array('local_ads_before_comments_type', '=', 'PostLayout.imageAd'),
    ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link Type', 'textdomain' ),
  'id' => 'before_comments_ad_image_link_type',
  'type' => 'radio',
  'default' => 'NavigationType.url',
  'items' => array(
    'NavigationType.url' => __( 'Full URL', 'textdomain' ),
    'NavigationType.main' => __( 'Main Page', 'textdomain' ),
    'NavigationType.category' => __( 'Category', 'textdomain' ),
    'NavigationType.page' => __( 'Page', 'textdomain' ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link URL', 'textdomain' ),
  'id' => 'before_comments_ad_image_link_url',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('before_comments_ad_image_link_type', '=', 'NavigationType.url'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Main Pages', 'textdomain' ),
  'id' => 'before_comments_ad_image_link_main',
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
    'show_if' => array('before_comments_ad_image_link_type', '=', 'NavigationType.main'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Categories', 'textdomain' ),
  'id' => 'before_comments_ad_image_link_category',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => AppbearItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('before_comments_ad_image_link_type', '=', 'NavigationType.category'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Pages', 'textdomain' ),
  'id' => 'before_comments_ad_image_link_page',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => AppbearItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('before_comments_ad_image_link_type', '=', 'NavigationType.page'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Ad Image', 'textdomain' ),
  'id' => 'before_comments_ad_image_file',
  'type' => 'file',
));
$admob->close_mixed_field();

$admob = $settings->add_section( array(
  'name' => __( 'Single Category Page Ads Settings', 'textdomain' ),
  'id' => 'ads-section-archives-category',
  'options' => array( 'toggle' => true )
));

$admob->open_mixed_field(array(
  'name' =>  __('Enable Single Category Ads', 'textdomain' ),
  'options' => array(
    'show_if' => array(
      // array('local_ads_single_cat', '=', 'true'),
      // array('local_ads_single_cat_type', '=', 'PostLayout.imageAd'),
    ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Enable Ads', 'textdomain' ),
  'id' => 'local_ads_single_cat',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  ),
));

$admob->add_field(array(
  'name' => __( 'Show Ad Every', 'textdomain' ),
  'id' => 'ads_single_cat_offset',
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
  'name' => __( 'Ad Type', 'textdomain' ),
  'type' => 'image_selector',
  'default' => 'PostLayout.adMob',
  'items' => array(
    'PostLayout.adMob' => APPBEAR_URL . 'options/img/blocks/ad.png',
    'PostLayout.htmlAd' => APPBEAR_URL . 'options/img/blocks/adHtml.png',
    'PostLayout.imageAd' => APPBEAR_URL . 'options/img/blocks/aimg.png',
  ),
  'options' => array(
    'width' => '155px',
    'show_if' => array('local_ads_single_cat', '=', 'true'),
  ),
));

$admob->add_field(array(
  'name' => __( 'AdMob Banner Size', 'textdomain' ),
  'id' => 'single_cat_admob_banner_size',
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
    'show_if' => array(
      array('local_ads_single_cat', '=', 'true'),
      array('local_ads_single_cat_type', '=', 'PostLayout.adMob'),
    ),
  ),
));

$admob->add_field(array(
  'name' => __( 'Ad HTML Code', 'textdomain' ),
  'id' => 'single_cat_ad_section_html',
  'type' => 'textarea',
  'desc' => __( 'Add your ad spcial HTML markup', 'textdomain' ),
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
  'name' =>  __(' Image Ad Options', 'textdomain' ),
  'options' => array(
    'show_if' => array(
      array('local_ads_single_cat', '=', 'true'),
      array('local_ads_single_cat_type', '=', 'PostLayout.imageAd'),
    ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link Type', 'textdomain' ),
  'id' => 'single_cat_ad_image_link_type',
  'type' => 'radio',
  'default' => 'NavigationType.url',
  'items' => array(
    'NavigationType.url' => __( 'Full URL', 'textdomain' ),
    'NavigationType.main' => __( 'Main Page', 'textdomain' ),
    'NavigationType.category' => __( 'Category', 'textdomain' ),
    'NavigationType.page' => __( 'Page', 'textdomain' ),
  ),
));
$admob->add_field(array(
  'name' => __( 'Link URL', 'textdomain' ),
  'id' => 'single_cat_ad_image_link_url',
  'type' => 'text',
  'grid' => '2-of-6',
  'options' => array(
    'show_if' => array('single_cat_ad_image_link_type', '=', 'NavigationType.url'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Main Pages', 'textdomain' ),
  'id' => 'single_cat_ad_image_link_main',
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
    'show_if' => array('single_cat_ad_image_link_type', '=', 'NavigationType.main'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Categories', 'textdomain' ),
  'id' => 'single_cat_ad_image_link_category',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => AppbearItems::terms( 'category' ),
  'options' => array(
    'show_if' => array('single_cat_ad_image_link_type', '=', 'NavigationType.category'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Pages', 'textdomain' ),
  'id' => 'single_cat_ad_image_link_page',
  'type' => 'select',
  'attributes' => array( 'required' => true ),
  'items' => AppbearItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
  'options' => array(
    'show_if' => array('single_cat_ad_image_link_type', '=', 'NavigationType.page'),
  ),
));
$admob->add_field(array(
  'name' => __( 'Ad Image', 'textdomain' ),
  'id' => 'single_cat_ad_image_file',
  'type' => 'file',
));
$admob->close_mixed_field();

$settings->close_tab_item('advertisement');
