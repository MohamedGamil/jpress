<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * JPress_Ads_Shortcode Class
 *
 * This class handles categories fields implementation
 *
 *
 * @since 0.2.2
 */
class JPress_Ads_Shortcode {
  const SHORTCODE_NAME = 'jpress_ad';
  const ALLOWED_AD_TYPES = [ 'adMob', 'htmlAd', 'imageAd' ];
  const AD_TYPES_FRIENDLY_NAMES = [
    'adMob' => [ 'admob', 'ad' ],
    'htmlAd' => [ 'html' ],
    'imageAd' => [ 'image', 'img' ],
  ];
  const PARAGRAPH_DELIMITER = '</p>';

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

    static::$_localInstance = new JPress_Ads_Shortcode();
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

    add_shortcode( static::SHORTCODE_NAME, array ( $this, 'jpress_ad_shortcode' ) );
    add_filter( 'the_content', array( $this, 'article_inline_ad' ) );
  }

  /**
   * Filter Post Content to insert inline ads, based on the global single in-post ad settings.
   *
   * @param string $content Post Content
   * @return string
   */
  public function article_inline_ad( $content ) {
    if ( ( is_singular('post') || $this->_isRestful() ) && ! is_admin() ) {
      $inPostAds = jpress_get_ads_in_post_options();

      if ($inPostAds->enabled === false) {
        return $content;
      }

      $delimiter = static::PARAGRAPH_DELIMITER;
      $paragraphs = explode($delimiter, $content);
      $adCode = $this->jpress_ad_shortcode((array) $inPostAds);

      foreach ( $paragraphs as $index => $paragraph ){
        if ( trim( $paragraph ) ) {
          $paragraphs[$index] .= $delimiter;
        }

        if ( $inPostAds->offset == ($index + 1) ) {
          $paragraphs[$index] .= $adCode;
        }
      }

      $content = implode( '', $paragraphs );
    }

    return $content;
  }

  /**
   * Render JPress Ads Shortcode
   *
   * @param array $attributes
   * @param string $content
   * @return string
   */
  public function jpress_ad_shortcode( $attributes, $content = null ) {
    $attributes = shortcode_atts( array(
      'type' => 'adMob',
      'size' => 'banner',
      'content' => '',
      'image' => '',
      'action' => '',
      'target' => '',
      'targetTitle' => '',
    ), $attributes );

    if ( is_null($content) === true ) {
      $content = isset($attributes['content']) && empty($attributes['content']) === false ? $attributes['content'] : '';
    }

    $attributes['type'] = $this->_parseFriendlyType($attributes['type']);
    $attributes['content'] = html_entity_decode($content);

    // NOTE: Debug line..
    // dd($attributes);

    return jpress_get_template('shortcodes/ads', $attributes);
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

    return str_replace('PostLayout.', '', $type);
  }

  /**
   * Is a RESTful API Request?
   *
   * @return boolean
   */
  private function _isRestful() {
    return strpos($_SERVER[ 'REQUEST_URI' ], '/wp-json/') !== false;
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
