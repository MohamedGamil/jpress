<?php


/**
 * AppBear Notices Helpers
 */
class Appbear_Notice {
  const OPTION_KEY = 'appbear_flash_notices';

  /**
   * Instant notices bag.
   *
   * @var array
   */
  static protected $_notices = [];

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

    static::$_localInstance = new Appbear_Notice();
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
   * Add New Info Notice / Alert
   *
   * @param string $message
   * @return void
   */
  public static function info($message) {
    static::notice('info', $message);
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
   * @param string $isInstant  Is alert instant (Not Flash)
   * @return void
   */
  public static function notice($type, $message, $isDismissable = true, $isInstant = false) {
    $notices = $isInstant === true ? static::$_notices : static::_getAll();

    $notices[] = array(
      'type' => $type,
      'message' => $message,
      'dismissable' => $isDismissable,
    );

    if ($isInstant === true) {
      static::$_notices = $notices;
    } else {
      update_option( static::OPTION_KEY, $notices);
    }
  }

  /**
   * Get All Notices
   *
   * @return array
   */
  protected static function _getAll() {
    $notices = maybe_unserialize(get_option( static::OPTION_KEY, ''));
    return is_array($notices) ? $notices : array();
  }

  /**
   * Purge All Notices
   *
   * @return void
   */
  protected static function _purge() {
    delete_option(static::OPTION_KEY);
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

    add_action('admin_notices', array( &$this, 'display_notices' ));
  }

  /**
   * Display Notices
   *
   * @return void
   */
  public function display_notices() {
    $html = '';
    $notices = array_merge( static::$_notices, $this->_getNotices() );

    foreach ( $notices as $notice ) {
      $html .= appbear_get_template( 'alerts/notice', $notice );
    }

    if (empty($html) === false) {
      echo $html;
    }

    $this->_cleanup();
  }

  /**
   * Cleanup
   *
   * @return void
   */
  protected function _cleanup() {
    return static::_purge();
  }

  /**
   * Get Flash Notices
   *
   * @return array
   */
  private function _getNotices() {
    return static::_getAll();
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
