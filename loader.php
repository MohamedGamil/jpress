<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * JPress Loader
 */
class JPressLoader
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
	| Init JPress
	|---------------------------------------------------------------------------------------------------
	*/
	public function init() {
		// NOTE: Plugin initialization logic is borken to use multiple actions
		//           i.e. `wp_loaded` action should initialize theme specific integrations, and
    //          `init` action should initialize top-level general plugin logic

    add_action( 'init', array( $this, 'load_jpress' ), $this->priority );
		add_action( 'wp_loaded', array( $this, 'jpress_themes_init' ), $this->priority );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Init JPress
	|---------------------------------------------------------------------------------------------------
	*/
	public function load_jpress() {
		// NOTE: Is the following line added to prevent scope collesions for multiple versions of this plugin?
		if ( class_exists( 'JPress', false ) ) {
			return;
		}

    // Run the pre-init hook (Before initialization)
		do_action( 'jpress_init' );

		// JPress constants
		$this->constants();

		// Class autoloader
		$this->class_autoloader();

		// Loacalization
		$this->localization();

		// Includes
		$this->includes();

		// Automatic Updates
		$this->autoUpdateInit();

		// JPress generic initialization
		$this->jpress_core_init();

		JPress::init( $this->version );

    // Run the admin-only post-init hook (JPress Admin Initialization)
		if ( is_admin() ) {
			do_action( 'jpress_admin_init' );
    }

    // Run the post-init hook (After initialization)
		do_action( 'jpress_post_init' );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Constants
	|---------------------------------------------------------------------------------------------------
	*/
	public function constants() {
    define('JPRESS_DID_INIT', true);
    define( 'JPRESS_URL', trailingslashit( $this->_get_url() ) );
  }

	/*
	|---------------------------------------------------------------------------------------------------
	| WP localization
	|---------------------------------------------------------------------------------------------------
	*/
	public function localization() {
		$loaded = load_plugin_textdomain( 'jpress', false, trailingslashit ( plugin_basename( JPRESS_DIR ) ). 'languages/' );

		if ( ! $loaded ) {
			load_textdomain( 'jpress', JPRESS_DIR . 'languages/jpress-' . get_locale() . '.mo' );
		}
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Class autoloader
	|---------------------------------------------------------------------------------------------------
	*/
	public function class_autoloader() {
		include JPRESS_INCLUDES_DIR . 'class-autoloader.php';
		JPress\Includes\Autoloader::run();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| JPress files
	|---------------------------------------------------------------------------------------------------
	*/
	public function includes() {
		include JPRESS_INCLUDES_DIR . 'class-jpress.php';
		include JPRESS_INCLUDES_DIR . 'class-jpress-items.php';
		include JPRESS_INCLUDES_DIR . 'global-functions.php';
	}

	/**
   * Automatic updates initialization
   *
   * @since 0.2.7
   * @return void
   */
	public function autoUpdateInit() {
    include JPRESS_INCLUDES_DIR . 'class-jpress-auto-update.php';

    // Run the updater
    JPress_Automatic_Updates::run();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| JPress Core Initialization
	|---------------------------------------------------------------------------------------------------
	*/
	public function jpress_core_init() {
		if ( $this->_cannotInit() ) {
			return;
    }

		if ( ! defined( 'JPRESS_HIDE_DEMO' ) || ( defined( 'JPRESS_HIDE_DEMO' ) && JPRESS_HIDE_DEMO === false ) ) {
			if ( ( $appBearOptsClass = JPRESS_OPTIONS_DIR . 'jpress-options.php' ) && file_exists( $appBearOptsClass ) ) {
        require_once $appBearOptsClass;

        $appBearOptsClassInstance = new JPress_Options();
        $appBearOptsClassInstance->run();
			}
		}

		// Options & Plugin Custom Classes
		include JPRESS_OPTIONS_DIR . 'jpress-ads-shortcode.php';
		include JPRESS_OPTIONS_DIR . 'jpress-categories.php';
		include JPRESS_OPTIONS_DIR . 'jpress-deeplinking.php';
		include JPRESS_OPTIONS_DIR . 'jpress-notifications-metabox.php';
		include JPRESS_OPTIONS_DIR . 'jpress-apis.php';
		include JPRESS_OPTIONS_DIR . 'jpress-notice.php';
		include JPRESS_OPTIONS_DIR . 'demos-api.php';
		include JPRESS_OPTIONS_DIR . 'options.php';
    include JPRESS_OPTIONS_DIR . 'jpress-subscription.php';

    // Init Classes
    JPress_Ads_Shortcode::run();
    JPress_Endpoints::run();
    JPress_Demos_Endpoints::run();
    JPress_Deeplinking::run();
    JPress_Categories::run();
    JPress_Notifications_Metabox::run();
    JPress_Notice::run();
  }

  /**
   * Initialize themes integrations
   *
   * @return void
   */
	public function jpress_themes_init() {
		if ( $this->_cannotInit() ) {
			return;
    }

    // Run the pre-themes-init hook (Before themes-integrations initialization)
		do_action( 'jpress_themes_init' );

    // NOTE: Themes specific integrations go here..
    // TODO: To be chnaged to load files febending on the current active theme

		include plugin_dir_path( __FILE__ ) . '/themes/tielabs.php';
  }

	/*
	|---------------------------------------------------------------------------------------------------
	| Get JPress Url
	|---------------------------------------------------------------------------------------------------
	*/
	private function _get_url() {
		if ( stripos( JPRESS_DIR, 'themes') !== false ) {
      $temp = explode( 'themes', JPRESS_DIR );
			$jpress_url = content_url() . '/themes' . $temp[1];
		} else {
      $dirs = explode(DIRECTORY_SEPARATOR, __DIR__);
      $pluginDirName = end($dirs);
      $jpress_url = plugins_url() . "/{$pluginDirName}";
    }

		return str_replace( "\\", "/", $jpress_url );
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
