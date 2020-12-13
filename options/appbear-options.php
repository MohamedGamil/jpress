<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * AppBear Options
 *
 * This class handles all AppBear options declerations
 *
 *
 * @since 0.0.2
 */
class AppBear_Options
{
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
      add_action( 'appbear_admin_init', array( $this, 'init' ) );
    }
  }


  /*
   * Initialize AppBear options
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
  public function appbear_plugin_updater() {
    // To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
    $doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;

    if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
      return;
    }

    // retrieve our license key from the DB
    $license_key = $this->_getLicenseKey();

    // setup the updater
    $edd_updater = new AppBear_subscription( APPBEAR_STORE_URL, __FILE__,
      array(
        'version' => '1.0',                    // current version number
        'license' => $license_key,             // license key (used get_option above to retrieve from DB)
        'item_id' => APPBEAR_ITEM_ID,       // ID of the product
        'author'  => 'Easy Digital Downloads', // author of this plugin
        'beta'    => false,
      )
    );
  }


  /*
   * Register license option
   */
  public function appbear_register_option() {
    register_setting('appbear_license_status', 'appbear_license_key', array( $this, 'appbear_sanitize_license' ) );
  }


  /*
   * Sanitize license key
   */
  public function appbear_sanitize_license( $new ) {
    $old = appbear_get_license_key();

    if( $old && $old !== $new ) {
      // new license has been entered, so must reactivate
      delete_option( 'appbear_license_status' );
    }

    return $new;
  }


  /*
   * Initialize options for no or invalid license state
   */
  public function notifyUserLicense() {
    $activationURL = admin_url( 'admin.php?page=appbear-activation' );
    $message = __('You must connect your AppBear account to activate your license.', 'textdomain')
                  . ' <a href="'. $activationURL .'">'
                  . __('Click here to activate.', 'textdomain')
                  . '</a> ';

    $key = 'appbear-license-notices';

    add_settings_error( $key, 'appbear_license_status', $message, 'warning' );
    // set_transient( 'settings_errors', get_settings_errors(), 30 );

    if ( appbear_check_license() === false ) {
      settings_errors( $key );
    }
  }


  /*
   * Initialize options for valid license state
   */
  protected function _initOptions() {
    $this->_initSettingsPage();
    $this->_initTranslationsPage();
  }


  /*
   * Initialize settings page options
   */
  protected function _initSettingsPage() {
    // NOTE: START Settings Page
    $settings_arg = array(
      'id' => 'appbear-settings',
      'title' => 'appBear',
      'menu_title' => 'appBear',
      'menu_side_title' => 'Settings',
      'icon' => APPBEAR_URL . 'img/appbear-light-small.png',//Menu icon
      'skin' => 'purple',// Skins: blue, lightblue, green, teal, pink, purple, bluepurple, yellow, orange'
      'layout' => 'wide',//wide
      'header' => array(
          'icon' => '<img src="' . APPBEAR_URL . 'img/a-logo.svg"/>',
          'desc' => 'No coding required. Your app syncs with your site automatically.',
      ),
      'import_message' => __( 'Settings imported. This is just an example. No data imported.', 'textdomain' ),
      'capability' => 'manage_options',
      // 'parent' => 'appbear-settings',
    );

    $settings = appbear_new_admin_page( $settings_arg );

    $settings->add_main_tab( array(
      'name' => 'Main tab',
      'id' => 'main-tab',
      'items' => array(
          'general' => '<i class="appbear-icon fa fa-cog"></i>'.__( 'General', 'textdomain' ),
          'user_guide' => '<i class="appbear-icon appbear-icon-photo"></i>'.__( 'User Guide', 'textdomain' ),
          'topbar' => '<i class="appbear-icon fa fa-sliders"></i>'.__( 'Topbar', 'textdomain' ),
          'sidemenu' => '<i class="appbear-icon fa fa-bars"></i>'.__( 'Side Menu', 'textdomain' ),
          'homepage' => '<i class="appbear-icon appbear-icon-home"></i>'.__( 'Home Tab', 'textdomain' ),
          'bottombar' => '<i class="appbear-icon fa fa-th-large"></i>'.__( 'Bottom Bar', 'textdomain' ),
          'archives' => '<i class="appbear-icon fa-tags"></i>'.__( 'Archives', 'textdomain' ),
          'styling' => '<i class="appbear-icon fa fa-paint-brush"></i>'.__( 'Styling', 'textdomain' ),
          // 'typography' => '<i class="appbear-icon appbear-icon-font"></i>'.__( 'Typography', 'textdomain' ),
          'advertisement' => '<i class="appbear-icon appbear-icon-photo"></i>'.__( 'Advertisement', 'textdomain' ),
          'settings' => '<i class="appbear-icon appbear-icon-cogs"></i>'.__( 'Settings Tab', 'textdomain' ),
          'import' => '<i class="appbear-icon appbear-icon-database"></i>'.__( 'Import/Export', 'textdomain' ),
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

    $settings->open_tab_item('general');

    $section_header_1 = $settings->add_section( array(
      'name' => __( 'General Settings', 'textdomain' ),
      'id' => 'section-general-settings',
      'options' => array( 'toggle' => true )
    ));

    $section_header_1->add_field(array(
      'name' => __( 'Date format', 'textdomain' ),
      'id' => 'time_format',
      'type' => 'radio',
      'default' => 'traditional',
      'items' => array(
        'traditional' => __( 'Traditional', 'textdomain' ),
        'modern' => __( 'Time Ago Format', 'textdomain' ),
      )
    ));

    $section_header_1->add_field(array(
      'name' => __( 'Switch between dark & light', 'textdomain' ),
      'id' => 'switch_theme_mode',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value'  => 'true',
        'off_value' => 'false'
      )
    ));

    $section_header_1->add_field( array(
      'id' => 'thememode',
      'name' => __( 'Default Theme Mode', 'textdomain' ),
      'type' => 'select',
      'default' => 'ThemeMode_light',
      'items' => array(
        'ThemeMode_light' => __( 'Light', 'textdomain' ),
        'ThemeMode_dark' => __( 'Dark', 'textdomain' ),
      ),
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));

    $section_header_1->add_field( array(
      'id' => 'menu_type',
      'name' => __( 'Menu Type', 'textdomain' ),
      'type' => 'select',
      'default' => 'both',
      'items' => array(
        'bottombar' => __( 'Bottom Bar Only', 'textdomain' ),
        'sidemenu' => __( 'Side Menu Only', 'textdomain' ),
        'both' => __( 'Bottom Bar & Side Menu', 'textdomain' ),
      )
    ));


    // NOTE: General Styling
    $section_header_1->open_mixed_field(array('name' => __('Background color', 'textdomain' ),'desc'      => __( 'Application background color.', 'textdomain' ),));
    $section_header_1->add_field(array(
      'id'        => 'styling-themeMode_light-scaffoldbackgroundcolor',
      'type'      => 'colorpicker',
      'default'   => '#FFFFFF',
        'options' => array(
        'show_name' => array('switch_theme_mode', '=', 'true'),
      )
    ));

    $section_header_1->add_field(array(
      'id'        => 'styling-themeMode_dark-scaffoldbackgroundcolor',
      'name'      => __( 'Drak Mode', 'textdomain' ),
      'type'      => 'colorpicker',
      'default'   => '#333739',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));

    $section_header_1->close_mixed_field();

    $section_header_1->open_mixed_field(array('name' => __('Main Color', 'textdomain' ),'desc'      => __( 'The main color of the application.', 'textdomain' ),));

    $section_header_1->add_field(array(
      'id'        => 'styling-themeMode_light-primary',
      'type'      => 'colorpicker',
      'default'   => '#0088ff',
    ));

    $section_header_1->add_field(array(
      'id'        => 'styling-themeMode_dark-primary',
      'name'      => __( 'Drak Mode', 'textdomain' ),
      'type'      => 'colorpicker',
      'default'   => '#0088ff',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));

    $section_header_1->close_mixed_field();

    $section_header_1->open_mixed_field(array('name' => __('Primary text Color', 'textdomain' ),'desc'      => __( 'All text color on application such as post titles, sections titles, posts content, pages content and settings page.', 'textdomain' ),));

    $section_header_1->add_field(array(
      'id'        => 'styling-themeMode_light-secondary',
      'type'      => 'colorpicker',
      'default'   => '#333739',
    ));

    $section_header_1->add_field(array(
      'id'        => 'styling-themeMode_dark-secondary',
      'name'      => __( 'Dark Mode', 'textdomain' ),
      'type'      => 'colorpicker',
      'default'   => '#FFFFFF',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));

    $section_header_1->close_mixed_field();

    $section_header_1->open_mixed_field(array('name' => __('Meta text color', 'textdomain' ),'desc' => __( 'All small text color on application such as meta posts.', 'textdomain' ),));
    $section_header_1->add_field(array(
      'id' => 'styling-themeMode_light-secondaryvariant',
      'type' => 'colorpicker',
      'default' => '#8A8A89',
    ));

    $section_header_1->add_field(array(
      'id' => 'styling-themeMode_dark-secondaryvariant',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#8A8A89',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));

    $section_header_1->close_mixed_field();
    $settings->close_tab_item('general');


    // NOTE: Top Bar Page
    $settings->open_tab_item('topbar');
    $settings->add_field(array(
      'name' => __('Logo (Light)', 'textdomain' ),
      'id' => 'logo-light',
      'type' => 'file',
      'default' => APPBEAR_URL .'img/jannah-logo-light.png',
    ));
    $settings->add_field(array(
      'name' => __('Logo (Dark)', 'textdomain' ),
      'id' => 'logo-dark',
      'type' => 'file',
      'default' => APPBEAR_URL .'img/jannah-logo-dark.png',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $settings->add_field(array(
      'name' => __( 'Logo Postition', 'textdomain' ),
      'id' => 'appbar-position',
      'type' => 'radio',
      'default' => 'LogoPosition.start',
      'items' => array(
        'LogoPosition.start' => __( 'Start', 'textdomain' ),
        'LogoPosition.center' => __( 'Center', 'textdomain' ),
      )
    ));
    $settings->add_field( array(
      'name' => __('Side menu icon', 'textdomain'),
      'id' => 'sidenavbar-icon',
      'type' => 'icon_selector',
      'default' => '0xe808',
      'items' => array_merge(
        AppbearItems::icon_fonts()
      ),
      'options' => array(
        'wrap_height' => '220px',
        'size' => '36px',
        'hide_search' => false,
        'hide_buttons' => true,
        'show_if' => array('menu_type', '!=', 'bottombar')
      ),
    ));
    $settings->open_mixed_field(array('name' => __('Show search button', 'textdomain' )));
    $settings->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'topbar_search_button',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $settings->add_field( array(
      'name' => __('Search icon', 'textdomain'),
      'id' => 'appbar-searchicon',
      'type' => 'icon_selector',
      'default' => '0xe820',
      'items' => array_merge(
        AppbearItems::icon_fonts()
      ),
      'options' => array(
        'wrap_height' => '220px',
        'size' => '36px',
        'hide_search' => false,
        'hide_buttons' => true,
        'show_if' => array('topbar_search_button', '=', 'true')
      ),
    ));
    $settings->close_mixed_field();

    $settings->open_mixed_field(array('name' => __('Background color', 'textdomain' )));
    $settings->add_field(array(
      'id' => 'styling-themeMode_light-appBarBackgroundColor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#FFFFFF',
    ));

    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-appBarBackgroundColor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#333739',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));
    $settings->close_mixed_field();

    $settings->open_mixed_field(array('name' => __('Icons/Text colors', 'textdomain' )));
    $settings->add_field(array(
      'id' => 'styling-themeMode_light-appBarColor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#333739',
    ));

    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-appBarColor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#FFFFFF',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));
    $settings->close_mixed_field();
    $settings->close_tab_item('topbar');


    // NOTE: Side-Menu Page
    $settings->open_tab_item('sidemenu');
    $appbear_sidemenu_styling = $settings->add_section( array(
      'name' => __( 'Side Menu Styling', 'textdomain' ),
      'id' => 'section-sidemenu',
      'options' => array( 'toggle' => true )
    ));

    $appbear_sidemenu_styling->open_mixed_field(array('name' => __('Background color', 'textdomain' )));
    $appbear_sidemenu_styling->add_field(array(
      'id' => 'styling-themeMode_light-background',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#FFFFFF',
    ));

    $appbear_sidemenu_styling->add_field(array(
      'id' => 'styling-themeMode_dark-background',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#333739',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));
    $appbear_sidemenu_styling->close_mixed_field();

    $appbear_sidemenu_styling->open_mixed_field(array('name' => __('Icon/Text color', 'textdomain' )));
    $appbear_sidemenu_styling->add_field(array(
      'id' => 'styling-themeMode_light-sideMenuIconsTextColor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#333739',
    ));

    $appbear_sidemenu_styling->add_field(array(
      'id' => 'styling-themeMode_dark-sideMenuIconsTextColor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#FFFFFF',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));
    $appbear_sidemenu_styling->close_mixed_field();


    $sidemenu_items = $settings->add_section( array(
      'name' => __( 'Side Menu Items', 'textdomain' ),
      'id' => 'section-sidemenu-items',
      'options' => array( 'toggle' => true )
    ));

    $tabs = $sidemenu_items->add_group( array(
      'name' => __('Menu Items', 'textdomain'),
      'id' => 'navigators',
      'options' => array(
        'add_item_text' => __('New Tab', 'textdomain'),
      ),
      'controls' => array(
        'name' =>  __('Menu Item', 'textdomain').' #',
        'position' => 'left',
        'readonly_name' => true,
        'images' => false,
      ),
    ));

    $tabs->add_field(array(
      'name' => __( 'Tab Type', 'textdomain' ),
      'id' => 'type',
      'type' => 'radio',
      'default' => 'NavigationType.category',
      'items' => array(
        'NavigationType.main' => __( 'Main Page', 'textdomain' ),
        'NavigationType.category' => __( 'Category', 'textdomain' ),
        'NavigationType.page' => __( 'Page', 'textdomain' ),
      )
    ));
    $tabs->open_mixed_field(array('name' => __('Tab Icon', 'textdomain' )));
    $tabs->add_field(array(
      'name' => __( 'Enable', 'textdomain' ),
      'id' => 'side_menu_tab_icon',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->add_field( array(
      'name' => __('Tab icon', 'textdomain'),
      'id' => 'icon',
      'type' => 'icon_selector',
      'default' => '0xe9f5',
      'items' => array_merge(
        AppbearItems::icon_fonts()
      ),
      'options' => array(
      'wrap_height' => '220px',
      'size' => '36px',
      'hide_search' => false,
      'hide_buttons' => true,
        'show_if' => array('side_menu_tab_icon', '=', 'true'),
      ),
    ));
    $tabs->close_mixed_field();

    $tabs->add_field(array(
      'name' => __( 'Main Pages', 'textdomain' ),
      'id' => 'main',
      'type' => 'select',
      'default' => 'MainPage.home',
      'items' => array(
        'MainPage.home' => __( 'Home', 'textdomain' ),
        'MainPage.sections' => __( 'Sections', 'textdomain' ),
        'MainPage.favourites' => __( 'Favorites', 'textdomain' ),
        'MainPage.settings' => __( 'Settings', 'textdomain' ),
      ),
      'options' => array(
        'show_if' => array('type', '=', 'NavigationType.main'),
      ),
      'attributes' => array( 'required' => true ),
    ));
    $tabs->add_field(array(
      'name' => __( 'Categories', 'textdomain' ),
      'id' => 'category',
      'type' => 'select',
      'items' => AppbearItems::terms( 'category' ),
      'options' => array(
        'show_if' => array('type', '=', 'NavigationType.category'),
      ),
      'attributes' => array( 'required' => true ),
    ));
    $tabs->add_field(array(
      'name' => __( 'Pages', 'textdomain' ),
      'id' => 'page',
      'type' => 'select',
      'items' => AppbearItems::posts_by_post_type( 'page' ),
      'options' => array(
        'show_if' => array('type', '=', 'NavigationType.page'),
      ),
      'attributes' => array( 'required' => true ),
    ));

    $tabs->open_mixed_field(array('name' => __('Customized Title', 'textdomain' )));
    $tabs->add_field(array(
      'name' => __( 'Enable', 'textdomain' ),
      'id' => 'cutomized_title',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->add_field(array(
      'name' => __( 'New Title', 'textdomain' ),
      'id' => 'title',
      'type' => 'text',
      'grid' => '2-of-6',
      'options' => array(
        'show_if' => array('cutomized_title', '=', 'true'),
      ),
    ));
    $tabs->close_mixed_field();
    $settings->close_tab_item('sidemenu');


    // NOTE: Bottom Bar Page
    $settings->open_tab_item('bottombar');
    $appbear_bottombar_styling = $settings->add_section( array(
      'name' => __( 'Bottom Bar Styling', 'textdomain' ),
      'id' => 'section-bottombar-styling',
      'options' => array( 'toggle' => true )
    ));

    $appbear_bottombar_styling->open_mixed_field(array('name' => __('InActive tab text color', 'textdomain' )));
    $appbear_bottombar_styling->add_field(array(
      'id' => 'styling-themeMode_light-bottomBarInActiveColor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#BCBCBC',
    ));

    $appbear_bottombar_styling->add_field(array(
      'id' => 'styling-themeMode_dark-bottomBarInActiveColor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#838483',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));
    $appbear_bottombar_styling->close_mixed_field();

    $appbear_bottombar_styling->open_mixed_field(array('name' => __('Active tab text color', 'textdomain' )));
    $appbear_bottombar_styling->add_field(array(
      'id' => 'styling-themeMode_light-bottomBarActiveColor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#0088ff',
    ));

    $appbear_bottombar_styling->add_field(array(
      'id' => 'styling-themeMode_dark-bottomBarActiveColor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#0088ff',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));
    $appbear_bottombar_styling->close_mixed_field();

    $appbear_bottombar_tabs = $settings->add_section( array(
      'name' => __( 'Bottom Bar Tabs', 'textdomain' ),
      'id' => 'section-bottombar-tabs',
      'options' => array(
      'toggle' => true,
        'show_if' => array('menu_type', '!=', 'sidemenu'),
      ),
    ));


    $tabs = $appbear_bottombar_tabs->add_group( array(
      'name' => __('Tabs', 'textdomain'),
      'id' => 'bottombar_tabs',
      'options' => array(
      'add_item_text' => __('New Tab', 'textdomain'),
        'show_if' => array('menu_type', '!=', 'sidemenu'),

      ),
      'controls' => array(
      'name' =>  __('Tab', 'textdomain').' #',
      'readonly_name' => true,
      'images' => false,
      ),
    ));

    $tabs->add_field(array(
      'name' => __( 'Tab Type', 'textdomain' ),
      'id' => 'type',
      'type' => 'radio',
      'default' => 'NavigationType.category',
      'items' => array(
        'NavigationType.main' => __( 'Main Page', 'textdomain' ),
        'NavigationType.category' => __( 'Category', 'textdomain' ),
        'NavigationType.page' => __( 'Page', 'textdomain' ),
      )
    ));

    $tabs->open_mixed_field(array('name' => __('Tab Icon', 'textdomain' )));

    $tabs->add_field(array(
      'name' => __( 'Enable', 'textdomain' ),
      'id' => 'bottom_bar_icon_enable',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->add_field( array(
      'name' => __('Tab icon', 'textdomain'),
      'id' => 'icon',
      'type' => 'icon_selector',
      'default' => '0xe800',
      'items' => array_merge( AppbearItems::icon_fonts() ),
      'options' => array(
        'wrap_height' => '220px',
        'size' => '36px',
        'hide_search' => false,
        'hide_buttons' => true,
        // 'show_if' => array('bottom_bar_icon_enable', '=', 'true'),
        'show_if' => array('bottom_bar_enable_tabs', '=', 'true'),
      ),
    ));
    $tabs->close_mixed_field();

    $tabs->add_field(array(
      'name' => __( 'Main Pages', 'textdomain' ),
      'id' => 'main',
      'type' => 'select',
      'default' => 'MainPage.home',
      'items' => array(
        'MainPage.home' => __( 'Home', 'textdomain' ),
        'MainPage.sections' => __( 'Sections', 'textdomain' ),
        'MainPage.favourites' => __( 'Favorites', 'textdomain' ),
        'MainPage.settings' => __( 'Settings', 'textdomain' ),
      ),
      'options' => array(
        'show_if' => array('type', '=', 'NavigationType.main'),
      ),
      'attributes' => array( 'required' => true ),
    ));
    $tabs->add_field(array(
      'name' => __( 'Categories', 'textdomain' ),
      'id' => 'category',
      'type' => 'select',
      'items' => AppbearItems::terms( 'category' ),
      'options' => array(
        'show_if' => array('type', '=', 'NavigationType.category'),
      ),
      'attributes' => array( 'required' => true ),
    ));
    $tabs->add_field(array(
      'name' => __( 'Pages', 'textdomain' ),
      'id' => 'page',
      'type' => 'select',
      'items' => AppbearItems::posts_by_post_type( 'page' ),
      'options' => array(
        'show_if' => array('type', '=', 'NavigationType.page'),
      ),
      'attributes' => array( 'required' => true ),
    ));

    $tabs->open_mixed_field(array('name' => __('Title', 'textdomain' )));
    $tabs->add_field(array(
      'id' => 'title_enable',
      'name' => __( 'Enable', 'textdomain' ),
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
      'on_value' => 'true',
      'off_value' => 'false',
        'show_if' => array('menu_type', '!=', 'sidemenu'),
      )
    ));
    $tabs->add_field(array(
      'id' => 'cutomized_title',
      'name' => __( 'Enable Customized Title', 'textdomain' ),
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
      'on_value' => 'true',
      'off_value' => 'false',
        'show_if' => array('title_enable', '=', 'true'),
      )
    ));
    $tabs->add_field(array(
      'id' => 'title',
      'type' => 'text',
      'name' => __( 'Title', 'textdomain' ),
      'grid' => '5-of-6',
      'options' => array(
        'show_if' => array('cutomized_title', '=', 'true'),
      ),
    ));
    $tabs->close_mixed_field();
    $settings->close_tab_item('bottombar');


    // NOTE: Home Page
    $settings->open_tab_item('homepage');

    $tabs = $settings->add_section( array(
      'name' => __( 'Homepage tabs', 'textdomain' ),
      'id' => 'section-homepage-tabs',
      'options' => array( 'toggle' => true )
    ));
    $tabs->add_field(array(
      'name' => __( 'Enable Tabs', 'textdomain' ),
      'id' => 'tabsbar_categories_tab',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->add_field( array(
      'name' => __( 'Layout', 'textdomain' ),
      'id' => 'tabs-tabslayout',
      'type' => 'image_selector',
      'default' => 'TabsLayout.tab1',
      'items' => array(
        'TabsLayout.tab1' => APPBEAR_URL . 'options/img/topbar_tabs/tab_1.png',
        'TabsLayout.tab2' => APPBEAR_URL . 'options/img/topbar_tabs/tab_2.png',
        'TabsLayout.tab3' => APPBEAR_URL . 'options/img/topbar_tabs/tab_3.png',
        'TabsLayout.tab4' => APPBEAR_URL . 'options/img/topbar_tabs/tab_4.png',
        'TabsLayout.tab5' => APPBEAR_URL . 'options/img/topbar_tabs/tab_5.png',
        'TabsLayout.tab6' => APPBEAR_URL . 'options/img/topbar_tabs/tab_6.png'
      ),
      'options' => array(
        'width' => '200px',
        'show_if' => array('tabsbar_categories_tab', '=', 'true'),
      ),
    ));


    $tabs->open_mixed_field(array('name' => __('Background color', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
    $tabs->add_field(array(
      'id' => 'styling-themeMode_light-tabbarbackgroundcolor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#FFFFFF',
    ));

    $tabs->add_field(array(
      'id' => 'styling-themeMode_dark-tabbarbackgroundcolor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#333739',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $tabs->close_mixed_field();


    $tabs->open_mixed_field(array('name' => __('InActive Tab Text color', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
    $tabs->add_field(array(
      'id' => 'styling-themeMode_light-tabbartextcolor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#7F7F7F',
    ));

    $tabs->add_field(array(
      'id' => 'styling-themeMode_dark-tabbartextcolor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#8A8A89',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $tabs->close_mixed_field();

    $tabs->open_mixed_field(array('name' => __('Active Tab Text color', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
    $tabs->add_field(array(
      'id' => 'styling-themeMode_light-tabbaractivetextcolor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#333739',
    ));

    $tabs->add_field(array(
      'id' => 'styling-themeMode_dark-tabbaractivetextcolor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#FFFFFF',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $tabs->close_mixed_field();

    $tabs->open_mixed_field(array('name' => __('Indicator color', 'textdomain' ),'desc' => __('The line under/outline/background the active tab', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
    $tabs->add_field(array(
      'id' => 'styling-themeMode_light-tabbarindicatorcolor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#0088FF',
    ));

    $tabs->add_field(array(
      'id' => 'styling-themeMode_dark-tabbarindicatorcolor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#0088FF',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $tabs->close_mixed_field();


    $homepage_tabs = $tabs->add_group( array(
      'name' => __('Tabs', 'textdomain'),
      'id' => 'tabsbaritems',
      'options' => array(
      'add_item_text' => __('New Tab', 'textdomain'),
        'show_if' => array('tabsbar_categories_tab', '=', 'true')
      ),
      'controls' => array(
        'name' =>  __('Tabs Item', 'textdomain').' #',
        'position' => 'top',
        'readonly_name' => true,
        'images' => false,
      ),
    ));
    $homepage_tabs->add_field(array(
      'name' => __( 'Category', 'textdomain' ),
      'id' => 'categories',
      'type' => 'select',
      'items' => AppbearItems::terms( 'category' ),
      'options' => array(
      'multiple' => true,
      'search' => true,
        'show_if' => array('local-tabs-tab_type', '=', 'category'),
      ),
    ));

    $homepage_tabs->open_mixed_field(array('name' => __('Customized Title', 'textdomain' )));
    $homepage_tabs->add_field(array(
      'name' => __( 'Enable', 'textdomain' ),
      'id' => 'customized-title',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $homepage_tabs->add_field(array(
      'name' => __( 'New Title', 'textdomain' ),
      'id' => 'title',
      'type' => 'text',
      'grid' => '2-of-6',
      'options' => array(
        'show_if' => array('customized-title', '=', 'true'),
      ),
    ));
    $homepage_tabs->close_mixed_field();

    $tabs->open_mixed_field(array('name' => __('Exclude Posts', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
    $tabs->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'local-tabs-exclude_posts',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->add_field(array(
      'id' => 'tabs-exclude_posts',
      'name' => __( 'Posts ID/IDs', 'textdomain' ),
      'type' => 'text',
      'grid' => '5-of-6',
      'desc' => __( 'Enter a post ID, or IDs separated by comma', 'textdomain' ),
      'options' => array(
        'show_if' => array('local-tabs-exclude_posts', '=', 'true')
      )
    ));
    $tabs->close_mixed_field();
    $tabs->open_mixed_field(array('name' => __('Offset', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
    $tabs->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'local-tabs-offset_posts',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->add_field(array(
      'id' => 'tabs-offset_posts',
      'name' => __( 'Offset Count', 'textdomain' ),
      'type' => 'number',
      'grid' => '5-of-6',
      'desc' => __( 'Number of posts to pass over', 'textdomain' ),
      'options' => array(
        'show_unit' => false,
        'show_if' => array('local-tabs-offset_posts', '=', 'true')
      )
    ));
    $tabs->close_mixed_field();
    $tabs->add_field(array(
      'name' => __( 'Number of posts to show', 'textdomain' ),
      'id' => 'tabs-count',
      'type' => 'select',
      'default' => '3',
      'items' => array(
        '1' => 1,
        '3' => 3,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '9' => 9,
        '10' => 10,
      ),
      'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))
    ));
    $tabs->add_field(array(
      'name' => __( 'Sort Order', 'textdomain' ),
      'id' => 'tabs-sort',
      'type' => 'select',
      'default' => 'latest',
      'items' => array(
        'latest' => __( 'Recent Posts', 'textdomain' ),
        'rand' => __( 'Random Posts', 'textdomain' ),
        'modified' => __( 'Last Modified Posts', 'textdomain' ),
        'popular' => __( 'Most Commented posts', 'textdomain' ),
        'title' => __( 'Alphabetically', 'textdomain' ),
      ),
      'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))
    ));
    $tabs->add_field( array(
      'id' => 'tabs-postlayout',
      'name' => __( 'Posts Layout', 'textdomain' ),
      'type' => 'image_selector',
      'default' => 'PostLayout.startThumbPost',
      'items' => array(
        'PostLayout.cardPost' => APPBEAR_URL . 'options/img/blocks/cardPost.png',
        'PostLayout.endThumbPost' => APPBEAR_URL . 'options/img/blocks/endThumbPost.png',
        'PostLayout.featuredMetaPost' => APPBEAR_URL . 'options/img/blocks/featuredMetaPost.png',
        'PostLayout.featuredPost' => APPBEAR_URL . 'options/img/blocks/featuredPost.png',
        'PostLayout.gridPost' => APPBEAR_URL . 'options/img/blocks/gridPost.png',
        'PostLayout.imagePost' => APPBEAR_URL . 'options/img/blocks/imagePost.png',
        'PostLayout.minimalPost' => APPBEAR_URL . 'options/img/blocks/minimalPost.png',
        'PostLayout.relatedPost' => APPBEAR_URL . 'options/img/blocks/relatedPost.png',
        'PostLayout.simplePost' => APPBEAR_URL . 'options/img/blocks/simplePost.png',
        'PostLayout.startThumbPost' => APPBEAR_URL . 'options/img/blocks/startThumbPost.png',
        'PostLayout.startThumbPostCompact' => APPBEAR_URL . 'options/img/blocks/startThumbPostCompact.png',
      ),
      'options' => array(
        'width' => '155px',
        'show_if' => array('tabsbar_categories_tab', '=', 'true')
      ),
    ));
    $tabs->add_field(array(
      'name' => __( 'Is first post "Featured"?', 'textdomain' ),
      'id' => 'local-tabs-firstfeatured',
      'type' => 'switcher',
      'default'	=>	'false',
      'desc' => __( 'Enable this to make the first post of this section with different post layout', 'textdomain' ),
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false',
        'show_if' => array('tabsbar_categories_tab', '=', 'true')
      ),
    ));
    $tabs->add_field( array(
      'id' => 'tabs-firstfeatured',
      'name' => __( 'Featured Post Layout', 'textdomain' ),
      'type' => 'image_selector',
      'default' => 'PostLayout.featuredPost',
      'items' => array(
        'PostLayout.cardPost' => APPBEAR_URL . 'options/img/blocks/cardPost.png',
        'PostLayout.featuredMetaPost' => APPBEAR_URL . 'options/img/blocks/featuredMetaPost.png',
        'PostLayout.featuredPost' => APPBEAR_URL . 'options/img/blocks/featuredPost.png',
        'PostLayout.imagePost' => APPBEAR_URL . 'options/img/blocks/imagePost.png',
        'PostLayout.simplePost' => APPBEAR_URL . 'options/img/blocks/simplePost.png',
      ),
      'options' => array(
        'width' => '155px',
        'show_if' => array('tabsbar_categories_tab', '=', 'true')
      ),
    ));
    $tabs->open_mixed_field(array('name' => __('Advanced Settings', 'textdomain' ), 'options' => array('show_if' => array('tabsbar_categories_tab', '=', 'true'))));
    $tabs->add_field(array(
      'name' => __( 'Catgeory', 'textdomain' ),
      'id' => 'tabs-options-category',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->add_field(array(
      'name' => __( 'Read Time', 'textdomain' ),
      'id' => 'tabs-options-readtime',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->add_field(array(
      'name' => __( 'Created Date', 'textdomain' ),
      'id' => 'tabs-options-date',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->add_field(array(
      'name' => __( 'Favorite', 'textdomain' ),
      'id' => 'tabs-options-save',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->add_field(array(
      'name' => __( 'Share', 'textdomain' ),
      'id' => 'tabs-options-share',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $tabs->close_mixed_field();


    $homepage = $settings->add_section( array(
      'name' => __( 'Personalize the home page', 'textdomain' ),
      'id' => 'section-homepage-builder',
      'options' => array( 'toggle' => true )
    ));

    $homepage->open_mixed_field(
    array(
      'name' =>  __('Customize Homepage Title in tabs', 'textdomain' ),
      'options' => array(
    'show_if' => array('tabsbar_categories_tab', '=', 'true')
    )
    )
    );
    $homepage->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'local-hompage_title',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $homepage->add_field(array(
      'id' => 'homepage-sections-title',
      'name' => __( 'Title', 'textdomain' ),
      'type' => 'text',
      'grid' => '5-of-6',
      'default' => __( 'Home', 'textdomain' ),
      'options' => array(
    'show_if' => array('local-hompage_title', '=', 'true')
      )
    ));
    $homepage->close_mixed_field();
    $section = $homepage->add_group( array(
      'name' => __( 'Homepage Sections', 'textdomain' ),
      'id' => 'sections',
      'options' => array(
      'add_item_text' => __('New Section', 'textdomain'),
      ),
      'controls' => array(
      'name' =>  __('Section', 'textdomain').' #',
      'readonly_name' => false,
      'images' => true,
      'position' => 'left',
      'default_image' => APPBEAR_URL . '/img/transparent.png',
      'image_field_id' => 'postlayout',
      'height' => '190px',
      ),
    ));
    $section->open_mixed_field(array('name' =>  __('Section Title', 'textdomain' )));
    $section->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'local-section_title',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $section->add_field(array(
      'id' => 'title',
      'name' => __( 'Title', 'textdomain' ),
      'type' => 'text',
      'grid' => '5-of-6',
      'desc' => __( 'If you don\'t need this section to have title, then switch it off', 'textdomain' ),
      'options' => array(
    'show_if' => array('local-section_title', '=', 'true')
      )
    ));
    $section->close_mixed_field();

    $section->add_field(array(
      'name' => __( "Show 'See All' Button", 'textdomain' ),
      'id' => 'local-enable_see_all',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      ),
      'options' => array(
    'show_if' => array('local-section_title', '=', 'true')
      )
    ));
    $section->add_field(array(
      'name' => __( 'Show posts by', 'textdomain' ),
      'id' => 'showposts',
      'type' => 'radio',
      'default' => 'categories',
      'items' => array(
      'categories' => __( 'Categories', 'textdomain' ),
      'tags' => __( 'Tags', 'textdomain' ),
      )
    ));
    $section->add_field( array(
      'id' => 'categories',
      'name' => __( 'Categories', 'textdomain' ),
      'type' => 'checkbox',
      'default' => '$all$',
      'items' => AppbearItems::terms( 'category' ),
      'desc' => __( 'Select all categories you need to show thier posts in that section', 'textdomain' ),
      'options' => array(
    'show_if' => array('showposts', '=', 'categories')
      )
    ));
    $section->add_field(array(
      'id' => 'tags',
      'name' => __( 'Tags', 'textdomain' ),
      'type' => 'checkbox',
      'default' => '$all$',
      'items' => AppbearItems::terms( 'post_tag' ),
      'desc' => __( 'Select all tags you need to show thier posts in that section', 'textdomain' ),
      'options' => array(
    'show_if' => array('showposts', '=', 'tags')
      )
    ));
    $section->close_mixed_field();
    $section->open_mixed_field(array('name' => __('Exclude Posts', 'textdomain' )));
    $section->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'local-enable_exclude_posts',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $section->add_field(array(
      'id' => 'local-exclude_posts',
      'name' => __( 'Posts ID/IDs', 'textdomain' ),
      'type' => 'text',
      'grid' => '5-of-6',
      'desc' => __( 'Enter a post ID, or IDs separated by comma', 'textdomain' ),
      'options' => array(
    'show_if' => array('local-enable_exclude_posts', '=', 'true')
      )
    ));
    $section->close_mixed_field();
    $section->open_mixed_field(array('name' => __('Offset', 'textdomain' )));
    $section->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'local-enable_offset_posts',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $section->add_field(array(
      'id' => 'local-offset_posts',
      'name' => __( 'Count', 'textdomain' ),
      'type' => 'number',
      'grid' => '5-of-6',
      'desc' => __( 'Number of posts to pass over', 'textdomain' ),
      'options' => array(
      'show_unit'=>false,
    'show_if' => array('local-enable_offset_posts', '=', 'true')
      )
    ));
    $section->close_mixed_field();
    $section->add_field(array(
      'name' => __( 'Sort Order', 'textdomain' ),
      'id' => 'local-sort',
      'type' => 'select',
      'default' => 'latest',
      'items' => array(
        'latest' => __( 'Recent Posts', 'textdomain' ),
        'rand' => __( 'Random Posts', 'textdomain' ),
        'modified' => __( 'Last Modified Posts', 'textdomain' ),
        'popular' => __( 'Most Commented posts', 'textdomain' ),
        'title' => __( 'Alphabetically', 'textdomain' ),
      )
    ));
    $section->add_field(array(
      'name' => __( 'Number of posts to show', 'textdomain' ),
      'id' => 'local-count',
      'type' => 'select',
      'default' => '3',
      'items' => array(
        '1' => __( '1 Post', 'textdomain' ),
        '2' => __( '2 Posts', 'textdomain' ),
        '3' => __( '3 Posts', 'textdomain' ),
        '4' => __( '4 Posts', 'textdomain' ),
        '5' => __( '5 Posts', 'textdomain' ),
        '6' => __( '6 Posts', 'textdomain' ),
        '7' => __( '7 Posts', 'textdomain' ),
        '8' => __( '8 Posts', 'textdomain' ),
        '9' => __( '9 Posts', 'textdomain' ),
        '10' => __( '10 Posts', 'textdomain' ),
      )
    ));
    $section->add_field( array(
      'id' => 'postlayout',
      'name' => __( 'Posts Layout', 'textdomain' ),
      'type' => 'image_selector',
      'default' => 'PostLayout.startThumbPost',
      'items' => array(
      'PostLayout.cardPost' => APPBEAR_URL . 'options/img/blocks/cardPost.png',
      'PostLayout.endThumbPost' => APPBEAR_URL . 'options/img/blocks/endThumbPost.png',
      'PostLayout.featuredMetaPost' => APPBEAR_URL . 'options/img/blocks/featuredMetaPost.png',
      'PostLayout.featuredPost' => APPBEAR_URL . 'options/img/blocks/featuredPost.png',
      'PostLayout.gridPost' => APPBEAR_URL . 'options/img/blocks/gridPost.png',
      'PostLayout.imagePost' => APPBEAR_URL . 'options/img/blocks/imagePost.png',
      'PostLayout.minimalPost' => APPBEAR_URL . 'options/img/blocks/minimalPost.png',
      'PostLayout.relatedPost' => APPBEAR_URL . 'options/img/blocks/relatedPost.png',
      'PostLayout.simplePost' => APPBEAR_URL . 'options/img/blocks/simplePost.png',
      'PostLayout.startThumbPost' => APPBEAR_URL . 'options/img/blocks/startThumbPost.png',
      'PostLayout.startThumbPostCompact' => APPBEAR_URL . 'options/img/blocks/startThumbPostCompact.png',
      ),
      'options' => array(
      'width' => '155px',
      ),
    ));
    $section->add_field(array(
      'name' => __( 'Is first post "Featured"?', 'textdomain' ),
      'id' => 'local-firstfeatured',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      ),
      'desc' => __( 'Enable this to make the first post of this section with different post layout', 'textdomain' ),
    ));
    $section->add_field(array(
      'id' => 'firstFeatured',
      'name' => __( 'First Post Layout', 'textdomain' ),
      'type' => 'image_selector',
      'default' => 'PostLayout.featuredPost',
      'items' => array(
      'PostLayout.cardPost' => APPBEAR_URL . 'options/img/blocks/cardPost.png',
      'PostLayout.featuredMetaPost' => APPBEAR_URL . 'options/img/blocks/featuredMetaPost.png',
      'PostLayout.featuredPost' => APPBEAR_URL . 'options/img/blocks/featuredPost.png',
      'PostLayout.imagePost' => APPBEAR_URL . 'options/img/blocks/imagePost.png',
      'PostLayout.simplePost' => APPBEAR_URL . 'options/img/blocks/simplePost.png',
      ),
      'options' => array(
      'width' => '155px',
    'show_if' => array('local-firstfeatured', '=', 'true')
      )
    ));
    $section->add_field(array(
      'name' => __( 'Add separator after the block?', 'textdomain' ),
      'id' => 'separator',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $section->open_mixed_field(array('name' => __('Advanced Settings', 'textdomain' )));
    $section->add_field(array(
      'name' => __( 'Catgeory', 'textdomain' ),
      'id' => 'options-category',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $section->add_field(array(
      'name' => __( 'Read Time', 'textdomain' ),
      'id' => 'options-readtime',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $section->add_field(array(
      'name' => __( 'Created Date', 'textdomain' ),
      'id' => 'options-date',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $section->add_field(array(
      'name' => __( 'Favorite', 'textdomain' ),
      'id' => 'options-save',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $section->add_field(array(
      'name' => __( 'Share', 'textdomain' ),
      'id' => 'options-share',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $section->close_mixed_field();
    $settings->close_tab_item('homepage');


    // NOTE: Archives Page
    $settings->open_tab_item('archives');
    $archives_categories = $settings->add_section( array(
      'name' => __( 'Categories List Page Settings', 'textdomain' ),
      'id' => 'section-archives-categories',
      'options' => array( 'toggle' => true )
    ));
    $archives_categories->add_field( array(
      'id' => 'archives-categories-postlayout',
      'name' => __( 'Categories Page Layout', 'textdomain' ),
      'type' => 'image_selector',
      'default' => 'CategoriesLayout.cat1',
      'items' => array(
      'CategoriesLayout.cat1' => APPBEAR_URL . 'options/img/categories/cat_1.png',
      'CategoriesLayout.cat2' => APPBEAR_URL . 'options/img/categories/cat_2.png',
      'CategoriesLayout.cat3' => APPBEAR_URL . 'options/img/categories/cat_3.png',
      'CategoriesLayout.cat4' => APPBEAR_URL . 'options/img/categories/cat_4.png',
      'CategoriesLayout.cat5' => APPBEAR_URL . 'options/img/categories/cat_5.png',
      ),
      'options' => array(
      'width' => '155px',
      ),
    ));
    $archives_category = $settings->add_section( array(
      'name' => __( 'Single Category Page Settings', 'textdomain' ),
      'id' => 'section-archives-category',
      'options' => array( 'toggle' => true )
    ));
    $archives_category->add_field( array(
      'id' => 'archives-category-postlayout',
      'name' => __( 'Single Category Posts Layout', 'textdomain' ),
      'type' => 'image_selector',
      'default' => 'PostLayout.startThumbPost',
      'items' => array(
      'PostLayout.cardPost' => APPBEAR_URL . 'options/img/blocks/cardPost.png',
      'PostLayout.endThumbPost' => APPBEAR_URL . 'options/img/blocks/endThumbPost.png',
      'PostLayout.featuredMetaPost' => APPBEAR_URL . 'options/img/blocks/featuredMetaPost.png',
      'PostLayout.featuredPost' => APPBEAR_URL . 'options/img/blocks/featuredPost.png',
      'PostLayout.gridPost' => APPBEAR_URL . 'options/img/blocks/gridPost.png',
      'PostLayout.imagePost' => APPBEAR_URL . 'options/img/blocks/imagePost.png',
      'PostLayout.minimalPost' => APPBEAR_URL . 'options/img/blocks/minimalPost.png',
      'PostLayout.simplePost' => APPBEAR_URL . 'options/img/blocks/simplePost.png',
      'PostLayout.startThumbPost' => APPBEAR_URL . 'options/img/blocks/startThumbPost.png',
      'PostLayout.startThumbPostCompact' => APPBEAR_URL . 'options/img/blocks/startThumbPostCompact.png',
      ),
      'options' => array(
      'width' => '155px',
      ),
    ));
    $archives_category->add_field(array(
      'name' => __( 'Sort Order', 'textdomain' ),
      'id' => 'local-archives-category-sort',
      'type' => 'select',
      'default' => 'latest',
      'items' => array(
        'latest' => __( 'Recent Posts', 'textdomain' ),
        'rand' => __( 'Random Posts', 'textdomain' ),
        'modified' => __( 'Last Modified Posts', 'textdomain' ),
        'popular' => __( 'Most Commented posts', 'textdomain' ),
        'title' => __( 'Alphabetically', 'textdomain' ),
      )
    ));
    $archives_category->add_field(array(
      'name' => __( 'Number of posts to show', 'textdomain' ),
      'id' => 'local-archives-category-count',
      'type' => 'select',
      'default' => '10',
      'items' => array(
        '1' => __( '1 Post', 'textdomain' ),
        '2' => __( '2 Posts', 'textdomain' ),
        '3' => __( '3 Posts', 'textdomain' ),
        '4' => __( '4 Posts', 'textdomain' ),
        '5' => __( '5 Posts', 'textdomain' ),
        '6' => __( '6 Posts', 'textdomain' ),
        '7' => __( '7 Posts', 'textdomain' ),
        '8' => __( '8 Posts', 'textdomain' ),
        '9' => __( '9 Posts', 'textdomain' ),
        '10' => __( '10 Posts', 'textdomain' ),
      )
    ));
    $archives_category->open_mixed_field(array('name' => __('Advanced Settings', 'textdomain' )));
    $archives_category->add_field(array(
      'name' => __( 'Read Time', 'textdomain' ),
      'id' => 'archives-category-options-readtime',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_category->add_field(array(
      'name' => __( 'Created Date', 'textdomain' ),
      'id' => 'archives-category-options-date',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_category->add_field(array(
      'name' => __( 'Favorite', 'textdomain' ),
      'id' => 'archives-category-options-save',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_category->add_field(array(
      'name' => __( 'Share', 'textdomain' ),
      'id' => 'archives-category-options-share',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_category->close_mixed_field();

    $archives_search = $settings->add_section( array(
      'name' => __( 'Search Page Settings', 'textdomain' ),
      'id' => 'section-archives-search',
      'options' => array( 'toggle' => true )
    ));
    $archives_search->add_field( array(
      'id' => 'archives-search-postlayout',
      'name' => __( 'Search Page Posts Layout', 'textdomain' ),
      'type' => 'image_selector',
      'default' => 'PostLayout.startThumbPost',
      'items' => array(
      'PostLayout.cardPost' => APPBEAR_URL . 'options/img/blocks/cardPost.png',
      'PostLayout.endThumbPost' => APPBEAR_URL . 'options/img/blocks/endThumbPost.png',
      'PostLayout.featuredMetaPost' => APPBEAR_URL . 'options/img/blocks/featuredMetaPost.png',
      'PostLayout.featuredPost' => APPBEAR_URL . 'options/img/blocks/featuredPost.png',
      'PostLayout.gridPost' => APPBEAR_URL . 'options/img/blocks/gridPost.png',
      'PostLayout.imagePost' => APPBEAR_URL . 'options/img/blocks/imagePost.png',
      'PostLayout.minimalPost' => APPBEAR_URL . 'options/img/blocks/minimalPost.png',
      'PostLayout.simplePost' => APPBEAR_URL . 'options/img/blocks/simplePost.png',
      'PostLayout.startThumbPost' => APPBEAR_URL . 'options/img/blocks/startThumbPost.png',
      'PostLayout.startThumbPostCompact' => APPBEAR_URL . 'options/img/blocks/startThumbPostCompact.png',
      ),
      'options' => array(
      'width' => '155px',
      ),
    ));
    $archives_search->add_field(array(
      'name' => __( 'Sort Order', 'textdomain' ),
      'id' => 'local-archives-search-sort',
      'type' => 'select',
      'default' => 'latest',
      'items' => array(
        'latest' => __( 'Recent Posts', 'textdomain' ),
        'rand' => __( 'Random Posts', 'textdomain' ),
        'modified' => __( 'Last Modified Posts', 'textdomain' ),
        'popular' => __( 'Most Commented posts', 'textdomain' ),
        'title' => __( 'Alphabetically', 'textdomain' ),
      )
    ));
    $archives_search->add_field(array(
      'name' => __( 'Number of posts to show', 'textdomain' ),
      'id' => 'local-archives-search-count',
      'type' => 'select',
      'default' => '10',
      'items' => array(
        '1' => __( '1 Post', 'textdomain' ),
        '2' => __( '2 Posts', 'textdomain' ),
        '3' => __( '3 Posts', 'textdomain' ),
        '4' => __( '4 Posts', 'textdomain' ),
        '5' => __( '5 Posts', 'textdomain' ),
        '6' => __( '6 Posts', 'textdomain' ),
        '7' => __( '7 Posts', 'textdomain' ),
        '8' => __( '8 Posts', 'textdomain' ),
        '9' => __( '9 Posts', 'textdomain' ),
        '10' => __( '10 Posts', 'textdomain' ),
      )
    ));
    $archives_search->open_mixed_field(array('name' => __('Advanced Settings', 'textdomain' )));
    $archives_search->add_field(array(
      'name' => __( 'Catgeory', 'textdomain' ),
      'id' => 'archives-search-options-category',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_search->add_field(array(
      'name' => __( 'Read Time', 'textdomain' ),
      'id' => 'archives-search-options-readtime',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_search->add_field(array(
      'name' => __( 'Created Date', 'textdomain' ),
      'id' => 'archives-search-options-date',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_search->add_field(array(
      'name' => __( 'Favorite', 'textdomain' ),
      'id' => 'archives-search-options-save',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_search->add_field(array(
      'name' => __( 'Share', 'textdomain' ),
      'id' => 'archives-search-options-share',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_search->close_mixed_field();

    $archives_favorites = $settings->add_section( array(
      'name' => __( 'Favorites Page Settings', 'textdomain' ),
      'id' => 'section-archives-favorites',
      'options' => array( 'toggle' => true )
    ));
    $archives_favorites->add_field( array(
      'id' => 'archives-favorites-postlayout',
      'name' => __( 'Favorites Page Posts Layout', 'textdomain' ),
      'type' => 'image_selector',
      'default' => 'PostLayout.startThumbPost',
      'items' => array(
      'PostLayout.cardPost' => APPBEAR_URL . 'options/img/blocks/cardPost.png',
      'PostLayout.endThumbPost' => APPBEAR_URL . 'options/img/blocks/endThumbPost.png',
      'PostLayout.featuredMetaPost' => APPBEAR_URL . 'options/img/blocks/featuredMetaPost.png',
      'PostLayout.featuredPost' => APPBEAR_URL . 'options/img/blocks/featuredPost.png',
      'PostLayout.gridPost' => APPBEAR_URL . 'options/img/blocks/gridPost.png',
      'PostLayout.imagePost' => APPBEAR_URL . 'options/img/blocks/imagePost.png',
      'PostLayout.minimalPost' => APPBEAR_URL . 'options/img/blocks/minimalPost.png',
      'PostLayout.simplePost' => APPBEAR_URL . 'options/img/blocks/simplePost.png',
      'PostLayout.startThumbPost' => APPBEAR_URL . 'options/img/blocks/startThumbPost.png',
      'PostLayout.startThumbPostCompact' => APPBEAR_URL . 'options/img/blocks/startThumbPostCompact.png',
      ),
      'options' => array(
      'width' => '155px',
      ),
    ));
    $archives_favorites->add_field(array(
      'name' => __( 'Sort Order', 'textdomain' ),
      'id' => 'local-archives-favorites-sort',
      'type' => 'select',
      'default' => 'latest',
      'items' => array(
        'latest' => __( 'Recent Posts', 'textdomain' ),
        'rand' => __( 'Random Posts', 'textdomain' ),
        'modified' => __( 'Last Modified Posts', 'textdomain' ),
        'popular' => __( 'Most Commented posts', 'textdomain' ),
        'title' => __( 'Alphabetically', 'textdomain' ),
      )
    ));
    $archives_favorites->add_field(array(
      'name' => __( 'Number of posts to show', 'textdomain' ),
      'id' => 'local-archives-favorites-count',
      'type' => 'select',
      'default' => '10',
      'items' => array(
        '1' => __( '1 Post', 'textdomain' ),
        '2' => __( '2 Posts', 'textdomain' ),
        '3' => __( '3 Posts', 'textdomain' ),
        '4' => __( '4 Posts', 'textdomain' ),
        '5' => __( '5 Posts', 'textdomain' ),
        '6' => __( '6 Posts', 'textdomain' ),
        '7' => __( '7 Posts', 'textdomain' ),
        '8' => __( '8 Posts', 'textdomain' ),
        '9' => __( '9 Posts', 'textdomain' ),
        '10' => __( '10 Posts', 'textdomain' ),
      )
    ));
    $archives_favorites->open_mixed_field(array('name' => __('Advanced Settings', 'textdomain' )));
    $archives_favorites->add_field(array(
      'name' => __( 'Catgeory', 'textdomain' ),
      'id' => 'archives-favorites-options-category',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_favorites->add_field(array(
      'name' => __( 'Read Time', 'textdomain' ),
      'id' => 'archives-favorites-options-readtime',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_favorites->add_field(array(
      'name' => __( 'Created Date', 'textdomain' ),
      'id' => 'archives-favorites-options-date',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_favorites->add_field(array(
      'name' => __( 'Favorite', 'textdomain' ),
      'id' => 'archives-favorites-options-save',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_favorites->add_field(array(
      'name' => __( 'Share', 'textdomain' ),
      'id' => 'archives-favorites-options-share',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $archives_favorites->close_mixed_field();

    $settings->close_tab_item('archives');


    // NOTE: Styling Page
    $settings->open_tab_item('styling');

    $settings->open_mixed_field(array('name' => __('Shadow Color', 'textdomain' )));
    $settings->add_field(array(
      'id' => 'styling-themeMode_light-shadowColor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => 'rgba(0,0,0,0.15)',
      'options' => array(
      'format' => 'rgba',
      'show_default_button' => true,
      ),
    ));

    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-shadowColor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => 'rgba(0,0,0,0.15)',
      'options' => array(
      'format' => 'rgba',
      'show_default_button' => true,
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $settings->close_mixed_field();

    $settings->open_mixed_field(array('name' => __('Dividers Color', 'textdomain' )));
    $settings->add_field(array(
      'id' => 'styling-themeMode_light-dividerColor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => 'rgba(0,0,0,0.05)',
      'options' => array(
      'format' => 'rgba',
      'show_default_button' => true,
      ),
    ));

    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-dividerColor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => 'rgba(255,255,255,0.13)',
      'options' => array(
      'format' => 'rgba',
      'show_default_button' => true,
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $settings->close_mixed_field();

    $settings->open_mixed_field(array('name' => __('Inputs Background Color', 'textdomain' ),'desc' => __( 'All inputs background color on search, sort by select and indicator.', 'textdomain' ),));
    $settings->add_field(array(
      'id' => 'styling-themeMode_light-inputsbackgroundcolor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => 'rgba(0,0,0,0.04)',
      'options' => array(
      'format' => 'rgba',
      'show_default_button' => true,
      ),
    ));

    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-inputsbackgroundcolor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => 'rgba(255,255,255,0.07)',
      'options' => array(
      'format' => 'rgba',
      'show_default_button' => true,
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $settings->close_mixed_field();

    $settings->open_mixed_field(array('name' => __('Buttons Background color', 'textdomain' )));
    $settings->add_field(array(
      'id' => 'styling-themeMode_light-buttonsbackgroudcolor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#0088FF',
      'options' => array(
      'format' => 'hex',
      'show_default_button' => true,
      ),
    ));
    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-buttonsbackgroudcolor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#0088FF',
      'options' => array(
      'format' => 'hex',
      'show_default_button' => true,
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $settings->close_mixed_field();

    $settings->open_mixed_field(array('name' => __('Buttons Text color', 'textdomain' )));
    $settings->add_field(array(
      'id' => 'styling-themeMode_light-buttonTextColor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#FFFFFF',
      'options' => array(
      'format' => 'hex',
      'show_default_button' => true,
      ),
    ));
    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-buttonTextColor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#FFFFFF',
      'options' => array(
      'format' => 'hex',
      'show_default_button' => true,
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $settings->close_mixed_field();
    $settings->open_mixed_field(array('name' => __('Success Message Background color', 'textdomain' )));
    $settings->add_field(array(
      'id' => 'styling-themeMode_light-successcolor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#006900',
      'options' => array(
      'format' => 'hex',
      'show_default_button' => true,
      ),
    ));
    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-successcolor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#006900',
      'options' => array(
      'format' => 'hex',
      'show_default_button' => true,
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $settings->close_mixed_field();

    $settings->open_mixed_field(array('name' => __('Error Message Background color', 'textdomain' )));
    $settings->add_field(array(
      'id' => 'styling-themeMode_light-errorcolor',
      //'name' => __( 'Light Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#FF0000',
      'options' => array(
      'format' => 'hex',
      'show_default_button' => true,
      ),
    ));
    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-errorcolor',
      'name' => __( 'Dark Mode', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#FF0000',
      'options' => array(
      'format' => 'hex',
      'show_default_button' => true,
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));
    $settings->close_mixed_field();
    $settings->close_tab_item('styling');


    // NOTE: Advertisement / Admob Page
    $settings->open_tab_item('advertisement');

    $admob = $settings->add_section( array(
      'name' => __( 'Admob advertisement platform', 'textdomain' ),
      'id' => 'section-advertisement-admob',
      'options' => array( 'toggle' => true )
    ));

    $admob->add_field(
    array(
      'name' => __( 'Android App ID', 'textdomain' ),
      'id' => 'advertisement_android_app_id_text',
      'type' => 'text',
    ));

    $admob->add_field(
    array(
      'name' => __( 'iOS App ID', 'textdomain' ),
      'id' => 'advertisement_ios_app_id_text',
      'type' => 'text',
    ));

    $admob->open_mixed_field(array('name' => __('Admob Banner', 'textdomain' )));

    $admob->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'local-admob_banner',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));

    $admob->add_field(
      array(
        'name' => __( 'Android ID', 'textdomain' ),
        'id' => 'advertisement_android_banner_id_text',
        'type' => 'text',
        'options'	=>	array(
        'show_if' => array('local-admob_banner', '=', 'true')
      ),
    ));

    $admob->add_field(
      array(
        'name' => __( 'iOS ID', 'textdomain' ),
        'id' => 'advertisement_ios_banner_id_text',
        'type' => 'text',
        'options'	=>	array(
        'show_if' => array('local-admob_banner', '=', 'true')
      ),
    ));

    $admob->close_mixed_field();

    $admob->open_mixed_field(array('name' => __('Admob Banner Positions', 'textdomain' ),'options'	=>	array('show_if' => array('local-admob_banner', '=', 'true')),));

    $admob->add_field(
      array(
        'name' => __( 'Above the Top Bar', 'textdomain' ),
        'id' => 'advertisement_top_toggle',
        'type' => 'switcher',
        'default'	=>	'false',
        'options' => array(
          'on_value' => 'true',
          'off_value' => 'false'
        )
    ));

    $admob->add_field(
      array(
        'name' => __( 'Above the Bottom Bar', 'textdomain' ),
        'id' => 'advertisement_bottom_toggle',
        'type' => 'switcher',
        'default'	=>	'false',
        'options' => array(
          'on_value' => 'true',
          'off_value' => 'false'
        )
    ));


    $admob->add_field(
      array(
        'name' => __( 'At the end of the Posts', 'textdomain' ),
        'id' => 'advertisement_after_post_toggel',
        'type' => 'switcher',
        'default'	=>	'false',
        'options' => array(
          'on_value' => 'true',
          'off_value' => 'false'
        )
    ));

    $admob->close_mixed_field();

    $admob->open_mixed_field(array('name' => __('Admob Interstatial', 'textdomain' )));

    $admob->add_field(
      array(
        'name' => __( 'Enable', 'textdomain' ),
        'id' => 'local-advertisement_admob_interstatial',
        'type' => 'switcher',
        'default'	=>	'false',
        'options' => array(
          'on_value' => 'true',
          'off_value' => 'false'
        )
    ));


    $admob->add_field(
      array(
        'name' => __( 'Android ID', 'textdomain' ),
        'id' => 'advertisement_android_interstatial_id_text',
        'type' => 'text',
        'options'	=>	array(
        'show_if' => array('local-advertisement_admob_interstatial', '=', 'true')
      ),
    ));

    $admob->add_field(
    array(
      'name' => __( 'iOS ID', 'textdomain' ),
      'id' => 'advertisement_ios_interstatial_id_text',
      'type' => 'text',
    'options'	=>	array(
    'show_if' => array('local-advertisement_admob_interstatial', '=', 'true')
      ),
    ));

    $admob->close_mixed_field();

    $admob->open_mixed_field(array('name' => __('Admob Interstatial Positions', 'textdomain' ),'options'	=>	array('show_if' => array('local-advertisement_admob_interstatial', '=', 'true')),));

    $admob->add_field(
    array(
      'name' => __( 'Before View Post', 'textdomain' ),
      'id' => 'advertisement_interstatial_before_post_toggle',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $admob->close_mixed_field();

    $admob->open_mixed_field(array('name' => __('Admob Rewarded', 'textdomain' )));

    $admob->add_field(
    array(
      'name' => __( 'Enable', 'textdomain' ),
      'id' => 'local-advertisement_android_rewarded',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));

    $admob->add_field(
    array(
      'name' => __( 'Android ID', 'textdomain' ),
      'id' => 'advertisement_android_rewarded_id_text',
      'type' => 'text',
    'options'	=>	array(
    'show_if' => array('local-advertisement_android_rewarded', '=', 'true')
      ),
    ));
    $admob->add_field(
    array(
      'name' => __( 'iOS ID', 'textdomain' ),
      'id' => 'advertisement_android_rewarded_ios_text',
      'type' => 'text',
    'options'	=>	array(
    'show_if' => array('local-advertisement_android_rewarded', '=', 'true')
      ),
    ));
    $admob->close_mixed_field();
    $admob->open_mixed_field(array('name' => __('Admob Rewarded Positions', 'textdomain' ),'options'	=>	array('show_if' => array('local-advertisement_android_rewarded', '=', 'true')),));
    $admob->add_field(
    array(
      'name' => __( 'Before View Post', 'textdomain' ),
      'id' => 'advertisement_rewarded_before_post_toggle',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $admob->close_mixed_field();


    $settings->close_tab_item('advertisement');


    // NOTE: User Guide Page
    $settings->open_tab_item('user_guide');

    $section_header_2 = $settings->add_section( array(
      'name' => __( 'User Guide Slides', 'textdomain' ),
      'id' => 'local-section_userguide_slides',
      'desc' => __( 'Slides which your clients will see when they first start your application', 'textdomain' ),
      'options' => array( 'toggle' => true )
    ));

    $section_header_2->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'onboarding',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));

    $slides = $section_header_2->add_group( array(
      'name' => __('User Guide Slides', 'textdomain'),
      'id' => 'onboardmodels',
      'options' => array(
      'add_item_text' => __('New Slide', 'textdomain'),
        'show_if' => array('onboarding', '=', 'true'),
      ),
      'controls' => array(
      'name' =>  __('Slide', 'textdomain').' #',
      'readonly_name' => false,
      'images' => true,
      'default_image' => APPBEAR_URL . '/img/transparent.png',
      'image_field_id' => 'image',
      'height' => '190px',
      ),
    ));

    $slides->add_field(array(
      'id' => 'title',
      'name' => __('Slide Title', 'textdomain'),
      'type' => 'text',
      'grid' => '3-of-6',
    ));

    $slides->add_field(array(
      'id' => 'subTitle',
      'name' => __('SubTitle', 'textdomain'),
      'type' => 'text',
    'grid' => '3-of-6'
    ));

    $slides->add_field(array(
      'id' => 'image',
      'name' => __( 'Image', 'textdomain' ),
      'type' => 'file',
    ));

    $settings->close_tab_item('user_guide');


    // NOTE: Typography Page
    $settings->open_tab_item('typography');

    $fontfamily = $settings->add_section( array(
      'name' => __( 'Font Family', 'textdomain' ),
      'id' => 'section-typography-fontfamily',
      'options' => array( 'toggle' => true )
    ));
    $fontfamily->add_field( array(
      'id' => 'section-typography-fontfamily-heading',
      'name' => __( 'Headings Font Family',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => array(
        '' => __('Default', 'textdomain'),
        __( 'Web Safe Fonts',   'textdomain' ) => AppbearItems::web_safe_fonts(),
        __( 'Google Fonts',   'textdomain' ) => AppbearItems::dart_google_fonts()
      ),
      'options' => array(
        'search' => true, // NOTE: Displays an input to search items. Default: false
      )
    ));
    $fontfamily->add_field( array(
      'id' => 'section-typography-fontfamily-body',
      'name' => __( 'Body Font Family',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => array(
        '' => __('Default', 'textdomain'),
        'Web Safe Fonts' => AppbearItems::web_safe_fonts(),
        'Google Fonts' => AppbearItems::dart_google_fonts()
      ),
      'options' => array(
        'search' => true, // NOTE: Displays an input to search items. Default: false
      )
    ));


    $font = $settings->add_section( array(
      'name' => __( 'Font Sizes, Weights and Line Heights', 'textdomain' ),
      'id' => 'section-typography-font',
      'options' => array( 'toggle' => true )
    ));
    $font->open_mixed_field(array('name' => __('Heading: H1', 'textdomain' )));
    $font->add_field( array(
      'id' => 'section-typography-font-h1-size',
      'name' => __( 'Font Size',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_size(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h1-line_height',
      'name' => __( 'Line Height',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::line_height(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h1-weight',
      'name' => __( 'Font Weight',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_weight(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h1-transform',
      'name' => __( 'Capitalization',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::text_transform(),
    ));
    $font->close_mixed_field();
    $font->open_mixed_field(array('name' => __('Heading: H2', 'textdomain' )));
    $font->add_field( array(
      'id' => 'section-typography-font-h2-size',
      'name' => __( 'Font Size',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_size(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h2-line_height',
      'name' => __( 'Line Height',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::line_height(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h2-weight',
      'name' => __( 'Font Weight',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_weight(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h2-transform',
      'name' => __( 'Capitalization',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::text_transform(),
    ));
    $font->close_mixed_field();
    $font->open_mixed_field(array('name' => __('Heading: H3', 'textdomain' ),'desc' => __( 'Example: Sections Title')));
    $font->add_field( array(
      'id' => 'section-typography-font-h3-size',
      'name' => __( 'Font Size',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_size(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h3-line_height',
      'name' => __( 'Line Height',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::line_height(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h3-weight',
      'name' => __( 'Font Weight',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_weight(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h3-transform',
      'name' => __( 'Capitalization',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::text_transform(),
    ));
    $font->close_mixed_field();
    $font->open_mixed_field(array('name' => __('Heading: H4', 'textdomain' ),'desc' => __( 'Example: Post Titles',   'textdomain' )));
    $font->add_field( array(
      'id' => 'section-typography-font-h4-size',
      'name' => __( 'Font Size',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_size(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h4-line_height',
      'name' => __( 'Line Height',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::line_height(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h4-weight',
      'name' => __( 'Font Weight',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_weight(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h4-transform',
      'name' => __( 'Capitalization',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::text_transform(),
    ));
    $font->close_mixed_field();
    $font->open_mixed_field(array('name' => __('Heading: H5', 'textdomain' )));
    $font->add_field( array(
      'id' => 'section-typography-font-h5-size',
      'name' => __( 'Font Size',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_size(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h5-line_height',
      'name' => __( 'Line Height',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::line_height(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h5-weight',
      'name' => __( 'Font Weight',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_weight(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h5-transform',
      'name' => __( 'Capitalization',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::text_transform(),
    ));
    $font->close_mixed_field();
    $font->open_mixed_field(array('name' => __('Heading: H6', 'textdomain' )));
    $font->add_field( array(
      'id' => 'section-typography-font-h6-size',
      'name' => __( 'Font Size',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_size(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h6-line_height',
      'name' => __( 'Line Height',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::line_height(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h6-weight',
      'name' => __( 'Font Weight',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_weight(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-h6-transform',
      'name' => __( 'Capitalization',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::text_transform(),
    ));
    $font->close_mixed_field();
    $font->open_mixed_field(array('name' => __('Subtitle 1', 'textdomain' ),'desc' => __( 'Example: Meta (tags, author, category, ...)',   'textdomain' ),));
    $font->add_field( array(
      'id' => 'section-typography-font-subtitle1-size',
      'name' => __( 'Font Size',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_size(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-subtitle1-line_height',
      'name' => __( 'Line Height',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::line_height(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-subtitle1-weight',
      'name' => __( 'Font Weight',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_weight(),
    ));
    $font->add_field( array(
      'id' => 'section-typography-font-subtitle1-transform',
      'name' => __( 'Capitalization',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::text_transform(),
    ));

    $font->close_mixed_field();

    $font->open_mixed_field(array('name' => __('Subtitle 2', 'textdomain' ),'desc' => __( 'Example: Bottom Bar and Homepage tabs Text')));

    $font->add_field( array(
      'id' => 'section-typography-font-subtitle2-size',
      'name' => __( 'Font Size',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_size(),
    ));

    $font->add_field( array(
      'id' => 'section-typography-font-subtitle2-line_height',
      'name' => __( 'Line Height',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::line_height(),
    ));

    $font->add_field( array(
      'id' => 'section-typography-font-subtitle2-weight',
      'name' => __( 'Font Weight',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_weight(),
    ));

    $font->add_field( array(
      'id' => 'section-typography-font-subtitle2-transform',
      'name' => __( 'Capitalization',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::text_transform(),
    ));

    $font->close_mixed_field();

    $font->open_mixed_field(array('name' => __('Body 1', 'textdomain' ),'desc' => __( 'Example: Page Titles')));

    $font->add_field( array(
      'id' => 'section-typography-font-body1-size',
      'name' => __( 'Font Size',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_size(),
    ));

    $font->add_field( array(
      'id' => 'section-typography-font-body1-line_height',
      'name' => __( 'Line Height',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::line_height(),
    ));

    $font->add_field( array(
      'id' => 'section-typography-font-body1-weight',
      'name' => __( 'Font Weight',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_weight(),
    ));

    $font->add_field( array(
      'id' => 'section-typography-font-body1-transform',
      'name' => __( 'Capitalization',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::text_transform(),
    ));

    $font->close_mixed_field();

    $font->open_mixed_field(array('name' => __('Body 2', 'textdomain' )));

    $font->add_field( array(
      'id' => 'section-typography-font-body2-size',
      'name' => __( 'Font Size',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_size(),
    ));

    $font->add_field( array(
      'id' => 'section-typography-font-body2-line_height',
      'name' => __( 'Line Height',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::line_height(),
    ));

    $font->add_field( array(
      'id' => 'section-typography-font-body2-weight',
      'name' => __( 'Font Weight',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::font_weight(),
    ));

    $font->add_field( array(
      'id' => 'section-typography-font-body2-transform',
      'name' => __( 'Capitalization',   'textdomain' ),
      'type' => 'select',
      'default' => '',
      'items' => AppbearItems::text_transform(),
    ));

    $font->close_mixed_field();

    $settings->close_tab_item('typography');


    // NOTE: Settings Page
    $settings->open_tab_item('settings');

    $section_header_social = $settings->add_section( array(
      'name' => __( 'Social', 'textdomain' ),
      'id' => 'local-section_social_links',
      'desc' => __( 'Add social networks links to your application', 'textdomain' ),
      'options' => array( 'toggle' => true )
    ));

    $section_header_social->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'social_enabled',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));

    $socialLinks = $section_header_social->add_group( array(
      'id' => 'social',
      'name' => __('Social', 'textdomain'),
      'controls' => array(
        'name' =>  __('Social Link', 'textdomain').' #',
        'readonly_name' => false,
        'images' => false,
      ),
      'options' => array(
      'add_item_text' => __('New Social Link', 'textdomain'),
        'show_if' => array('social_enabled', '=', 'true'),
      ),
    ));

    $socialLinks->open_mixed_field(array('name' => __('Title', 'textdomain' )));
    $socialLinks->add_field(array(
      'name' => __( 'Enable', 'textdomain' ),
      'id' => 'social_link_title',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));
    $socialLinks->add_field(array(
      'id' => 'title',
      'name' => __('Social Link Title', 'textdomain'),
      'type' => 'text',
      'grid' => '3-of-6',
      'options' => array(
        'show_if' => array( 'social_link_title', '=', 'true' ),
      ),
    ));
    $socialLinks->close_mixed_field();

    $socialLinks->add_field( array(
      'name' => __('Icon', 'textdomain'),
      'id' => 'icon',
      'type' => 'icon_selector',
      'default' => '0xe95d',
      'items' => array_merge(
        AppbearItems::icon_fonts()
      ),
      'options' => array(
        'wrap_height' => '220px',
        'size' => '36px',
        'hide_search' => false,
        'hide_buttons' => true,
      ),
    ));

    $socialLinks->add_field(array(
      'id' => 'url',
      'name' => __('URL', 'textdomain'),
      'type' => 'text',
      'grid' => '3-of-6'
    ));

    $settings->open_mixed_field(array('name' => __('Styling', 'textdomain' ),));

    $settings->add_field(array(
      'id' => 'styling-themeMode_light-settingBackgroundColor',
      'name' => __( 'Background Color', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#0088ff',
    ));

    $settings->add_field(array(
      'id' => 'styling-themeMode_light-settingTextColor',
      'name' => __( 'Text Color', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#0088ff',
    ));

    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-settingBackgroundColor',
      'name' => __( 'Background Color (Dark Mode)', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#0088ff',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));

    $settings->add_field(array(
      'id' => 'styling-themeMode_dark-settingTextColor',
      'name' => __( 'Text Color (Dark Mode)', 'textdomain' ),
      'type' => 'colorpicker',
      'default' => '#0088ff',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));

    $settings->close_mixed_field();

    $settings->add_field(array(
      'name' => __( 'Text size option', 'textdomain' ),
      'id' => 'settingspage-textSize',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      ),
      'desc' => __( 'Give your vistiors the ability to change the text size of the application', 'textdomain' ),
    ));

    $settings->add_field(array(
      'name' => __( 'Switch between Dark/Light modes', 'textdomain' ),
      'id' => 'settingspage-darkMode',
      'type' => 'switcher',
      'default'	=>	'false',
      'desc' => __( 'Give your vistiors the ability to switch between Dark/Light modes', 'textdomain' ),
      'options' => array(
      'on_value' => 'true',
      'off_value' => 'false',
        'show_if' => array('switch_theme_mode', '=', 'true'),
      )
    ));

    $settings->add_field(array(
      'name' => __( 'Rate application', 'textdomain' ),
      'id' => 'settingspage-rateApp',
      'type' => 'switcher',
      'default'	=>	'true',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      ),
      'desc' => __( 'Show rate appliction button on the settings page', 'textdomain' ),
    ));

    $settings->add_field(array(
      'name' => __( 'Share application', 'textdomain' ),
      'id' => 'local-settingspage-share',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      ),
      'desc' => __( 'Show share appliction button on the settings page', 'textdomain' ),
    ));

    $settings->open_mixed_field(array('name' => __('Share info', 'textdomain' ),'options'	=>	array('show_if' => array('local-settingspage-share', '=', 'true'))));

    $settings->add_field(
    array(
      'name' => __( 'Headline', 'textdomain' ),
      'id' => 'settingspage-shareApp-title',
    'type' => 'text'
    ));

    $settings->add_field(array(
      'name' => __( 'Image', 'textdomain' ),
      'id' => 'settingspage-shareApp-image',
      'type' => 'file',
      'desc' => __( 'The image that will be shared with the application link', 'textdomain' ),
    ));

    $settings->add_field(
    array(
      'name' => __( 'Android Link', 'textdomain' ),
      'id' => 'settingspage-shareApp-android',
    'type' => 'text'
    ));

    $settings->add_field(
    array(
      'name' => __( 'iOS Link', 'textdomain' ),
      'id' => 'settingspage-shareApp-ios',
    'type' => 'text'
    ));

    $settings->close_mixed_field();

    $settings->open_mixed_field(array('name' => __('About us', 'textdomain' )));

    $settings->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'local-settingspage-aboutus',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));

    $settings->add_field( array(
      'id' => 'settingspage-aboutUs',
      'name' => __( 'About us page',   'textdomain' ),
      'type' => 'select',
      'items' => AppbearItems::posts_by_post_type( 'page', array( 'posts_per_page' => -1 ) ),
    'options'	=>	array(
    'show_if' => array('local-settingspage-aboutus', '=', 'true')
      ),
    ));

    $settings->close_mixed_field();

    $settings->add_field( array(
      'id' => 'settingspage-privacyPolicy',
      'name' => __( 'Privacy page',   'textdomain' ),
      'type' => 'select',
      'default' => get_option( 'wp_page_for_privacy_policy' ),
      'items' => AppbearItems::posts_by_post_type( 'page' ),
    ));

    //  . ' ' . get_option( 'wp_page_for_privacy_policy' )
    $settings->add_field( array(
      'id' => 'settingspage-termsAndConditions',
      'name' => __( 'Terms and conditions page',   'textdomain' ),
      'type' => 'select',
      'default' => get_option( 'wp_page_for_privacy_policy' ),
      'items' => AppbearItems::posts_by_post_type( 'page' ),
    ));

    $settings->open_mixed_field(array('name' => __('Contact us', 'textdomain' )));

    $settings->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'settingspage-contactus',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));

    $settings->add_field(array(
      'name' => __( 'Email/s', 'textdomain' ),
      'id' => 'local-settingspage-contactus',
      'type' => 'textarea',
      'desc' => __( 'Those emails will be the emails which will receive the contact us messages from the applications.', 'textdomain' ),
      'grid' => '5-of-6',
      'default' => get_bloginfo( 'admin_email' ),
      'options' => array(
      'desc_tooltip' => true,
    'show_if' => array('settingspage-contactus', '=', 'true')
      )
    ));

    $settings->close_mixed_field();

    $settings->add_field(array(
      'name' => __( 'About application', 'textdomain' ),
      'id' => 'local-settingspage-aboutapp',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      ),
      'desc' => __( 'Show about appliction page onn the settings page, which will be needed if you need to activate the development mode too', 'textdomain' ),
    ));
    $settings->open_mixed_field(array('name' => __('About Info', 'textdomain' ),'options'	=>	array('show_if' => array('local-settingspage-aboutapp', '=', 'true'))));

    $settings->add_field(array(
      'name' => __('Logo (Light)', 'textdomain' ),
      'id' => 'settingspage-aboutapp-logo-light',
      'type' => 'file',
      'default' => APPBEAR_URL .'img/jannah-logo-light.png',
    ));

    $settings->add_field(array(
      'name' => __('Logo (Dark)', 'textdomain' ),
      'id' => 'settingspage-aboutapp-logo-dark',
      'type' => 'file',
      'default' => APPBEAR_URL .'img/jannah-logo-dark.png',
      'options' => array(
        'show_if' => array('switch_theme_mode', '=', 'true'),
      ),
    ));

    $settings->add_field(
    array(
      'name' => __( 'Title', 'textdomain' ),
      'id' => 'settingspage-aboutapp-title',
      'type' => 'text',
      'default' => get_bloginfo( 'name' ),
    ));

    $settings->add_field(
    array(
      'name' => __( 'Description', 'textdomain' ),
      'id' => 'settingspage-aboutapp-content',
      'type' => 'textarea',
      'default' => get_bloginfo( 'description' ),
    ));

    $settings->close_mixed_field();
    $settings->add_field(array(
      'name' => __( 'Enable Demos', 'textdomain' ),
      'id' => 'settingspage-demos',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      ),
    ));

    $settings->open_mixed_field(
    array(
      'name' => __('Development Mode', 'textdomain' ),
      'desc' => __( 'The development mode allows you to only save changes to your mobile application and after you see the result, you can deactivate it and publish the changes to all your visitors.', 'textdomain' ),
    'options'	=>	array('show_if' => array('local-settingspage-aboutapp', '=', 'true'))
    )
    );

    $settings->add_field(array(
      'name' => __( 'Enabled', 'textdomain' ),
      'id' => 'settingspage-devmode',
      'type' => 'switcher',
      'default'	=>	'false',
      'options' => array(
        'on_value' => 'true',
        'off_value' => 'false'
      )
    ));


    $settings->close_mixed_field();

    $settings->close_tab_item('settings');


    // NOTE: Import / Export Page
    $settings->open_tab_item('import');
    $settings->add_import_field(array(
      'name' => 'Select Demo',
      'default' => 'http://appbearframework.com/demos/blank.json',
      'desc' => 'Choose a demo, then click import button',
      'items' => array(
        APPBEAR_URL . 'options/demos/demo1.json' => APPBEAR_URL . 'options/img/demos/demo1.jpg',
        APPBEAR_URL . 'options/demos/demo2.json' => APPBEAR_URL . 'options/img/demos/demo2.jpg',
        APPBEAR_URL . 'options/demos/demo3.json' => APPBEAR_URL . 'options/img/demos/demo3.jpg',
        APPBEAR_URL . 'options/demos/demo4.json' => APPBEAR_URL . 'options/img/demos/demo4.jpg',
        APPBEAR_URL . 'options/demos/demo5.json' => APPBEAR_URL . 'options/img/demos/demo5.jpg',
        APPBEAR_URL . 'options/demos/demo6.json' => APPBEAR_URL . 'options/img/demos/demo6.jpg',
        APPBEAR_URL . 'options/demos/demo7.json' => APPBEAR_URL . 'options/img/demos/demo7.jpg',
        APPBEAR_URL . 'options/demos/demo8.json' => APPBEAR_URL . 'options/img/demos/demo8.jpg'
      ),
      'options' => array(
        'import_from_file' => false,
        'import_from_url' => false,
        'width' => '200px'
      )
    ));

    $settings->add_export_field(array(
      'name' => 'Export',
      'desc' => 'Download and make a backup of your options.',
    ));
    $settings->close_tab_item('import');

    $settings->close_tab('main-tab');
  }


  /*
   * Initialize translaions page options
   */
  protected function _initTranslationsPage() {
		$translations_arg = array(
			'id' => 'appbear-translations',
			'title' => 'appBear Translations',
			'menu_title' => 'Translations',
			'icon' => APPBEAR_URL . 'img/appbear-light-small.png',//Menu icon
			'skin' => 'purple',// Skins: blue, lightblue, green, teal, pink, purple, bluepurple, yellow, orange'
			'layout' => 'wide',//wide
			'header' => array(
				'icon' => '<img src="' . APPBEAR_URL . 'img/a-logo.svg"/>',
				'desc' => 'No coding required. Your app syncs with your site automatically.',
			),
			'import_message' => __( 'Settings imported. This is just an example. No data imported.', 'textdomain' ),
			'capability' => 'manage_options',
			'parent' => 'appbear-settings',
		);
		$translations = appbear_new_admin_page( $translations_arg );

		$translations_section	=	$translations->add_section( array(
			'name' => 'Tanslations',
			'id' => 'section-general-header',
			'options' => array(
				'toggle' => true,
			)
		));

    $translations_section->add_field(array(
			'name' => 'Back',
			'default' => "Back",
			'id' => 'translate-back',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'SKIP',
			'default' => "SKIP",
			'id' => 'translate-skip',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Done',
			'default' => "Done",
			'id' => 'translate-done',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Contact Us',
			'default' => "Contact Us",
			'id' => 'translate-contactUs',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Receiving updates\nfrom server...',
			'default' => "Receiving updates\n from server...",
			'id' => 'translate-loadingUpdates',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Base URL',
			'default' => "Base URL",
			'id' => 'translate-baseUrl',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Change Base Url',
			'default' => "Change Base Url",
			'id' => 'translate-baseUrlTitle',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Change the url where the data comes from, Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Mus mauris vitae ultricies leo integer.',
			'default' => "Change the url where the data comes from, Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Mus mauris vitae ultricies leo integer.",
			'id' => 'translate-baseUrlDesc',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Url should not be empty.',
			'default' => "Url should not be empty.",
			'id' => 'translate-emptyBaseUrl',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'This Url is already set.',
			'default' => "This Url is already set.",
			'id' => 'translate-alreadyBaseUrl',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => "Let's talk",
			'default' => "Let's talk",
			'id' => 'translate-contactUsTitle',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Sahifa is your news entertainment music fashion website. We provide you with the latest breaking news and videos straight from entertainment industry world.',
			'default' => "Sahifa is your news entertainment music fashion website. We provide you with the latest breaking news and videos straight from entertainment industry world.",
			'id' => 'translate-contactUsSubTitle',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Your Name',
			'default' => "Your Name",
			'id' => 'translate-yourName',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Your Email',
			'default' => "Your Email",
			'id' => 'translate-yourEmail',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Your Message',
			'default' => "Your Message",
			'id' => 'translate-yourMessage',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Send',
			'default' => "Send",
			'id' => 'translate-send',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Settings',
			'default' => "Settings",
			'id' => 'translate-settings',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'About Us',
			'default' => "About Us",
			'id' => 'translate-aboutUs',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Layout',
			'default' => "Layout",
			'id' => 'translate-layout',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Text Size',
			'default' => "Text Size",
			'id' => 'translate-textSize',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Aa',
			'default' => "Aa",
			'id' => 'translate-aA',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Dark Mode',
			'default' => "Dark Mode",
			'id' => 'translate-darkMode',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Rate this app',
			'default' => "Rate this app",
			'id' => 'translate-rateApp',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Share the app',
			'default' => "Share the app",
			'id' => 'translate-shareApp',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Privacy policy',
			'default' => "Privacy policy",
			'id' => 'translate-privacyPolicy',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Terms and Conditions',
			'default' => "Terms and Conditions",
			'id' => 'translate-termsAndConditions',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Powered by',
			'default' => "Powered by",
			'id' => 'translate-poweredBy',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Logout',
			'default' => "Logout",
			'id' => 'translate-logout',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'RELATED POSTS',
			'default' => "RELATED POSTS",
			'id' => 'translate-relatedPosts',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'LEAVE A COMMENT',
			'default' => "LEAVE A COMMENT",
			'id' => 'translate-leaveComment',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'COMMENTS',
			'default' => "COMMENTS",
			'id' => 'translate-commentsCount',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Reply',
			'default' => "Reply",
			'id' => 'translate-reply',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Reply to',
			'default' => "Reply to",
			'id' => 'translate-replyTo',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'By',
			'default' => "By",
			'id' => 'translate-By',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Cancel',
			'default' => "Cancel",
			'id' => 'translate-cancel',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Submit',
			'default' => "Submit",
			'id' => 'translate-submit',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Comment',
			'default' => "Comment",
			'id' => 'translate-comment',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Name',
			'default' => "Name",
			'id' => 'translate-name',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Post Comment',
			'default' => "Post Comment",
			'id' => 'translate-postComment',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Post Reply',
			'default' => "Post Reply",
			'id' => 'translate-postReply',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => "Let's go",
			'default' => "Let's go",
			'id' => 'translate-lets',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'No Favorites Yet',
			'default' => "No Favorites Yet",
			'id' => 'translate-noFav',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'No Posts Found',
			'default' => "No Posts Found",
			'id' => 'translate-noPosts',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => ' must not be empty',
			'default' => " must not be empty",
			'id' => 'translate-mustNotBeEmpty',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Loading more...',
			'default' => "Loading more...",
			'id' => 'translate-loadingMore',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Load more',
			'default' => "Load more",
			'id' => 'translate-loadingMoreQuestions',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Something went wrong',
			'default' => "Something went wrong",
			'id' => 'translate-someThingWentWrong',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Search',
			'default' => "Search",
			'id' => 'translate-search',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'No more items',
			'default' => "No more items",
			'id' => 'translate-noMore',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Removed from favourites',
			'default' => "Removed from favourites",
			'id' => 'translate-removedToFav',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Added to favourites',
			'default' => "Added to favourites",
			'id' => 'translate-addedToFav',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Type to search',
			'default' => "Type to search",
			'id' => 'translate-typeToSearch',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Version ',
			'default' => "Version ",
			'id' => 'translate-version',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Your app version is up to date ',
			'default' => "Your app version is up to date ",
			'id' => 'translate-yourVersionUpToDate',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Update the latest version ',
			'default' => "Update the latest version ",
			'id' => 'translate-yourVersionNotUpToDate',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Continue clicking to activate development mode',
			'default' => "Continue clicking to activate development mode",
			'id' => 'translate-upgradeHint',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'About app',
			'default' => "About app",
			'id' => 'translate-aboutApp',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Taps left',
			'default' => "Taps left",
			'id' => 'translate-tapsLeft',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Development Mode is active',
			'default' => "Development Mode is active",
			'id' => 'translate-devModeActive',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'No Results',
			'default' => "No Result",
			'id' => 'translate-noResults',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'No Sections',
			'default' => "Please add home sections from admin panel",
			'id' => 'translate-noSections',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'No Main Page',
			'default' => "At least one main page must be added from admin panel",
			'id' => 'translate-noMainPage',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'No Boards',
			'default' => "No boarding slides",
			'id' => 'translate-noBoards',
			'type' => 'text',
			'grid' => '6-of-6',
		));
			$translations_section->add_field(array(
			'name' => 'Error Page Title',
			'default' => "Oops",
			'id' => 'translate-errorPageTitle',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'Retry',
			'default' => "Retry",
			'id' => 'translate-retry',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
			'name' => 'No Internet Connection!',
			'default' => "No Internet Connection!",
			'id' => 'translate-noInternet',
			'type' => 'text',
			'grid' => '6-of-6',
		));

    $translations_section->add_field(array(
      'name' => 'Please check your internet connection and try again',
      'default' => "Please check your internet connection and try again",
      'id' => 'translate-checkInternet',
      'type' => 'text',
      'grid' => '6-of-6',
		));

		$translations_section->add_field(array(
			'name' => 'No Comments',
			'default' => "No Comments",
			'id' => 'translate-noComments',
			'type' => 'text',
			'grid' => '6-of-6',
		));

		$translations_section->add_field(array(
			'name' => 'See All',
			'default' => "See All",
			'id' => 'translate-seeMore',
			'type' => 'text',
			'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Confirm Demo Title',
      'default' => "Confirm Demo Title",
      'id' => 'translate-confirmDemoTitle',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Confirm Demo Message',
      'default' => "Confirm Demo Message",
      'id' => 'translate-confirmDemoMessage',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Choose Your Demo',
      'default' => "Choose Your Demo",
      'id' => 'translate-chooseYourDemo',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Confirm Reset Title',
      'default' => "Confirm Reset Title",
      'id' => 'translate-confirmResetTitle',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Confirm Reset Message',
      'default' => "Confirm Reset Message",
      'id' => 'translate-confirmResetMessage',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Yes',
      'default' => "Yes",
      'id' => 'translate-yes',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Reset',
      'default' => "Reset",
      'id' => 'translate-reset',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Custom Demo',
      'default' => "Custom Demo",
      'id' => 'translate-customDemo',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Custom Demo Title',
      'default' => "Custom Demo Title",
      'id' => 'translate-customDemoTitle',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Custom Demo Body',
      'default' => "Custom Demo Body",
      'id' => 'translate-customDemoBody',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Confirm Custom Demo Title',
      'default' => "Confirm Custom Demo Title",
      'id' => 'translate-confirmCustomDemoTitle',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Confirm Custom Demo Message',
      'default' => "Confirm Custom Demo Message",
      'id' => 'translate-confirmCustomDemoMessage',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'This page intended for demo purposes only',
      'default' => "This page intended for demo purposes only",
      'id' => 'translate-demosHint',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Get Our',
      'default' => "Get Our",
      'id' => 'translate-getOur',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'AppBear',
      'default' => "AppBear",
      'id' => 'translate-appBear',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Plugin',
      'default' => "Plugin",
      'id' => 'translate-plugin',
      'type' => 'text',
      'grid' => '6-of-6',
    ));

    $translations_section->add_field(array(
      'name' => 'Next',
      'default' => "Next",
      'id' => 'translate-next',
      'type' => 'text',
      'grid' => '6-of-6',
    ));
  }


  /*
   * Initialize options for no or invalid license state
   */
  protected function _noLicenseInit() {
    if ( appbear_check_license() === true ) {
      return;
    }

		add_action( 'init', array( $this, 'appbear_plugin_updater' ) );
    add_action( 'admin_init', array( $this, 'appbear_register_option' ) );

		$activation_args = array(
			'id' => 'appbear-activation',
			'title' => 'Connect AppBear',
			'menu_title' => 'Connect AppBear',
			'menu_side_title' => 'Connect AppBear',
			'icon' => APPBEAR_URL . 'img/appbear-light-small.png',//Menu icon
			'skin' => 'purple',// Skins: blue, lightblue, green, teal, pink, purple, bluepurple, yellow, orange'
			'layout' => 'wide',//wide
			'header' => array(
				'icon' => '<img src="' . APPBEAR_URL . 'img/a-logo.svg"/>',
				'desc' => 'Connect and activate your AppBear account.',
			),
			'import_message' => __( 'Settings imported. This is just an example. No data imported.', 'textdomain' ),
			'capability' => 'manage_options',
			'parent' => 'appbear-settings',
		);

		$activation = appbear_new_admin_page( $activation_args );

		$activation_section	=	$activation->add_section( array(
			'name' => 'Activation',
			'id' => 'section-general-activation',
    ));

    $publicKey = appbear_get_public_key();
		$activation_section->add_field(array(
			'id' => 'custom-title',
			'name' => __( 'Enter your license key', 'textdomain' ),
			'type' => 'title',
			'desc' => (
        __('You have to activate your license before start controlling application settings, if you do not have key and you need to activate the demo version please type 000 in the Key field.')
        . '<br>'
        . ($publicKey
          ? ( __('Your public key is: ') . "( <strong>{$publicKey}</strong> )" )
          : ( __('Activate your license by saving changes to obtain a public key.') )
        )
      ),
    ));

		$activation_section->add_field(array(
			'name' => 'Key',
			'default' => $this->_getLicenseKey(),
			'id' => 'appbear_license_key',
			'type' => 'text',
			'grid' => '6-of-6',
    ));

    if ( get_option('appbear_license_status') === 'valid' && empty($publicKey) === false ) {
      $activation_section->add_field(array(
        'name' => '<strong style="color:green">'. __('License Active!') .'</strong>',
        'id' => 'appbear_public_key',
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
        'id' => 'appbear_public_key',
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
    return appbear_get_license_key();
  }
}
