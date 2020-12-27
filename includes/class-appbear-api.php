<?php

namespace Appbear\Includes;


/**
 * AppBear RESTFul API Adapter
 */
class AppbearAPI {
  const TIMEOUT_DURATION = 15;
  const VERYIFY_SSL = false;

  /**
   * Options & Headers internal stores
   *
   * @var array
   */
  protected static $_opts = array();
  protected static $_headers = array();

  /**
   * Check a given license key for validity
   *
   * @TODO: Needs further refactoring
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

    return wp_remote_post( APPBEAR_STORE_URL, array(
      'timeout' => static::TIMEOUT_DURATION,
      'sslverify' => static::VERYIFY_SSL,
      'body' => $api_params,
    ));
  }

  /**
   * Activate a given license key
   *
   * @TODO: Needs further refactoring
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
    $endpoint = '/wp-json/appbear-edd-addon/v1/notifications';
    $params = array(
      'title' => $title,
      'body' => $body,
      'notification' => array(
        'title' => $title,
        'body' => $body,
      ),
      'data' => array(
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        'title' => $title,
        'type' => $type,
        'id' => $ID,
      ),
    );

    return static::_send( $endpoint, $params );
  }

  /**
   * Apply request common headers & options
   *
   * @param boolean $includeAuthHeaders  Include authentication headers (TRUE / FALSE)
   * @param boolean $forceDisableSSLVerification  Force disable SSL Verification (TRUE / FALSE)
   * @param boolean $timeoutDuration  Override default timeout duration
   */
  protected static function _headers($includeAuthHeaders = true, $forceDisableSSLVerification = null, $timeoutDuration = null) {
    $SSLVerification = is_null($forceDisableSSLVerification) ? static::VERYIFY_SSL : $forceDisableSSLVerification === true;
    $timeout = is_null($timeoutDuration) ? static::TIMEOUT_DURATION : absint($timeoutDuration);
    $headers = array(
      'Content-Type' => 'application/json; charset=utf-8',
    );

    if ($includeAuthHeaders) {
      $headers['X-AppBear-Key'] = appbear_get_public_key();
    }

    static::$_opts = array(
      'timeout' => $timeout,
      'sslverify' => $SSLVerification,
    );

    static::$_headers = $headers;
  }

  /**
   * Send request using `wp_remote_post`,
   *
   * @param string $endpoint Endpoint relative URL
   * @param array $body Request parameters
   * @param boolean $includeAuthHeaders Include authentication headers (TRUE / FALSE)
   * @return array|WP_ERROR The response or WP_Error on failure.
   */
  protected static function _send($endpoint = '', $body = array(), $includeAuthHeaders = true) {
    $headers = static::_headers($includeAuthHeaders);
    $opts = array_merge(
      static::$_opts,
      array(
        'headers' => $headers,
        'body' => json_encode($body),
      )
    );

    return wp_remote_post( APPBEAR_STORE_URL . $endpoint, $opts );
  }
}
