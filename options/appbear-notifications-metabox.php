<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use Appbear\Includes\AppbearAPI;


/**
 * AppBear_Notifications_Metabox Class
 *
 * This class handles push notifications fields implementation
 *
 *
 * @since 0.0.5
 */
class AppBear_Notifications_Metabox {
  const OPTION_KEY = 'appbear_post_push_notifications';

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

    static::$_localInstance = new AppBear_Notifications_Metabox();
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

    add_action( 'add_meta_boxes', array ( $this, 'add_meta_box' ), 1, 2 );
    add_action( 'save_post', array ( $this, 'save_post' ), 1, 3 );
    add_action( 'admin_enqueue_scripts', array ( $this, 'enqueue_scripts' ), 1, 2 );
  }

  /**
   * Add a meta box
   *
   * @return void
   */
  public function add_meta_box( $taxonomy ) {
    add_meta_box(
      'appbear-notifications-metabox',
      esc_html__( 'AppBear Push Notifications', 'textdomain' ),
      array( $this, 'display_meta_box' ),
      'post',
      'side',
      'high'
    );
  }

  /**
   * Displays the meta box
   *
   * @param mixed $post
   * @return void
   */
  public function display_meta_box( $post ) {
    $data = array(
      'post' => $post,
    );

    echo appbear_get_template('metabox/notifications', $data);
  }

  /**
   * Save post action hook
   *
   * @param integer $postId
   * @return void
   */
  public function save_post( $postID, $post, $update ) {
    // Skip updates and auto-saves
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || $update === true || empty($_POST)) {
      return;
    }

    // Get actual post ID if current save is for a revision
    if ( $parentID = wp_is_post_revision( $postID ) ) {
      $postID = $parentID;
    }

    $inputs = [
      'appbear_notifications_send',
      'appbear_notifications_title',
      'appbear_notifications_message',
    ];

    foreach ($inputs as $key => $field) {
      $fieldKey = str_replace('appbear_notifications_', '', $field);
      $inputs[$fieldKey] = isset($_POST[$field]) ? trim(sanitize_text_field($_POST[$field])) : '';
      unset($inputs[$key]);
    }

    if ($inputs['send'] !== 'on') {
      return;
    }

    if ( empty($inputs['title']) ) {
      // TODO: Should display a warning message that notification title it empty and required.
      return;
    }

    $response = AppbearAPI::send_notification($inputs['title'], $inputs['body']);

    if ( is_wp_error( $response ) ) {
      $data = json_decode( wp_remote_retrieve_body( $response ) );
      dd(0, $data);
      // TODO: Display error message and possibly store response items regarding request limits in wp options

      // $message = __( 'An error occurred, please try again.' );
      // update_option( 'appbear_license_status', json_decode($response['body'])->license );

      // add_settings_error( $this->settings_notice_key(), $this->id, $message, 'error' );
      // set_transient( 'settings_errors', get_settings_errors(), 30 );
      // return false;
    }
    else {
      $data = json_decode( wp_remote_retrieve_body( $response ) );

      if ( isset($data['errors']) ) {
        // TODO: Errors which may include limits related, or delegated call errors
      }
      elseif ( isset($data['success']) ) {
        // TODO: Success state and update metadata
      }

      // TODO: Store reponse items and display a success message
      // $this->_update(..)
    }
  }

  /**
   * Enqueue Scripts
   */
  public function enqueue_scripts() {
    wp_enqueue_style( 'appbear-notifications-metabox', APPBEAR_URL . 'options/css/notifications_metabox.css' );
    wp_enqueue_script( 'appbear-notifications-metabox', APPBEAR_URL . 'options/js/notifications_metabox.js', array('jquery') );
  }

  /**
   * Get push notifications metadata
   *
   * @return array
   */
  protected function _get() {
    return $this->_metadata();
  }

  /**
   * Update push notifications metadata
   *
   * @param array $options New options
   * @return boolean
   */
  protected function _update(array $options = array()) {
    $meta = array_merge(
      $this->_metadata(),
      $options
    );

    return $this->_metadata($meta);
  }

  /**
   * Delete push notifications metadata
   *
   * @return array
   */
  protected function _omit() {
    return $this->_metadata([]);
  }

  /**
   * Can Initialize
   *
   * @return boolean
   */
  private function _canInit() {
    return is_admin() === true;
  }

  /**
   * Get / Update Post Push Notifications Meta Options
   *
   * @param array $newOptions Update metadata with given options array
   * @return array|boolean Metadata array or boolean if updating
   */
  private function _metadata(array $newOptions = null)
  {
    if (is_null($newOptions) === true) {
      return get_option( static::OPTION_KEY );
    }
    else {
      return update_option( static::OPTION_KEY, $newOptions );
    }
  }
}
