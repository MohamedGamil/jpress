<?php

/**
 * Settings Page
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


if (isset($data['settingspage-textSize']) && $data['settingspage-textSize'] !== 'false') {
  $options['settingsPage']['textSize'] = $data['settingspage-textSize'];
}

if (isset($data['switch_theme_mode']) && $data['switch_theme_mode'] !== 'false' && isset($data['settingspage-darkMode']) && $data['settingspage-darkMode'] !== 'false') {
  $options['settingsPage']['darkMode'] = $data['settingspage-darkMode'];
}

if (isset($data['settingspage-rateApp']) && $data['settingspage-rateApp'] !== 'false') {
  $options['settingsPage']['rateApp'] = $data['settingspage-rateApp'];
}

if (isset($data['local-settingspage-share']) && $data['local-settingspage-share'] !== 'false') {
  $shareApp = array();
  $prefix = 'settingspage-shareApp-';

  foreach ( array('title', 'image', 'android', 'ios') as $k ) {
    $key = $prefix . $k;

    if (isset($data[$key]) && empty($data[$key]) === false) {
      $shareApp[$k] = $data[$key];
    }
  }

  if (empty($shareApp) === false) {
    $options['settingsPage']['shareApp'] = $shareApp;
  }
}

if (isset($data['local-settingspage-aboutus']) && $data['local-settingspage-aboutus'] !== 'false') {
  $options['settingsPage']['aboutUs'] = "/wp-json/appbear/v1/page?id=" . $data['settingspage-aboutUs'];
}

if (isset($data['settingspage-privacyPolicy']) && $data['settingspage-privacyPolicy'] != '') {
  $options['settingsPage']['privacyPolicy'] = "/wp-json/appbear/v1/page?id=" . $data['settingspage-privacyPolicy'];
}

if (isset($data['settingspage-termsAndConditions']) && $data['settingspage-termsAndConditions'] != '') {
  $options['settingsPage']['termsAndConditions'] = "/wp-json/appbear/v1/page?id=" . $data['settingspage-termsAndConditions'];
}

if (isset($data['settingspage-contactus']) && $data['settingspage-contactus'] !== 'false') {
  $options['settingsPage']['contactUs'] = "/wp-json/appbear/v1/contact-us";
}

if (isset($data['local-settingspage-aboutapp']) && $data['local-settingspage-aboutapp'] !== 'false') {
  $options['settingsPage']['aboutApp']["aboutLogoLight"] = empty($data['settingspage-aboutapp-logo-light']) === false ? $data['settingspage-aboutapp-logo-light'] : $data['logo-light'];

  if (isset($data['switch_theme_mode']) && $data['switch_theme_mode'] !== 'false') {
    $options['settingsPage']['aboutApp']["aboutLogoDark"] = empty($data['settingspage-aboutapp-logo-dark']) === false ? $data['settingspage-aboutapp-logo-dark'] : $data['logo-dark'];
  }

  $options['settingsPage']['aboutApp']["title"] = empty($data['settingspage-aboutapp-title']) === false ? $data['settingspage-aboutapp-title'] : get_bloginfo('name');
  $options['settingsPage']['aboutApp']["content"] = empty($data['settingspage-aboutapp-content']) === false ? $data['settingspage-aboutapp-content'] : get_bloginfo('description');
  $options['settingsPage']['shortCodes'] = "true";

  if (isset($data['settingspage-devmode']) && $data['settingspage-devmode'] !== 'false') {
    // $options['settingsPage']['devMode']["time"] = $data['settingspage-devmode-time'];
    $options['settingsPage']['devMode']["time"] = "6000";
    // $options['settingsPage']['devMode']["count"] = $data['settingspage-devmode-count'];
    $options['settingsPage']['devMode']["count"] = "3";
    $options['settingsPage']['devMode']["addUrl"] = "/?edd_action=save_development_token";
    $options['settingsPage']['devMode']["removeUrl"] = "/?edd_action=remove_development_token";
    $options['basicUrls']["devMode"] =  "wp-json/appbear/v1/dev-mode";
  }
}

if (isset($data['settingspage-demos']) && $data['settingspage-demos'] !== 'false') {
  $options['settingsPage']['demos'] = "true";
}
