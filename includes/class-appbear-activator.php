<?php

/**
 * Fired during plugin activation
 *
 * @link       http://jpress.io
 * @since      0.0.2
 *
 * @package    JPress
 * @subpackage JPress/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.2
 * @package    JPress
 * @subpackage JPress/includes
 * @author     JPress <info@jpress.io>
 */
class JPress_Activator {
  const JANNAH_SOCIAL_ICONS = [
    'facebook' => '0xe95d',
    'twitter' => '0xe961',
    'pinterest' => '',
    'dribbble' => '',
    'linkedin' => '',
    'flickr' => '',
    'youtube' => '0xe96c',
    'reddit' => '',
    'tumblr' => '',
    'vimeo' => '',
    'wordpress' => '',
    'yelp' => '',
    'last.fm' => '',
    'xing' => '',
    'deviantart' => '',
    'apple' => '0xe97d',
    'foursquare' => '',
    'github' => '',
    'soundcloud' => '',
    'behance' => '',
    'instagram' => '0xe95e',
    'paypal' => '0xe9ae',
    'spotify' => '',
    'google_play' => '',
    '500px' => '',
    'vk.com' => '',
    'odnoklassniki' => '',
    'bitbucket' => '',
    'mixcloud' => '',
    'medium' => '',
    'twitch' => '',
    'viadeo' => '',
    'snapchat' => '0xe9b7',
    'telegram' => '',
    'tripadvisor' => '',
    'steam' => '',
    'tiktok' => '',
  ];

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
    jpress_seed_default_demo(false);
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
    elseif ( empty($options['logo_inverted']) === true && empty($options['logo']) === false ) {
      static::_updateOption( 'logo-dark', $options['logo'] );
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

    if ( isset($options['global_color']) === false || ( isset($options['global_color']) && empty($options['global_color']) === true ) ) {
      return;
    }

    $skinMode = isset($options['dark_skin']) && $options['dark_skin'] === 'true' ? 'dark' : 'light';
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
        $socialTitle = ucfirst( str_replace('_', ' ', $key) );
        $socialItem = array(
          'social_link_title' => 'true',
          'title' => $socialTitle,
          'url' => $item,
        );

        if ( ($socialIcon = static::_guessSocialIcon($key)) !== false ) {
          $socialItem['icon'] = $socialIcon;
        }

        $social[] = $socialItem;
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
    $current = jpress_get_option('%ALL%');
    $changes = array_merge( $current, $options );

    // NOTE: Debug line
    // dd($options);

    update_option( JPRESS_PRIMARY_OPTIONS, $changes, false );
  }

  /**
   * Guess a social icon by given social network name / key
   *
   * @since 0.1.6
   * @param string $networkName
   * @return string|boolean network icon, or false if it does not exist or empty
   */
  private static function _guessSocialIcon($networkName) {
    $networkName = strtolower($networkName);

    if ( isset(static::JANNAH_SOCIAL_ICONS[$networkName]) && empty(static::JANNAH_SOCIAL_ICONS[$networkName]) === false ) {
      return static::JANNAH_SOCIAL_ICONS[$networkName];
    }

    return false;
  }
}
