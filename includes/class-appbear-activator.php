<?php

/**
 * Fired during plugin activation
 *
 * @link       http://appbear.io
 * @since      0.0.2
 *
 * @package    App_Bear
 * @subpackage App_Bear/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.2
 * @package    App_Bear
 * @subpackage App_Bear/includes
 * @author     AppBear <info@appbear.io>
 */
class App_Bear_Activator {

  /**
   * Internal storage of 3rd-party themes options,
   * plugin's options changes.
   *
   * @var array
   */
  private static $_themeOptions = array();
  private static $_pluginOptions = array();

	/**
	 * Activate plugin hook entry method
	 *
	 * @since    0.0.2
	 */
	public static function activate() {
    static::seedDefaultDemo();
    static::jannahThemeIntegration();
	}

  /**
   * Apply Default Demo to Plugin Options
   *
	 * @since    0.0.12
   * @return void
   */
  public static function seedDefaultDemo() {
    appbear_seed_default_demo(false);
  }

  /**
   * Integration with Jannah Theme
   *
	 * @since    0.0.11
   * @return void
   */
  public static function jannahThemeIntegration() {
    $activeTheme = wp_get_theme();
    $activeThemeName = strtolower($activeTheme->Name);

    if ( $activeThemeName === 'jannah' ) {
      // Get theme options
      static::$_themeOptions = get_option( apply_filters( 'TieLabs/theme_options', '' ) );

      // Prevent updating plugin options if there no theme options
      if (empty(static::$_themeOptions) === true) {
        return;
      }

      // Apply theme options
      static::_applyLogoOptions();
      static::_applyColorsOptions();
      static::_applySocialOptions();

      // Update plugin options
      static::_commitOptionsChanges();
    }
  }

  /**
   * Jannah Theme Logo Integration
   *
	 * @since    0.0.11
   * @return void
   */
  private static function _applyLogoOptions() {
    $options = static::$_themeOptions;

    if ( empty($options['logo']) === false ) {
      static::_updateOption( 'logo-light', $options['logo'] );
    }

    if ( empty($options['logo_inverted']) === false ) {
      static::_updateOption( 'logo-dark', $options['logo_inverted'] );
    }
  }

  /**
   * Jannah Theme Colors Integration
   *
	 * @since    0.0.11
   * @return void
   */
  private static function _applyColorsOptions() {
    $options = static::$_themeOptions;

    if ( empty($options['global_color']) === true ) {
      return;
    }

    $skinMode = $options['dark_skin'] === 'true' ? 'dark' : 'light';
    $optKey = "styling-themeMode_{$skinMode}-primary";

    static::_updateOption( $optKey, $options['global_color'] );
  }

  /**
   * Jannah Theme Social Netowrks Integration
   *
	 * @since    0.0.11
   * @return void
   */
  private static function _applySocialOptions() {
    $options = static::$_themeOptions;

    if ( is_array($options['social']) && empty($options['social']) === false ) {
      $social = array();

      foreach ( $options['social'] as $key => $item ) {
        $social[] = array(
          'social_link_title' => 'true',
          'title' => ucfirst($key),
          'url' => $item,
          'icon' => '',
        );
      }

      static::_updateOption( 'social_enabled', 'true' );
      static::_updateOption( 'social', $social );
    }
  }

  /**
   * Update plugin options array to be later committed
   *
	 * @since    0.0.11
   * @param string $key
   * @param mixed $value
   * @return void
   */
  private static function _updateOption($key, $value) {
    static::$_pluginOptions[$key] = $value;
  }

  /**
   * Commit Jannah Theme Options Changes to Plugin Options
   *
	 * @since    0.0.11
   * @return void
   */
  private static function _commitOptionsChanges() {
    $options = static::$_pluginOptions;
    $current = appbear_get_option('%ALL%');
    $changes = array_merge( $current, $options );

    update_option( APPBEAR_PRIMARY_OPTIONS, $changes, false );
  }
}
