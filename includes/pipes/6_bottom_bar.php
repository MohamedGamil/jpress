<?php

/**
 * Bottom Bar
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


if ($data['menu_type'] !== 'sidemenu' && isset($data["bottombar_tabs"]) && !empty($data["bottombar_tabs"])) {
  $options['bottomBar']['navigators'] = array();

  foreach ($data['bottombar_tabs'] as $key => $navigator) {
    if ($key === 1000) {
      continue;
    }

    unset($navigator['bottombar_tabs_type']);
    unset($navigator['bottombar_tabs_visibility']);
    unset($navigator['bottombar_tabs_name']);
    unset($navigator['side_menu_tab_icon']);

    switch($navigator['type']) {
      case 'NavigationType.category':
        $category = get_category_by_slug($navigator['category']);
        if (!isset($navigator["cutomized_title"]) || $navigator["cutomized_title"] == 'false') {
          $navigator['title'] = $category->name;
        }
        unset($navigator['main']);
        $navigator['url']   = '/wp-json/jpress/v1/posts?categories=' . $category->term_id;
      break;
      case 'NavigationType.page':
        $post = get_post($navigator['page']);
        if (!$post)
          break;
        if (!isset($navigator["cutomized_title"]) || $navigator["cutomized_title"] == 'false') {
          $navigator['title'] = $post->post_title;
        }
        $navigator['url']   = '/wp-json/jpress/v1/page?id=' . $post->ID;
        unset($navigator['main']);
      break;
      case 'NavigationType.main':
        if (!isset($navigator["cutomized_title"]) || $navigator["cutomized_title"] == 'false') {
          switch($navigator['main']) {
            case 'MainPage.home':
              $navigator['title'] = __('Home', 'textdomain' );
            break;
            case 'MainPage.sections':
              $navigator['title'] = __('Categories', 'textdomain' );
            break;
            case 'MainPage.favourites':
              $navigator['title'] = __('Favorites', 'textdomain' );
            break;
            case 'MainPage.settings':
              $navigator['title'] = __('Settings', 'textdomain' );
            break;
            case 'MainPage.contactUs':
              $navigator['title'] = __('Contact us', 'textdomain' );
            break;
          }
        }
      break;
    }

    unset($navigator['category']);
    unset($navigator['page']);
    unset($navigator['cutomized_title']);
    array_push($options['bottomBar']['navigators'], $navigator);
  }
}

// NOTE: Remove bottom bar if the menu type is set to "sidemenu"
if ( $data['menu_type'] === 'sidemenu' ) {
  unset($options['bottomBar']);
}
