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
   * Initialize Post
   */
  public function init_post() {

  }

  /**
   * Enqueue Deeplinking Scripts
   */
  public function enqueue_scripts() {
    $deeplinkingOpts = appbear_get_deeplinking_opts();
    $deeplinkJs1 = APPBEAR_URL . 'js/browser-deeplink.js';
    $deeplinkJs2 = APPBEAR_URL . 'options/js/deeplinking.js';
    $deeplinkCss = APPBEAR_URL . 'options/css/deeplinking.css';

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
        ios_url: "https://apps.apple.com/us/app/id'. $deeplinkingOpts->appid_ios .'",
        android_url: "https://play.google.com/store/apps/details?id='. $deeplinkingOpts->name_android .'",
        open: function () {
          deeplink.open("' . get_the_ID() . '");
        },
      };
    ');
  }

  private function _canInit() {
    return is_single() === true;
  }
}
