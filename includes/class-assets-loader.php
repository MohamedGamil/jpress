<?php

namespace Appbear\Includes;

class AssetsLoader {
    public static $version;
    public static $js_loaded = false;
    public static $css_loaded = false;
    protected $jpress;
    protected $object_type;

    public function __construct( $version = '1.0.0' ){
        self::$version = $version;

        add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ), 10 );
    }

    public function load_assets( $hook ){
        self::load_google_fonts();
        self::load_scripts();
        self::load_styles();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Google Fonts
    |---------------------------------------------------------------------------------------------------
    */

    private static function load_google_fonts(){
        wp_enqueue_style( 'jpress-open-sans', '//fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i,700', false );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Add scripts
    |---------------------------------------------------------------------------------------------------
    */
    private static function load_scripts(){
        if( self::$js_loaded ){
            return;
        }


        //Libs
        // wp_register_script( 'jpress-spinner', JPRESS_URL .'libs/spinner/spinner.min.js', array(), self::$version );
        // wp_enqueue_script( 'jpress-spinner' );

        // wp_register_script( 'jpress-colorpicker', JPRESS_URL .'libs/tinyColorPicker/jqColorPicker.min.js', array(), self::$version );
        // wp_enqueue_script( 'jpress-colorpicker' );

        wp_register_script( 'jpress-radiocheckbox', JPRESS_URL . 'libs/icheck/icheck.min.js', array(), self::$version );
        wp_enqueue_script( 'jpress-radiocheckbox' );

        // wp_register_script( 'jpress-sui-dropdown', JPRESS_URL .'libs/semantic-ui/components/dropdown.min.js', array(), self::$version );
        // wp_enqueue_script( 'jpress-sui-dropdown' );

        // wp_register_script( 'jpress-sui-transition', JPRESS_URL .'libs/semantic-ui/components/transition.min.js', array(), self::$version );
        // wp_enqueue_script( 'jpress-sui-transition' );

        // wp_register_script( 'jpress-tipso', JPRESS_URL .'libs/tipso/tipso.min.js', array(), self::$version );
        // wp_enqueue_script( 'jpress-tipso' );

        wp_register_script( 'jpress-ace-editor', JPRESS_URL . 'libs/ace/ace.js', array(), self::$version );
        wp_enqueue_script( 'jpress-ace-editor' );

        // wp_register_script( 'jpress-switcher', JPRESS_URL .'libs/jpress-switcher/jpress-switcher.js', array(), self::$version );
        // wp_enqueue_script( 'jpress-switcher' );

        // wp_register_script( 'jpress-img-selector', JPRESS_URL .'libs/jpress-image-selector/jpress-image-selector.js', array(), self::$version );
        // wp_enqueue_script( 'jpress-img-selector' );

        // wp_register_script( 'jpress-tab', JPRESS_URL .'libs/jpress-tabs/jpress-tabs.js', array(), self::$version );
        // wp_enqueue_script( 'jpress-tab' );

        // wp_register_script( 'jpress-confirm', JPRESS_URL .'libs/jpress-confirm/jpress-confirm.js', array(), self::$version );
        // wp_enqueue_script( 'jpress-confirm' );

        //tagsinput script
        wp_register_script( 'tagsinput', JPRESS_URL .'libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'tagsinput' );

        //Wordpress scripts
        $deps_scripts = array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jpress-libs' );
        if( function_exists( 'wp_enqueue_media' ) ){
            wp_enqueue_media();
        } else{
            wp_enqueue_script( 'media-upload' );
        }

        //deep linking
        wp_register_script( 'browser-deeplink', JPRESS_URL . 'js/browser-deeplink.js', array( 'jquery' ) );
        wp_enqueue_script( 'browser-deeplink' );

        //Appbear scripts
        wp_register_script( 'jpress-libs', JPRESS_URL . 'js/jpress-libs.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'jpress-libs' );

        wp_register_script( 'jpress', JPRESS_URL . 'js/jpress.js', $deps_scripts );
        wp_enqueue_script( 'jpress' );

        wp_register_script( 'jpress-events', JPRESS_URL . 'js/jpress-events.js', $deps_scripts );
        wp_enqueue_script( 'jpress-events' );

        wp_localize_script( 'jpress', 'JPRESS_JS', self::localization() );

        self::$js_loaded = true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Add styles
    |---------------------------------------------------------------------------------------------------
    */
    private static function load_styles(){
      if ( self::$css_loaded ) {
        return;
      }

      wp_register_style( 'jpress-sui-icon', JPRESS_URL . 'libs/semantic-ui/components/icon.min.css', array(), self::$version );
      wp_enqueue_style( 'jpress-sui-icon' );

      wp_register_style( 'jpress-sui-flag', JPRESS_URL . 'libs/semantic-ui/components/flag.min.css', array(), self::$version );
      wp_enqueue_style( 'jpress-sui-flag' );

      wp_register_style( 'jpress-sui-dropdown', JPRESS_URL . 'libs/semantic-ui/components/dropdown.min.css', array(), self::$version );
      wp_enqueue_style( 'jpress-sui-dropdown' );

      wp_register_style( 'jpress-sui-transition', JPRESS_URL . 'libs/semantic-ui/components/transition.min.css', array(), self::$version );
      wp_enqueue_style( 'jpress-sui-transition' );

      wp_register_style( 'jpress-sui-menu', JPRESS_URL . 'libs/semantic-ui/components/menu.min.css', array(), self::$version );
      wp_enqueue_style( 'jpress-sui-menu' );

      wp_register_style( 'jpress-tipso', JPRESS_URL . 'libs/tipso/tipso.min.css', array(), self::$version );
      wp_enqueue_style( 'jpress-tipso' );

      wp_register_style( 'jpress-switcher', JPRESS_URL . 'libs/jpress-switcher/jpress-switcher.css', array(), self::$version );
      wp_enqueue_style( 'jpress-switcher' );

      wp_register_style( 'jpress-radiocheckbox', JPRESS_URL . 'libs/icheck/skins/flat/_all.css', array(), self::$version );
      wp_enqueue_style( 'jpress-radiocheckbox' );

      //tagsinput style
      wp_register_style( 'tagsinput', JPRESS_URL .'libs/bootstrap-tagsinput/bootstrap-tagsinput.css', array(), self::$version );
      wp_enqueue_style( 'tagsinput' );

      wp_register_style( 'tagsinput-typeahead', JPRESS_URL .'libs/bootstrap-tagsinput/bootstrap-tagsinput-typeahead.css', array(), self::$version );
      wp_enqueue_style( 'tagsinput-typeahead' );

      //Main styles
      wp_register_style( 'jpress-icons', JPRESS_URL . 'css/jpress-icons.css', array(), self::$version );
      wp_enqueue_style( 'jpress-icons' );

      // FontAwesome
      switch(true) {
        // case Functions::is_fontawesome_version( '5.15.1' ) === true:
          // wp_register_style( 'jpress-font-awesome', JPRESS_URL . 'css/font-awesome-5.6.3.css', array(), self::$version );
          // break;
        case Functions::is_fontawesome_version( '5.x' ) === true:
            wp_register_style( 'jpress-font-awesome', JPRESS_URL . 'css/fa-5.15.1.css', array(), self::$version );
          break;
        default:
          wp_register_style( 'jpress-font-awesome', JPRESS_URL . 'css/font-awesome.css', array(), self::$version );
          break;
      }

      wp_enqueue_style( 'jpress-font-awesome' );

      wp_register_style( 'jpress-font-spotlayer', JPRESS_URL . 'css/font-spotlayer.css', array(), self::$version );
      wp_enqueue_style( 'jpress-font-spotlayer' );

      if(is_rtl()){
          wp_register_style( 'jpress-rtl', JPRESS_URL . 'css/jpress-rtl.css', array(), self::$version );
          wp_enqueue_style( 'jpress-rtl' );
      }else{
          wp_register_style( 'jpress', JPRESS_URL . 'css/jpress.css', array(), self::$version );
          wp_enqueue_style( 'jpress' );
      }

      self::$css_loaded = true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | WP Localization
    |---------------------------------------------------------------------------------------------------
    */
    public static function localization(){
        $l10n = array(
          'ajax_url' => admin_url( 'admin-ajax.php' ),
          'ajax_nonce' => wp_create_nonce( 'jpress_ajax_nonce' ),
          'text' => array(
            'popup' => array(
                'accept_button' => _x( 'Accept', 'Button - On confirm popup', 'jpress' ),
                'cancel_button' => _x( 'Cancel', 'Button - On confirm popup', 'jpress' ),
            ),
            'remove_item_popup' => array(
                'title' => _x( 'Delete', 'Title - On popup "remove item"', 'jpress' ),
                'content' => _x( 'Are you sure you want to delete?', 'Content - On popup "remove item"', 'jpress' ),
            ),
            'validation_url_popup' => array(
                'title' => _x( 'Validation', 'Title - On popup "Validation url"', 'jpress' ),
                'content' => _x( 'Please enter a valid url', 'Content - On popup "Validation url"', 'jpress' ),
            ),
            'reset_popup' => array(
                'title' => _x( 'Reset values', 'Title - On popup "Reset values"', 'jpress' ),
                'content' => _x( 'Are you sure you want to reset all options to the default values? All saved data will be lost.', 'Content - On popup "Reset values"', 'jpress' ),
            ),
            'import_popup' => array(
                'title' => _x( 'Import values', 'Title - On popup "Import values"', 'jpress' ),
                'content' => _x( 'Are you sure you want to import all options? All current values will be lost and well be overwritten.', 'Content - On popup "Import values"', 'jpress' ),
            ),
          ),
          '_field_icons' => array(),
        );

        return $l10n;
    }


}
