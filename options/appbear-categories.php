<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * AppBear_Categories Class
 *
 * This class handles categories fields implementation
 *
 *
 * @since 0.0.5
 */
class AppBear_Categories {
  const OPTION_KEY = 'appbear_categories_metadata';

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

    static::$_localInstance = new AppBear_Categories();
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

    add_action( 'category_add_form_fields', array ( $this, 'add_category_image' ), 1, 2 );
    add_action( 'created_category', array ( $this, 'save_category_image' ), 1, 2 );
    add_action( 'category_edit_form_fields', array ( $this, 'update_category_image' ), 1, 2 );
    add_action( 'edited_category', array ( $this, 'updated_category_image' ), 1, 2 );
    add_action( 'admin_enqueue_scripts', array ( $this, 'enqueue_scripts' ), 1, 2 );
  }

  /**
   * Add a form field in the new category page
   *
   * @param mixed $taxonomy
   * @return void
   */
  public function add_category_image( $taxonomy ) {
    $data = array( 'taxonomy' => $taxonomy );
    echo appbear_get_template('categories/category_image_field', $data);
  }

  /**
   * Edit the form field
   *
   * @param mixed $term
   * @param mixed $taxonomy
   * @return void
   */
  public function update_category_image( $term, $taxonomy ) {
    $catOpts = $this->_get($term->term_id);
    $image = isset($catOpts['image']) ? $catOpts['image'] : '';
    $data = array(
      'term' => $term,
      'taxonomy' => $taxonomy,
      'image' => $image,
    );

    echo appbear_get_template('categories/category_image_field_update', $data);
  }

  /**
   * Save the form field
   *
   * @param integer $term_id
   * @param integer $tt_id
   * @return void
   */
  public function save_category_image( $term_id, $tt_id ) {
    if( isset( $_POST['appbear-category-image-id'] ) && '' !== $_POST['appbear-category-image-id'] ){
      $image = $_POST['appbear-category-image-id'];

      $this->_update($term_id, array(
        'image' => $image,
      ));
    }
  }

  /**
   * Update the form field value
   *
   * @param [type] $term_id
   * @param [type] $tt_id
   * @return void
   */
  public function updated_category_image( $term_id, $tt_id ) {
    $this->_updateOrDelete($term_id, array(
      'image' => isset($_POST['appbear-category-image-id']) ? $_POST['appbear-category-image-id'] : '',
    ));
  }

  /**
   * Enqueue Deeplinking Scripts
   */
  public function enqueue_scripts() {
    wp_enqueue_media();
    wp_enqueue_script( 'appbear-browser-deeplink-init', APPBEAR_URL . 'options/js/cat_image.js', array('jquery') );

    // $deeplinkCss = APPBEAR_URL . 'options/css/deeplinking.css';
    // wp_enqueue_style( 'appbear-browser-deeplink-widget', $deeplinkCss );
  }

  /**
   * Get category options by ID
   *
   * @param integer $catID Category ID
   * @return array
   */
  protected function _get($catID) {
    $meta = $this->_metadata();
    $catID = absint($catID);

    return isset($meta[$catID]) ? $meta[$catID] : [];
  }

  /**
   * Update or delete-if-convenient category options by ID
   *
   * @param integer $catID Category ID
   * @param array $catOptions Category new options
   * @return array
   */
  protected function _updateOrDelete($catID, array $catOptions = array()) {
    $meta = $this->_metadata();
    $catID = absint($catID);

    if (empty($catOptions) && isset($meta[$catID])) {
      return $this->_omit( $catID );
    } elseif (empty($catOptions) === false) {
      return $this->_update( $catID, $catOptions );
    }
  }

  /**
   * Create or update a given category metadata by ID
   *
   * @param integer $catID Category ID
   * @param array $catOptions Category new options
   * @return array
   */
  protected function _update($catID, array $catOptions = array()) {
    $meta = $this->_metadata();
    $meta[ absint($catID) ] = $catOptions;

    return $this->_metadata($meta);
  }

  /**
   * Omit or delete a given category metadata by ID
   *
   * @param integer $catID Category ID
   * @return array
   */
  protected function _omit($catID) {
    $meta = $this->_metadata();

    if (empty($meta)) {
      return false;
    }

    unset($meta[ absint($catID) ]);

    return $this->_metadata($meta);
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
   * Get / Update Categories Meta Options
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
