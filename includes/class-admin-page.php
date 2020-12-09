<?php

namespace Appbear\Includes;


// FIXME: Missing docs comment
class AdminPage extends AppbearCore {

	public function __construct( $args = array() ) {

		if ( ! is_array( $args ) || Functions::is_empty( $args ) || empty( $args['id'] ) ) {
			return;
		}

		$args['id'] = sanitize_title( $args['id'] );

		$this->args = wp_parse_args( $args, array(
			'id' => '',
			'title' => __( 'Admin Page', 'appbear' ),
			'menu_side_title' => false,
			'menu_title' => __( 'Appbear Page', 'appbear' ),
			'parent' => false,
			'capability' => 'manage_options',
			'position' => null,
			'icon' => '',
			'container' => false,
		) );

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
		add_action( "admin_action_appbear_process_form_{$this->object_id}", array( $this, 'admin_action_appbear_process_form' ), 10 );
		add_action( "appbear_after_save_fields_admin-page_{$this->object_id}", array( $this, 'after_save_fields' ), 10, 3 );
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

		$display .= "<div class='wrap appbear-wrap-admin-page'>";
		if ( ! empty( $this->args['title'] ) && empty( $this->args['header'] ) ) {
			$display .= "<h1 class='appbear-admin-page-title'>";
			$display .= "<i class='appbear-icon appbear-icon-cog'></i>";
			$display .= esc_html( get_admin_page_title() );
			$display .= "</h1>";
		}
		$display .= $this->get_form( $this->args['form_options'] );
		$display .= "</div>";
		echo $style . $display;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Nuevo formulario basado en Appbear
	|---------------------------------------------------------------------------------------------------
	*/
	public function get_form( $form_options = array(), $echo = false ) {
		$form = "";
		$args = wp_parse_args( $form_options, $this->arg( 'form_options' ) );

		//Form action
		$args['action'] = "admin.php?action=appbear_process_form_{$this->object_id}";

		$form .= $args['insert_before'];
		$form .= "<form id='{$args['id']}' class='appbear-form' action='{$args['action']}' method='{$args['method']}' enctype='multipart/form-data'>";
		$form .= wp_referer_field( false );
		$form .= "<input type='hidden' name='appbear_id' value='{$this->object_id}'>";
		$form .= $this->build_appbear( $this->get_object_id(), false );
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
	public function admin_action_appbear_process_form() {
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

		if ( ! isset( $_POST[$save_button] ) && ! isset( $_POST['appbear-reset'] ) && ! isset( $_POST['appbear-import'] ) ) {
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

	// FIXME: Missing docs comment
	// FIXME: Unused method
	public function removeEmpties(&$array) {
		foreach($array as $key=>$val) {
			if (count($val)==0 || $val == '' || $val == null || $val == 'false') {
				return false;
			}
			else {
				$this->removeEmpties($val);
			}
		}
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Redfine the options for remote save
	|---------------------------------------------------------------------------------------------------
	*/
	public function redefine_options($k) {
		$remove =   array('_wp_http_referer','appbear_id','appbear_nonce_appbear-settings','appbear-save','appbear-import-field');
		if (is_array($k)) {
			foreach($k as $key => $value) {
				if (in_array($key, $remove) || strpos($key, 'local-') !== false) {
					return false;
				}
			}
		}else{
			if (in_array($k, $remove) || strpos($k, 'local-') !== false) {
				return false;
			}
		}
		return true;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Activa mensaje de campos actualizados y redirecciona
	|---------------------------------------------------------------------------------------------------
	*/
	public function after_save_fields( $data, $object_id, $updated_fields = array() ) {
    // dd($data);

		if ( $this->id !== $object_id ) {
			return;
		}

		//Para evitar error cuando se está guardando campos automáticamente al activar un plugin o tema
		//$appbear->save_fields(0, array( 'display_message_on_save' => false ));
		if ( isset( $data['display_message_on_save'] ) && $data['display_message_on_save'] == false ) {
			return;
    }

		$type = 'updated';
    $this->update_message = $this->arg( 'saved_message' );

		if ( $this->reset ) {
			$this->update_message = $this->arg( 'reset_message' );
    }

		if ( $this->import ) {
      $this->update_message = $this->arg( 'import_message' );

			if ( $this->update_error ) {
				$this->update_message = $this->arg( 'import_message_error' );
				$type = 'error';
			}
		}

		// Add settings error
		if ( isset( $data['appbear_license_key'] ) ) {
      $this->_updateLicenseKey( $data['appbear_license_key'] );
    }
    else {
			if ( empty( $data ) ) {
				return -1;
      }

      if ( ( $isValidLicense = $this->_checkLicenseStatus() ) === true || _appbear_is_dev_mode() ) {
        $change_language = false;

        switch($this->id) {
          // Parsing the translations to be read in mobile application
          case 'appbear-translations':
            $translationsKeys = array(
              'back', 'skip', 'done', 'contactUs', 'loadingUpdates', 'baseUrl', 'baseUrlTitle', 'baseUrlDesc', 'emptyBaseUrl', 'alreadyBaseUrl',
              'contactUsTitle', 'contactUsSubTitle', 'yourName', 'yourEmail', 'yourMessage', 'send', 'settings', 'aboutUs', 'layout', 'textSize',
              'aA', 'darkMode', 'rateApp', 'shareApp', 'privacyPolicy', 'termsAndConditions', 'poweredBy', 'logout', 'relatedPosts', 'leaveComment',
              'commentsCount', 'reply', 'replyTo', 'By', 'cancel', 'submit', 'comment', 'name', 'postComment', 'postReply', 'lets', 'noFav', 'noPosts',
              'mustNotBeEmpty', 'loadingMore', 'loadingMoreQuestions', 'someThingWentWrong', 'search', 'noMore', 'removedToFav', 'addedToFav',
              'typeToSearch', 'version', 'yourVersionUpToDate', 'yourVersionNotUpToDate', 'upgradeHint', 'aboutApp', 'tapsLeft', 'devModeActive',
              'noResults', 'noSections', 'noMainPage', 'noBoards', 'errorPageTitle', 'retry', 'noInternet', 'checkInternet', 'noComments',
              'seeMore', 'confirmDemoTitle', 'confirmDemoMessage', 'chooseYourDemo', 'confirmResetTitle', 'confirmResetMessage', 'yes', 'reset',
              'customDemo', 'customDemoTitle', 'customDemoBody', 'confirmCustomDemoTitle', 'confirmCustomDemoMessage', 'getOur', 'appBear',
              'plugin', 'next',
            );
            $translations = array();

            foreach ( $translationsKeys as $key ) {
              $translations[$key] = $data[ 'translate-' . $key ];
            }

            $translations = str_replace( '\\', '', $translations );
            update_option( 'appbear-language', $translations );

            $change_language = true;
          break;

          // Parsing the configuration to be read in mobile application
					case 'appbear-settings':
            $options['rtl'] = is_rtl() ? 'true' : 'false';
            $options['themeMode'] = str_replace( '_', '.', $data['thememode'] );

            if (isset($data['statusbarwhiteforeground']) && $data['statusbarwhiteforeground'] !== 'false') {
              $options['statusBarWhiteForeground'] = $data['statusbarwhiteforeground'];
            }

            /*
            * onboardmodels array
            */
            if (isset($data['onboarding']) && $data['onboarding'] != 'false') {
              $options['onboardModels'] = array();

              foreach ($data['onboardmodels'] as $key => $slide) {
                if ($key === 1000) {
                  continue;
                }

                unset($slide['onboardmodels_type']);
                unset($slide['onboardmodels_visibility']);
                unset($slide['image_id']);

                $slide['subTitle']  =   $slide['subtitle'];

                unset($slide['subtitle']);
                array_push($options['onboardModels'], $slide);
              }
            }

            /*
            * logo array
            */
            $options['logo']['light'] = $data['logo-light'];

            if (isset($data['switch_theme_mode']) && $data['switch_theme_mode'] !== 'false') {
              $options['logo']['dark'] = $data['logo-dark'];
            }

            /*
            * appbar array
            */
            $options['appBar']['layout'] = 'AppBarLayout.header2';
            $options['appBar']['position'] = $data['appbar-position'];

            if (isset($data["topbar_search_button"]) && $data["topbar_search_button"] !== 'false') {
              $options['appBar']['searchIcon'] = $data['appbar-searchicon'];
            }

            /*
            * sideNavbar array
            */
            if (isset($data["menu_type"]) && $data["menu_type"] !== 'bottombar') {
              $options['sideNavbar']['icon'] = $data['sidenavbar-icon'];
              $options['sideNavbar']['navigators'] = array();

              foreach ($data['navigators'] as $key=> $navigator) {
                if ($key === 1000) {
                  continue;
                }

                unset($navigator['navigators_type']);
                unset($navigator['navigators_visibility']);
                unset($navigator['navigators_name']);

                switch ($navigator['type']) {
                  case 'NavigationType.category':
                    $category = get_category_by_slug($navigator['category']);

                    if (empty($category)) {
                        break;
                    }

                    if (!isset($navigator["cutomized_title"]) || $navigator["cutomized_title"] == 'false') {
                      $navigator['title'] = $category->name;
                    }

                    unset($navigator['main']);
                    $navigator['url']   = '/wp-json/wl/v1/posts?category_id=' . $category->term_id;
                  break;

                  case 'NavigationType.page':
                    $post = get_post($navigator['page']);

                    if (!$post) {
                      break;
                    }

                    if (!isset($navigator["cutomized_title"]) || $navigator["cutomized_title"] == 'false') {
                      $navigator['title'] = $post->post_title;
                    }

                    $navigator['url']   = '/wp-json/wl/v1/page?id=' . $post->ID;
                    unset($navigator['main']);
                  break;

                  case 'NavigationType.main':
                    if (!isset($navigator["cutomized_title"]) || $navigator["cutomized_title"] == 'false') {
                      switch($navigator['main']) {
                        case 'MainPage.home':
                          $navigator['title'] = __('Home', 'textdomain' );
                        break;
                        case 'MainPage.sections':
                          $navigator['title'] = __('Categories', 'textdomain' );
                        break;
                        case 'MainPage.favorites':
                          $navigator['title'] = __('Favorites', 'textdomain' );
                        break;
                        case 'MainPage.settings':
                          $navigator['title'] = __('Settings', 'textdomain' );
                        break;
                      }
                    }
                  break;
                }

                unset($navigator['category']);
                unset($navigator['page']);
                unset($navigator['cutomized_title']);

                array_push($options['sideNavbar']['navigators'], $navigator);
              }
            }

            /*
            * bottomBar array
            */
            if ($data['menu_type']!='sidemenu' && isset($data["bottombar_tabs"]) && !empty($data["bottombar_tabs"])) {
              $options['bottomBar']['navigators'] = array();

              foreach ($data['bottombar_tabs'] as $key => $navigator) {
                if ($key === 1000) {
                  continue;
                }

                unset($navigator['bottombar_tabs_type']);
                unset($navigator['bottombar_tabs_visibility']);
                unset($navigator['bottombar_tabs_name']);
                unset($navigator['side_menu_tab_icon']);

                switch($navigator['type']) {
                  case 'NavigationType.category':
                    $category = get_category_by_slug($navigator['category']);
                    if (!isset($navigator["cutomized_title"]) || $navigator["cutomized_title"] == 'false') {
                      $navigator['title'] = $category->name;
                    }
                    unset($navigator['main']);
                    $navigator['url']   = '/wp-json/wl/v1/posts?category_id=' . $category->term_id;
                  break;
                  case 'NavigationType.page':
                    $post = get_post($navigator['page']);
                    if (!$post)
                      break;
                    if (!isset($navigator["cutomized_title"]) || $navigator["cutomized_title"] == 'false') {
                      $navigator['title'] = $post->post_title;
                    }
                    $navigator['url']   = '/wp-json/wl/v1/page?id=' . $post->ID;
                    unset($navigator['main']);
                  break;
                  case 'NavigationType.main':
                    if (!isset($navigator["cutomized_title"]) || $navigator["cutomized_title"] == 'false') {
                      switch($navigator['main']) {
                        case 'MainPage.home':
                          $navigator['title'] = __('Home', 'textdomain' );
                        break;
                        case 'MainPage.sections':
                          $navigator['title'] = __('Categories', 'textdomain' );
                        break;
                        case 'MainPage.favorites':
                          $navigator['title'] = __('Favorites', 'textdomain' );
                        break;
                        case 'MainPage.settings':
                          $navigator['title'] = __('Settings', 'textdomain' );
                        break;
                      }
                    }
                  break;
                }

                unset($navigator['category']);
                unset($navigator['page']);
                unset($navigator['cutomized_title']);
                array_push($options['bottomBar']['navigators'], $navigator);
              }
            }

            if ( $data['menu_type'] === 'sidemenu' ) {
              unset($options['bottomBar']);
            }

            /*
            * tabs array
            */
            if (isset($data['tabsbar_categories_tab']) && $data['tabsbar_categories_tab'] != 'false') {
              $options['tabs']['tabsLayout']  = $data['tabs-tabslayout'];

              if ( isset($data['local-hompage_title']) && $data['local-hompage_title'] !== 'false' ) {
                $options['tabs']['homeTab'] = $data['homepage-sections-title'];
              }

              $options['tabs']['tabs'] = array();

              foreach($data['tabsbaritems'] as $key => $slide) {
                if ( $key === 1000 || !isset($slide['categories'][0]) ) {
                  continue;
                }

                unset($slide['tabsbaritems_type']);
                unset($slide['tabsbaritems_visibility']);
                unset($slide['tabsbaritems_name']);

                $item   =   $item_options   =   array();

                $selected_categories = explode(',',$slide['categories'][0]);
                $category = get_category_by_slug($selected_categories[0]);
                $ids = $category->term_id;

                foreach ($selected_categories as $cat) {
                  if ($selected_categories[0] == $cat)
                    continue;

                  $other = get_category_by_slug($cat);
                  $ids .=',' . $other->term_id;
                }

                $item['url']   = '/wp-json/wl/v1/posts?&categories=' . $ids;

                if ($slide['customized-title'] == true && $slide['title'] != '') {
                  $item['title']  =   $slide['title'];
                }
                else {
                  $item['title'] = $category->name;
                }

                array_push($options['tabs']['tabs'], $item);
              }

              if (isset($data["local-tabs-firstfeatured"]) && $data["local-tabs-firstfeatured"] != 'false') {
                $options['tabs']['firstFeatured']  =   $data['tabs-firstfeatured'];
              }

              if (isset($data["local-tabs-seperator"]) && $data["local-tabs-seperator"] != 'false') {
                $item_options['seperator']  =   $data['tabs-seperator'];
              }

              if (isset($data["tabs-options-sort"]) && $data["tabs-options-sort"] != 'false') {
                $item_options["sort"]  =   $data['tabs-options-sort'];
              }

              if (isset($data["tabs-options-count"]) && $tabs["tabs-options-count"] != 'false') {
                $item_options["count"] =   $data['tabs-options-count'];
              }

              if (isset($data["tabs-options-category"]) && $data["tabs-options-category"] != 'false') {
                $item_options["category"]  =   $data["tabs-options-category"];
              }

              if (isset($data["tabs-options-author"]) && $data["tabs-options-author"] != 'false') {
                $item_options["author"]  =   $data["tabs-options-author"];
              }

              if (isset($data["tabs-options-readtime"]) && $data["tabs-options-readtime"] != 'false') {
                $item_options["readTime"]  =   $data["tabs-options-readtime"];
              }

              if (isset($data["tabs-options-date"]) && $data["tabs-options-date"] != 'false') {
                $item_options["date"]  =   $data["tabs-options-date"];
              }

              if (isset($data["tabs-options-share"]) && $data["tabs-options-share"] != 'false') {
                $item_options["share"] =   $data["tabs-options-share"];
              }

              if (isset($data["tabs-options-save"]) && $data["tabs-options-save"] != 'false') {
                $item_options["save"]  =   $data["tabs-options-save"];
              }

              if (isset($data["tabs-options-tags"]) && $data["tabs-options-tags"] != 'false') {
                $item_options["tags"]  =   $data["tabs-options-tags"];
              }

              $options['tabs']['postLayout'] = $data['tabs-postlayout'];
              $options['tabs']['options'] = $item_options;
            }

            /*
            * Homepage array
            */
            $options['homePage']['sections'] = array();

            foreach($data['sections'] as $key => $section) {
              if ($key === 1000) {
                continue;
              }

              unset($section['sections_type']);
              unset($section['sections_visibility']);

              $item = $item_options = array();

              if ($section['local-hompage_title'] == true && $section['homepage-sections-title'] != '') {
                $item['hometab'] = $data['homepage-sections-title'];
              }

              if (isset($section["local-section_title"]) && $section["local-section_title"] != 'false') {
                $item['title']  =   $section['title'];

                if (isset($section["local-enable_see_all"]) && !($section["local-enable_see_all"] == 'false'||$section["local-enable_see_all"]=="off")) {
                  $item['seeMore']  =   array(
                    'name'  =>  $item['title'],
                    'url'   =>  $item['url']
                  );
                }
              }

              $item['url'] = '/wp-json/wl/v1/posts?';

              switch($section['showposts']) {
                case 'categories':
                  $selected_categories = explode( ',', $section['categories'][0] );
                  $category = get_category_by_slug( $selected_categories[0] );
                  $ids = '';

                  foreach ($section['categories'] as $key => $cat) {
                    $other = get_category_by_slug($cat);
                    $ids = ($key == 0) ? $other->term_id : ($ids . ',' . $other->term_id);
                  }

                  $item['url'] .= '&categories=' . $ids;
                break;

                case 'tags':
                  $selected_tags = explode( ',', $section['tags'][0] );
                  $tag = get_term_by( 'slug', $selected_tags[0], 'post_tag' );
                  $ids = '';

                  foreach ($section['tags'] as $key => $tag) {
                    $other = get_term_by( 'slug', $tag, 'post_tag' );
                    $ids = ($key == 0) ? $other->term_id : ($ids . ',' . $other->term_id);
                  }

                  $item['url'] .= '&tags=' . $ids;
                break;
              }

              if (isset($section['local-enable_exclude_posts']) && $section['local-exclude_posts'] != '') {
                // dd($section['local-exclude_posts']);
                // $postsIds = explode(',', $section['local-exclude_posts']);
                $item['url'] .= '&exclude=' . $section['local-exclude_posts'];
              }

              if (isset($section['local-enable_offset_posts']) && $section['local-offset_posts'] != '') {
                $item['url'] .= "&offset=" . $section['local-offset_posts'];
              }

              if (isset($section['local-sort'])) {
                $item['url'] .= "&sort=" . $section['local-sort'];
              }

              if (isset($section["local-enable_see_all"]) && !($section["local-enable_see_all"] == 'false'||$section["local-enable_see_all"]=="off")) {
                $item['seeMore']  =   array(
                  'name'  =>  $item['title'],
                  'url'   =>  $item['url']
                );
              }

              if (isset($section['local-count'])) {
                $item['url'] .= "&count=" . $section['local-count'];
              }

              $item['postLayout'] = $section['postlayout'];

              if (isset($section["local-firstfeatured"]) && $section["local-firstfeatured"] != 'false') {
                $item['firstFeatured']  =   $section['firstfeatured'];
              }

              if (isset($section["separator"]) && $section["separator"] != 'false') {
                $item['separator']  =   $section['separator'];
              }

              if (isset($section["options-sort"]) && $section["options-sort"] != 'false') {
                $item_options["sort"]  =   $section['options-sort'];
              }

              if (isset($section["options-count"]) && $section["options-count"] != 'false') {
                $item_options["count"] =   $section['options-count'];
              }

              if (isset($section["options-category"]) && $section["options-category"] != 'false') {
                $item_options["category"]  =   $section["options-category"];
              }

              if (isset($section["options-author"]) && $section["options-author"] != 'false') {
                $item_options["author"]  =   $section["options-author"];
              }

              if (isset($section["options-readtime"]) && $section["options-readtime"] != 'false') {
                $item_options["readTime"]  =   $section["options-readtime"];
              }

              if (isset($section["options-date"]) && $section["options-date"] != 'false') {
                $item_options["date"]  =   $section["options-date"];
              }

              if (isset($section["options-share"]) && $section["options-share"] != 'false') {
                $item_options["share"] =   $section["options-share"];
              }

              if (isset($section["options-save"]) && $section["options-save"] != 'false') {
                $item_options["save"]  =   $section["options-save"];
              }

              if (isset($section["options-tags"]) && $section["options-tags"] != 'false') {
                $item_options["tags"]  =   $section["options-tags"];
              }

              // NOTE: Ensure all options are sent correctly
              $item['options'] = array_merge( array( 'category' => true ), $item_options);

              array_push($options['homePage']['sections'], $item);

              // dd($options['homePage']['sections'][0]);
            }

            /*
            * archives array
            */
            $options['archives']['categories']['layout'] = $data['archives-categories-postlayout'];
            $options['archives']['categories']['url'] = "/wp-json/wl/v1/categories";
            $options['archives']['single']['answerButton'] = "true";

            $options['archives']['category']['postLayout'] = $data['archives-category-postlayout'];
            $options['archives']['category']['options']['count'] = $data['local-archives-category-count'];

            if (isset($data['archives-category-options-category']) && $data['archives-category-options-category'] != 'false') {
              $options['archives']['category']['options']['category'] = $data['archives-category-options-category'];
            }

            if (isset($data['archives-category-options-author']) && $data['archives-category-options-author'] != 'false') {
              $options['archives']['category']['options']['author'] = $data['archives-category-options-author'];
            }

            if (isset($data['archives-category-options-tags']) && $data['archives-category-options-tags'] != 'false') {
              $options['archives']['category']['options']['tags'] = $data['archives-category-options-tags'];
            }

            if (isset($data['archives-category-options-readtime']) && $data['archives-category-options-readtime'] != 'false') {
              $options['archives']['category']['options']['readTime'] = $data['archives-category-options-readtime'];
            }

            if (isset($data['archives-category-options-date']) && $data['archives-category-options-date'] != 'false') {
              $options['archives']['category']['options']['date'] = $data['archives-category-options-date'];
            }

            if (isset($data['archives-category-options-save']) && $data['archives-category-options-save'] != 'false') {
              $options['archives']['category']['options']['save'] = $data['archives-category-options-save'];
            }

            if (isset($data['archives-category-options-share']) && $data['archives-category-options-share'] != 'false') {
              $options['archives']['category']['options']['share'] = $data['archives-category-options-share'];
            }

            $options['archives']['search']['postLayout'] = $data['archives-search-postlayout'];
            $options['archives']['search']['options']['count'] = $data['local-archives-search-count'];

            if (isset($data['archives-search-options-category']) && $data['archives-search-options-category'] != 'false') {
              $options['archives']['search']['options']['category'] = $data['archives-search-options-category'];
            }

            if (isset($data['archives-search-options-author']) && $data['archives-search-options-author'] != 'false') {
              $options['archives']['search']['options']['author'] = $data['archives-search-options-author'];
            }

            if (isset($data['archives-search-options-tags']) && $data['archives-search-options-tags'] != 'false') {
              $options['archives']['search']['options']['tags'] = $data['archives-search-options-tags'];
            }

            if (isset($data['archives-search-options-readtime']) && $data['archives-search-options-readtime'] != 'false') {
              $options['archives']['search']['options']['readTime'] = $data['archives-search-options-readtime'];
            }

            if (isset($data['archives-search-options-date']) && $data['archives-search-options-date'] != 'false') {
              $options['archives']['search']['options']['date'] = $data['archives-search-options-date'];
            }

            if (isset($data['archives-search-options-save']) && $data['archives-search-options-save'] != 'false') {
              $options['archives']['search']['options']['save'] = $data['archives-search-options-save'];
            }

            if (isset($data['archives-search-options-share']) && $data['archives-search-options-share'] != 'false') {
              $options['archives']['search']['options']['share'] = $data['archives-search-options-share'];
            }

            $options['archives']['favorites']['postLayout'] = $data['archives-favorites-postlayout'];
            $options['archives']['favorites']['url'] = '/wp-json/wl/v1/posts?&ids=';
            $options['archives']['favorites']['options']['count'] = $data['local-archives-favorites-count'];

            if (isset($data['archives-favorites-options-category']) && $data['archives-favorites-options-category'] != 'false') {
              $options['archives']['favorites']['options']['category'] = $data['archives-favorites-options-category'];
            }

            if (isset($data['archives-favorites-options-author']) && $data['archives-favorites-options-author'] != 'false') {
              $options['archives']['favorites']['options']['author'] = $data['archives-favorites-options-author'];
            }

            if (isset($data['archives-favorites-options-tags']) && $data['archives-favorites-options-tags'] != 'false') {
              $options['archives']['favorites']['options']['tags'] = $data['archives-favorites-options-tags'];
            }

            if (isset($data['archives-favorites-options-readtime']) && $data['archives-favorites-options-readtime'] != 'false') {
              $options['archives']['favorites']['options']['readTime'] = $data['archives-favorites-options-readtime'];
            }

            if (isset($data['archives-favorites-options-date']) && $data['archives-favorites-options-date'] != 'false') {
              $options['archives']['favorites']['options']['date'] = $data['archives-favorites-options-date'];
            }

            if (isset($data['archives-favorites-options-save']) && $data['archives-favorites-options-save'] != 'false') {
              $options['archives']['favorites']['options']['save'] = $data['archives-favorites-options-save'];
            }

            if (isset($data['archives-favorites-options-share']) && $data['archives-favorites-options-share'] != 'false') {
              $options['archives']['favorites']['options']['share'] = $data['archives-favorites-options-share'];
            }

            /*
            * adMob array
            */
            if (!(!isset($data['advertisement_android_app_id_text']) || $data['advertisement_android_app_id_text'] == '') || !(!isset($data['advertisement_ios_app_id_text']) || $data['advertisement_ios_app_id_text'] == '')) {
              if (isset($data['advertisement_android_app_id_text']) && $data['advertisement_android_app_id_text'] != '') {
                $options['adMob']['androidAppId']   =  $data['advertisement_android_app_id_text'];
              }

              if (isset($data['advertisement_ios_app_id_text']) && $data['advertisement_ios_app_id_text'] != '') {
                $options['adMob']['iosAppId']   =  $data['advertisement_ios_app_id_text'];
              }

              if (isset($data['local-admob_banner']) && $data['local-admob_banner'] != 'false') {
                $options['adMob']['banner']['androidBannerId']   =  $data['advertisement_android_banner_id_text'];
                $options['adMob']['banner']['iosBannerId']   =  $data['advertisement_ios_banner_id_text'];

                if (isset($data['advertisement_top_toggle']) && $data['advertisement_top_toggle'] != 'false') {
                  $options['adMob']['banner']['positions']['top']   =  $data['advertisement_top_toggle'];
                }

                if (isset($data['advertisement_bottom_toggle']) && $data['advertisement_bottom_toggle'] != 'false') {
                  $options['adMob']['banner']['positions']['bottom']   =  $data['advertisement_bottom_toggle'];
                }

                if (isset($data['advertisement_after_post_toggel']) && $data['advertisement_after_post_toggel'] != 'false') {
                  $options['adMob']['banner']['positions']['afterPost']   =  $data['advertisement_after_post_toggel'];
                }
              }

              if (isset($data['local-advertisement_admob_interstatial']) && $data['local-advertisement_admob_interstatial'] != 'false') {
                $options['adMob']['interstatial']['androidInterstatialId']   =  $data['advertisement_android_interstatial_id_text'];
                $options['adMob']['interstatial']['iosInterstatialId']   =  $data['advertisement_ios_interstatial_id_text'];

                if (isset($data['advertisement_interstatial_before_post_toggle']) && $data['advertisement_interstatial_before_post_toggle'] != 'false') {
                  $options['adMob']['interstatial']['positions']['beforePost']   =  $data['advertisement_interstatial_before_post_toggle'];
                }

                if (isset($data['advertisement_interstatial_before_comment_toggle']) && $data['advertisement_interstatial_before_comment_toggle'] != 'false') {
                  $options['adMob']['interstatial']['positions']['beforeComment']   =  $data['advertisement_interstatial_before_comment_toggle'];
                }
              }

              if (isset($data['local-advertisement_android_rewarded']) && $data['local-advertisement_android_rewarded'] != 'false') {
                $options['adMob']['rewarded']['androidRewardedId']   =  $data['advertisement_android_rewarded_id_text'];
                $options['adMob']['rewarded']['iosRewardedId']   =  $data['advertisement_android_rewarded_ios_text'];

                if (isset($data['advertisement_rewarded_before_post_toggle']) && $data['advertisement_rewarded_before_post_toggle'] != 'false') {
                  $options['adMob']['rewarded']['positions']['beforePost']   =  $data['advertisement_rewarded_before_post_toggle'];
                }

                if (isset($data['advertisement_rewarded_before_comment_toggle']) && $data['advertisement_rewarded_before_comment_toggle'] != 'false') {
                  $options['adMob']['rewarded']['positions']['beforeComment']   =  $data['advertisement_rewarded_before_comment_toggle'];
                }
              }
            }

            /*
            * styling array
            */
            $options['styling']['ThemeMode.light']['scaffoldBackgroundColor'] = $data['styling-themeMode_light-scaffoldbackgroundcolor'];
            $options['styling']['ThemeMode.light']['primary'] = $data['styling-themeMode_light-primary'];
            $options['styling']['ThemeMode.light']['secondary'] = $data['styling-themeMode_light-secondary'];
            $options['styling']['ThemeMode.light']['secondaryVariant'] = $data['styling-themeMode_light-secondaryvariant'];
            $options['styling']['ThemeMode.light']['appBarBackgroundColor'] = $data['styling-themeMode_light-appbarbackgroundcolor'];
            $options['styling']['ThemeMode.light']['appBarColor'] = $data['styling-themeMode_light-appbarcolor'];
            $options['styling']['ThemeMode.light']['background'] = $data['styling-themeMode_light-background'];
            $options['styling']['ThemeMode.light']['sidemenutextcolor'] = $data['styling-themeMode_light-sidemenuiconstextcolor'];
            $options['styling']['ThemeMode.light']['bottomBarBackgroundColor'] = $data['styling-themeMode_light-bottombarbackgroundcolor'];
            $options['styling']['ThemeMode.light']['bottomBarInActiveColor'] = $data['styling-themeMode_light-bottombarinactivecolor'];
            $options['styling']['ThemeMode.light']['bottomBarActiveColor'] = $data['styling-themeMode_light-bottombaractivecolor'];
            $options['styling']['ThemeMode.light']['tabBarBackgroundColor'] = $data['styling-themeMode_light-tabbarbackgroundcolor'];
            $options['styling']['ThemeMode.light']['tabBarTextColor'] = $data['styling-themeMode_light-tabbartextcolor'];
            $options['styling']['ThemeMode.light']['tabBarActiveTextColor'] = $data['styling-themeMode_light-tabbaractivetextcolor'];
            $options['styling']['ThemeMode.light']['tabBarIndicatorColor'] = $data['styling-themeMode_light-tabbarindicatorcolor'];
            $options['styling']['ThemeMode.light']['shadowColor'] = $data['styling-themeMode_light-shadowcolor'];
            $options['styling']['ThemeMode.light']['dividerColor'] = $data['styling-themeMode_light-dividercolor'];
            $options['styling']['ThemeMode.light']['inputsbackgroundcolor'] = $data['styling-themeMode_light-inputsbackgroundcolor'];
            $options['styling']['ThemeMode.light']['buttonsbackgroudcolor'] = $data['styling-themeMode_light-buttonsbackgroudcolor'];
            $options['styling']['ThemeMode.light']['buttonTextColor'] = $data['styling-themeMode_light-buttontextcolor'];
            $options['styling']['ThemeMode.light']['settingBackgroundColor'] = $data['styling-themeMode_light-settingbackgroundcolor'];
            $options['styling']['ThemeMode.light']['settingTextColor'] = $data['styling-themeMode_light-settingtextcolor'];
            $options['styling']['ThemeMode.light']['errorColor'] =  $data['styling-themeMode_light-errorcolor'];
            $options['styling']['ThemeMode.light']['successColor'] = $data['styling-themeMode_light-successcolor'];

            if (isset($data['switch_theme_mode']) && $data['switch_theme_mode'] !== 'false') {
              $options['styling']['ThemeMode.dark']['scaffoldBackgroundColor'] = $data['styling-themeMode_dark-scaffoldbackgroundcolor'];
              $options['styling']['ThemeMode.dark']['primary'] = $data['styling-themeMode_dark-primary'];
              $options['styling']['ThemeMode.dark']['secondary'] = $data['styling-themeMode_dark-secondary'];
              $options['styling']['ThemeMode.dark']['secondaryVariant'] = $data['styling-themeMode_dark-secondaryvariant'];
              $options['styling']['ThemeMode.dark']['appBarBackgroundColor'] = $data['styling-themeMode_dark-appbarbackgroundcolor'];
              $options['styling']['ThemeMode.dark']['appBarColor'] = $data['styling-themeMode_dark-appbarcolor'];
              $options['styling']['ThemeMode.dark']['background'] = $data['styling-themeMode_dark-background'];
              $options['styling']['ThemeMode.dark']['sidemenutextcolor'] = $data['styling-themeMode_dark-sidemenuiconstextcolor'];
              $options['styling']['ThemeMode.dark']['bottomBarBackgroundColor'] = $data['styling-themeMode_dark-bottombarbackgroundcolor'];
              $options['styling']['ThemeMode.dark']['bottomBarInActiveColor'] = $data['styling-themeMode_dark-bottombarinactivecolor'];
              $options['styling']['ThemeMode.dark']['bottomBarActiveColor'] = $data['styling-themeMode_dark-bottombaractivecolor'];
              $options['styling']['ThemeMode.dark']['tabBarBackgroundColor'] = $data['styling-themeMode_dark-tabbarbackgroundcolor'];
              $options['styling']['ThemeMode.dark']['tabBarTextColor'] = $data['styling-themeMode_dark-tabbartextcolor'];
              $options['styling']['ThemeMode.dark']['tabBarActiveTextColor'] = $data['styling-themeMode_dark-tabbaractivetextcolor'];
              $options['styling']['ThemeMode.dark']['tabBarIndicatorColor'] = $data['styling-themeMode_dark-tabbarindicatorcolor'];
              $options['styling']['ThemeMode.dark']['shadowColor'] = $data['styling-themeMode_dark-shadowcolor'];
              $options['styling']['ThemeMode.dark']['dividerColor'] = $data['styling-themeMode_dark-dividercolor'];
              $options['styling']['ThemeMode.dark']['inputsbackgroundcolor'] = $data['styling-themeMode_dark-inputsbackgroundcolor'];
              $options['styling']['ThemeMode.dark']['buttonsbackgroudcolor'] = $data['styling-themeMode_dark-buttonsbackgroudcolor'];
              $options['styling']['ThemeMode.dark']['buttonTextColor'] = $data['styling-themeMode_dark-buttontextcolor'];
              $options['styling']['ThemeMode.dark']['settingBackgroundColor'] = $data['styling-themeMode_dark-settingbackgroundcolor'];
              $options['styling']['ThemeMode.dark']['settingTextColor'] = $data['styling-themeMode_dark-settingtextcolor'];
              $options['styling']['ThemeMode.dark']['errorColor'] = $data['styling-themeMode_dark-errorcolor'];
              $options['styling']['ThemeMode.dark']['successColor'] = $data['styling-themeMode_dark-successcolor'];
            }

            if (isset($data['settingspage-textsize']) && $data['settingspage-textsize'] != 'false') {
              $options['settingsPage']['textSize'] = $data['settingspage-textsize'];
            }

            if (isset($data['switch_theme_mode']) && $data['switch_theme_mode'] != 'false' && isset($data['settingspage-darkmode']) && $data['settingspage-darkmode'] != 'false') {
              $options['settingsPage']['darkMode'] = $data['settingspage-darkmode'];
            }

            if (isset($data['settingspage-rateapp']) && $data['settingspage-rateapp'] != 'false') {
              $options['settingsPage']['rateApp'] = $data['settingspage-rateapp'];
            }

            if (isset($data['local-settingspage-share']) && $data['local-settingspage-share'] != 'false') {
              $options['settingsPage']['shareApp']['title'] = $data['settingspage-shareapp-title'];
              $options['settingsPage']['shareApp']['image'] = $data['settingspage-shareapp-image'];
              $options['settingsPage']['shareApp']['android'] = $data['settingspage-shareapp-android'];
              $options['settingsPage']['shareApp']['ios'] = $data['settingspage-shareapp-ios'];
            }

            if (isset($data['local-settingspage-aboutus']) && $data['local-settingspage-aboutus'] != 'false') {
              $options['settingsPage']['aboutUs'] = "/wp-json/wl/v1/page?id=" . $data['settingspage-aboutus'];
            }

            if (isset($data['settingspage-privacypolicy']) && $data['settingspage-privacypolicy'] != '') {
              $options['settingsPage']['privacyPolicy'] = "/wp-json/wl/v1/page?id=" . $data['settingspage-privacypolicy'];
            }

            if (isset($data['settingspage-termsandconditions']) && $data['settingspage-termsandconditions'] != '') {
              $options['settingsPage']['termsAndConditions'] = "/wp-json/wl/v1/page?id=" . $data['settingspage-termsandconditions'];
            }

            if (isset($data['settingspage-contactus']) && $data['settingspage-contactus'] != 'false') {
              $options['settingsPage']['contactUs'] = "/wp-json/wl/v1/contact-us";
            }

            if (isset($data['local-settingspage-aboutapp']) && $data['local-settingspage-aboutapp'] != 'false') {
              $options['settingsPage']['aboutApp']["aboutLogoLight"] = $data['settingspage-aboutapp-logo-light'];

              if (isset($data['switch_theme_mode']) && $data['switch_theme_mode'] != 'false') {
                $options['settingsPage']['aboutApp']["aboutLogoDark"] = $data['settingspage-aboutapp-logo-dark'];
              }

              $options['settingsPage']['aboutApp']["title"] = $data['settingspage-aboutapp-title'];
              $options['settingsPage']['aboutApp']["content"] = $data['settingspage-aboutapp-content'];
              $options['settingsPage']['shortCodes'] = "true";

              if (isset($data['settingspage-devmode']) && $data['settingspage-devmode'] != 'false') {
                // $options['settingsPage']['devMode']["time"] = $data['settingspage-devmode-time'];
                $options['settingsPage']['devMode']["time"] = "6000";
                // $options['settingsPage']['devMode']["count"] = $data['settingspage-devmode-count'];
                $options['settingsPage']['devMode']["count"] = "3";
                $options['settingsPage']['devMode']["addUrl"] = "/?edd_action=save_development_token";
                $options['settingsPage']['devMode']["removeUrl"] = "/?edd_action=remove_development_token";
                $options['basicUrls']["devMode"] =  "wp-json/wl/v1/dev-mode";
              }
            }

            if (isset($data['settingspage-demos']) && $data['settingspage-demos'] != 'false') {
              $options['settingsPage']['demos'] = "true";
            }

            if (isset($data['section-typography-fontfamily-heading']) && $data['section-typography-fontfamily-heading'] != '') {
              $options['typography']['headline1']["fontFamily"]   = $data['section-typography-fontfamily-heading'];
              $options['typography']['headline2']["fontFamily"]   = $data['section-typography-fontfamily-heading'];
              $options['typography']['headline3']["fontFamily"]   = $data['section-typography-fontfamily-heading'];
              $options['typography']['headline4']["fontFamily"]   = $data['section-typography-fontfamily-heading'];
              $options['typography']['headline5']["fontFamily"]   = $data['section-typography-fontfamily-heading'];
            }

            if (isset($data['section-typography-fontfamily-heading']) && $data['section-typography-fontfamily-body'] != '') {
              $options['typography']['subtitle1']["fontFamily"]   = $data['section-typography-fontfamily-body'];
              $options['typography']['subtitle2']["fontFamily"]   = $data['section-typography-fontfamily-body'];
              $options['typography']['bodyText1']["fontFamily"]   = $data['section-typography-fontfamily-body'];
              $options['typography']['bodyText2']["fontFamily"]   = $data['section-typography-fontfamily-body'];
            }

            if (isset($data['section-typography-font-h1-size']) && $data['section-typography-font-h1-size'] != '') {
              $options['typography']['headline1']["fontSize"]     = $data['section-typography-font-h1-size'];
            }

            if (isset($data['section-typography-font-h1-line_height']) && $data['section-typography-font-h1-line_height'] != '') {
              $options['typography']['headline1']["lineHeight"]   = $data['section-typography-font-h1-line_height'];
            }

            if (isset($data['section-typography-font-h1-weight']) && $data['section-typography-font-h1-weight'] != '') {
              $options['typography']['headline1']["fontWeight"]   = $data['section-typography-font-h1-weight'];
            }

            if (isset($data['section-typography-font-h1-transform']) && $data['section-typography-font-h1-transform'] != '') {
              $options['typography']['headline1']["fontTransform"]    = $data['section-typography-font-h1-transform'];
            }

            if (isset($data['section-typography-font-h2-size']) && $data['section-typography-font-h2-size'] != '') {
              $options['typography']['headline2']["fontSize"]     = $data['section-typography-font-h2-size'];
            }

            if (isset($data['section-typography-font-h2-line_height']) && $data['section-typography-font-h2-line_height'] != '') {
              $options['typography']['headline2']["lineHeight"]   = $data['section-typography-font-h2-line_height'];
            }

            if (isset($data['section-typography-font-h2-weight']) && $data['section-typography-font-h2-weight'] != '') {
              $options['typography']['headline2']["fontWeight"]   = $data['section-typography-font-h2-weight'];
            }

            if (isset($data['section-typography-font-h2-transform']) && $data['section-typography-font-h2-transform'] != '') {
              $options['typography']['headline2']["fontTransform"]    = $data['section-typography-font-h2-transform'];
            }

						if (isset($data['section-typography-font-h3-size']) && $data['section-typography-font-h3-size'] != '') {
							$options['typography']['headline3']["fontSize"]     = $data['section-typography-font-h3-size'];
            }

						if (isset($data['section-typography-font-h3-line_height']) && $data['section-typography-font-h3-line_height'] != '') {
							$options['typography']['headline3']["lineHeight"]   = $data['section-typography-font-h3-line_height'];
						}

            if (isset($data['section-typography-font-h3-weight']) && $data['section-typography-font-h3-weight'] != '') {
							$options['typography']['headline3']["fontWeight"]   = $data['section-typography-font-h3-weight'];
						}

            if (isset($data['section-typography-font-h3-transform']) && $data['section-typography-font-h3-transform'] != '') {
							$options['typography']['headline3']["fontTransform"]    = $data['section-typography-font-h3-transform'];
						}

						if (isset($data['section-typography-font-h4-size']) && $data['section-typography-font-h4-size'] != '') {
							$options['typography']['headline4']["fontSize"]     = $data['section-typography-font-h4-size'];
						}

            if (isset($data['section-typography-font-h4-line_height']) && $data['section-typography-font-h4-line_height'] != '') {
							$options['typography']['headline4']["lineHeight"]   = $data['section-typography-font-h4-line_height'];
						}

            if (isset($data['section-typography-font-h4-weight']) && $data['section-typography-font-h4-weight'] != '') {
							$options['typography']['headline4']["fontWeight"]   = $data['section-typography-font-h4-weight'];
						}

            if (isset($data['section-typography-font-h4-transform']) && $data['section-typography-font-h4-transform'] != '') {
							$options['typography']['headline4']["fontTransform"]    = $data['section-typography-font-h4-transform'];
						}

						if (isset($data['section-typography-font-h5-size']) && $data['section-typography-font-h5-size'] != '') {
							$options['typography']['headline5']["fontSize"]     = $data['section-typography-font-h5-size'];
						}

            if (isset($data['section-typography-font-h5-line_height']) && $data['section-typography-font-h5-line_height'] != '') {
							$options['typography']['headline5']["lineHeight"]   = $data['section-typography-font-h5-line_height'];
						}

            if (isset($data['section-typography-font-h5-weight']) && $data['section-typography-font-h5-weight'] != '') {
							$options['typography']['headline5']["fontWeight"]   = $data['section-typography-font-h5-weight'];
						}

            if (isset($data['section-typography-font-h5-transform']) && $data['section-typography-font-h5-transform'] != '') {
							$options['typography']['headline5']["fontTransform"]    = $data['section-typography-font-h5-transform'];
						}

						if (isset($data['section-typography-font-subtitle1-size']) && $data['section-typography-font-subtitle1-size'] != '') {
							$options['typography']['subtitle1']["fontSize"]     = $data['section-typography-font-subtitle1-size'];
            }

						if (isset($data['section-typography-font-subtitle1-line_height']) && $data['section-typography-font-subtitle1-line_height'] != '') {
							$options['typography']['subtitle1']["lineHeight"]   = $data['section-typography-font-subtitle1-line_height'];
            }

						if (isset($data['section-typography-font-subtitle1-weight']) && $data['section-typography-font-subtitle1-weight'] != '') {
							$options['typography']['subtitle1']["fontWeight"]   = $data['section-typography-font-subtitle1-weight'];
            }

						if (isset($data['section-typography-font-subtitle1-transform']) && $data['section-typography-font-subtitle1-transform'] != '') {
							$options['typography']['subtitle1']["fontTransform"]    = $data['section-typography-font-subtitle1-transform'];
						}

						if (isset($data['section-typography-font-subtitle2-size']) && $data['section-typography-font-subtitle2-size'] != '') {
							$options['typography']['subtitle2']["fontSize"]     = $data['section-typography-font-subtitle2-size'];
            }

						if (isset($data['section-typography-font-subtitle2-size']) && $data['section-typography-font-subtitle2-line_height'] != '') {
							$options['typography']['subtitle2']["lineHeight"]   = $data['section-typography-font-subtitle2-line_height'];
            }

						if (isset($data['section-typography-font-subtitle2-weight']) && $data['section-typography-font-subtitle2-weight'] != '') {
							$options['typography']['subtitle2']["fontWeight"]   = $data['section-typography-font-subtitle2-weight'];
            }

						if (isset($data['section-typography-font-subtitle2-transform']) && $data['section-typography-font-subtitle2-transform'] != '') {
							$options['typography']['subtitle2']["fontTransform"]    = $data['section-typography-font-subtitle2-transform'];
						}

						if (isset($data['section-typography-font-body1-size']) && $data['section-typography-font-body1-size'] != '') {
							$options['typography']['bodyText1']["fontSize"]     = $data['section-typography-font-body1-size'];
            }

						if (isset($data['section-typography-font-body1-line_height']) && $data['section-typography-font-body1-line_height'] != '') {
							$options['typography']['bodyText1']["lineHeight"]   = $data['section-typography-font-body1-line_height'];
            }

						if (isset($data['section-typography-font-body1-weight']) && $data['section-typography-font-body1-weight'] != '') {
							$options['typography']['bodyText1']["fontWeight"]   = $data['section-typography-font-body1-weight'];
            }

						if (isset($data['section-typography-font-body1-transform']) && $data['section-typography-font-body1-transform'] != '') {
							$options['typography']['bodyText1']["fontTransform"]    = $data['section-typography-font-body1-transform'];
						}

						if (isset($data['section-typography-font-body2-size']) && $data['section-typography-font-body2-size'] != '') {
							$options['typography']['bodyText2']["fontSize"]     = $data['section-typography-font-body2-size'];
            }

						if (isset($data['section-typography-font-body2-line_height']) && $data['section-typography-font-body2-line_height'] != '') {
							$options['typography']['bodyText2']["lineHeight"]   = $data['section-typography-font-body2-line_height'];
            }

						if (isset($data['section-typography-font-body2-weight']) && $data['section-typography-font-body2-weight'] != '') {
							$options['typography']['bodyText2']["fontWeight"]   = $data['section-typography-font-body2-weight'];
            }

						if (isset($data['section-typography-font-body2-transform']) && $data['section-typography-font-body2-transform'] != '') {
							$options['typography']['bodyText2']["fontTransform"]    = $data['section-typography-font-body2-transform'];
						}

            $options['basicUrls']["getPost"] = "/wp-json/wl/v1/post";
            $options['basicUrls']["submitComment"] = "/wp-json/wl/v1/add-comment";
            $options['basicUrls']["removeUrl"] = "/?edd_action=remove_development_token";
            $options['basicUrls']["saveToken"] = "/?edd_action=save_token";
            $options['basicUrls']["translations"] = "/wp-json/wl/v1/translations";
            $options['basicUrls']["getPostWPJSON"] = "/wp-json/wl/v1/post";
            $options['basicUrls']["getTags"] = "/wp-json/wl/v1/posts?tags=";
            $options['basicUrls']["getTagsPosts"] = "/wp-json/wl/v1/posts?tags=";
            $options['basicUrls']["login"] = "/wp-json/wl/v1/login";
            $options['basicUrls']["selectDemo"] = "/wp-json/wl/v1/selectDemo";
            $options['basicUrls']["demos"] = "/wp-json/wl/v1/demos";

            $options['baseUrl'] = get_home_url().'/';
            $options['defaultLayout'] = "Layout.standard";
            $options['searchApi'] = "/wp-json/wl/v1/posts?s=";
            $options['commentsApi'] = "/wp-json/wl/v1/comments?id=";
            $options['commentAdd'] = "/wp-json/wl/v1/add-comment";
            $options['relatedPostsApi'] = "/wp-json/wl/v1/posts?related_id=";
            $options['lang'] = "en";
            $options['validConfig'] = "true";

            update_option( 'appbear_default_lang', $options['lang'] );

            $new_version                                                                = 1;
            $old_version                                                                = get_option( 'appbear_version' );

            if (isset($old_version)) {
              $new_version = $old_version + 1;
            }

            update_option( 'appbear_version', $new_version );

            $license_status = get_option( 'appbear_license_status' );
            $license_key = $this->_getLicenseKey();

            if ( $license_status === 'valid' ) {

              $url  = APPBEAR_STORE_URL . '/?edd_action=save_settings&item_id=1044&license=' . $license_key;

              $post = [
                'settings' => json_encode($options, JSON_UNESCAPED_UNICODE)
              ];

              $ch = curl_init($url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
              $response = curl_exec($ch);
              curl_close($ch);
            }
					break;
        }

				$base_url = get_home_url();
        $base_url = substr($base_url, -1) === '/' ? substr($base_url, 0, -1) : $base_url;
				$licensedBase = str_replace( 'http://', '', str_replace( 'http://', '', $base_url ) );
				$licensedBase = str_replace( 'https://', '', str_replace( 'https://', '', $licensedBase ) );

				if ( $change_language === false ) {
					$tes = wp_remote_get(APPBEAR_STORE_URL . '/?edd_action=send_silent_fcm_message&site_url=' . $licensedBase);
        }
        else {
					$tes = wp_remote_get(APPBEAR_STORE_URL . '/?edd_action=send_silent_fcm_message&site_url=' . $licensedBase . '&change_translations=true');
				}

				update_option( 'appbear_license_status', $isValidLicense ? 'valid' : 'invalid' );
				add_settings_error( $this->settings_notice_key(), $this->id, $this->arg( 'saved_message' ).', ' . __('Your settings has been updated successfully'), 'updated' );
				set_transient( 'settings_errors', get_settings_errors(), 30 );
			}
		}
	}

  /*
   * Check license status
   *
   * @return boolean TRUE if license key is valid
   */
  private function _checkLicenseStatus() {
    $license = $this->_getLicenseKey();

    $api_params = array(
      'edd_action' => 'check_license',
      'license' => $license,
      'item_name' => urlencode( APPBEAR_ITEM_NAME ),
      'url'       => home_url()
    );

    // Call the custom API.
    $response = wp_remote_post( APPBEAR_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

    if ( is_wp_error( $response ) ) {
      $message = __( 'An error occurred, please try again.' );
      update_option( 'appbear_license_status', json_decode($response['body'])->license );

      add_settings_error( $this->settings_notice_key(), $this->id, $message, 'error' );
      set_transient( 'settings_errors', get_settings_errors(), 30 );
      return false;
    }

    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

    if ( $license_data->license !== 'valid' ) {
      $invalidReason = isset($license_data->error) ? $license_data->error : 'unknown';

      update_option( 'appbear_license_status', $license_data->license );

      switch( $invalidReason ) {
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
          $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), APPBEAR_ITEM_NAME );
          break;

        case 'no_activations_left':
          $message = __( 'Your license key has reached its activation limit.' );
          break;

        default :
          $message = __( 'An error occurred, please try again.' );
          break;
      }

      add_settings_error( $this->settings_notice_key(), $this->id, $message, 'error' );
      set_transient( 'settings_errors', get_settings_errors(), 30 );
    }

    return $license_data->license === 'valid';
  }

  /*
   * Update license key
   *
   * @param string $licenseKey License key
   */
  private function _updateLicenseKey( $licenseKey ) {
    update_option( 'appbear_license_key', $licenseKey );

    // NOTE: Why re-fetch key if we can just use "$licenseKey"?
    $license = $this->_getLicenseKey();

    // Data to send in our API request
    $api_params = array(
      'edd_action' => 'activate_license',
      'license'    => $license,
      'item_name'  => urlencode( APPBEAR_ITEM_NAME ), // the name of our product in EDD
      'url'        => home_url()
    );

    // Call the custom API.
    $response = wp_remote_post( APPBEAR_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

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

      if ( false === $license_data->success ) {
        switch( $license_data->error ) {
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
            $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), APPBEAR_ITEM_NAME );
            break;

          case 'no_activations_left':
            $message = __( 'Your license key has reached its activation limit.' );
            break;

          default :
            $message = __( 'An error occurred, please try again.' );
            break;
        }
      }
    }

    // Check if anything passed on a message constituting a failure
    if ( ! empty( $message ) ) {
      update_option( 'appbear_license_status', $license_data->error );

      add_settings_error( $this->settings_notice_key(), $this->id, $message, 'error' );
      set_transient( 'settings_errors', get_settings_errors(), 30 );
    }
    else {
      update_option( 'appbear_license_status', $license_data->license );
      add_settings_error( $this->settings_notice_key(), $this->id, $this->arg( 'saved_message' ) . ', ' . __('Your license has been activated successfully'), 'updated' );
      set_transient( 'settings_errors', get_settings_errors(), 30 );
    }

    return $message;
  }

  /*
   * Get license key
   */
  private function _getLicenseKey() {
    return appbear_get_license_key();
  }
}
