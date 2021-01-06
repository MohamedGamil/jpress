<?php

/**
 * Archives -Single, Category, and Multiple Categories- Options
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


$options['archives']['categories']['layout'] = $data['archives-categories-postlayout'];
$options['archives']['categories']['url'] = "/wp-json/appbear/v1/categories";

if (isset($data['archives-single-options-category']) && $data['archives-single-options-category'] !== 'false') {
  $options['archives']['single']['category'] = $data['archives-single-options-category'];
}

if (isset($data['archives-single-options-author']) && $data['archives-single-options-author'] !== 'false') {
  $options['archives']['single']['author'] = $data['archives-single-options-author'];
}

if (isset($data['archives-single-options-tags']) && $data['archives-single-options-tags'] !== 'false') {
  $options['archives']['single']['tags'] = $data['archives-single-options-tags'];
}

if (isset($data['archives-single-options-readtime']) && $data['archives-single-options-readtime'] !== 'false') {
  $options['archives']['single']['readTime'] = $data['archives-single-options-readtime'];
}

if (isset($data['archives-single-options-date']) && $data['archives-single-options-date'] !== 'false') {
  $options['archives']['single']['date'] = $data['archives-single-options-date'];
}

if (isset($data['archives-single-options-save']) && $data['archives-single-options-save'] !== 'false') {
  $options['archives']['single']['save'] = $data['archives-single-options-save'];
}

if (isset($data['archives-single-options-share']) && $data['archives-single-options-share'] !== 'false') {
  $options['archives']['single']['share'] = $data['archives-single-options-share'];
}

if (isset($data['archives-single-options-tts']) && $data['archives-single-options-tts'] !== 'false') {
  $options['archives']['single']['textToSpeech'] = $data['archives-single-options-tts'];
}

$options['archives']['single']['ads'] = array();

if (isset($data['local_ads_interstatial_before_post']) && $data['local_ads_interstatial_before_post'] === 'true') {
  $options['archives']['single']['ads']['interstitial']['afterCount'] = $data['local_ads_interstatial_before_post_offset'];
}

if (isset($data['local_ads_after_post']) && $data['local_ads_after_post'] === 'true') {
  $options['archives']['single']['ads']['afterPost'] = array(
    'type' => $data['local_ads_after_post_type'],
  );

  switch($data['local_ads_after_post_type']) {
    case 'PostLayout.adMob':
      $options['archives']['single']['ads']['afterPost']['adSize'] = $data['after_post_admob_banner_size'];
      break;
    case 'PostLayout.htmlAd':
      $options['archives']['single']['ads']['afterPost']['content'] = $data['after_post_ad_section_html'];
      break;
    case 'PostLayout.imageAd':
      $linkValue = $data['after_post_ad_image_link_url'];
      $linkType = $data['after_post_ad_image_link_type'];
      $linkTitle = '';

      switch ($linkType) {
        case 'NavigationType.category':
          $category = get_category_by_slug($data['after_post_ad_image_link_category']);

          if (empty($category) === false) {
            $linkTitle = $category->name;
            $linkValue = '/wp-json/appbear/v1/posts?categories=' . $category->term_id;
          }
          break;

        case 'NavigationType.page':
          $post = get_post($data['after_post_ad_image_link_page']);

          if ($post) {
            $linkTitle = $post->post_title;
            $linkValue = '/wp-json/appbear/v1/page?id=' . $post->ID;
          }
          break;

        case 'NavigationType.main':
          $linkValue = $data['after_post_ad_image_link_main'];

          switch($linkValue) {
            case 'MainPage.home':
              $linkTitle = __('Home', 'textdomain' );
            break;
            case 'MainPage.sections':
              $linkTitle = __('Categories', 'textdomain' );
            break;
            case 'MainPage.favourites':
              $linkTitle = __('Favorites', 'textdomain' );
            break;
            case 'MainPage.settings':
              $linkTitle = __('Settings', 'textdomain' );
            break;
            case 'MainPage.contactUs':
              $linkTitle = __('Contact us', 'textdomain' );
            break;
          }
          break;
      }

      $options['archives']['single']['ads']['afterPost'] += array(
        'img' => $data['after_post_ad_image_file'],
        'action' => $linkType,
        'target' => $linkValue,
        'targetTitle' => $linkTitle,
      );
      break;
  }
}

if (isset($data['local_ads_before_comments']) && $data['local_ads_before_comments'] === 'true') {
  $options['archives']['single']['ads']['beforeComments'] = array(
    'type' => $data['local_ads_before_comments_type'],
  );

  switch($data['local_ads_before_comments_type']) {
    case 'PostLayout.adMob':
      $options['archives']['single']['ads']['beforeComments']['adSize'] = $data['before_comments_admob_banner_size'];
      break;
    case 'PostLayout.htmlAd':
      $options['archives']['single']['ads']['beforeComments']['content'] = $data['before_comments_ad_section_html'];
      break;
    case 'PostLayout.imageAd':
      $linkValue = $data['before_comments_ad_image_link_url'];
      $linkType = $data['before_comments_ad_image_link_type'];
      $linkTitle = '';

      switch ($linkType) {
        case 'NavigationType.category':
          $category = get_category_by_slug($data['before_comments_ad_image_link_category']);

          if (empty($category) === false) {
            $linkTitle = $category->name;
            $linkValue = '/wp-json/appbear/v1/posts?categories=' . $category->term_id;
          }
          break;

        case 'NavigationType.page':
          $post = get_post($data['before_comments_ad_image_link_page']);

          if ($post) {
            $linkTitle = $post->post_title;
            $linkValue = '/wp-json/appbear/v1/page?id=' . $post->ID;
          }
          break;

        case 'NavigationType.main':
          $linkValue = $data['before_comments_ad_image_link_main'];

          switch($linkValue) {
            case 'MainPage.home':
              $linkTitle = __('Home', 'textdomain' );
            break;
            case 'MainPage.sections':
              $linkTitle = __('Categories', 'textdomain' );
            break;
            case 'MainPage.favourites':
              $linkTitle = __('Favorites', 'textdomain' );
            break;
            case 'MainPage.settings':
              $linkTitle = __('Settings', 'textdomain' );
            break;
            case 'MainPage.contactUs':
              $linkTitle = __('Contact us', 'textdomain' );
            break;
          }
          break;
      }

      $options['archives']['single']['ads']['beforeComments'] += array(
        'img' => $data['before_comments_ad_image_file'],
        'action' => $linkType,
        'target' => $linkValue,
        'targetTitle' => $linkTitle,
      );
      break;
  }
}

$options['archives']['category']['ads'] = array(
  'adsCount' => $data['ads_single_cat_offset'],
);

if (isset($data['local_ads_single_cat']) && $data['local_ads_single_cat'] === 'true') {
  $options['archives']['category']['ads'] = array(
    'type' => $data['local_ads_single_cat_type'],
  );

  switch($data['local_ads_single_cat_type']) {
    case 'PostLayout.adMob':
      $options['archives']['category']['ads']['adSize'] = $data['single_cat_admob_banner_size'];
      break;
    case 'PostLayout.htmlAd':
      $options['archives']['category']['ads']['content'] = $data['single_cat_ad_section_html'];
      break;
    case 'PostLayout.imageAd':
      $linkValue = $data['single_cat_ad_image_link_url'];
      $linkType = $data['single_cat_ad_image_link_type'];
      $linkTitle = '';

      switch ($linkType) {
        case 'NavigationType.category':
          $category = get_category_by_slug($data['single_cat_ad_image_link_category']);

          if (empty($category) === false) {
            $linkTitle = $category->name;
            $linkValue = '/wp-json/appbear/v1/posts?categories=' . $category->term_id;
          }
          break;

        case 'NavigationType.page':
          $post = get_post($data['single_cat_ad_image_link_page']);

          if ($post) {
            $linkTitle = $post->post_title;
            $linkValue = '/wp-json/appbear/v1/page?id=' . $post->ID;
          }
          break;

        case 'NavigationType.main':
          $linkValue = $data['single_cat_ad_image_link_main'];

          switch($linkValue) {
            case 'MainPage.home':
              $linkTitle = __('Home', 'textdomain' );
            break;
            case 'MainPage.sections':
              $linkTitle = __('Categories', 'textdomain' );
            break;
            case 'MainPage.favourites':
              $linkTitle = __('Favorites', 'textdomain' );
            break;
            case 'MainPage.settings':
              $linkTitle = __('Settings', 'textdomain' );
            break;
            case 'MainPage.contactUs':
              $linkTitle = __('Contact us', 'textdomain' );
            break;
          }
          break;
      }

      $options['archives']['category']['ads'] += array(
        'img' => $data['single_cat_ad_image_file'],
        'action' => $linkType,
        'target' => $linkValue,
        'targetTitle' => $linkTitle,
      );
      break;
  }
}

$options['archives']['category']['postLayout'] = $data['archives-category-postlayout'];
$options['archives']['category']['options']['count'] = $data['local-archives-category-count'];

if (isset($data['archives-category-options-category']) && $data['archives-category-options-category'] !== 'false') {
  $options['archives']['category']['options']['category'] = $data['archives-category-options-category'];
}

if (isset($data['archives-category-options-author']) && $data['archives-category-options-author'] !== 'false') {
  $options['archives']['category']['options']['author'] = $data['archives-category-options-author'];
}

if (isset($data['archives-category-options-tags']) && $data['archives-category-options-tags'] !== 'false') {
  $options['archives']['category']['options']['tags'] = $data['archives-category-options-tags'];
}

if (isset($data['archives-category-options-readtime']) && $data['archives-category-options-readtime'] !== 'false') {
  $options['archives']['category']['options']['readTime'] = $data['archives-category-options-readtime'];
}

if (isset($data['archives-category-options-date']) && $data['archives-category-options-date'] !== 'false') {
  $options['archives']['category']['options']['date'] = $data['archives-category-options-date'];
}

if (isset($data['archives-category-options-save']) && $data['archives-category-options-save'] !== 'false') {
  $options['archives']['category']['options']['save'] = $data['archives-category-options-save'];
}

if (isset($data['archives-category-options-share']) && $data['archives-category-options-share'] !== 'false') {
  $options['archives']['category']['options']['share'] = $data['archives-category-options-share'];
}

$options['archives']['search']['postLayout'] = $data['archives-search-postlayout'];
$options['archives']['search']['options']['count'] = $data['local-archives-search-count'];

if (isset($data['archives-search-options-category']) && $data['archives-search-options-category'] !== 'false') {
  $options['archives']['search']['options']['category'] = $data['archives-search-options-category'];
}

if (isset($data['archives-search-options-author']) && $data['archives-search-options-author'] !== 'false') {
  $options['archives']['search']['options']['author'] = $data['archives-search-options-author'];
}

if (isset($data['archives-search-options-tags']) && $data['archives-search-options-tags'] !== 'false') {
  $options['archives']['search']['options']['tags'] = $data['archives-search-options-tags'];
}

if (isset($data['archives-search-options-readtime']) && $data['archives-search-options-readtime'] !== 'false') {
  $options['archives']['search']['options']['readTime'] = $data['archives-search-options-readtime'];
}

if (isset($data['archives-search-options-date']) && $data['archives-search-options-date'] !== 'false') {
  $options['archives']['search']['options']['date'] = $data['archives-search-options-date'];
}

if (isset($data['archives-search-options-save']) && $data['archives-search-options-save'] !== 'false') {
  $options['archives']['search']['options']['save'] = $data['archives-search-options-save'];
}

if (isset($data['archives-search-options-share']) && $data['archives-search-options-share'] !== 'false') {
  $options['archives']['search']['options']['share'] = $data['archives-search-options-share'];
}

$options['archives']['favorites']['postLayout'] = $data['archives-favorites-postlayout'];
$options['archives']['favorites']['url'] = '/wp-json/appbear/v1/posts?&ids=';
$options['archives']['favorites']['options']['count'] = $data['local-archives-favorites-count'];

if (isset($data['archives-favorites-options-category']) && $data['archives-favorites-options-category'] !== 'false') {
  $options['archives']['favorites']['options']['category'] = $data['archives-favorites-options-category'];
}

if (isset($data['archives-favorites-options-author']) && $data['archives-favorites-options-author'] !== 'false') {
  $options['archives']['favorites']['options']['author'] = $data['archives-favorites-options-author'];
}

if (isset($data['archives-favorites-options-tags']) && $data['archives-favorites-options-tags'] !== 'false') {
  $options['archives']['favorites']['options']['tags'] = $data['archives-favorites-options-tags'];
}

if (isset($data['archives-favorites-options-readtime']) && $data['archives-favorites-options-readtime'] !== 'false') {
  $options['archives']['favorites']['options']['readTime'] = $data['archives-favorites-options-readtime'];
}

if (isset($data['archives-favorites-options-date']) && $data['archives-favorites-options-date'] !== 'false') {
  $options['archives']['favorites']['options']['date'] = $data['archives-favorites-options-date'];
}

if (isset($data['archives-favorites-options-save']) && $data['archives-favorites-options-save'] !== 'false') {
  $options['archives']['favorites']['options']['save'] = $data['archives-favorites-options-save'];
}

if (isset($data['archives-favorites-options-share']) && $data['archives-favorites-options-share'] !== 'false') {
  $options['archives']['favorites']['options']['share'] = $data['archives-favorites-options-share'];
}
