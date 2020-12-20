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
    // ...
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

    $requestOpts = array(
      'timeout' => static::TIMEOUT_DURATION,
      'sslverify' => static::VERYIFY_SSL,
      'body' => $api_params,
    );

    return wp_remote_post( APPBEAR_STORE_URL, $requestOpts );
  }

  /**
   * Send a new push notification
   *
   * @param string $title
   * @param string $body
   * @param string $type Post type
   * @param string $ID Post ID
   * @return array|WP_ERROR The response or WP_Error on failure.
   */
  public static function send_notification($title, $body, $type = 'post', $ID = '') {
    $params = array(
      'title' => $title,
      'body' => $body,
      'data' => array(
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        'type' => $type,
        'id' => $ID,
      ),
    );

    $endpoint = APPBEAR_STORE_URL . '/wp-json/appbear-edd-addon/v1/notifications';
    $public_key = appbear_get_public_key();
    $opts = array(
      'timeout' => static::TIMEOUT_DURATION,
      'sslverify' => static::VERYIFY_SSL,
      'body' => json_encode($params),
      'headers' => array(
        'Content-Type' => 'application/json; charset=utf-8',
        'X-AppBear-Key' => $public_key,
      ),
    );

    return wp_remote_post( $endpoint, $opts );
  }
}
