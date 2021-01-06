<?php

/**
 * Defaults options that is sent to the API anyways
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


// NOTE: RTL & Theme Colors Mode
$options['rtl'] = is_rtl() ? 'true' : 'false';
$options['themeMode'] = str_replace( '_', '.', $data['thememode'] );

// @DEPRECATED: since 0.0.4  should be removed in the next update
if (isset($data['statusbarwhiteforeground']) && $data['statusbarwhiteforeground'] !== 'false') {
  $options['statusBarWhiteForeground'] = $data['statusbarwhiteforeground'];
}

/*
 * App Bar Layout & Position
 */
$options['appBar']['layout'] = 'AppBarLayout.header2';

if (isset($data['appbar-position']) === true) {
  $options['appBar']['position'] = $data['appbar-position'];
}

/*
 * Top Search Button
 */
if (isset($data["topbar_search_button"]) && $data["topbar_search_button"] !== 'false') {
  $options['appBar']['searchIcon'] = $data['appbar-searchicon'];
}

// URLs
$options['basicUrls']["getPost"] = "/wp-json/appbear/v1/post";
$options['basicUrls']["submitComment"] = "/wp-json/appbear/v1/add-comment";
$options['basicUrls']["removeUrl"] = "/?edd_action=remove_development_token";
$options['basicUrls']["saveToken"] = "/?edd_action=save_token";
$options['basicUrls']["translations"] = "/wp-json/appbear/v1/translations";
$options['basicUrls']["getPostWPJSON"] = "/wp-json/appbear/v1/post";
$options['basicUrls']["getTags"] = "/wp-json/appbear/v1/posts?tags=";
$options['basicUrls']["getTagsPosts"] = "/wp-json/appbear/v1/posts?tags=";
$options['basicUrls']["login"] = "/wp-json/appbear/v1/login";
$options['basicUrls']["selectDemo"] = "/wp-json/appbear/v1/selectDemo?";
$options['basicUrls']["demos"] = "/wp-json/appbear/v1/demos";

// Other settings
$options['defaultLayout'] = "Layout.standard";
$options['searchApi'] = "/wp-json/appbear/v1/posts?s=";
$options['commentsApi'] = "/wp-json/appbear/v1/comments?id=";
$options['commentAdd'] = "/wp-json/appbear/v1/add-comment";
$options['relatedPostsApi'] = "/wp-json/appbear/v1/posts?related_id=";
$options['lang'] = "en";
$options['validConfig'] = "true";
$options['ttsLanguage'] = appbear_get_tts_locale();
