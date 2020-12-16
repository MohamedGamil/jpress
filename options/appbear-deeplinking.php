<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * AppBear_Deeplinking Class
 *
 * This class handles deeplinking integration
 *
 *
 * @since 0.0.5
 */
class AppBear_Deeplinking {
  /**
   * Internal initilization state &
   * internal singlton instance.
   *
   * @var boolean
   */
  static protected $_didInit = false;
  static protected $_localInstance = null;

  /**
   * Internal store of deeplinking options.
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

    static::$_localInstance = new AppBear_Deeplinking();
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
    if (static::$_didInit === true) {
      return;
    }

    add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ));
  }

  /**
   * Initialize options
   */
  public function init_options() {
    $this->options = appbear_get_deeplinking_opts();
  }

  /**
   * Enqueue Deeplinking Scripts
   */
  public function enqueue_scripts() {
    $this->init_options();

    $deeplinkingOpts = $this->options;
    $deeplinkJs1 = APPBEAR_URL . 'js/browser-deeplink.js';
    $deeplinkJs2 = APPBEAR_URL . 'options/js/deeplinking.js';
    $deeplinkCss = APPBEAR_URL . 'options/css/deeplinking.css';
    $baseDeeplinkURL = $this->_getDeeplink();
    $deeplinkURL = $this->_getDeeplink( 'post', get_the_ID() );

    if ($deeplinkingOpts->widget_enabled !== 'true' || $this->_canInit() === false) {
      return;
    }

    wp_enqueue_style( 'appbear-browser-deeplink-widget', $deeplinkCss );
    wp_enqueue_script( 'appbear-browser-deeplink-lib', $deeplinkJs1, array('jquery') );
    wp_register_script( 'appbear-browser-deeplink', '' );
    wp_enqueue_script( 'appbear-browser-deeplink', array('jquery') );
    wp_enqueue_script( 'appbear-browser-deeplink-init', $deeplinkJs2, array('jquery') );

    wp_add_inline_script('appbear-browser-deeplink', '
      deeplink.setup({
          iOS: {
            appId: "' . $deeplinkingOpts->appid_ios . '",
            appName: "' . $deeplinkingOpts->name_ios . '"
          },
          android: {
            appId: "' . $deeplinkingOpts->name_android . '",
          },
      });

      window.AppBear_Deeplinking = {
        base_url: "' . $baseDeeplinkURL . '",
        deeplink_url: "' . $deeplinkURL . '",
        ios_url: "https://apps.apple.com/us/app/id'. $deeplinkingOpts->appid_ios .'",
        android_url: "https://play.google.com/store/apps/details?id='. $deeplinkingOpts->name_android .'",
        fg_color: "'. $deeplinkingOpts->widget_fg_color .'",
        bg_color: "'. $deeplinkingOpts->widget_bg_color .'",
        open: function () {
          console.log("Opening Deeplink URL: \"'. $deeplinkURL .'\" ");
          // deeplink.open("' . $deeplinkURL . '");
          window.location = "'. $deeplinkURL .'";
        },
      };
    ');
  }

  /**
   * Get Full Deeplink URL
   *
   * @param string $type  Post Type
   * @param string|integer $ID  Post ID
   * @return string
   */
  protected function _getDeeplink($type = null, $ID = null) {
    $baseURL = $this->options->scheme_url;
    $deeplinkURL = $baseURL . '/?type=%s&id=%s';

    if ( is_null($type) && is_null($ID) ) {
      return $baseURL;
    }

    // TODO: Check valid post types

    return sprintf( $deeplinkURL, trim($type), trim($ID) );
  }

  /**
   * Can Initialize
   *
   * @return boolean
   */
  private function _canInit() {
    return is_single() === true;
  }
}
