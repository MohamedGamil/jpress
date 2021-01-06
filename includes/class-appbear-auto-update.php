<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Performs Automatic Updates.
 *
 * @link       http://appbear.io
 * @since      0.2.7
 *
 * @package    App_Bear
 * @subpackage App_Bear/includes
 */


// Require the updater class
require_once APPBEAR_INCLUDES_DIR . 'class-appbear-updater.php';


/**
 * Performs Automatic Updates.
 *
 * This class handles automatic updates and checking for newer version of AppBear plugin.
 *
 * @since      0.2.7
 * @package    App_Bear
 * @subpackage App_Bear/includes
 * @author     AppBear <info@appbear.io>
 */
class App_Bear_Automatic_Updates {
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
	 *
	 * @since    0.2.7
	 */
  static public function run() {
    if (static::$_didInit === true && is_null(static::$_localInstance) === false) {
      return;
    }

    static::$_localInstance = new App_Bear_Automatic_Updates();
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

    $this->_initUpdater();
  }

  /**
   * Initialize the updater. Hooked into `init` to work with the
   * wp_version_check cron job, which allows auto-updates.
   */
  private function _initUpdater() {
    // To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
    $doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;

    if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
      return;
    }

    $args = array(
      'version' => APPBEAR_VERSION,
      'item_id' => APPBEAR_ITEM_ID,
      'license' => appbear_get_license_key(),
      'author' => 'AppBear Team',
      'beta' => false,
    );

    // Setup the updater
    new App_Bear_Updater( APPBEAR_STORE_URL, APPBEAR_FC_FILE, $args );
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
