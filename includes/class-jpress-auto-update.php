<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Performs Automatic Updates.
 *
 * @link       http://jpress.dedulab.com
 * @since      0.2.7
 *
 * @package    JPress
 * @subpackage JPress/includes
 */


// Require the updater class
require_once JPRESS_INCLUDES_DIR . 'class-jpress-updater.php';


/**
 * Performs Automatic Updates.
 *
 * This class handles automatic updates and checking for newer version of JPress plugin.
 *
 * @since      0.2.7
 * @package    JPress
 * @subpackage JPress/includes
 * @author     JPress <info@jpress.dedulab.com>
 */
class JPress_Automatic_Updates {
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

    static::$_localInstance = new JPress_Automatic_Updates();
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
      'version' => JPRESS_VERSION,
      'item_id' => JPRESS_ITEM_ID,
      'license' => jpress_get_license_key(),
      'author' => 'JPress Team',
      'beta' => false,
    );

    // Setup the updater
    new JPress_Updater( JPRESS_STORE_URL, JPRESS_FC_FILE, $args );
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
