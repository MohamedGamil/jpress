<?php

/**
 * Social
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


if ( isset($data['social_enabled'], $data['social']) && $data['social_enabled'] === 'true' && empty($data['social']) === false ) {
  $options['settingsPage']['social'] = array();

  foreach($data['social'] as $key => $section) {
    if ($key === 1000) {
      continue;
    }

    unset($section['sections_type']);
    unset($section['sections_visibility']);

    $item = array_merge(
      array(
        'title' => '',
        'icon' => '',
        'url' => '',
      ),
      array(
        'title' => $section['title'],
        'icon' => $section['icon'],
        'url' => $section['url'],
      ),
    );

    if ($section['social_link_title'] === 'false') {
      unset($item['title']);
    }

    $options['settingsPage']['social'][] = $item;
  }

  // dd($options['settingsPage']['social']);
}
