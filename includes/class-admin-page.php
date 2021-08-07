<?php

namespace JPress\Includes;

use JPress\Includes\JPressAPI;


/**
 * JPress Admin Page
 */
class AdminPage extends JPressCore {
  const OPTIONS_FILTERS_DIR = JPRESS_INCLUDES_DIR . 'pipes' . DIRECTORY_SEPARATOR;
  const ALLOW_REDIRECT_ON_LICENSE_ACTIVATION = true;

  /**
   * Class constructor
   */
	public function __construct( $args = array() ) {
		if ( ! is_array( $args ) || Functions::is_empty( $args ) || empty( $args['id'] ) ) {
			return;
		}

		$args['id'] = sanitize_title( $args['id'] );

		$this->args = wp_parse_args( $args, array(
			'id' => '',
			'title' => __( 'Admin Page', 'jpress' ),
			'menu_side_title' => false,
			'menu_title' => __( 'JPress Page', 'jpress' ),
			'parent' => false,
			'capability' => 'manage_options',
			'position' => null,
			'icon' => '',
			'container' => false,
		));

		$this->object_type = 'admin-page';
		parent::__construct( $this->args );

		$this->set_object_id();

		$this->hooks();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Acceso al id del objecto actual, post id o page id
	|---------------------------------------------------------------------------------------------------
	*/
	public function set_object_id( $object_id = 0 ) {
		if ( $object_id ) {
			$this->object_id = $object_id;
		}
		if ( $this->object_id ) {
			return $this->object_id;
		}
		$this->object_id = $this->id;
		return $this->object_id;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Register Hooks
	|---------------------------------------------------------------------------------------------------
	*/
	private function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_page' ), 101 );//101 para que se agrege después de otros items
		add_action( "admin_action_jpress_process_form_{$this->object_id}", array( $this, 'admin_action_jpress_process_form' ), 10 );
		add_action( "jpress_after_save_fields_admin-page_{$this->object_id}", array( $this, 'after_save_fields' ), 10, 3 );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Nombre para el aviso al guardar cambios
	|---------------------------------------------------------------------------------------------------
	*/
	public function settings_notice_key() {
		return $this->get_object_id() . '-notices';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Registramos las opciones
	|---------------------------------------------------------------------------------------------------
	*/
	public function init() {
		register_setting( $this->id, $this->id );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Add menu page
	|---------------------------------------------------------------------------------------------------
	*/
	public function add_admin_page() {
		if ( $this->args['parent'] === false ) {
			if ($this->args['menu_side_title']) {
				add_menu_page( $this->args['title'], $this->args['menu_title'], $this->args['capability'], $this->args['id'], array( $this, 'build_admin_page' ), $this->args['icon'], $this->args['position'] );
				add_submenu_page( $this->args['id'], $this->args['title'], $this->args['menu_side_title'], $this->args['capability'], $this->args['id'], array( $this, 'build_admin_page' ) );
			}else{
				add_menu_page( $this->args['title'], $this->args['menu_title'], $this->args['capability'], $this->args['id'], array( $this, 'build_admin_page' ), $this->args['icon'], $this->args['position'] );
			}
		} else{
			add_submenu_page( $this->args['parent'], $this->args['title'], $this->args['menu_title'], $this->args['capability'], $this->args['id'], array( $this, 'build_admin_page' ) );
		}
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye la página de opciones
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_admin_page() {
		$this->set_object_id( $this->id );//Set the object ID for the current admin page

		$display = "";
		$style = "
			<style>
			#setting-error-{$this->id} {
				margin-left: 1px;
				margin-right: 20px;
				margin-top: 10px;
			}
			</style>
		";

		//Check for settings notice
		$settings_error = get_settings_errors( $this->settings_notice_key() );
		if ( $settings_error ) {
			settings_errors( $this->settings_notice_key() );
		}

		$display .= "<div class='wrap jpress-wrap-admin-page'>";
		if ( ! empty( $this->args['title'] ) && empty( $this->args['header'] ) ) {
			$display .= "<h1 class='jpress-admin-page-title'>";
			$display .= "<i class='jpress-icon jpress-icon-cog'></i>";
			$display .= esc_html( get_admin_page_title() );
			$display .= "</h1>";
		}
		$display .= $this->get_form( $this->args['form_options'] );
		$display .= "</div>";
		echo $style . $display;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Nuevo formulario basado en JPress
	|---------------------------------------------------------------------------------------------------
	*/
	public function get_form( $form_options = array(), $echo = false ) {
		$form = "";
		$args = wp_parse_args( $form_options, $this->arg( 'form_options' ) );

		//Form action
		$args['action'] = "admin.php?action=jpress_process_form_{$this->object_id}";

		$form .= $args['insert_before'];
		$form .= "<form id='{$args['id']}' class='jpress-form' action='{$args['action']}' method='{$args['method']}' enctype='multipart/form-data'>";
		$form .= wp_referer_field( false );
		$form .= "<input type='hidden' name='jpress_id' value='{$this->object_id}'>";
		$form .= $this->build_jpress( $this->get_object_id(), false );
		if ( empty( $this->args['header'] ) ) {
			$form .= $this->get_form_buttons( $args );
		}
		$form .= "</form>";
		$form .= $args['insert_after'];

		if ( ! $echo ) {
			return $form;
		}
		echo $form;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Guarda un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function set_field_value( $field_id, $value = '' ) {
		$field_id = $this->get_field_id( $field_id );
		$options = (array) get_option( $this->id );
		$options[$field_id] = $value;
		return update_option( $this->id, $options );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Obtiene el valor de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function get_field_value( $field_id, $default = '' ) {
		$value = '';
		$field_id = $this->get_field_id( $field_id );
		$options = get_option( $this->id );
		if ( isset( $options[$field_id] ) ) {
			$value = $options[$field_id];
		}
		if ( Functions::is_empty( $value ) ) {
			return $default;
		}
		return $value;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Obtiene todos los campos con sus valores
	|---------------------------------------------------------------------------------------------------
	*/
	public function get_options() {
		$options = get_option( $this->id );
		if ( is_array( $options ) && ! empty( $options ) ) {
			return $options;
		}
		return array();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Save Options
	|---------------------------------------------------------------------------------------------------
	*/
	public function admin_action_jpress_process_form() {
		if ( $this->can_save_form() ) {
			$this->save_fields( $this->get_object_id(), $_POST );
		}

		/**
		 * Redirect back to the settings page that was submitted
		 */
    $goback = add_query_arg( 'settings-updated', 'true', wp_get_referer() );
		wp_redirect( $goback );
		exit;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Comprueba si el formulario se debe guardar
	|---------------------------------------------------------------------------------------------------
	*/
	private function can_save_form() {
		$args = $this->arg( 'form_options' );
		$save_button = $args['save_button_name'];

		if ( ! isset( $_POST[$save_button] ) && ! isset( $_POST['jpress-reset'] ) && ! isset( $_POST['jpress-import'] ) ) {
			return false;
		}

		//Verify nonce
		if ( isset( $_POST[$this->get_nonce()] ) ) {
			if ( ! wp_verify_nonce( $_POST[$this->get_nonce()], $this->get_nonce() ) ) {
				return false;
			}
		} else{
			return false;
		}

		return true;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Redfine the options for remote save
	|---------------------------------------------------------------------------------------------------
	*/
	public function redefine_options($k) {
		$remove =   array('_wp_http_referer','jpress_id','jpress_nonce_jpress-settings','jpress-save','jpress-import-field');

    if (is_array($k)) {
			foreach($k as $key => $value) {
				if (in_array($key, $remove) || strpos($key, 'local-') !== false) {
					return false;
				}
			}
    }
    else {
			if (in_array($k, $remove) || strpos($k, 'local-') !== false) {
				return false;
			}
    }

		return true;
	}

	/**
	 * Activa mensaje de campos actualizados y redirecciona,
   * After save hook.
   *
   * @param array $data
   * @param mixed $object_id
   * @param array $updated_fields
   * @return void
   */
	public function after_save_fields( $data, $object_id, $updated_fields = array() ) {
    if ( empty( $data ) ) {
      return -1;
    }

		if ( $this->id !== $object_id ) {
			return;
		}

		//Para evitar error cuando se está guardando campos automáticamente al activar un plugin o tema
    //$jpress->save_fields(0, array( 'display_message_on_save' => false ));

		if ( isset( $data['display_message_on_save'] ) && $data['display_message_on_save'] == false ) {
			return;
    }

    $this->update_message = $this->arg( 'saved_message' );

		if ( $this->reset ) {
			$this->update_message = $this->arg( 'reset_message' );
    }

		if ( $this->import ) {
      $this->update_message = $this->arg( 'import_message' );

			if ( $this->update_error ) {
				$this->update_message = $this->arg( 'import_message_error' );
			}
		}

		// NOTE: Add settings error
		if ( isset( $data[JPRESS_LICENSE_KEY_OPTION] ) ) {
      $updated = $this->_updateLicenseKey( $data[JPRESS_LICENSE_KEY_OPTION] ) === true;
      $settingsURL = admin_url('admin.php?page=jpress-settings');

      if ( $updated && static::ALLOW_REDIRECT_ON_LICENSE_ACTIVATION && wp_redirect( $settingsURL ) ) {
        exit;
      }
    }

    // NOTE: Save settings
    else {
      $options = array();
      $updated = true;
      $updatedMessage = __('Your settings has been updated successfully', 'jpress');
      $updatedClass = 'updated';

      // NOTE: Apply default demo data if doing a reset with a hard reset to default options
      if (isset($data['jpress-reset']) && $data['jpress-reset'] === 'true') {
        jpress_seed_default_demo(true);

        $opts = jpress_get_option('%ALL%');
        $data = array_merge( $opts, $data );
      }

      // NOTE: Include options pipes / filters
      include static::OPTIONS_FILTERS_DIR . '1_defaults.php';
      include static::OPTIONS_FILTERS_DIR . '2_translations.php';
      include static::OPTIONS_FILTERS_DIR . '3_onboarding.php';
      include static::OPTIONS_FILTERS_DIR . '4_logo.php';
      include static::OPTIONS_FILTERS_DIR . '5_sidenav.php';
      include static::OPTIONS_FILTERS_DIR . '6_bottom_bar.php';
      include static::OPTIONS_FILTERS_DIR . '7_tabs.php';
      include static::OPTIONS_FILTERS_DIR . '8_sections.php';
      include static::OPTIONS_FILTERS_DIR . '9_archives.php';
      include static::OPTIONS_FILTERS_DIR . '10_ads.php';
      include static::OPTIONS_FILTERS_DIR . '11_social.php';
      include static::OPTIONS_FILTERS_DIR . '12_styling.php';
      include static::OPTIONS_FILTERS_DIR . '13_settings.php';
      include static::OPTIONS_FILTERS_DIR . '14_typography.php';

      if (isset($options['lang']) === true) {
        update_option( 'jpress_default_lang', $options['lang'] );
      }

      // Omit empty values / arrays
      $options = $this->_removeEmptyOptions($options);

      // Save settings request
      // $response = JPressAPI::save_settings($options);

      $options['baseUrl'] = trailingslashit(get_home_url());
      $options['copyrights'] = JPRESS_COPYRIGHTS_URL;
      $options['validConfig'] = 'true';

      // NOTE: No longer needed
      update_option( JPRESS_OPTIONS_KEY, $options );

      // NOTE: No longer needed
      // Parse response then update deeplinking options
      // $responseObject = json_decode( wp_remote_retrieve_body( $response ), true );

      // NOTE: Debug line
      // dd($responseObject, $options);

      // NOTE: Handle update response
      // $this->_updateDeeplinkingOptions( $responseObject );

      // if ( isset($responseObject['version']) && $newVersion = (int) $responseObject['version'] ) {
      //   update_option( 'jpress-version', $newVersion, false );
      // }

      // if (isset($responseObject['success']) && (bool) $responseObject['success'] === true) {
      // }

      // NOTE: Reset & invalidate license key / status
      // else {
      //   jpress_invalidate_license(true);
      //   $updated = false;
      // }

      if ( $updated === false ) {
        $updatedMessage = __('Error! Unable to completely save your settings, please check your license key and plan limits.', 'jpress');
        $updatedClass = 'error';
      }

      $updatedMessage = $this->arg( 'saved_message' ) . ', ' . $updatedMessage;

      add_settings_error( $this->settings_notice_key(), $this->id, $updatedMessage, $updatedClass );
      set_transient( 'settings_errors', get_settings_errors(), 30 );
		}
  }

  /**
   * Remove empty / null options before sending the request
   *
   * @param array $options
   * @return array
   */
  private function _removeEmptyOptions(array $options) {
    foreach ($options as $key => &$opt) {
      if (is_array($opt) && empty($opt) === false) {
        $opt = $this->_removeEmptyOptions($opt);

        if (empty($opt)) {
          unset($options[$key]);
        }
      }
      elseif (empty($opt) || is_null($opt) || trim($opt) === '') {
        unset($options[$key]);
      }
    }

    return $options;
  }

  /**
   * Update license key
   *
   * @param string $licenseKey License key
   * @return boolean
   */
  private function _updateLicenseKey( $licenseKey ) {
    update_option( JPRESS_LICENSE_KEY_OPTION, $licenseKey, false );

    // NOTE: Why re-fetch key if we can just use "$licenseKey"?
    $license = $this->_getLicenseKey();
    $response = JPressAPI::activate_license($license);

    // Make sure the response came back okay
    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
      if ( is_wp_error( $response ) ) {
        $message = $response->get_error_message();
      }
      else {
        $message = __( 'An error occurred, please try again.' );
      }
    }
    else {
      $license_data = json_decode( wp_remote_retrieve_body( $response ) );

      // NOTE: Debug line
      // dd($license, $license_data);

      if ( true === $license_data->success ) {
        return true;
      }
      else {
        $errorKey = isset($license_data->error) ? $license_data->error : 'invalid';

        switch( $errorKey ) {
          case 'expired' :
            $message = sprintf(
              __( 'Your license key expired on %s.' ),
              date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
            );
          break;

          case 'disabled' :
          case 'revoked' :
            $message = __( 'Your license key has been disabled.' );
          break;

          case 'missing' :
            $message = __( 'Invalid license.' );
          break;

          case 'invalid' :
          case 'site_inactive' :
            $message = __( 'Your license is not active for this URL.' );
            break;

          case 'item_name_mismatch' :
            $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), JPRESS_ITEM_NAME );
            break;

          case 'no_activations_left':
            $message = __( 'Your license key has reached its activation limit.' );
            break;

          default :
            $message = __( 'An error occurred, please try again.' );
            break;
        }
      }

      return false;
    }

    // Check if anything passed on a message constituting a failure
    if ( ! empty( $message ) ) {
      update_option( JPRESS_LICENSE_STATUS_KEY_OPTION, $license_data->error, false );

      add_settings_error( $this->settings_notice_key(), $this->id, $message, 'error' );
      set_transient( 'settings_errors', get_settings_errors(), 30 );
    }
    else {
      update_option( JPRESS_LICENSE_STATUS_KEY_OPTION, $license_data->license, false );
      add_settings_error( $this->settings_notice_key(), $this->id, $this->arg( 'saved_message' ) . ', ' . __('Your license has been activated successfully'), 'updated' );
      set_transient( 'settings_errors', get_settings_errors(), 30 );
    }

    return $message;
  }

  /**
   * Update Deeplinking Options
   *
   * @param array $options
   * @return void
   */
  private function _updateDeeplinkingOptions(array $options) {
    $options = is_array($options) && count($options) === 1 ? $options[0] : $options;
    $options = isset($options['data']) && is_array($options['data']) ? $options['data'] : array();
    $deeplinkingOpts = array(
      'ios_app_id' => isset($options['ios_app_id']) ? $options['ios_app_id'] : '',
      'ios_bundle' => isset($options['ios_bundle']) ? $options['ios_bundle'] : '',
      'android_bundle' => isset($options['android_bundle']) ? $options['android_bundle'] : '',
    );

    update_option( JPRESS_DEEPLINKING_OPTION, $deeplinkingOpts, false );
  }

  /*
   * Get license key
   */
  private function _getLicenseKey() {
    return jpress_get_license_key();
  }
}
