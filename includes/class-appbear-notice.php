<?php

namespace Appbear\Includes;


/**
 * AppBear Notices Helpers
 */
class AppbearNotice {
  /**
   * Notices bag.
   *
   * @var array
   */
  public static $notices = [];

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
  public static function run() {
    if (static::$_didInit === true && is_null(static::$_localInstance) === false) {
      return;
    }

    static::$_localInstance = new AppbearNotice();
    static::$_didInit = true;
  }

  /**
   * Add New Success Notice / Alert
   *
   * @param string $message
   * @return void
   */
  public static function success($message) {
    static::notice('success', $message);
  }

  /**
   * Add New Warning Notice / Alert
   *
   * @param string $message
   * @return void
   */
  public static function warning($message) {
    static::notice('warning', $message);
  }

  /**
   * Add New Error Notice / Alert
   *
   * @param string $message
   * @return void
   */
  public static function error($message) {
    static::notice('error', $message);
  }

  /**
   * Add New Notice / Alert
   *
   * @param string $type  Notice type
   * @param string $message  Message
   * @param string $isDismissable  Is alert dismissable
   * @return void
   */
  public static function notice($type, $message, $isDismissable = true) {
    static::$notices[] = array(
      'type' => $type,
      'message' => $message,
      'dismissable' => $isDismissable,
    );
  }


  /**
   * Class Constructor
   */
  public function __construct() {
    $this->init();
  }

  /**
   * Initialize Addon
   *
   * @return void
   */
  public function init() {
    if (static::$_didInit === true || $this->_canInit() === false) {
      return;
    }

    // dd(static::$notices);
    add_action('admin_notices', array( $this, 'display_notices' ));
  }

  /**
   * Display Notices
   *
   * @return void
   */
  public function display_notices() {
    $html = '';

    foreach (static::$notices as $notice) {
      $html .= appbear_get_template( 'alert/notice', $notice );

      echo $html;die;
    }

    if (empty($html) === false) {
      echo $html;
    }
  }

  /**
   * Can Initialize
   *
   * @return boolean
   */
  private function _canInit() {
    return is_admin() === true;
  }
}


// Run Class
AppbearNotice::run();
