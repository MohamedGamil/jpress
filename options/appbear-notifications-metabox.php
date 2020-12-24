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
  const DISABLE_IF_UPDATING = true;

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
    $updating = isset($_GET['post'], $_GET['action']) && $_GET['action'] === 'edit';
    $stats = $this->_get();
    $data = array(
      'stats' => $stats,
      'post' => $post,
      'checked' => $updating === false,
      'activated' => $this->_isValidLicense() === true,
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
    // Skip auto-saves and requests with an invalid license state
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || empty($_POST) === true || $this->_isValidLicense() === false ) {
      return;
    }

    // Get actual post ID if current save is for a revision
    if ( $parentID = wp_is_post_revision( $postID ) ) {
      $postID = $parentID;
    }

    $fields = [
      'appbear_notifications_send',
      'appbear_notifications_title',
      'appbear_notifications_message',
    ];

    $inputs = [];

    foreach ($fields as $field) {
      $key = str_replace('appbear_notifications_', '', $field);
      $inputs[$key] = isset($_POST[$field]) ? trim(sanitize_text_field($_POST[$field])) : '';
    }

    if ($inputs['send'] !== 'on') {
      return;
    }

    if ( empty($inputs['title']) === true ) {
      $this->_serveSubmitError('Input Error! Please submit at least a notification title, and you may add a notification message.');
      return;
    }

    $response = AppbearAPI::send_notification( $inputs['title'], $inputs['body'], 'post', $postID );

    // NOTE: Debug line
    dd( $response );

    if ( is_wp_error( $response ) === false ) {
      $data = json_decode( wp_remote_retrieve_body( $response ) );

      if ( isset($data['success']) && $data['success'] ) {
        $usageData = isset($data['data']) ? $data['data'] : [
          'remaining' => -1,
          'sent_count' => -1,
          'plan_total' => -1,
        ];

        foreach ($usageData as &$opt) {
          $opt = (int) $opt;
        }

        $this->_update($usageData);
        $this->_serveSuccessMessage();
        return;
      }
    }

    $this->_serveSubmitError();
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
    $data = $this->_metadata();
    $data = $data !== false ? $data : [
      'remaining' => '?',
      'sent_count' => '?',
      'plan_total' => '?',
    ];

    foreach ($data as &$field) {
      $field = $field === '?' ? $field : (int) $field;
    }

    return $data;
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
   * Present an error message
   *
   * @return void
   */
  protected function _serveSubmitError($message = null) {
    $message = __($message, 'textdomain') ?? __('AppBear Notification Error! Unable to send notification, please check your inputs and verify that your current plan allows it.', 'textdomain');

    // TODO: ...
  }

  /**
   * Present an success message
   *
   * @return void
   */
  protected function _serveSuccessMessage($message = null) {
    $message = __($message, 'textdomain') ?? __('AppBear notification sent successfully.', 'textdomain');

    // TODO: ...
  }

  /**
   * Can Initialize
   *
   * @return boolean
   */
  private function _canInit() {
    $updating = isset($_GET['post'], $_GET['action']) && $_GET['action'] === 'edit' && empty($_POST);

    return is_admin() === true && (
      ( static::DISABLE_IF_UPDATING === true && $updating === false )
      || static::DISABLE_IF_UPDATING === false
    );
  }

  /**
   * Get / Update Post Push Notifications Meta Options
   *
   * @param array $newOptions Update metadata with given options array
   * @return array|boolean Metadata array or boolean if updating
   */
  private function _metadata(array $newOptions = null) {
    if (is_null($newOptions) === true) {
      return get_option( static::OPTION_KEY );
    }
    else {
      return update_option( static::OPTION_KEY, $newOptions, false );
    }
  }

  /**
   * Is license valid
   *
   * @return boolean
   */
  private function _isValidLicense() {
    return appbear_check_license();
  }
}
