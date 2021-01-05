<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * AppBear_Ads_Shortcode Class
 *
 * This class handles categories fields implementation
 *
 *
 * @since 0.2.2
 */
class AppBear_Ads_Shortcode {
  const SHORTCODE_NAME = 'appbear_ad';
  const ALLOWED_AD_TYPES = [ 'adMob', 'htmlAd', 'imageAd' ];
  const AD_TYPES_FRIENDLY_NAMES = [
    'adMob' => [ 'admob', 'ad' ],
    'htmlAd' => [ 'html' ],
    'imageAd' => [ 'image', 'img' ],
  ];

  /**
   * Internal initilization state &
   * internal singlton instance.
   *
   * @var boolean
   */
  static protected $_didInit = false;
  static protected $_localInstance = null;

  /**
   * Internal store of categories options.
   *
   * @var object
   */
  public $options = null;

  /**
   * Run hooks initilization
   */
  static public function run() {
    if (static::$_didInit === true && is_null(static::$_localInstance) === false) {
      return;
    }

    static::$_localInstance = new AppBear_Ads_Shortcode();
    static::$_didInit = true;
  }

  /**
   * Class Constructor
   */
  public function __construct() {
    $this->init();
  }

  /**
   * Initialization
   */
  public function init() {
    if (static::$_didInit === true || $this->_canInit() === false) {
      return;
    }

    add_shortcode( static::SHORTCODE_NAME, array ( $this, 'appbear_ad_shortcode' ) );
  }

  /**
   * Render AppBear Ads Shortcode
   *
   * @param array $attributes
   * @param string $content
   * @return string
   */
  public function appbear_ad_shortcode( $attributes, $content = null ) {
    $attributes = shortcode_atts( array(
      'type' => 'adMob',
      'size' => 'banner',
      'content' => '',
      'image' => '',
      'action' => '',
      'target' => '',
    ), $attributes );

    if ( is_null($content) === true ) {
      $content = isset($attributes['content']) && empty($attributes['content']) === false ? $attributes['content'] : '';
    }

    $attributes['type'] = $this->_parseFriendlyType($attributes['type']);
    $attributes['content'] = html_entity_decode($content);

    // NOTE: Debug line..
    // dd($attributes);

    return appbear_get_template('shortcodes/ads', $attributes);
  }

  /**
   * Edit the form field
   *
   * @param mixed $term
   * @param mixed $taxonomy
   * @return void
   */
  protected function _parseFriendlyType( $type ) {
    if ( in_array($type, static::ALLOWED_AD_TYPES) === false ) {
      foreach ( static::AD_TYPES_FRIENDLY_NAMES as $type_ => $names ) {
        if (in_array($type, $names) === true) {
          $type = $type_;
          break;
        }
      }
    }

    return $type;
  }

  /**
   * Can Initialize
   *
   * @return boolean
   */
  private function _canInit() {
    return true;
  }
}
