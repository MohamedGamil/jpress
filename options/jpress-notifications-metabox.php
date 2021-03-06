<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use JPress\Includes\JPressAPI;


/**
 * JPress_Notifications_Metabox Class
 *
 * This class handles push notifications fields implementation
 *
 *
 * @since 0.0.5
 */
class JPress_Notifications_Metabox {
  const OPTION_KEY = 'jpress_push_notifications_stats';
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

    static::$_localInstance = new JPress_Notifications_Metabox();
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
    // add_action( 'wp_insert_post_empty_content', array ( $this, 'save_post_validation' ), PHP_INT_MAX, 2 );
    add_action( 'enqueue_block_editor_assets', array ( $this, 'enqueue_scripts' ), 1, 2 );
  }

  /**
   * Add a meta box
   *
   * @return void
   */
  public function add_meta_box( $taxonomy ) {
    add_meta_box(
      'jpress-notifications-metabox',
      esc_html__( 'JPress Push Notifications', 'jpress' ),
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

    echo jpress_get_template('metabox/notifications', $data);
  }

  public function save_post_validation($maybeEmpty, $post) {
    if (empty($_POST) === true) {
      return false;
    }

    // dd($post);
    return true;
  }

  /**
   * Save post action hook
   *
   * @param integer $postId
   * @return void
   */
  public function save_post( $postID, $post, $update ) {
    // Skip auto-saves and requests with an invalid license state
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || empty($_POST) === true || $this->_isValidLicense() === false /* || current_user_can( 'edit_post', $postID ) === false */ ) {
      return;
    }

    // Get actual post ID if current save is for a revision
    if ( $parentID = wp_is_post_revision( $postID ) ) {
      $postID = $parentID;
    }

    $fields = [
      'jpress_notifications_send',
      'jpress_notifications_title',
      'jpress_notifications_message',
    ];

    $inputs = [];

    foreach ($fields as $field) {
      $key = str_replace('jpress_notifications_', '', $field);
      $inputs[$key] = isset($_POST[$field]) ? trim(sanitize_text_field($_POST[$field])) : '';
    }

    if ($inputs['send'] !== 'on') {
      return true;
    }

    if ( empty($inputs['title']) === true || empty($inputs['message']) === true ) {
      $this->_serveSubmitError('Input Error! Please submit notification title and message.');
      return false;
    }

    $response = JPressAPI::send_notification( $inputs['title'], $inputs['message'], 'post', $postID );

    // NOTE: Debug line
    // dd( $inputs, $response );

    if ( is_wp_error( $response ) === false ) {
      $data = json_decode( wp_remote_retrieve_body( $response ), true );

      if ( isset($data['success']) && $data['success'] === true ) {
        $usageData = isset($data['data']) && is_null($data['data']) === false ? $data['data'] : [
          'remaining' => -1,
          'sent_count' => -1,
          'plan_total' => -1,
        ];

        foreach ($usageData as $key => $opt) {
          $opt = is_scalar($opt) ? $opt : -1;
          $usageData[$key] = max( $opt * 1, -1 );
        }

        // NOTE: Debug line
        // dd( $usageData );

        $this->_update($usageData);
        $this->_serveSuccessMessage();
        return true;
      }
    }

    $this->_serveSubmitError();

    return false;
  }

  /**
   * Enqueue Scripts
   */
  public function enqueue_scripts() {
    wp_enqueue_style( 'jpress-notifications-metabox-css', JPRESS_URL . 'options/css/notifications_metabox.css' );

    wp_enqueue_script(
      'jpress-notifications-metabox-js',
      JPRESS_URL . 'options/js/notifications_metabox.js',
      array( 'jquery', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-edit-post', 'word-count', )
    );
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

    // NOTE: Debug line..
    // dd($this->_metadata(), $data);

    return $data;
  }

  /**
   * Update push notifications metadata
   *
   * @param array $options New options
   * @return boolean
   */
  protected function _update(array $options = array()) {
    $oldData = $this->_metadata();
    $oldData = $oldData === false ? [] : $oldData;
    $meta = array_merge(
      $oldData,
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
    $message = __($message, 'jpress') ?? __('JPress Notification Error! Unable to send notification, please check your inputs and verify that your current plan allows it.', 'jpress');
    $error = new WP_Error(400, $message, $message);

    wp_die($message);
  }

  /**
   * Present an success message
   *
   * @FIXME: This is useless since WordPress does not appear to allow any server-side responses on post submission hook.
   * @return void
   */
  protected function _serveSuccessMessage($message = null) {
    $message = __($message, 'jpress') ?? __('JPress notification sent successfully.', 'jpress');

    // TODO: ...
    // jpress_notice($message);
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
    return jpress_check_license();
  }
}
