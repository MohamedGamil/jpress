<?php

namespace Appbear\Includes;


/**
 * AppBear RESTFul API Adapter
 */
class AppbearAPI {
  const TIMEOUT_DURATION = 15;
  const VERYIFY_SSL = false;

  /**
   * Class constructor
   */
	public function __construct( $args = array() ) {

  }

  /**
   * Check a given license key for validity
   *
   * @param string $licenseKey
   * @return array|WP_ERROR The response or WP_Error on failure.
   */
  public static function check_license($licenseKey) {
    $api_params = array(
      'edd_action' => 'check_license',
      'license' => $licenseKey,
      'item_name' => urlencode( APPBEAR_ITEM_NAME ),
      'url'       => home_url()
    );

    return wp_remote_post( APPBEAR_STORE_URL, array( 'timeout' => static::TIMEOUT_DURATION, 'sslverify' => static::VERYIFY_SSL, 'body' => $api_params ) );
  }

  /**
   * Activate a given license key
   *
   * @param string $licenseKey
   * @return array|WP_ERROR The response or WP_Error on failure.
   */
  public static function activate_license($licenseKey) {
    $api_params = array(
      'edd_action' => 'activate_license',
      'license'    => $licenseKey,
      'item_name'  => urlencode( APPBEAR_ITEM_NAME ), // the name of our product in EDD
      'url'        => home_url()
    );

    return wp_remote_post( APPBEAR_STORE_URL, array( 'timeout' => static::TIMEOUT_DURATION, 'sslverify' => static::VERYIFY_SSL, 'body' => $api_params ) );
  }
}
