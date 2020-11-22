<?php namespace Appbear\Includes;

class AssetsLoader {
    public static $version;
    public static $js_loaded = false;
    public static $css_loaded = false;
    protected $appbear;
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
        wp_enqueue_style( 'appbear-open-sans', '//fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i,700', false );
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
        // wp_register_script( 'appbear-spinner', APPBEAR_URL .'libs/spinner/spinner.min.js', array(), self::$version );
        // wp_enqueue_script( 'appbear-spinner' );

        // wp_register_script( 'appbear-colorpicker', APPBEAR_URL .'libs/tinyColorPicker/jqColorPicker.min.js', array(), self::$version );
        // wp_enqueue_script( 'appbear-colorpicker' );

        wp_register_script( 'appbear-radiocheckbox', APPBEAR_URL . 'libs/icheck/icheck.min.js', array(), self::$version );
        wp_enqueue_script( 'appbear-radiocheckbox' );

        // wp_register_script( 'appbear-sui-dropdown', APPBEAR_URL .'libs/semantic-ui/components/dropdown.min.js', array(), self::$version );
        // wp_enqueue_script( 'appbear-sui-dropdown' );

        // wp_register_script( 'appbear-sui-transition', APPBEAR_URL .'libs/semantic-ui/components/transition.min.js', array(), self::$version );
        // wp_enqueue_script( 'appbear-sui-transition' );

        // wp_register_script( 'appbear-tipso', APPBEAR_URL .'libs/tipso/tipso.min.js', array(), self::$version );
        // wp_enqueue_script( 'appbear-tipso' );

        wp_register_script( 'appbear-ace-editor', APPBEAR_URL . 'libs/ace/ace.js', array(), self::$version );
        wp_enqueue_script( 'appbear-ace-editor' );

        // wp_register_script( 'appbear-switcher', APPBEAR_URL .'libs/appbear-switcher/appbear-switcher.js', array(), self::$version );
        // wp_enqueue_script( 'appbear-switcher' );

        // wp_register_script( 'appbear-img-selector', APPBEAR_URL .'libs/appbear-image-selector/appbear-image-selector.js', array(), self::$version );
        // wp_enqueue_script( 'appbear-img-selector' );

        // wp_register_script( 'appbear-tab', APPBEAR_URL .'libs/appbear-tabs/appbear-tabs.js', array(), self::$version );
        // wp_enqueue_script( 'appbear-tab' );

        // wp_register_script( 'appbear-confirm', APPBEAR_URL .'libs/appbear-confirm/appbear-confirm.js', array(), self::$version );
        // wp_enqueue_script( 'appbear-confirm' );

        //tagsinput script
        wp_register_script( 'tagsinput', APPBEAR_URL .'libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'tagsinput' );

        //Wordpress scripts
        $deps_scripts = array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'appbear-libs' );
        if( function_exists( 'wp_enqueue_media' ) ){
            wp_enqueue_media();
        } else{
            wp_enqueue_script( 'media-upload' );
        }

        //deep linking
        wp_register_script( 'browser-deeplink', APPBEAR_URL . 'js/browser-deeplink.js', array( 'jquery' ) );
        wp_enqueue_script( 'browser-deeplink' );

