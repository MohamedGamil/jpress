<?php

/**
 * Fired during plugin activation
 *
 * @link       http://jpress.dedulab.com
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
 * @author     JPress <info@jpress.dedulab.com>
 */
class JPress_Activator {

	/**
	 * Activate plugin hook entry method
	 *
	 * @since    0.0.2
	 */
	public static function activate() {
    static::seedDefaultDemo();
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
}
