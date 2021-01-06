<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * AppBear Loader
 */
class AppbearLoader
{
  /**
   * Plugin Version
   *
   * @var string
   */
  private $version;

  /**
   * Plugin Priority
   *
   * @var string
   */
	private $priority;

  /**
   * Class constructor
   */
	public function __construct( $version = '1.0.0', $priority = 1000 ) {
		$this->version = $version;
    $this->priority = $priority;
  }

	/*
	|---------------------------------------------------------------------------------------------------
	| Init Appbear
	|---------------------------------------------------------------------------------------------------
	*/
	public function init() {
		// NOTE: Plugin initialization logic is borken to use multiple actions
		//           i.e. `wp_loaded` action should initialize theme specific integrations, and
    //          `init` action should initialize top-level general plugin logic

    add_action( 'init', array( $this, 'load_appbear' ), $this->priority );
		add_action( 'wp_loaded', array( $this, 'appbear_themes_init' ), $this->priority );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Init Appbear
	|---------------------------------------------------------------------------------------------------
	*/
	public function load_appbear() {
		// NOTE: Is the following line added to prevent scope collesions for multiple versions of this plugin?
		if ( class_exists( 'Appbear', false ) ) {
			return;
		}

    // Run the pre-init hook (Before initialization)
		do_action( 'appbear_init' );

		// Appbear constants
		$this->constants();

		// Class autoloader
		$this->class_autoloader();

		// Loacalization
		$this->localization();

		// Includes
		$this->includes();

		// AppBear generic initialization
		$this->appbear_core_init();

		Appbear::init( $this->version );

    // Run the admin-only post-init hook (AppBear Admin Initialization)
		if ( is_admin() ) {
			do_action( 'appbear_admin_init' );
    }

    // Run the post-init hook (After initialization)
		do_action( 'appbear_post_init' );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Constants
	|---------------------------------------------------------------------------------------------------
	*/
	public function constants() {
    define('APPBEAR_DID_INIT', true);
    define( 'APPBEAR_URL', trailingslashit( $this->_get_url() ) );
  }

	/*
	|---------------------------------------------------------------------------------------------------
	| WP localization
	|---------------------------------------------------------------------------------------------------
	*/
	public function localization() {
		$loaded = load_plugin_textdomain( 'appbear', false, trailingslashit ( plugin_basename( APPBEAR_DIR ) ). 'languages/' );

		if ( ! $loaded ) {
			load_textdomain( 'appbear', APPBEAR_DIR . 'languages/appbear-' . get_locale() . '.mo' );
		}
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Class autoloader
	|---------------------------------------------------------------------------------------------------
	*/
	public function class_autoloader() {
		include APPBEAR_INCLUDES_DIR . 'class-autoloader.php';
		Appbear\Includes\Autoloader::run();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Appbear files
	|---------------------------------------------------------------------------------------------------
	*/
	public function includes() {
		include APPBEAR_INCLUDES_DIR . 'class-appbear.php';
		include APPBEAR_INCLUDES_DIR . 'class-appbear-items.php';
		include APPBEAR_INCLUDES_DIR . 'global-functions.php';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| AppBear Core Initialization
	|---------------------------------------------------------------------------------------------------
	*/
	public function appbear_core_init() {
		if ( $this->_cannotInit() ) {
			return;
    }

		if ( ! defined( 'APPBEAR_HIDE_DEMO' ) || ( defined( 'APPBEAR_HIDE_DEMO' ) && APPBEAR_HIDE_DEMO === false ) ) {
			if ( ( $appBearOptsClass = APPBEAR_OPTIONS_DIR . 'appbear-options.php' ) && file_exists( $appBearOptsClass ) ) {
        require_once $appBearOptsClass;

        $appBearOptsClassInstance = new AppBear_Options();
        $appBearOptsClassInstance->run();
			}
		}

		// Options & Plugin Custom Classes
		include APPBEAR_OPTIONS_DIR . 'appbear-ads-shortcode.php';
		include APPBEAR_OPTIONS_DIR . 'appbear-categories.php';
		include APPBEAR_OPTIONS_DIR . 'appbear-deeplinking.php';
		include APPBEAR_OPTIONS_DIR . 'appbear-notifications-metabox.php';
		include APPBEAR_OPTIONS_DIR . 'appbear-apis.php';
		include APPBEAR_OPTIONS_DIR . 'appbear-notice.php';
		include APPBEAR_OPTIONS_DIR . 'demos-api.php';
		include APPBEAR_OPTIONS_DIR . 'options.php';
    include APPBEAR_OPTIONS_DIR . 'AppBear_subscription.php';

    // Init Classes
    AppBear_Ads_Shortcode::run();
    AppBear_Endpoints::run();
    AppBear_Demos_Endpoints::run();
    AppBear_Deeplinking::run();
    AppBear_Categories::run();
    AppBear_Notifications_Metabox::run();
    Appbear_Notice::run();
  }

  /**
   * Initialize themes integrations
   *
   * @return void
   */
	public function appbear_themes_init() {
		if ( $this->_cannotInit() ) {
			return;
    }

    // Run the pre-themes-init hook (Before themes-integrations initialization)
		do_action( 'appbear_themes_init' );

    // NOTE: Themes specific integrations go here..
    // TODO: To be chnaged to load files febending on the current active theme

		include plugin_dir_path( __FILE__ ) . '/themes/tielabs.php';
  }

	/*
	|---------------------------------------------------------------------------------------------------
	| Get Appbear Url
	|---------------------------------------------------------------------------------------------------
	*/
	private function _get_url() {
		if ( stripos( APPBEAR_DIR, 'themes') !== false ) {
      $temp = explode( 'themes', APPBEAR_DIR );
			$appbear_url = content_url() . '/themes' . $temp[1];
		} else {
      $dirs = explode(DIRECTORY_SEPARATOR, __DIR__);
      $pluginDirName = end($dirs);
      $appbear_url = plugins_url() . "/{$pluginDirName}";
    }

		return str_replace( "\\", "/", $appbear_url );
  }

  /**
   * Should the loader initialize plugin logic?
   *
   * @return boolean
   */
	private function _cannotInit() {
		return function_exists( 'my_simple_metabox' ) === true;
	}
}
