<?php

/**
 * Home Sections
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


// NOTE: Set default "sections" & "sections_url" values
$options['homePage']['sections'] = array();
$options['homePage']['sections_url'] = '/wp-json/jpress/v1/posts?';

foreach($data['sections'] as $key => $section) {
  if ($key === 1000) {
    continue;
  }

  unset($section['sections_type']);
  unset($section['sections_visibility']);

  $item = $item_options = array();

  $item['postLayout'] = $section['postlayout'];

  // NOTE: Ads Sections
  if ( in_array($section['postlayout'], [ 'PostLayout.adMob', 'PostLayout.imageAd', 'PostLayout.htmlAd' ]) ) {
    switch($section['postlayout']) {
      // AdMob
      case 'PostLayout.adMob':
        $item['adSize'] = $section['admob_banner_size'];
        break;

      // HTML Ad
      case 'PostLayout.htmlAd':
        $item['content'] = $section['ad_section_html'];
        break;

      // Image Ad
      case 'PostLayout.imageAd':
        $linkValue = $section['ad_image_link_url'];
        $linkType = $section['ad_image_link_type'];
        $linkTitle = '';

        switch ($linkType) {
          case 'NavigationType.category':
            $category = get_category_by_slug($section['ad_image_link_category']);

            if (empty($category) === false) {
              $linkTitle = $category->name;
              $linkValue = '/wp-json/jpress/v1/posts?categories=' . $category->term_id;
            }
            break;

          case 'NavigationType.page':
            $post = get_post($section['ad_image_link_page']);

            if ($post) {
              $linkTitle = $post->post_title;
              $linkValue = '/wp-json/jpress/v1/page?id=' . $post->ID;
            }
            break;

          case 'NavigationType.main':
            $linkValue = $section['ad_image_link_main'];

            switch($linkValue) {
              case 'MainPage.home':
                $linkTitle = __('Home', 'jpress' );
              break;
              case 'MainPage.sections':
                $linkTitle = __('Categories', 'jpress' );
              break;
              case 'MainPage.favourites':
                $linkTitle = __('Favorites', 'jpress' );
              break;
              case 'MainPage.settings':
                $linkTitle = __('Settings', 'jpress' );
              break;
              case 'MainPage.contactUs':
                $linkTitle = __('Contact us', 'jpress' );
              break;
            }
            break;
        }

        $item += array(
          'img' => $section['ad_image_file'],
          'action' => $linkType,
          'target' => $linkValue,
          'targetTitle' => $linkTitle,
        );
        break;
    }

    array_push($options['homePage']['sections'], $item);
    $options['homePage']['sections_url'] .= '&sections[]=advert';
    continue;
  }

  if (isset($section['local-hompage_title']) && $section['local-hompage_title'] == true && $section['homepage-sections-title'] != '') {
    $item['hometab'] = $data['homepage-sections-title'];
  }

  if (isset($section["local-section_title"]) && $section["local-section_title"] !== 'false') {
    $item['title'] =   stripslashes($section['title']);

    if (isset($section["local-enable_see_all"]) && !($section["local-enable_see_all"] == 'false'||$section["local-enable_see_all"]=="off")) {
      $item['seeMore']  =   array(
        'name'  =>  $item['title'],
        'url'   =>  $item['url']
      );
    }
  }

  if (isset($section["local-enable_load_more"]) && !($section["local-enable_load_more"] == 'false'||$section["local-enable_load_more"]=="off")) {
    $item['loadMore']  =   "true";
  }

  $item['url'] = '/wp-json/jpress/v1/posts?';

  switch($section['showposts']) {
    case 'categories':
      $queryURL = '';
      $selected_categories = $section['categories'];

      if (empty($selected_categories) === false) {
        $ids = '';

        foreach ($selected_categories as $idx => $cat) {
          $category = get_category_by_slug($cat);
          $termId = $category->term_id ? $category->term_id : false;

          if ( $idx !== 0 && empty($termId) === false ) {
            $ids .= ',';
          }

          $ids .= $termId ? $termId : '';
        }

        $queryURL .= empty($ids) === false ? "categories={$ids}" : '';
      }

      $item['url'] .= $queryURL;
    break;

    case 'tags':
      $selected_tags = explode( ',', $section['tags'][0] );
      $tag = get_term_by( 'slug', $selected_tags[0], 'post_tag' );
      $ids = '';

      foreach ($section['tags'] as $key => $tag) {
        $other = get_term_by( 'slug', $tag, 'post_tag' );
        $ids = ($key == 0) ? $other->term_id : ($ids . ',' . $other->term_id);
      }

      $item['url'] .= '&tags=' . $ids;
    break;
  }

  if (isset($section['local-enable_exclude_posts']) && $section['local-exclude_posts'] != '') {
    // dd($section['local-exclude_posts']);
    // $postsIds = explode(',', $section['local-exclude_posts']);
    $item['url'] .= '&exclude=' . $section['local-exclude_posts'];
  }

  if (isset($section['local-enable_offset_posts']) && $section['local-offset_posts'] != '') {
    $item['url'] .= "&offset=" . $section['local-offset_posts'];
  }

  if (isset($section['local-sort'])) {
    $item['url'] .= "&sort=" . $section['local-sort'];
  }

  if (isset($section["local-enable_see_all"]) && !($section["local-enable_see_all"] == 'false'||$section["local-enable_see_all"]=="off")) {
    $item['seeMore']  =   array(
      'name'  =>  $item['title'],
      'url'   =>  $item['url']
    );
  }

  $item['url'] .= "&count=" . ( isset($section['local-count']) ? $section['local-count'] : '3' );
  $item['url'] .= "&sort=" . ( isset($section['local-sort']) ? $section['local-sort'] : 'latest' );

  if (isset($section["local-firstfeatured"]) && $section["local-firstfeatured"] !== 'false') {
    $item['firstFeatured']  =   $section['firstFeatured'];
  }

  if (isset($section["separator"]) && $section["separator"] !== 'false') {
    $item['separator'] = $section['separator'];
  }

  $item_options["sort"]  = isset($slide['local-sort']) ? $slide['local-sort'] : '';
  $item_options["count"] = isset($slide['local-count']) ? $slide['local-count'] : '';

  if (isset($section["options-category"]) && $section["options-category"] !== 'false') {
    $item_options["category"]  =   $section["options-category"];
  }

  if (isset($section["options-author"]) && $section["options-author"] !== 'false') {
    $item_options["author"]  =   $section["options-author"];
  }

  if (isset($section["options-readtime"]) && $section["options-readtime"] !== 'false') {
    $item_options["readTime"]  =   $section["options-readtime"];
  }

  if (isset($section["options-date"]) && $section["options-date"] !== 'false') {
    $item_options["date"]  =   $section["options-date"];
  }

  if (isset($section["options-share"]) && $section["options-share"] !== 'false') {
    $item_options["share"] =   $section["options-share"];
  }

  if (isset($section["options-save"]) && $section["options-save"] !== 'false') {
    $item_options["save"]  =   $section["options-save"];
  }

  if (isset($section["options-tags"]) && $section["options-tags"] !== 'false') {
    $item_options["tags"]  =   $section["options-tags"];
  }

  // NOTE: Ensure all options are sent correctly
  // FIXME: Category should not be selected by default, but it is left for now to prevent app content crashing!
  $item['options'] = array_merge( array( 'category' => 'true' ), $item_options);

  array_push($options['homePage']['sections'], $item);

  $urlPts = explode('?', $item['url']);
  $urlParams = end($urlPts);

  $options['homePage']['sections_url'] .= '&sections[]=' . urlencode($urlParams);

  // NOTE: Debug line..
  // dd($item);
}
