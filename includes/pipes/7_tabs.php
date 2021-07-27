<?php

/**
 * Home Tabs
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


if (isset($data['tabsbar_categories_tab']) && $data['tabsbar_categories_tab'] !== 'false') {
  $options['tabs']['tabsLayout']  = $data['tabs-tabslayout'];

  if ( isset($data['local-hompage_title']) && $data['local-hompage_title'] !== 'false' ) {
    $options['tabs']['homeTab'] = $data['homepage-sections-title'];
  }

  $options['tabs']['tabs'] = array();

  // NOTE: Debug line
  // dd($data['tabsbaritems']);

  foreach($data['tabsbaritems'] as $key => $slide) {
    if ( $key === 1000 || !isset($slide['categories'][0]) ) {
      continue;
    }

    unset($slide['tabsbaritems_type']);
    unset($slide['tabsbaritems_visibility']);
    unset($slide['tabsbaritems_name']);

    $item = $item_options = array();

    $tabQueryURL = '/wp-json/jpress/v1/posts?';
    $selected_categories = explode(',', $slide['categories'][0]);
    $firstCat = false;

    if (empty($selected_categories) === false) {
      $ids = '';

      foreach ($selected_categories as $idx => $cat) {
        $category = get_category_by_slug($cat);
        $termId = $category->term_id ? $category->term_id : false;

        if ($idx === 0) {
          $firstCat = $category;
        }

        if ( $idx !== 0 && empty($termId) === false ) {
          $ids .= ',';
        }

        $ids .= $termId ? $termId : '';
      }

      $tabQueryURL .= empty($ids) === false ? "categories={$ids}" : '';
    }

    $item['url']   = $tabQueryURL;
    $item['url'] .= "&count=" . ( isset($slide['tabs-count']) ? $slide['tabs-count'] : '3' );
    $item['url'] .= "&sort=" . ( isset($slide['tabs-sort']) ? $slide['tabs-sort'] : 'latest' );

    if ($slide['customized-title'] == true && $slide['title'] != '') {
      $item['title']  = stripslashes($slide['title']);
    }
    else {
      $item['title'] = $firstCat !== false ? $firstCat->name : '';
    }

    // FIXME: Should be removed, this options does not exist!
    if (isset($slide["local-tabs-seperator"]) && $slide["local-tabs-seperator"] !== 'false') {
      $item['seperator']  =   $slide['tabs-seperator'];
    }

    // if (isset($slide["local-tabs-firstfeatured"]) && $slide["local-tabs-firstfeatured"] !== 'false') {
    //   $options['tabs']['firstFeatured']  = $slide['tabs-firstfeatured'];
    // }

    $item['postLayout'] = $slide['tabs-postlayout'];
    $item_options["sort"]  = $slide['tabs-sort'];
    $item_options["count"] = $slide['tabs-count'];

    if (isset($slide["tabs-options-category"]) && $slide["tabs-options-category"] !== 'false') {
      $item_options["category"]  = $slide["tabs-options-category"];
    }

    if (isset($slide["tabs-options-author"]) && $slide["tabs-options-author"] !== 'false') {
      $item_options["author"]  = $slide["tabs-options-author"];
    }

    if (isset($slide["tabs-options-readtime"]) && $slide["tabs-options-readtime"] !== 'false') {
      $item_options["readTime"]  = $slide["tabs-options-readtime"];
    }

    if (isset($slide["tabs-options-date"]) && $slide["tabs-options-date"] !== 'false') {
      $item_options["date"]  = $slide["tabs-options-date"];
    }

    if (isset($slide["tabs-options-share"]) && $slide["tabs-options-share"] !== 'false') {
      $item_options["share"] = $slide["tabs-options-share"];
    }

    if (isset($slide["tabs-options-save"]) && $slide["tabs-options-save"] !== 'false') {
      $item_options["save"]  = $slide["tabs-options-save"];
    }

    if (isset($slide["tabs-options-tags"]) && $slide["tabs-options-tags"] !== 'false') {
      $item_options["tags"]  = $slide["tabs-options-tags"];
    }

    // TODO: Should the above options become nested inside an options array?
    $item['options'] = array_merge( array( 'category' => 'true' ), $item_options);

    // NOTE: Debug line
    // dd($item);

    array_push($options['tabs']['tabs'], $item);
  }
}