        //Appbear scripts
        wp_register_script( 'appbear-libs', APPBEAR_URL . 'js/appbear-libs.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'appbear-libs' );

        wp_register_script( 'appbear', APPBEAR_URL . 'js/appbear.js', $deps_scripts );
        wp_enqueue_script( 'appbear' );

        wp_register_script( 'appbear-events', APPBEAR_URL . 'js/appbear-events.js', $deps_scripts );
        wp_enqueue_script( 'appbear-events' );

        wp_localize_script( 'appbear', 'APPBEAR_JS', self::localization() );

        self::$js_loaded = true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Add styles
    |---------------------------------------------------------------------------------------------------
    */
    private static function load_styles(){
        if( self::$css_loaded ){
            return;
        }

        wp_register_style( 'appbear-sui-icon', APPBEAR_URL . 'libs/semantic-ui/components/icon.min.css', array(), self::$version );
        wp_enqueue_style( 'appbear-sui-icon' );

        wp_register_style( 'appbear-sui-flag', APPBEAR_URL . 'libs/semantic-ui/components/flag.min.css', array(), self::$version );
        wp_enqueue_style( 'appbear-sui-flag' );

        wp_register_style( 'appbear-sui-dropdown', APPBEAR_URL . 'libs/semantic-ui/components/dropdown.min.css', array(), self::$version );
        wp_enqueue_style( 'appbear-sui-dropdown' );

        wp_register_style( 'appbear-sui-transition', APPBEAR_URL . 'libs/semantic-ui/components/transition.min.css', array(), self::$version );
        wp_enqueue_style( 'appbear-sui-transition' );

        wp_register_style( 'appbear-sui-menu', APPBEAR_URL . 'libs/semantic-ui/components/menu.min.css', array(), self::$version );
        wp_enqueue_style( 'appbear-sui-menu' );

        wp_register_style( 'appbear-tipso', APPBEAR_URL . 'libs/tipso/tipso.min.css', array(), self::$version );
        wp_enqueue_style( 'appbear-tipso' );

        wp_register_style( 'appbear-switcher', APPBEAR_URL . 'libs/appbear-switcher/appbear-switcher.css', array(), self::$version );
        wp_enqueue_style( 'appbear-switcher' );

        wp_register_style( 'appbear-radiocheckbox', APPBEAR_URL . 'libs/icheck/skins/flat/_all.css', array(), self::$version );
        wp_enqueue_style( 'appbear-radiocheckbox' );

        //tagsinput style
        wp_register_style( 'tagsinput', APPBEAR_URL .'libs/bootstrap-tagsinput/bootstrap-tagsinput.css', array(), self::$version );
        wp_enqueue_style( 'tagsinput' );

        wp_register_style( 'tagsinput-typeahead', APPBEAR_URL .'libs/bootstrap-tagsinput/bootstrap-tagsinput-typeahead.css', array(), self::$version );
        wp_enqueue_style( 'tagsinput-typeahead' );


        //Main styles
        wp_register_style( 'appbear-icons', APPBEAR_URL . 'css/appbear-icons.css', array(), self::$version );
        wp_enqueue_style( 'appbear-icons' );

        if( Functions::is_fontawesome_version( '5.x' ) ){
            wp_register_style( 'appbear-font-awesome', APPBEAR_URL . 'css/font-awesome-5.6.3.css', array(), self::$version );
        } else{
            wp_register_style( 'appbear-font-awesome', APPBEAR_URL . 'css/font-awesome.css', array(), self::$version );
        }
        wp_enqueue_style( 'appbear-font-awesome' );

        wp_register_style( 'appbear-font-spotlayer', APPBEAR_URL . 'css/font-spotlayer.css', array(), self::$version );
        wp_enqueue_style( 'appbear-font-spotlayer' );

        if(is_rtl()){
            wp_register_style( 'appbear-rtl', APPBEAR_URL . 'css/appbear-rtl.css', array(), self::$version );
            wp_enqueue_style( 'appbear-rtl' );
        }else{
            wp_register_style( 'appbear', APPBEAR_URL . 'css/appbear.css', array(), self::$version );
            wp_enqueue_style( 'appbear' );
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
            'ajax_nonce' => wp_create_nonce( 'appbear_ajax_nonce' ),
            'text' => array(
                'popup' => array(
                    'accept_button' => _x( 'Accept', 'Button - On confirm popup', 'appbear' ),
                    'cancel_button' => _x( 'Cancel', 'Button - On confirm popup', 'appbear' ),
                ),
                'remove_item_popup' => array(
                    'title' => _x( 'Delete', 'Title - On popup "remove item"', 'appbear' ),
                    'content' => _x( 'Are you sure you want to delete?', 'Content - On popup "remove item"', 'appbear' ),
                ),
                'validation_url_popup' => array(
                    'title' => _x( 'Validation', 'Title - On popup "Validation url"', 'appbear' ),
                    'content' => _x( 'Please enter a valid url', 'Content - On popup "Validation url"', 'appbear' ),
                ),
                'reset_popup' => array(
                    'title' => _x( 'Reset values', 'Title - On popup "Reset values"', 'appbear' ),
                    'content' => _x( 'Are you sure you want to reset all options to the default values? All saved data will be lost.', 'Content - On popup "Reset values"', 'appbear' ),
                ),
                'import_popup' => array(
                    'title' => _x( 'Import values', 'Title - On popup "Import values"', 'appbear' ),
                    'content' => _x( 'Are you sure you want to import all options? All current values will be lost and well be overwritten.', 'Content - On popup "Import values"', 'appbear' ),
                ),
            )
        );
        return $l10n;
    }


}