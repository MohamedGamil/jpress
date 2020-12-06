<?php

class AppbearLoader148 {
	private $version;
	private $priority;

	public function __construct( $version = '1.0.0', $priority = 1000 ){
		$this->version = $version;
		$this->priority = $priority;
	}
	/*
	|---------------------------------------------------------------------------------------------------
	| Init Appbear
	|---------------------------------------------------------------------------------------------------
	*/
	public function init(){
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
	public function load_appbear(){

		// NOTE: Is the following line added to prevent scope collesions for multiple versions of this plugin?
		if ( class_exists( 'Appbear', false ) ) {
			return;
		}

		//Appbear constants
		$this->constants();

		//Class autoloader
		$this->class_autoloader();

		//Loacalization
		$this->localization();

		//Includes
		$this->includes();

		//appBear
		$this->appBear();

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
	public function constants(){
		define( 'APPBEAR_VERSION',  $this->version );
		define( 'APPBEAR_PRIORITY',  $this->priority );
		define( 'APPBEAR_SLUG',  'appbear' );
		define( 'APPBEAR_DIR', trailingslashit( dirname( __FILE__ ) ) );
		define( 'APPBEAR_URL', trailingslashit( $this->get_url() ) );
		defined('APPBEAR_FONTAWESOME_VERSION') or define('APPBEAR_FONTAWESOME_VERSION', '4.x');
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| WP localization
	|---------------------------------------------------------------------------------------------------
	*/
	public function localization(){
		$loaded = load_plugin_textdomain( 'appbear', false, trailingslashit ( plugin_basename( APPBEAR_DIR ) ). 'languages/' );

		if( ! $loaded ){
			load_textdomain( 'appbear', APPBEAR_DIR . 'languages/appbear-' . get_locale() . '.mo' );
		}
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Class autoloader
	|---------------------------------------------------------------------------------------------------
	*/
	public function class_autoloader(){
		include dirname( __FILE__ ) . '/includes/class-autoloader.php';
		Appbear\Includes\Autoloader::run();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Appbear files
	|---------------------------------------------------------------------------------------------------
	*/
	public function includes(){
		include dirname( __FILE__ ) . '/includes/class-appbear.php';
		include dirname( __FILE__ ) . '/includes/class-appbear-items.php';
		include dirname( __FILE__ ) . '/includes/global-functions.php';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| appBear files
	|---------------------------------------------------------------------------------------------------
	*/
	public function appBear(){
		if( function_exists( 'appBear_options' ) || function_exists( 'my_simple_metabox' ) ){
			return;
		}

		if( ! defined( 'APPBEAR_HIDE_DEMO' ) || ( defined( 'APPBEAR_HIDE_DEMO' ) && ! APPBEAR_HIDE_DEMO ) ){
			if( file_exists( dirname( __FILE__ ) . '/options/admin-page.php' ) ){
				include dirname( __FILE__ ) . '/options/admin-page.php';
			}
		}

		// APIs File
		include dirname( __FILE__ ) . '/options/appbear-apis.php';
		include dirname( __FILE__ ) . '/options/demos-api.php';
		include dirname( __FILE__ ) . '/options/options.php';
		include dirname( __FILE__ ) . '/options/functions.php';
		include dirname( __FILE__ ) . '/options/AppBear_subscription.php';

		include dirname( __FILE__ ) . '/themes/tielabs.php'; // to be chnaged to load files febending on the current active theme
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Get Appbear Url
	|---------------------------------------------------------------------------------------------------
	*/
	private function get_url(){
		// BUG: This logic doesn't work with all hosts!
		if( stripos( APPBEAR_DIR, 'themes') !== false ){
			$temp = explode( 'themes', APPBEAR_DIR );
			$appbear_url = content_url() . '/themes' . $temp[1];
		} else {
			$temp = explode( 'plugins', APPBEAR_DIR );
			$appbear_url = content_url() . '/plugins' . $temp[1];
		}
		$appbear_url = str_replace( "\\", "/", $appbear_url );
		return $appbear_url;
	}

}
