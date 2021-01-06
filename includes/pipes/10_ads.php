<?php

/**
 * Ads (adMob) arrays
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


$options['adMob'] = array(
  'bannerAndroidId' => '',
  'bannerIosId' => '',
  'interstitialAndroidId' => '',
  'interstitialIosId' => '',
);

if (isset($data['local-admob_banner']) && $data['local-admob_banner'] !== 'false') {
  $options['adMob']['bannerAndroidId'] = $data['advertisement_android_banner_id_text'];
  $options['adMob']['bannerIosId'] = $data['advertisement_ios_banner_id_text'];
}

if (isset($data['local-advertisement_admob_interstatial']) && $data['local-advertisement_admob_interstatial'] !== 'false') {
  $options['adMob']['interstitialAndroidId'] = $data['advertisement_android_interstatial_id_text'];
  $options['adMob']['interstitialIosId'] = $data['advertisement_ios_interstatial_id_text'];
}
