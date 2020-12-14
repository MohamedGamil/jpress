<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * AppBear Loader
 */
class AppbearLoader148
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
		// FIXME: Plugin initialization logic should be borken to use multiple actions
		//           i.e. `wp_loaded` action should initialize theme specific integrations, and
		//          `init` action should initialize general plugin logic
		add_action( 'init', array( $this, 'load_appbear' ), $this->priority );
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

		// Appbear constants
		$this->constants();

		// Class autoloader
		$this->class_autoloader();

		// Loacalization
		$this->localization();

		// Includes
		$this->includes();

		// AppBear generic initialization
		$this->appBear();

		// AppBear themes integrations
		$this->appBear_themes();

		//Appbear hooks
		if ( is_admin() ) {
			do_action( 'appbear_admin_init' );
    }

		do_action( 'appbear_init' );

		Appbear::init( $this->version );
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
		include plugin_dir_path( __FILE__ ) . '/includes/class-autoloader.php';
		Appbear\Includes\Autoloader::run();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Appbear files
	|---------------------------------------------------------------------------------------------------
	*/
	public function includes() {
		include plugin_dir_path( __FILE__ ) . '/includes/class-appbear.php';
		include plugin_dir_path( __FILE__ ) . '/includes/class-appbear-items.php';
		include plugin_dir_path( __FILE__ ) . '/includes/global-functions.php';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| appBear files
	|---------------------------------------------------------------------------------------------------
	*/
	public function appBear() {
		if ( $this->_cannotInit() ) {
			return;
		}

		if ( ! defined( 'APPBEAR_HIDE_DEMO' ) || ( defined( 'APPBEAR_HIDE_DEMO' ) && ! APPBEAR_HIDE_DEMO ) ) {
			if ( ( $appBearOptsClass = APPBEAR_DIR . '/options/appbear-options.php' ) && file_exists( $appBearOptsClass ) ) {
        require_once $appBearOptsClass;

        $appBearOptsClassInstance = new AppBear_Options();
        $appBearOptsClassInstance->run();
			}
		}

		// Options & Plugin Custom Classes
		include plugin_dir_path( __FILE__ ) . '/options/functions.php';
		include plugin_dir_path( __FILE__ ) . '/options/appbear-apis.php';
		include plugin_dir_path( __FILE__ ) . '/options/demos-api.php';
		include plugin_dir_path( __FILE__ ) . '/options/options.php';
    include plugin_dir_path( __FILE__ ) . '/options/AppBear_subscription.php';

    // Init Classes
    AppBear_Endpoints::run();
    AppBear_Demos_Endpoints::run();
  }

  /**
   * Initialize themes integrations
   *
   * @return void
   */
	public function appBear_themes() {
		if ( $this->_cannotInit() ) {
			return;
    }

    // NOTE: Themes specific integrations go here..
		include plugin_dir_path( __FILE__ ) . '/themes/tielabs.php'; // to be chnaged to load files febending on the current active theme
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
