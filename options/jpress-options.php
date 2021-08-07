<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * JPress Options
 *
 * This class handles all JPress options declerations
 *
 *
 * @since 0.0.2
 */
class JPress_Options {
  const OPTIONS_PAGES_DIR = JPRESS_OPTIONS_DIR . 'pages' . DIRECTORY_SEPARATOR;

	/**
	 * Class internal initialization state
	 */
  protected $_didInit = false;


	/**
	 * Class Constructor
	 */
	public function __construct() {
		// ...
  }


  /*
   * Run initialization routine
   */
  public function run() {
    if ( $this->_didInit === false ) {
      add_action( 'jpress_admin_init', array( $this, 'init' ) );
    }
  }


  /*
   * Initialize JPress options
   */
  public function init() {
    if ( $this->_didInit === true ) {
      return;
    }

    $this->_didInit = true;

    add_action('admin_notices', array( $this, 'notifyUserLicense' ));

    $this->_initOptions();
    $this->_noLicenseInit();
  }


  /*
   * Plugin updater
   */
  public function jpress_plugin_updater() {
    // To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
    $doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;

    if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
      return;
    }

    // retrieve our license key from the DB
    $license_key = $this->_getLicenseKey();

    // setup the updater
    $edd_updater = new JPress_subscription( JPRESS_STORE_URL, __FILE__,
      array(
        'version' => '1.0',                    // current version number
        'license' => $license_key,             // license key (used get_option above to retrieve from DB)
        'item_id' => JPRESS_ITEM_ID,       // ID of the product
        'author'  => 'Easy Digital Downloads', // author of this plugin
        'beta'    => false,
      )
    );
  }


  /*
   * Register license option
   */
  public function jpress_register_option() {
    register_setting(JPRESS_LICENSE_STATUS_KEY_OPTION, JPRESS_LICENSE_KEY_OPTION, array( $this, 'jpress_sanitize_license' ) );
  }


  /*
   * Sanitize license key
   */
  public function jpress_sanitize_license( $new ) {
    $old = jpress_get_license_key();

    if( $old && $old !== $new ) {
      // new license has been entered, so must reactivate
      jpress_invalidate_license();
    }

    return $new;
  }


  /*
   * Initialize options for no or invalid license state
   */
  public function notifyUserLicense() {
    $activationURL = admin_url( 'admin.php?page=jpress-activation' );
    $message = __('You must connect your JPress account to activate your license.', 'textdomain')
                  . ' <a href="'. $activationURL .'">'
                  . __('Click here to activate.', 'textdomain')
                  . '</a> ';

    if ( jpress_check_license() === false ) {
      jpress_notice($message, 'warning');
    }
  }


  /*
   * Initialize options for valid license state
   */
  protected function _initOptions() {
    $this->_initSettingsPage();
  }


  /*
   * Initialize settings page options
   */
  protected function _initSettingsPage() {
    // NOTE: Init Settings Page
    $settings_arg = array(
      'id' => JPRESS_PRIMARY_OPTIONS,
      'title' => 'JPress',
      'menu_title' => 'JPress',
      'menu_side_title' => 'Settings',
      'icon' => JPRESS_URL . 'assets/img/jpress-light-small.png',//Menu icon
      'skin' => 'blue',// Skins: blue, lightblue, green, teal, pink, purple, bluepurple, yellow, orange'
      'layout' => 'wide',//wide
      'header' => array(
        'icon' => '<img src="' . JPRESS_URL . 'assets/img/jpress-logo-light.svg"/>',
        'desc' => 'No coding required. Your app syncs with your site automatically.',
      ),
      'import_message' => __( 'Settings imported. This is just an example. No data imported.', 'textdomain' ),
      'capability' => 'manage_options',
      // 'parent' => JPRESS_PRIMARY_OPTIONS,
    );

    $settings = jpress_new_admin_page( $settings_arg );

    // Add main tab
    $settings->add_main_tab( array(
      'name' => 'Main tab',
      'id' => 'main-tab',
      'items' => array(
          'general' => '<i class="jpress-icon fa fa-cog"></i>'.__( 'General', 'textdomain' ),
          'user_guide' => '<i class="jpress-icon jpress-icon-photo"></i>'.__( 'User Guide', 'textdomain' ),
          'topbar' => '<i class="jpress-icon fa fa-sliders"></i>'.__( 'Topbar', 'textdomain' ),
          'sidemenu' => '<i class="jpress-icon fa fa-bars"></i>'.__( 'Side Menu', 'textdomain' ),
          'homepage' => '<i class="jpress-icon jpress-icon-home"></i>'.__( 'Home Tab', 'textdomain' ),
          'bottombar' => '<i class="jpress-icon fa fa-th-large"></i>'.__( 'Bottom Bar', 'textdomain' ),
          'archives' => '<i class="jpress-icon fa-tags"></i>'.__( 'Archives', 'textdomain' ),
          'styling' => '<i class="jpress-icon fa fa-paint-brush"></i>'.__( 'Styling', 'textdomain' ),
          // 'typography' => '<i class="jpress-icon jpress-icon-font"></i>'.__( 'Typography', 'textdomain' ),
          'advertisement' => '<i class="jpress-icon jpress-icon-photo"></i>'.__( 'Advertisement', 'textdomain' ),
          'settings' => '<i class="jpress-icon jpress-icon-cogs"></i>'.__( 'Settings Tab', 'textdomain' ),
          'translations' => '<i class="jpress-icon jpress-icon-language"></i>'.__( 'Translations', 'textdomain' ),
          'import' => '<i class="jpress-icon jpress-icon-database"></i>'.__( 'Import/Export', 'textdomain' ),
      ),
      'options' => array(
          'conditions' => array(
              'sidemenu' => array(
                  'show_if' => array('menu_type', '!=', 'bottombar'),
              ),
              'bottombar' => array(
                  'show_if' => array('menu_type', '!=', 'sidemenu'),
              ),
          ),
      )
    ));

    // NOTE: Include options pages / tabs
    include static::OPTIONS_PAGES_DIR . '1_general.php';
    include static::OPTIONS_PAGES_DIR . '2_top_bar.php';
    include static::OPTIONS_PAGES_DIR . '3_side_menu.php';
    include static::OPTIONS_PAGES_DIR . '4_bottom_bar.php';
    include static::OPTIONS_PAGES_DIR . '5_home.php';
    include static::OPTIONS_PAGES_DIR . '6_archives.php';
    include static::OPTIONS_PAGES_DIR . '7_styling.php';
    include static::OPTIONS_PAGES_DIR . '8_ads.php';
    include static::OPTIONS_PAGES_DIR . '9_onboarding.php';
    include static::OPTIONS_PAGES_DIR . '10_typography.php';
    include static::OPTIONS_PAGES_DIR . '11_settings.php';
    include static::OPTIONS_PAGES_DIR . '12_translations.php';
    include static::OPTIONS_PAGES_DIR . '13_import.php';

    // Close main tab
    $settings->close_tab('main-tab');
  }


  /*
   * Initialize options for no or invalid license state
   */
  protected function _noLicenseInit() {
    if ( jpress_check_license() === true && JPRESS_ENABLE_CONNECT_PAGE_IF_ACTIVE === false ) {
      return;
    }

		add_action( 'init', array( $this, 'jpress_plugin_updater' ) );
    add_action( 'admin_init', array( $this, 'jpress_register_option' ) );

		$activation_args = array(
			'id' => 'jpress-activation',
			'title' => 'Connect JPress',
			'menu_title' => 'Connect JPress',
			'menu_side_title' => 'Connect JPress',
			'icon' => JPRESS_URL . 'assets/img/jpress-light-small.png',//Menu icon
			'skin' => 'blue',// Skins: blue, lightblue, green, teal, pink, purple, bluepurple, yellow, orange'
			'layout' => 'wide',//wide
			'header' => array(
        'icon' => '<img src="' . JPRESS_URL . 'assets/img/jpress-logo-light.svg"/>',
        'desc' => 'Connect and activate your JPress account.',
			),
			'import_message' => __( 'Settings imported. This is just an example. No data imported.', 'textdomain' ),
			'capability' => 'manage_options',
			'parent' => JPRESS_PRIMARY_OPTIONS,
		);

		$activation = jpress_new_admin_page( $activation_args );

		$activation_section	=	$activation->add_section( array(
			'name' => 'Activation',
			'id' => 'section-general-activation',
    ));

    $isValidLicense = jpress_check_license() === true;
		$activation_section->add_field(array(
			'id' => 'custom-title',
			'name' => __( 'Enter your license key', 'textdomain' ),
			'type' => 'title',
			'desc' => (
        '<br>'
        . __('<strong><u>You must purchase a license</u> on <a href="jpress.io" target="_blank">jpress.io</a> to unlock all features of JPress and get your own mobile app.</strong>')
        . '<br>'
        . __('Or you can get instant access to a demo of what your mobile app will look like and experience real-time customizations by installing JPress from <a href="#" target="_blank">Google Play</a> or <a href="#" target="_blank">Apple App Store</a>.')
        . '<br>'
        . '<br>'
        . ( $isValidLicense === false ? __('Enter and save your license key to activate JPress.') : '' )
      ),
    ));

		$activation_section->add_field(array(
			'name' => 'Key',
			'default' => $this->_getLicenseKey(),
			'id' => JPRESS_LICENSE_KEY_OPTION,
			'type' => 'text',
			'grid' => '6-of-6',
    ));

    if ( get_option(JPRESS_LICENSE_STATUS_KEY_OPTION) === 'valid' ) {
      $activation_section->add_field(array(
        'name' => '<strong style="color:green">'. __('License Active!') .'</strong>',
        'id' => 'jpress-license-status',
        'type' => '__text',
        'grid' => '6-of-6',
        'options' => array(
          'readonly' => true,
          'helper' => __('Obtain a public key by activating your license.'),
        ),
      ));
    } else {
      $activation_section->add_field(array(
        'name' => '<strong style="color:red">'. __('License Inactive!') .'</strong>',
        'id' => 'jpress-license-status',
        'type' => '__text',
        'grid' => '6-of-6',
        'options' => array(
          'readonly' => true,
          'helper' => __('Obtain a public key by activating your license.'),
        ),
      ));
    }
  }


  /*
   * Get license key
   */
  private function _getLicenseKey() {
    return jpress_get_license_key();
  }
}
