<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * AppBear_Demos_Endpoints Class
 *
 * This class handles the Demos API Endpoints, this will be a seprated plugin
 *
 *
 * @since 1.0
 */
class AppBear_Demos_Endpoints {


	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wl/v1';

	
	/**
	 * Class Constructor
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}


	/**
	 * Endpoint main method
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function register_routes() {

		// GET routes
		$get_routes = array(
			'selectdemo',
			'demos'
		);

		foreach ( $get_routes as $route ) {
			$this->register_rest_route( $route, 'GET' );
		}
	}


	/**
	 * register_rest_route
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function register_rest_route( $route, $method ) {

		$callback = 'do_'.str_replace( '-', '_', $route );

		if( method_exists( $this, $callback ) ){
			register_rest_route( $this->namespace, $route, array(
				'methods'  => $method,
				'callback' => array( $this, $callback ),
				'permission_callback' => '__return_true', // Required since WordPress 5.5
			) );
		}
	}


	/**
	 * do_demos
	 *
	 * @access public
	 * @since 1.0
	 * @return array()
	 */
	public function do_demos( $request ) {

		return apply_filters( 'AppBear/API/Demos/list', array(
			array(
				'id'   => 1,
				'name' => 'Games',
			),
			array(
				'id'   => 2,
				'name' => 'The Road Dark',
			),
			array(
				'id'   => 3,
				'name' => 'Journal',
			),
			array(
				'id'   => 4,
				'name' => 'Hotels',
			),
			array(
				'id'   => 5,
				'name' => 'J Videos',
			),
			array(
				'id'   => 6,
				'name' => 'Jannah News',
			),
			array(
				'id'   => 7,
				'name' => 'Road Light',
			),
			array(
				'id'   => 8,
				'name' => 'Sahifa',
			),
			array(
				'id'   => 9,
				'name' => 'عربي',
			),
		));
	}


	/**
	 * do_demos
	 *
	 * @access public
	 * @since 1.0
	 * @return array()
	 */
	public function do_selectdemo( $request ){

		if( empty( $request['demo'] ) ){
			return false;
		}


		switch( $request['demo'] ){
			case 1:
				$demo_data = array (
					'themeMode' => 'ThemeMode.dark',
					'logo' =>
					array (
					  'light' => 'http://appstage.tielabs.com/wp-content/uploads/2020/07/logo-demo-1.png',
					  'dark' => 'http://appstage.tielabs.com/wp-content/uploads/2020/07/logo-demo-1.png',
					),
					'appBar' =>
					array (
					  'layout' => 'AppBarLayout.header2',
					  'position' => 'LogoPosition.start',
					  'searchIcon' => '0xe820',
					),
					'bottomBar' =>
					array (
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe800',
						  'main' => 'MainPage.home',
						  'title_enable' => 'false',
						  'title' => 'Home',
						),
						1 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe801',
						  'main' => 'MainPage.sections',
						  'title_enable' => 'false',
						  'title' => 'Categories',
						),
						2 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe803',
						  'main' => 'MainPage.favourites',
						  'title_enable' => 'false',
						  'title' => 'Favorites',
						),
						3 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe935',
						  'main' => 'MainPage.settings',
						  'title_enable' => 'false',
						  'title' => 'Settings',
						),
					  ),
					),
					'tabs' =>
					array (
					  'tabsLayout' => 'TabsLayout.tab3',
					  'homeTab' => 'Top news',
					  'tabs' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=32',
						  'title' => 'Football',
						),
						1 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=33',
						  'title' => 'Racing',
						),
						2 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=28',
						  'title' => 'Sports',
						),
						3 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
					  ),
					  'postLayout' => 'PostLayout.startThumbPost',
					  'options' =>
					  array (
						'category' => 'true',
						'readTime' => 'true',
						'date' => 'true',
						'share' => 'true',
						'save' => 'true',
					  ),
					),
					'homePage' =>
					array (
					  'sections' =>
					  array (
						0 =>
						array (
						  'title' => '',
						  'seeMore' =>
						  array (
							'name' => '',
							'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest',
						  ),
						  'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest&count=9',
						  'postLayout' => 'PostLayout.cardPost',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'date' => 'true',
							'share' => 'true',
							'save' => 'true',
						  ),
						),
					  ),
					),
					'archives' =>
					array (
					  'categories' =>
					  array (
						'layout' => 'CategoriesLayout.cat1',
						'url' => '/wp-json/wl/v1/categories',
					  ),
					  'single' =>
					  array (
						'answerButton' => 'true',
					  ),
					  'category' =>
					  array (
						'postLayout' => 'PostLayout.startThumbPost',
						'options' =>
						array (
						  'count' => '10',
						  'readTime' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'search' =>
					  array (
						'postLayout' => 'PostLayout.startThumbPost',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'favorites' =>
					  array (
						'postLayout' => 'PostLayout.startThumbPost',
						'url' => '/wp-json/wl/v1/posts?&ids=',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					),
					'styling' =>
					array (
					  'ThemeMode.light' =>
					  array (
						'scaffoldBackgroundColor' => '#000000',
						'primary' => '#3F2878',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#3F2878',
						'appBarColor' => '#FFFFFF',
						'background' => '#FFFFFF',
						'sidemenutextcolor' => '#333739',
						'bottomBarBackgroundColor' => '#35393B',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#FFFFFF',
						'tabBarBackgroundColor' => '#3F2878',
						'tabBarTextColor' => '#8B8D8F',
						'tabBarActiveTextColor' => '#ffffff',
						'tabBarIndicatorColor' => '#000000',
						'shadowColor' => 'rgba(10, 10, 10, 0.15)',
						'dividerColor' => 'rgba(0,0,0,0.05)',
						'inputsbackgroundcolor' => 'rgba(0,0,0,0.04)',
						'buttonsbackgroudcolor' => '#3F2878',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#FFFFFF',
						'settingTextColor' => '#000000',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					  'ThemeMode.dark' =>
					  array (
						'scaffoldBackgroundColor' => '#000000',
						'primary' => '#3F2878',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#3F2878',
						'appBarColor' => '#FFFFFF',
						'background' => '#333739',
						'sidemenutextcolor' => '#FFFFFF',
						'bottomBarBackgroundColor' => '#35393B',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#ffffff',
						'tabBarBackgroundColor' => '#3F2878',
						'tabBarTextColor' => '#8A8A89',
						'tabBarActiveTextColor' => '#ffffff',
						'tabBarIndicatorColor' => '#101010',
						'shadowColor' => 'rgba(22, 22, 25, 0.15)',
						'dividerColor' => 'rgba(255,255,255,0.13)',
						'inputsbackgroundcolor' => 'rgba(255,255,255,0.07)',
						'buttonsbackgroudcolor' => '#3F2878',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#000000',
						'settingTextColor' => '#FFFFFF',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					),
					'settingsPage' =>
					array (
					  'textSize' => 'true',
					  'rateApp' => 'true',
					  'privacyPolicy' => '/wp-json/wl/v1/page?id=1046',
					  'termsAndConditions' => '/wp-json/wl/v1/page?id=1037',
					  'demos' => 'true',
					),
					'basicUrls' =>
					array (
					  'getPost' => '/wp-json/wl/v1/post',
					  'submitComment' => '/wp-json/wl/v1/add-comment',
					  'removeUrl' => '/?edd_action=remove_development_token',
					  'saveToken' => '/?edd_action=save_token',
					  'translations' => '/wp-json/wl/v1/translations',
					  'getPostWPJSON' => '/wp-json/wl/v1/post',
					  'getTags' => '/wp-json/wl/v1/posts?tags=',
					  'getTagsPosts' => '/wp-json/wl/v1/posts?tags=',
					  'login' => '/wp-json/wl/v1/login',
					  'selectDemo' => '/wp-json/wl/v1/selectDemo',
					  'demos' => '/wp-json/wl/v1/demos',
					),
					'baseUrl' => 'https://appstage.tielabs.com/',
					'defaultLayout' => 'Layout.standard',
					'searchApi' => '/wp-json/wl/v1/posts?s=',
					'commentsApi' => '/wp-json/wl/v1/comments?id=',
					'commentAdd' => '/wp-json/wl/v1/add-comment',
					'relatedPostsApi' => '/wp-json/wl/v1/posts?related_id=',
					'lang' => 'en',
					'validConfig' => 'true',
					'copyrights' => 'https://appbear.io/',
				  );
			break;
			case 2:
				$demo_data = array (
					'themeMode' => 'ThemeMode.dark',
					'logo' =>
					array (
					  'light' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-7.png',
					  'dark' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-2.png',
					),
					'appBar' =>
					array (
					  'layout' => 'AppBarLayout.header2',
					  'position' => 'LogoPosition.center',
					  'searchIcon' => '0xe820',
					),
					'bottomBar' =>
					array (
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe800',
						  'main' => 'MainPage.home',
						  'title_enable' => 'true',
						  'title' => 'Home',
						),
						1 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe801',
						  'main' => 'MainPage.sections',
						  'title_enable' => 'true',
						  'title' => 'Categories',
						),
						2 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe803',
						  'main' => 'MainPage.favourites',
						  'title_enable' => 'true',
						  'title' => 'Favorites',
						),
						3 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe935',
						  'main' => 'MainPage.settings',
						  'title_enable' => 'true',
						  'title' => 'Settings',
						),
					  ),
					),
					'tabs' =>
					array (
					  'tabsLayout' => 'TabsLayout.tab1',
					  'homeTab' => 'Top news',
					  'tabs' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=32',
						  'title' => 'Football',
						),
						1 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=33',
						  'title' => 'Racing',
						),
						2 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=28',
						  'title' => 'Sports',
						),
						3 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
						4 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=26',
						  'title' => 'Life Style',
						),
						5 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=11',
						  'title' => 'Travel',
						),
						6 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
					  ),
					  'firstFeatured' => 'PostLayout.cardPost',
					  'postLayout' => 'PostLayout.startThumbPost',
					  'options' =>
					  array (
						'category' => 'true',
						'readTime' => 'true',
						'date' => 'true',
						'share' => 'true',
						'save' => 'true',
					  ),
					),
					'homePage' =>
					array (
					  'sections' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest&count=9',
						  'seeMore' =>
						  array (
							'name' => NULL,
							'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest',
						  ),
						  'postLayout' => 'PostLayout.minimalPost',
						  'firstFeatured' => 'PostLayout.cardPost',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'date' => 'true',
							'share' => 'true',
							'save' => 'true',
						  ),
						),
					  ),
					),
					'archives' =>
					array (
					  'categories' =>
					  array (
						'layout' => 'CategoriesLayout.cat3',
						'url' => '/wp-json/wl/v1/categories',
					  ),
					  'single' =>
					  array (
						'answerButton' => 'true',
					  ),
					  'category' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'search' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'favorites' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'url' => '/wp-json/wl/v1/posts?&ids=',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					),
					'styling' =>
					array (
					  'ThemeMode.light' =>
					  array (
						'scaffoldBackgroundColor' => '#FFFFFF',
						'primary' => '#28B291',
						'secondary' => '#000000',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#FFFFFF',
						'appBarColor' => '#222222',
						'background' => '#FFFFFF',
						'sidemenutextcolor' => '#333739',
						'bottomBarBackgroundColor' => '#ffffff',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#222222',
						'tabBarBackgroundColor' => '#ffffff',
						'tabBarTextColor' => '#8B8D8F',
						'tabBarActiveTextColor' => '#222222',
						'tabBarIndicatorColor' => '#28B291',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(0,0,0,0.05)',
						'inputsbackgroundcolor' => 'rgba(0,0,0,0.04)',
						'buttonsbackgroudcolor' => '#28B291',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#FFFFFF',
						'settingTextColor' => '#000000',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					  'ThemeMode.dark' =>
					  array (
						'scaffoldBackgroundColor' => '#000000',
						'primary' => '#28B291',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#222222',
						'appBarColor' => '#FFFFFF',
						'background' => '#333739',
						'sidemenutextcolor' => '#FFFFFF',
						'bottomBarBackgroundColor' => '#222222',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#ffffff',
						'tabBarBackgroundColor' => '#222222',
						'tabBarTextColor' => '#8A8A89',
						'tabBarActiveTextColor' => '#ffffff',
						'tabBarIndicatorColor' => '#28B291',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(255,255,255,0.13)',
						'inputsbackgroundcolor' => 'rgba(255,255,255,0.07)',
						'buttonsbackgroudcolor' => '#28B291',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#000000',
						'settingTextColor' => '#FFFFFF',
						'errorColor' => '#5C0D1F',
						'successColor' => '#255834',
					  ),
					),
					'settingsPage' =>
					array (
					  'textSize' => 'true',
					  'darkMode' => 'true',
					  'rateApp' => 'true',
					  'shareApp' =>
					  array (
						'title' => 'Download Jannah Now...',
						'image' => 'http://appstage.tielabs.com/wp-content/uploads/2020/09/jannah-logo-light-1.png',
						'android' => 'https://play.google.com/store/apps/details?id=com.jannah.app',
						'ios' => '',
					  ),
					  'privacyPolicy' => '/wp-json/wl/v1/page?id=1046',
					  'termsAndConditions' => '/wp-json/wl/v1/page?id=1037',
					  'contactUs' => '/wp-json/wl/v1/contact-us',
					  'aboutApp' =>
					  array (
						'aboutLogoLight' => 'http://appstage.tielabs.com/wp-content/plugins/appBear-plugin/img/jannah-logo-light.png',
						'aboutLogoDark' => 'http://appstage.tielabs.com/wp-content/plugins/appBear-plugin/img/jannah-logo-dark.png',
						'title' => 'My WordPress Website',
						'content' => 'Just another WordPress sitern',
					  ),
					  'shortCodes' => 'true',
					  'devMode' =>
					  array (
						'time' => '6000',
						'count' => '3',
						'addUrl' => '/?edd_action=save_development_token',
						'removeUrl' => '/?edd_action=remove_development_token',
					  ),
					  'demos' => 'true',
					),
					'basicUrls' =>
					array (
					  'devMode' => 'wp-json/wl/v1/dev-mode',
					  'getPost' => '/wp-json/wl/v1/post',
					  'submitComment' => '/wp-json/wl/v1/add-comment',
					  'removeUrl' => '/?edd_action=remove_development_token',
					  'saveToken' => '/?edd_action=save_token',
					  'translations' => '/wp-json/wl/v1/translations',
					  'getPostWPJSON' => '/wp-json/wl/v1/post',
					  'getTags' => '/wp-json/wl/v1/posts?tags=',
					  'getTagsPosts' => '/wp-json/wl/v1/posts?tags=',
					  'login' => '/wp-json/wl/v1/login',
					  'selectDemo' => '/wp-json/wl/v1/selectDemo',
					  'demos' => '/wp-json/wl/v1/demos',
					),
					'baseUrl' => 'https://appstage.tielabs.com/',
					'defaultLayout' => 'Layout.standard',
					'searchApi' => '/wp-json/wl/v1/posts?s=',
					'commentsApi' => '/wp-json/wl/v1/comments?id=',
					'commentAdd' => '/wp-json/wl/v1/add-comment',
					'relatedPostsApi' => '/wp-json/wl/v1/posts?related_id=',
					'lang' => 'en',
					'validConfig' => 'true',
					'copyrights' => 'https://appbear.io/',
				  );
			break;
			case 3:
				$demo_data = array (
					'themeMode' => 'ThemeMode.dark',
					'logo' =>
					array (
					  'light' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-3.png',
					  'dark' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-3.png',
					),
					'appBar' =>
					array (
					  'layout' => 'AppBarLayout.header2',
					  'position' => 'LogoPosition.center',
					  'searchIcon' => '0xe820',
					),
					'bottomBar' =>
					array (
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe800',
						  'main' => 'MainPage.home',
						  'title_enable' => 'false',
						  'title' => 'Home',
						),
						1 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe801',
						  'main' => 'MainPage.sections',
						  'title_enable' => 'false',
						  'title' => 'Categories',
						),
						2 =>
						array (
						  'type' => 'NavigationType.category',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe802',
						  'title_enable' => 'false',
						  'title' => 'Creative',
						  'url' => '/wp-json/wl/v1/posts?category_id=31',
						),
						3 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe803',
						  'main' => 'MainPage.favourites',
						  'title_enable' => 'false',
						  'title' => 'Favorites',
						),
						4 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe935',
						  'main' => 'MainPage.settings',
						  'title_enable' => 'false',
						  'title' => 'Settings',
						),
					  ),
					),
					'tabs' =>
					array (
					  'tabsLayout' => 'TabsLayout.tab1',
					  'homeTab' => 'Top news',
					  'tabs' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=32',
						  'title' => 'Football',
						),
						1 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=33',
						  'title' => 'Racing',
						),
						2 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=28',
						  'title' => 'Sports',
						),
						3 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
						4 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=26',
						  'title' => 'Life Style',
						),
						5 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=11',
						  'title' => 'Travel',
						),
						6 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
					  ),
					  'firstFeatured' => 'PostLayout.cardPost',
					  'postLayout' => 'PostLayout.startThumbPost',
					  'options' =>
					  array (
						'category' => 'true',
						'readTime' => 'true',
						'date' => 'true',
						'share' => 'true',
						'save' => 'true',
					  ),
					),
					'homePage' =>
					array (
					  'sections' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest&count=9',
						  'seeMore' =>
						  array (
							'name' => NULL,
							'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest',
						  ),
						  'postLayout' => 'PostLayout.minimalPost',
						  'firstFeatured' => 'PostLayout.cardPost',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'date' => 'true',
							'share' => 'true',
							'save' => 'true',
						  ),
						),
					  ),
					),
					'archives' =>
					array (
					  'categories' =>
					  array (
						'layout' => 'CategoriesLayout.cat2',
						'url' => '/wp-json/wl/v1/categories',
					  ),
					  'single' =>
					  array (
						'answerButton' => 'true',
					  ),
					  'category' =>
					  array (
						'postLayout' => 'PostLayout.startThumbPostCompact',
						'options' =>
						array (
						  'count' => '10',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'search' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'favorites' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'url' => '/wp-json/wl/v1/posts?&ids=',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					),
					'styling' =>
					array (
					  'ThemeMode.light' =>
					  array (
						'scaffoldBackgroundColor' => '#FFFFFF',
						'primary' => '#FFFFFF',
						'secondary' => '#000000',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#FFFFFF',
						'appBarColor' => '#222222',
						'background' => '#FFFFFF',
						'sidemenutextcolor' => '#333739',
						'bottomBarBackgroundColor' => '#ffffff',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#222222',
						'tabBarBackgroundColor' => '#ffffff',
						'tabBarTextColor' => '#8B8D8F',
						'tabBarActiveTextColor' => '#ffffff',
						'tabBarIndicatorColor' => '#F50000',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(0,0,0,0.05)',
						'inputsbackgroundcolor' => 'rgba(0,0,0,0.04)',
						'buttonsbackgroudcolor' => '#FFF9BB',
						'buttonTextColor' => '#000000',
						'settingBackgroundColor' => '#FFFFFF',
						'settingTextColor' => '#000000',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					  'ThemeMode.dark' =>
					  array (
						'scaffoldBackgroundColor' => '#000000',
						'primary' => '#FFF9BB',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#FFF9BB',
						'appBarColor' => '#000000',
						'background' => '#333739',
						'sidemenutextcolor' => '#FFFFFF',
						'bottomBarBackgroundColor' => '#222222',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#ffffff',
						'tabBarBackgroundColor' => '#FFF9BB',
						'tabBarTextColor' => '#8A8A89',
						'tabBarActiveTextColor' => '#222222',
						'tabBarIndicatorColor' => '#F50000',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(255,255,255,0.13)',
						'inputsbackgroundcolor' => 'rgba(255,255,255,0.07)',
						'buttonsbackgroudcolor' => '#FFF9BB',
						'buttonTextColor' => '#000000',
						'settingBackgroundColor' => '#000000',
						'settingTextColor' => '#FFFFFF',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					),
					'settingsPage' =>
					array (
					  'textSize' => 'true',
					  'rateApp' => 'true',
					  'shareApp' =>
					  array (
						'title' => '',
						'image' => '',
						'android' => '',
						'ios' => '',
					  ),
					  'privacyPolicy' => '/wp-json/wl/v1/page?id=1046',
					  'termsAndConditions' => '/wp-json/wl/v1/page?id=1037',
					  'contactUs' => '/wp-json/wl/v1/contact-us',
					  'aboutApp' =>
					  array (
						'aboutLogoLight' => 'http://appstage.tielabs.com/wp-content/plugins/appBear-plugin/img/jannah-logo-light.png',
						'aboutLogoDark' => 'http://appstage.tielabs.com/wp-content/plugins/appBear-plugin/img/jannah-logo-dark.png',
						'title' => 'My WordPress Website',
						'content' => 'Just another WordPress site',
					  ),
					  'shortCodes' => 'true',
					  'devMode' =>
					  array (
						'time' => '6000',
						'count' => '3',
						'addUrl' => '/?edd_action=save_development_token',
						'removeUrl' => '/?edd_action=remove_development_token',
					  ),
					  'demos' => 'true',
					),
					'basicUrls' =>
					array (
					  'devMode' => 'wp-json/wl/v1/dev-mode',
					  'getPost' => '/wp-json/wl/v1/post',
					  'submitComment' => '/wp-json/wl/v1/add-comment',
					  'removeUrl' => '/?edd_action=remove_development_token',
					  'saveToken' => '/?edd_action=save_token',
					  'translations' => '/wp-json/wl/v1/translations',
					  'getPostWPJSON' => '/wp-json/wl/v1/post',
					  'getTags' => '/wp-json/wl/v1/posts?tags=',
					  'getTagsPosts' => '/wp-json/wl/v1/posts?tags=',
					  'login' => '/wp-json/wl/v1/login',
					  'selectDemo' => '/wp-json/wl/v1/selectDemo',
					  'demos' => '/wp-json/wl/v1/demos',
					),
					'baseUrl' => 'https://appstage.tielabs.com/',
					'defaultLayout' => 'Layout.standard',
					'searchApi' => '/wp-json/wl/v1/posts?s=',
					'commentsApi' => '/wp-json/wl/v1/comments?id=',
					'commentAdd' => '/wp-json/wl/v1/add-comment',
					'relatedPostsApi' => '/wp-json/wl/v1/posts?related_id=',
					'lang' => 'en',
					'validConfig' => 'true',
					'copyrights' => 'https://appbear.io/',
				);
			break;
			case 4:
				$demo_data = array (
					'themeMode' => 'ThemeMode.light',
					'logo' =>
					array (
					  'light' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-4-e1603273606416.png',
					),
					'appBar' =>
					array (
					  'layout' => 'AppBarLayout.header2',
					  'position' => 'LogoPosition.center',
					),
					'bottomBar' =>
					array (
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe800',
						  'main' => 'MainPage.home',
						  'title_enable' => 'false',
						  'title' => 'Home',
						),
						1 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe801',
						  'main' => 'MainPage.sections',
						  'title_enable' => 'false',
						  'title' => 'Categories',
						),
						2 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe803',
						  'main' => 'MainPage.favourites',
						  'title_enable' => 'false',
						  'title' => 'Favorites',
						),
						3 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe935',
						  'main' => 'MainPage.settings',
						  'title_enable' => 'false',
						  'title' => 'Settings',
						),
					  ),
					),
					'homePage' =>
					array (
					  'sections' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest&count=9',
						  'seeMore' =>
						  array (
							'name' => NULL,
							'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest',
						  ),
						  'postLayout' => 'PostLayout.minimalPost',
						  'firstFeatured' => 'PostLayout.cardPost',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'date' => 'true',
							'share' => 'true',
							'save' => 'true',
						  ),
						),
					  ),
					),
					'archives' =>
					array (
					  'categories' =>
					  array (
						'layout' => 'CategoriesLayout.cat5',
						'url' => '/wp-json/wl/v1/categories',
					  ),
					  'single' =>
					  array (
						'answerButton' => 'true',
					  ),
					  'category' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'search' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'favorites' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'url' => '/wp-json/wl/v1/posts?&ids=',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					),
					'styling' =>
					array (
					  'ThemeMode.light' =>
					  array (
						'scaffoldBackgroundColor' => '#FFFFFF',
						'primary' => '#BF9958',
						'secondary' => '#000000',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#BF9958',
						'appBarColor' => '#222222',
						'background' => '#FFFFFF',
						'sidemenutextcolor' => '#333739',
						'bottomBarBackgroundColor' => '#ffffff',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#222222',
						'tabBarBackgroundColor' => '#ffffff',
						'tabBarTextColor' => '#8B8D8F',
						'tabBarActiveTextColor' => '#ffffff',
						'tabBarIndicatorColor' => '#F50000',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(0,0,0,0.05)',
						'inputsbackgroundcolor' => 'rgba(0,0,0,0.04)',
						'buttonsbackgroudcolor' => '#BF9958',
						'buttonTextColor' => '#000000',
						'settingBackgroundColor' => '#FFFFFF',
						'settingTextColor' => '#000000',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					),
					'settingsPage' =>
					array (
					  'textSize' => 'true',
					  'rateApp' => 'true',
					  'shareApp' =>
					  array (
						'title' => '',
						'image' => '',
						'android' => '',
						'ios' => '',
					  ),
					  'privacyPolicy' => '/wp-json/wl/v1/page?id=1039',
					  'termsAndConditions' => '/wp-json/wl/v1/page?id=1037',
					  'contactUs' => '/wp-json/wl/v1/contact-us',
					  'aboutApp' =>
					  array (
						'aboutLogoLight' => 'http://appstage.tielabs.com/wp-content/plugins/appBear-plugin/img/jannah-logo-light.png',
						'title' => 'My WordPress Website',
						'content' => 'Just another WordPress site',
					  ),
					  'shortCodes' => 'true',
					  'devMode' =>
					  array (
						'time' => '6000',
						'count' => '3',
						'addUrl' => '/?edd_action=save_development_token',
						'removeUrl' => '/?edd_action=remove_development_token',
					  ),
					  'demos' => 'true',
					),
					'basicUrls' =>
					array (
					  'devMode' => 'wp-json/wl/v1/dev-mode',
					  'getPost' => '/wp-json/wl/v1/post',
					  'submitComment' => '/wp-json/wl/v1/add-comment',
					  'removeUrl' => '/?edd_action=remove_development_token',
					  'saveToken' => '/?edd_action=save_token',
					  'translations' => '/wp-json/wl/v1/translations',
					  'getPostWPJSON' => '/wp-json/wl/v1/post',
					  'getTags' => '/wp-json/wl/v1/posts?tags=',
					  'getTagsPosts' => '/wp-json/wl/v1/posts?tags=',
					  'login' => '/wp-json/wl/v1/login',
					  'selectDemo' => '/wp-json/wl/v1/selectDemo',
					  'demos' => '/wp-json/wl/v1/demos',
					),
					'baseUrl' => 'https://appstage.tielabs.com/',
					'defaultLayout' => 'Layout.standard',
					'searchApi' => '/wp-json/wl/v1/posts?s=',
					'commentsApi' => '/wp-json/wl/v1/comments?id=',
					'commentAdd' => '/wp-json/wl/v1/add-comment',
					'relatedPostsApi' => '/wp-json/wl/v1/posts?related_id=',
					'lang' => 'en',
					'validConfig' => 'true',
					'copyrights' => 'https://appbear.io/',
				  );
			break;
			case 5:
				$demo_data = array (
					'themeMode' => 'ThemeMode.dark',
					'logo' =>
					array (
					  'light' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-55.png',
					  'dark' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-55.png',
					),
					'appBar' =>
					array (
					  'layout' => 'AppBarLayout.header2',
					  'position' => 'LogoPosition.start',
					  'searchIcon' => '0xe820',
					),
					'bottomBar' =>
					array (
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'false',
						  'icon' => '0xe800',
						  'main' => 'MainPage.home',
						  'title_enable' => 'true',
						  'title' => 'Home',
						),
						1 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'false',
						  'icon' => '0xe801',
						  'main' => 'MainPage.sections',
						  'title_enable' => 'true',
						  'title' => 'Categories',
						),
						2 =>
						array (
						  'type' => 'NavigationType.category',
						  'bottom_bar_icon_enable' => 'false',
						  'icon' => '0xe802',
						  'title_enable' => 'true',
						  'title' => 'Videos',
						  'url' => '/wp-json/wl/v1/posts?category_id=2',
						),
						3 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'false',
						  'icon' => '0xe803',
						  'main' => 'MainPage.favourites',
						  'title_enable' => 'true',
						  'title' => 'Favorites',
						),
						4 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'false',
						  'icon' => '0xe935',
						  'main' => 'MainPage.settings',
						  'title_enable' => 'true',
						  'title' => 'Settings',
						),
					  ),
					),
					'tabs' =>
					array (
					  'tabsLayout' => 'TabsLayout.tab1',
					  'homeTab' => 'Top news',
					  'tabs' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=32',
						  'title' => 'Football',
						),
						1 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=33',
						  'title' => 'Racing',
						),
						2 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=28',
						  'title' => 'Sports',
						),
						3 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
						4 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=26',
						  'title' => 'Life Style',
						),
						5 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=11',
						  'title' => 'Travel',
						),
						6 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
					  ),
					  'firstFeatured' => 'PostLayout.imagePost',
					  'postLayout' => 'PostLayout.minimalPost',
					  'options' =>
					  array (
						'category' => 'true',
						'readTime' => 'true',
						'date' => 'true',
						'share' => 'true',
						'save' => 'true',
					  ),
					),
					'homePage' =>
					array (
					  'sections' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest&count=9',
						  'seeMore' =>
						  array (
							'name' => NULL,
							'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest',
						  ),
						  'postLayout' => 'PostLayout.minimalPost',
						  'firstFeatured' => 'PostLayout.imagePost',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'date' => 'true',
							'share' => 'true',
							'save' => 'true',
						  ),
						),
					  ),
					),
					'archives' =>
					array (
					  'categories' =>
					  array (
						'layout' => 'CategoriesLayout.cat4',
						'url' => '/wp-json/wl/v1/categories',
					  ),
					  'single' =>
					  array (
						'answerButton' => 'true',
					  ),
					  'category' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'search' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'favorites' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'url' => '/wp-json/wl/v1/posts?&ids=',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					),
					'styling' =>
					array (
					  'ThemeMode.light' =>
					  array (
						'scaffoldBackgroundColor' => '#000000',
						'primary' => '#BF2519',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#BF2519',
						'appBarColor' => '#FFFFFF',
						'background' => '#FFFFFF',
						'sidemenutextcolor' => '#333739',
						'bottomBarBackgroundColor' => '#222222',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#ffffff',
						'tabBarBackgroundColor' => '#BF2519',
						'tabBarTextColor' => '#ffffff',
						'tabBarActiveTextColor' => '#ffffff',
						'tabBarIndicatorColor' => '#ffffff',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(0,0,0,0.05)',
						'inputsbackgroundcolor' => 'rgba(0,0,0,0.04)',
						'buttonsbackgroudcolor' => '#BF2519',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#FFFFFF',
						'settingTextColor' => '#000000',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					  'ThemeMode.dark' =>
					  array (
						'scaffoldBackgroundColor' => '#000000',
						'primary' => '#BF2519',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#BF2519',
						'appBarColor' => '#FFFFFF',
						'background' => '#333739',
						'sidemenutextcolor' => '#FFFFFF',
						'bottomBarBackgroundColor' => '#222222',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#ffffff',
						'tabBarBackgroundColor' => '#BF2519',
						'tabBarTextColor' => '#ffffff',
						'tabBarActiveTextColor' => '#ffffff',
						'tabBarIndicatorColor' => '#ffffff',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(255,255,255,0.13)',
						'inputsbackgroundcolor' => 'rgba(255,255,255,0.07)',
						'buttonsbackgroudcolor' => '#BF2519',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#000000',
						'settingTextColor' => '#FFFFFF',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					),
					'settingsPage' =>
					array (
					  'textSize' => 'true',
					  'rateApp' => 'true',
					  'shareApp' =>
					  array (
						'title' => '',
						'image' => '',
						'android' => '',
						'ios' => '',
					  ),
					  'privacyPolicy' => '/wp-json/wl/v1/page?id=1046',
					  'termsAndConditions' => '/wp-json/wl/v1/page?id=1037',
					  'contactUs' => '/wp-json/wl/v1/contact-us',
					  'aboutApp' =>
					  array (
						'aboutLogoLight' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-55.png',
						'aboutLogoDark' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-55.png',
						'title' => 'My WordPress Website',
						'content' => 'Just another WordPress sitern',
					  ),
					  'shortCodes' => 'true',
					  'devMode' =>
					  array (
						'time' => '6000',
						'count' => '3',
						'addUrl' => '/?edd_action=save_development_token',
						'removeUrl' => '/?edd_action=remove_development_token',
					  ),
					  'demos' => 'true',
					),
					'basicUrls' =>
					array (
					  'devMode' => 'wp-json/wl/v1/dev-mode',
					  'getPost' => '/wp-json/wl/v1/post',
					  'submitComment' => '/wp-json/wl/v1/add-comment',
					  'removeUrl' => '/?edd_action=remove_development_token',
					  'saveToken' => '/?edd_action=save_token',
					  'translations' => '/wp-json/wl/v1/translations',
					  'getPostWPJSON' => '/wp-json/wl/v1/post',
					  'getTags' => '/wp-json/wl/v1/posts?tags=',
					  'getTagsPosts' => '/wp-json/wl/v1/posts?tags=',
					  'login' => '/wp-json/wl/v1/login',
					  'selectDemo' => '/wp-json/wl/v1/selectDemo',
					  'demos' => '/wp-json/wl/v1/demos',
					),
					'baseUrl' => 'https://appstage.tielabs.com/',
					'defaultLayout' => 'Layout.standard',
					'searchApi' => '/wp-json/wl/v1/posts?s=',
					'commentsApi' => '/wp-json/wl/v1/comments?id=',
					'commentAdd' => '/wp-json/wl/v1/add-comment',
					'relatedPostsApi' => '/wp-json/wl/v1/posts?related_id=',
					'lang' => 'en',
					'validConfig' => 'true',
					'copyrights' => 'https://appbear.io/',
				);
			break;
			case 6:
				$demo_data = array (
					'themeMode' => 'ThemeMode.dark',
					'logo' =>
					array (
					  'light' => 'http://appstage.tielabs.com/wp-content/uploads/2020/07/logo-demo-6-white.png',
					  'dark' => 'http://appstage.tielabs.com/wp-content/uploads/2020/07/logo-demo-6-white.png',
					),
					'appBar' =>
					array (
					  'layout' => 'AppBarLayout.header2',
					  'position' => 'LogoPosition.start',
					  'searchIcon' => '0xe820',
					),
					'bottomBar' =>
					array (
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe800',
						  'main' => 'MainPage.home',
						  'title_enable' => 'true',
						  'title' => 'Home',
						),
						1 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe801',
						  'main' => 'MainPage.sections',
						  'title_enable' => 'true',
						  'title' => 'Categories',
						),
						2 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe803',
						  'main' => 'MainPage.favourites',
						  'title_enable' => 'true',
						  'title' => 'Favorites',
						),
						3 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe935',
						  'main' => 'MainPage.settings',
						  'title_enable' => 'true',
						  'title' => 'Settings',
						),
						4 =>
						array (
						  'type' => 'NavigationType.page',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe979',
						  'title_enable' => 'true',
						  'title' => 'Privacy',
						  'url' => '/wp-json/wl/v1/page?id=1037',
						),
					  ),
					),
					'tabs' =>
					array (
					  'tabsLayout' => 'TabsLayout.tab1',
					  'homeTab' => 'Top news',
					  'tabs' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=32',
						  'title' => 'Football',
						),
						1 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=33',
						  'title' => 'Racing',
						),
						2 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=28',
						  'title' => 'Sports',
						),
						3 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
						4 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=26',
						  'title' => 'Life Style',
						),
						5 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=11',
						  'title' => 'Travel',
						),
						6 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
					  ),
					  'firstFeatured' => 'PostLayout.imagePost',
					  'postLayout' => 'PostLayout.minimalPost',
					  'options' =>
					  array (
						'category' => 'true',
						'readTime' => 'true',
						'date' => 'true',
						'share' => 'true',
						'save' => 'true',
					  ),
					),
					'homePage' =>
					array (
					  'sections' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest&count=9',
						  'seeMore' =>
						  array (
							'name' => NULL,
							'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest',
						  ),
						  'postLayout' => 'PostLayout.minimalPost',
						  'firstFeatured' => 'PostLayout.imagePost',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'date' => 'true',
							'share' => 'true',
							'save' => 'true',
						  ),
						),
					  ),
					),
					'archives' =>
					array (
					  'categories' =>
					  array (
						'layout' => 'CategoriesLayout.cat3',
						'url' => '/wp-json/wl/v1/categories',
					  ),
					  'single' =>
					  array (
						'answerButton' => 'true',
					  ),
					  'category' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'search' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'favorites' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'url' => '/wp-json/wl/v1/posts?&ids=',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					),
					'styling' =>
					array (
					  'ThemeMode.light' =>
					  array (
						'scaffoldBackgroundColor' => '#000000',
						'primary' => '#0080EE',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#0080EE',
						'appBarColor' => '#FFFFFF',
						'background' => '#FFFFFF',
						'sidemenutextcolor' => '#333739',
						'bottomBarBackgroundColor' => '#222222',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#ffffff',
						'tabBarBackgroundColor' => '#0080EE',
						'tabBarTextColor' => '#ffffff',
						'tabBarActiveTextColor' => '#ffffff',
						'tabBarIndicatorColor' => '#ffffff',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(0,0,0,0.05)',
						'inputsbackgroundcolor' => 'rgba(0,0,0,0.04)',
						'buttonsbackgroudcolor' => '#0080EE',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#FFFFFF',
						'settingTextColor' => '#000000',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					  'ThemeMode.dark' =>
					  array (
						'scaffoldBackgroundColor' => '#000000',
						'primary' => '#0080EE',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#0080EE',
						'appBarColor' => '#FFFFFF',
						'background' => '#333739',
						'sidemenutextcolor' => '#FFFFFF',
						'bottomBarBackgroundColor' => '#222222',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#ffffff',
						'tabBarBackgroundColor' => '#0080EE',
						'tabBarTextColor' => '#ffffff',
						'tabBarActiveTextColor' => '#ffffff',
						'tabBarIndicatorColor' => '#ffffff',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(255,255,255,0.13)',
						'inputsbackgroundcolor' => 'rgba(255,255,255,0.07)',
						'buttonsbackgroudcolor' => '#0080EE',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#000000',
						'settingTextColor' => '#FFFFFF',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					),
					'settingsPage' =>
					array (
					  'textSize' => 'true',
					  'rateApp' => 'true',
					  'shareApp' =>
					  array (
						'title' => '',
						'image' => '',
						'android' => '',
						'ios' => '',
					  ),
					  'privacyPolicy' => '/wp-json/wl/v1/page?id=1046',
					  'termsAndConditions' => '/wp-json/wl/v1/page?id=1037',
					  'contactUs' => '/wp-json/wl/v1/contact-us',
					  'aboutApp' =>
					  array (
						'aboutLogoLight' => 'http://appstage.tielabs.com/wp-content/uploads/2020/07/logo-demo-6-white.png',
						'aboutLogoDark' => 'http://appstage.tielabs.com/wp-content/uploads/2020/07/logo-demo-6-white.png',
						'title' => 'My WordPress Website',
						'content' => 'Just another WordPress sitern',
					  ),
					  'shortCodes' => 'true',
					  'devMode' =>
					  array (
						'time' => '6000',
						'count' => '3',
						'addUrl' => '/?edd_action=save_development_token',
						'removeUrl' => '/?edd_action=remove_development_token',
					  ),
					  'demos' => 'true',
					),
					'basicUrls' =>
					array (
					  'devMode' => 'wp-json/wl/v1/dev-mode',
					  'getPost' => '/wp-json/wl/v1/post',
					  'submitComment' => '/wp-json/wl/v1/add-comment',
					  'removeUrl' => '/?edd_action=remove_development_token',
					  'saveToken' => '/?edd_action=save_token',
					  'translations' => '/wp-json/wl/v1/translations',
					  'getPostWPJSON' => '/wp-json/wl/v1/post',
					  'getTags' => '/wp-json/wl/v1/posts?tags=',
					  'getTagsPosts' => '/wp-json/wl/v1/posts?tags=',
					  'login' => '/wp-json/wl/v1/login',
					  'selectDemo' => '/wp-json/wl/v1/selectDemo',
					  'demos' => '/wp-json/wl/v1/demos',
					),
					'baseUrl' => 'https://appstage.tielabs.com/',
					'defaultLayout' => 'Layout.standard',
					'searchApi' => '/wp-json/wl/v1/posts?s=',
					'commentsApi' => '/wp-json/wl/v1/comments?id=',
					'commentAdd' => '/wp-json/wl/v1/add-comment',
					'relatedPostsApi' => '/wp-json/wl/v1/posts?related_id=',
					'lang' => 'en',
					'validConfig' => 'true',
					'copyrights' => 'https://appbear.io/',
				);
			break;
			case 7:
				$demo_data = array (
					'themeMode' => 'ThemeMode.light',
					'logo' =>
					array (
					  'light' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-7.png',
					  'dark' => 'http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-2.png',
					),
					'appBar' =>
					array (
					  'layout' => 'AppBarLayout.header2',
					  'position' => 'LogoPosition.center',
					  'searchIcon' => '0xe820',
					),
					'bottomBar' =>
					array (
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe800',
						  'main' => 'MainPage.home',
						  'title_enable' => 'true',
						  'title' => 'Home',
						),
						1 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe801',
						  'main' => 'MainPage.sections',
						  'title_enable' => 'true',
						  'title' => 'Categories',
						),
						2 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe803',
						  'main' => 'MainPage.favourites',
						  'title_enable' => 'true',
						  'title' => 'Favorites',
						),
						3 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe935',
						  'main' => 'MainPage.settings',
						  'title_enable' => 'true',
						  'title' => 'Settings',
						),
					  ),
					),
					'tabs' =>
					array (
					  'tabsLayout' => 'TabsLayout.tab1',
					  'homeTab' => 'Top news',
					  'tabs' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=32',
						  'title' => 'Football',
						),
						1 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=33',
						  'title' => 'Racing',
						),
						2 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=28',
						  'title' => 'Sports',
						),
						3 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
						4 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=26',
						  'title' => 'Life Style',
						),
						5 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=11',
						  'title' => 'Travel',
						),
						6 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
					  ),
					  'firstFeatured' => 'PostLayout.cardPost',
					  'postLayout' => 'PostLayout.startThumbPost',
					  'options' =>
					  array (
						'category' => 'true',
						'readTime' => 'true',
						'date' => 'true',
						'share' => 'true',
						'save' => 'true',
					  ),
					),
					'homePage' =>
					array (
					  'sections' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest&count=9',
						  'seeMore' =>
						  array (
							'name' => NULL,
							'url' => '/wp-json/wl/v1/posts?&categories=13,28,11,17&offset=0&sort=latest',
						  ),
						  'postLayout' => 'PostLayout.minimalPost',
						  'firstFeatured' => 'PostLayout.cardPost',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'date' => 'true',
							'share' => 'true',
							'save' => 'true',
						  ),
						),
					  ),
					),
					'archives' =>
					array (
					  'categories' =>
					  array (
						'layout' => 'CategoriesLayout.cat2',
						'url' => '/wp-json/wl/v1/categories',
					  ),
					  'single' =>
					  array (
						'answerButton' => 'true',
					  ),
					  'category' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'search' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'favorites' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'url' => '/wp-json/wl/v1/posts?&ids=',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					),
					'styling' =>
					array (
					  'ThemeMode.light' =>
					  array (
						'scaffoldBackgroundColor' => '#FFFFFF',
						'primary' => '#28B291',
						'secondary' => '#000000',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#FFFFFF',
						'appBarColor' => '#222222',
						'background' => '#FFFFFF',
						'sidemenutextcolor' => '#333739',
						'bottomBarBackgroundColor' => '#ffffff',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#222222',
						'tabBarBackgroundColor' => '#ffffff',
						'tabBarTextColor' => '#8B8D8F',
						'tabBarActiveTextColor' => '#222222',
						'tabBarIndicatorColor' => '#28B291',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(0,0,0,0.05)',
						'inputsbackgroundcolor' => 'rgba(0,0,0,0.04)',
						'buttonsbackgroudcolor' => '#28B291',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#FFFFFF',
						'settingTextColor' => '#000000',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					  'ThemeMode.dark' =>
					  array (
						'scaffoldBackgroundColor' => '#000000',
						'primary' => '#28B291',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#222222',
						'appBarColor' => '#FFFFFF',
						'background' => '#333739',
						'sidemenutextcolor' => '#FFFFFF',
						'bottomBarBackgroundColor' => '#222222',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#ffffff',
						'tabBarBackgroundColor' => '#222222',
						'tabBarTextColor' => '#8A8A89',
						'tabBarActiveTextColor' => '#ffffff',
						'tabBarIndicatorColor' => '#28B291',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(255,255,255,0.13)',
						'inputsbackgroundcolor' => 'rgba(255,255,255,0.07)',
						'buttonsbackgroudcolor' => '#28B291',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#000000',
						'settingTextColor' => '#FFFFFF',
						'errorColor' => '#D91C49',
						'successColor' => '#33C75F',
					  ),
					),
					'settingsPage' =>
					array (
					  'textSize' => 'true',
					  'darkMode' => 'true',
					  'rateApp' => 'true',
					  'shareApp' =>
					  array (
						'title' => '',
						'image' => '',
						'android' => '',
						'ios' => '',
					  ),
					  'privacyPolicy' => '/wp-json/wl/v1/page?id=1046',
					  'termsAndConditions' => '/wp-json/wl/v1/page?id=1037',
					  'contactUs' => '/wp-json/wl/v1/contact-us',
					  'aboutApp' =>
					  array (
						'aboutLogoLight' => 'http://appstage.tielabs.com/wp-content/plugins/appBear-plugin/img/jannah-logo-light.png',
						'aboutLogoDark' => 'http://appstage.tielabs.com/wp-content/plugins/appBear-plugin/img/jannah-logo-dark.png',
						'title' => 'My WordPress Website',
						'content' => 'Just another WordPress sitern',
					  ),
					  'shortCodes' => 'true',
					  'devMode' =>
					  array (
						'time' => '6000',
						'count' => '3',
						'addUrl' => '/?edd_action=save_development_token',
						'removeUrl' => '/?edd_action=remove_development_token',
					  ),
					  'demos' => 'true',
					),
					'basicUrls' =>
					array (
					  'devMode' => 'wp-json/wl/v1/dev-mode',
					  'getPost' => '/wp-json/wl/v1/post',
					  'submitComment' => '/wp-json/wl/v1/add-comment',
					  'removeUrl' => '/?edd_action=remove_development_token',
					  'saveToken' => '/?edd_action=save_token',
					  'translations' => '/wp-json/wl/v1/translations',
					  'getPostWPJSON' => '/wp-json/wl/v1/post',
					  'getTags' => '/wp-json/wl/v1/posts?tags=',
					  'getTagsPosts' => '/wp-json/wl/v1/posts?tags=',
					  'login' => '/wp-json/wl/v1/login',
					  'selectDemo' => '/wp-json/wl/v1/selectDemo',
					  'demos' => '/wp-json/wl/v1/demos',
					),
					'baseUrl' => 'http://appstage.tielabs.com/',
					'defaultLayout' => 'Layout.standard',
					'searchApi' => '/wp-json/wl/v1/posts?s=',
					'commentsApi' => '/wp-json/wl/v1/comments?id=',
					'commentAdd' => '/wp-json/wl/v1/add-comment',
					'relatedPostsApi' => '/wp-json/wl/v1/posts?related_id=',
					'lang' => 'en',
					'validConfig' => 'true',
					'copyrights' => 'https://appbear.io/',
				  );
			break;
			case 8:
				$demo_data = array (
					'themeMode' => 'ThemeMode.light',
					'onboardModels' =>
					array (
					  0 =>
					  array (
						'title' => 'International',
						'image' => 'http://appstage.tielabs.com/wp-content/uploads/2020/07/svg_1.png',
						'subTitle' => 'Find the latest breaking news and information on the top stories, weather, business, entertainment, politics,  and more.',
					  ),
					  1 =>
					  array (
						'title' => 'Live news',
						'image' => 'http://appstage.tielabs.com/wp-content/uploads/2020/10/svg_2.png',
						'subTitle' => 'Sahifa News Live is a 24/7 streaming channel for breaking news, live events and latest news headlines, and more.',
					  ),
					  2 =>
					  array (
						'title' => 'World News and Video',
						'image' => 'http://appstage.tielabs.com/wp-content/uploads/2020/10/svg_3.png',
						'subTitle' => 'Get the latest Sahifa World News international news, features and analysis from Middle East, and more.',
					  ),
					),
					'logo' =>
					array (
					  'light' => 'https://www.bdaia.com/amr_work/news/demos/logos/logo-demo-8.png',
					  'dark' => 'https://www.bdaia.com/amr_work/news/demos/logos/logo-demo-6.png',
					),
					'appBar' =>
					array (
					  'layout' => 'AppBarLayout.header2',
					  'position' => 'LogoPosition.center',
					  'searchIcon' => '0xe820',
					),
					'sideNavbar' =>
					array (
					  'icon' => '0xed7f',
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.page',
						  'side_menu_tab_icon' => 'true',
						  'icon' => '0xe962',
						  'title' => 'TieLabs App',
						  'url' => '/wp-json/wl/v1/page?id=1046',
						),
						1 =>
						array (
						  'type' => 'NavigationType.category',
						  'side_menu_tab_icon' => 'true',
						  'icon' => '0xe97d',
						  'title' => 'Technology',
						  'url' => '/wp-json/wl/v1/posts?category_id=30',
						),
						2 =>
						array (
						  'type' => 'NavigationType.category',
						  'side_menu_tab_icon' => 'true',
						  'icon' => '0xe9b2',
						  'title' => 'Football',
						  'url' => '/wp-json/wl/v1/posts?category_id=32',
						),
						3 =>
						array (
						  'type' => 'NavigationType.category',
						  'side_menu_tab_icon' => 'true',
						  'icon' => '0xf0c3',
						  'title' => 'World',
						  'url' => '/wp-json/wl/v1/posts?category_id=2',
						),
						4 =>
						array (
						  'type' => 'NavigationType.page',
						  'side_menu_tab_icon' => 'true',
						  'icon' => '0xe972',
						  'title' => 'Help',
						  'url' => '/wp-json/wl/v1/page?id=1038',
						),
					  ),
					),
					'bottomBar' =>
					array (
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe800',
						  'main' => 'MainPage.home',
						  'title_enable' => 'true',
						  'title' => 'Home',
						),
						1 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe801',
						  'main' => 'MainPage.sections',
						  'title_enable' => 'true',
						  'title' => 'Categories',
						),
						2 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe803',
						  'main' => 'MainPage.favourites',
						  'title_enable' => 'true',
						  'title' => 'Favorites',
						),
						3 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe935',
						  'main' => 'MainPage.settings',
						  'title_enable' => 'true',
						  'title' => 'Settings',
						),
					  ),
					),
					'tabs' =>
					array (
					  'tabsLayout' => 'TabsLayout.tab1',
					  'homeTab' => 'Top news',
					  'tabs' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=32',
						  'title' => 'Football',
						),
						1 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=33',
						  'title' => 'Racing',
						),
						2 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=28',
						  'title' => 'Sports',
						),
						3 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
						4 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=26',
						  'title' => 'Life Style',
						),
						5 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=11',
						  'title' => 'Travel',
						),
						6 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=2',
						  'title' => 'World',
						),
					  ),
					  'firstFeatured' => 'PostLayout.cardPost',
					  'postLayout' => 'PostLayout.startThumbPost',
					  'options' =>
					  array (
						'category' => 'true',
						'readTime' => 'true',
						'date' => 'true',
						'share' => 'true',
						'save' => 'true',
					  ),
					),
					'homePage' =>
					array (
					  'sections' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=1,32,26,29,30&offset=0&sort=latest&count=3',
						  'seeMore' =>
						  array (
							'name' => NULL,
							'url' => '/wp-json/wl/v1/posts?&categories=1,32,26,29,30&offset=0&sort=latest',
						  ),
						  'postLayout' => 'PostLayout.startThumbPostCompact',
						  'firstFeatured' => 'PostLayout.featuredPost',
						  'separator' => 'true',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'date' => 'true',
							'share' => 'true',
							'save' => 'true',
						  ),
						),
						1 =>
						array (
						  'title' => 'TRENDING NEWS',
						  'seeMore' =>
						  array (
							'name' => 'TRENDING NEWS',
							'url' => '/wp-json/wl/v1/posts?&categories=31,16,32,13,21,26,27,33,28,29,19,30,11,1,2,17&offset=0&sort=latest',
						  ),
						  'url' => '/wp-json/wl/v1/posts?&categories=31,16,32,13,21,26,27,33,28,29,19,30,11,1,2,17&offset=0&sort=latest&count=3',
						  'postLayout' => 'PostLayout.endThumbPost',
						  'separator' => 'true',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'save' => 'true',
						  ),
						),
						2 =>
						array (
						  'title' => 'LIVE NEWS',
						  'seeMore' =>
						  array (
							'name' => 'LIVE NEWS',
							'url' => '/wp-json/wl/v1/posts?&categories=31,16,32,13,21,26,27,33,28,29,19,30,11,1,2,17&offset=0&sort=latest',
						  ),
						  'url' => '/wp-json/wl/v1/posts?&categories=31,16,32,13,21,26,27,33,28,29,19,30,11,1,2,17&offset=0&sort=latest&count=4',
						  'postLayout' => 'PostLayout.relatedPost',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'save' => 'true',
						  ),
						),
						3 =>
						array (
						  'title' => 'TRAVEL NEWS',
						  'seeMore' =>
						  array (
							'name' => 'TRAVEL NEWS',
							'url' => '/wp-json/wl/v1/posts?&categories=11&offset=0&sort=latest',
						  ),
						  'url' => '/wp-json/wl/v1/posts?&categories=11&offset=0&sort=latest&count=3',
						  'postLayout' => 'PostLayout.startThumbPostCompact',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'save' => 'true',
						  ),
						),
					  ),
					),
					'archives' =>
					array (
					  'categories' =>
					  array (
						'layout' => 'CategoriesLayout.cat1',
						'url' => '/wp-json/wl/v1/categories',
					  ),
					  'single' =>
					  array (
						'answerButton' => 'true',
					  ),
					  'category' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'search' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'favorites' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'url' => '/wp-json/wl/v1/posts?&ids=',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					),
					'styling' =>
					array (
					  'ThemeMode.light' =>
					  array (
						'scaffoldBackgroundColor' => '#FFFFFF',
						'primary' => '#f88c00',
						'secondary' => '#333739',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#FFFFFF',
						'appBarColor' => '#333739',
						'background' => '#FFFFFF',
						'sidemenutextcolor' => '#333739',
						'bottomBarBackgroundColor' => '#ffffff',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#333739',
						'tabBarBackgroundColor' => '#FFFFFF',
						'tabBarTextColor' => '#7f7f7f',
						'tabBarActiveTextColor' => '#f88c00',
						'tabBarIndicatorColor' => '#f88c00',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(0,0,0,0.05)',
						'inputsbackgroundcolor' => 'rgba(0,0,0,0.04)',
						'buttonsbackgroudcolor' => '#0080EE',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#FFFFFF',
						'settingTextColor' => '#000000',
						'errorColor' => '#FF0000',
						'successColor' => '#006900',
					  ),
					  'ThemeMode.dark' =>
					  array (
						'scaffoldBackgroundColor' => '#333739',
						'primary' => '#f88c00',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#333739',
						'appBarColor' => '#FFFFFF',
						'background' => '#333739',
						'sidemenutextcolor' => '#FFFFFF',
						'bottomBarBackgroundColor' => '#333739',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#ffffff',
						'tabBarBackgroundColor' => '#333739',
						'tabBarTextColor' => '#7f7f7f',
						'tabBarActiveTextColor' => '#FFFFFF',
						'tabBarIndicatorColor' => '#f88c00',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(255,255,255,0.13)',
						'inputsbackgroundcolor' => 'rgba(255,255,255,0.07)',
						'buttonsbackgroudcolor' => '#0080EE',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#000000',
						'settingTextColor' => '#000000',
						'errorColor' => '#FF0000',
						'successColor' => '#006900',
					  ),
					),
					'settingsPage' =>
					array (
					  'textSize' => 'true',
					  'darkMode' => 'true',
					  'rateApp' => 'true',
					  'shareApp' =>
					  array (
						'title' => '',
						'image' => '',
						'android' => '',
						'ios' => '',
					  ),
					  'privacyPolicy' => '/wp-json/wl/v1/page?id=1046',
					  'termsAndConditions' => '/wp-json/wl/v1/page?id=1037',
					  'contactUs' => '/wp-json/wl/v1/contact-us',
					  'aboutApp' =>
					  array (
						'aboutLogoLight' => 'http://appstage.tielabs.com/wp-content/uploads/2020/07/logo-demo-6-white.png',
						'aboutLogoDark' => 'http://appstage.tielabs.com/wp-content/uploads/2020/07/logo-demo-6-white.png',
						'title' => 'My WordPress Website',
						'content' => 'Just another WordPress sitern',
					  ),
					  'shortCodes' => 'true',
					  'devMode' =>
					  array (
						'time' => '6000',
						'count' => '3',
						'addUrl' => '/?edd_action=save_development_token',
						'removeUrl' => '/?edd_action=remove_development_token',
					  ),
					  'demos' => 'true',
					),
					'basicUrls' =>
					array (
					  'devMode' => 'wp-json/wl/v1/dev-mode',
					  'getPost' => '/wp-json/wl/v1/post',
					  'submitComment' => '/wp-json/wl/v1/add-comment',
					  'removeUrl' => '/?edd_action=remove_development_token',
					  'saveToken' => '/?edd_action=save_token',
					  'translations' => '/wp-json/wl/v1/translations',
					  'getPostWPJSON' => '/wp-json/wl/v1/post',
					  'getTags' => '/wp-json/wl/v1/posts?tags=',
					  'getTagsPosts' => '/wp-json/wl/v1/posts?tags=',
					  'login' => '/wp-json/wl/v1/login',
					  'selectDemo' => '/wp-json/wl/v1/selectDemo',
					  'demos' => '/wp-json/wl/v1/demos',
					),
					'baseUrl' => 'http://appstage.tielabs.com/',
					'defaultLayout' => 'Layout.standard',
					'searchApi' => '/wp-json/wl/v1/posts?s=',
					'commentsApi' => '/wp-json/wl/v1/comments?id=',
					'commentAdd' => '/wp-json/wl/v1/add-comment',
					'relatedPostsApi' => '/wp-json/wl/v1/posts?related_id=',
					'lang' => 'en',
					'validConfig' => 'true',
					'copyrights' => 'https://appbear.io/',
				);
			break;
			case 9:
				$demo_data = array (
					'rtl' => 'true',
					'themeMode' => 'ThemeMode.light',
					'onboardModels' =>
					array (
					  0 =>
					  array (
						'title' => 'International',
						'image' => 'http://Array/2020/07/svg_1.png',
						'subTitle' => 'Find the latest breaking news and information on the top stories, weather, business, entertainment, politics,  and more.',
					  ),
					  1 =>
					  array (
						'title' => 'Live news',
						'image' => 'http://Array/2020/10/svg_2.png',
						'subTitle' => 'Sahifa News Live is a 24/7 streaming channel for breaking news, live events and latest news headlines, and more.',
					  ),
					  2 =>
					  array (
						'title' => 'World News and Video',
						'image' => 'http://Array/2020/10/svg_3.png',
						'subTitle' => 'Get the latest Sahifa World News international news, features and analysis from Middle East, and more.',
					  ),
					),
					'logo' =>
					array (
					  'light' => 'https://www.bdaia.com/amr_work/news/demos/logos/logo-demo-8.png',
					  'dark' => 'https://www.bdaia.com/amr_work/news/demos/logos/logo-demo-6.png',
					),
					'appBar' =>
					array (
					  'layout' => 'AppBarLayout.header2',
					  'position' => 'LogoPosition.center',
					  'searchIcon' => '0xe820',
					),
					'sideNavbar' =>
					array (
					  'icon' => '0xed7f',
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.page',
						  'side_menu_tab_icon' => 'true',
						  'icon' => '0xe962',
						  'title' => 'سليدر #١٨',
						  'url' => '/wp-json/wl/v1/page?id=3815',
						),
						1 =>
						array (
						  'type' => 'NavigationType.category',
						  'side_menu_tab_icon' => 'true',
						  'icon' => '0xe97d',
						  'title' => 'الحياة والمجتمع',
						  'url' => '/wp-json/wl/v1/posts?category_id=6',
						),
						2 =>
						array (
						  'type' => 'NavigationType.category',
						  'side_menu_tab_icon' => 'true',
						  'icon' => '0xe9b2',
						  'title' => 'السباحة',
						  'url' => '/wp-json/wl/v1/posts?category_id=140',
						),
						3 =>
						array (
						  'type' => 'NavigationType.category',
						  'side_menu_tab_icon' => 'true',
						  'icon' => '0xf0c3',
						  'title' => 'التكنولوجيا',
						  'url' => '/wp-json/wl/v1/posts?category_id=64',
						),
						4 =>
						array (
						  'type' => 'NavigationType.page',
						  'side_menu_tab_icon' => 'true',
						  'icon' => '0xe972',
						  'title' => 'مساعدة',
						  'url' => '/wp-json/wl/v1/page?id=3815',
						),
					  ),
					),
					'bottomBar' =>
					array (
					  'navigators' =>
					  array (
						0 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe800',
						  'main' => 'MainPage.home',
						  'title_enable' => 'true',
						  'title' => 'الرئيسية',
						),
						1 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe801',
						  'main' => 'MainPage.sections',
						  'title_enable' => 'true',
						  'title' => 'الأقسام',
						),
						2 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe803',
						  'main' => 'MainPage.favourites',
						  'title_enable' => 'true',
						  'title' => 'المفضلة',
						),
						3 =>
						array (
						  'type' => 'NavigationType.main',
						  'bottom_bar_icon_enable' => 'true',
						  'icon' => '0xe935',
						  'main' => 'MainPage.settings',
						  'title_enable' => 'true',
						  'title' => 'الإعدادات',
						),
					  ),
					),
					'homePage' =>
					array (
					  'sections' =>
					  array (
						0 =>
						array (
						  'url' => '/wp-json/wl/v1/posts?&categories=&offset=0&sort=latest&count=3',
						  'seeMore' =>
						  array (
							'name' => NULL,
							'url' => '/wp-json/wl/v1/posts?&categories=&offset=0&sort=latest',
						  ),
						  'postLayout' => 'PostLayout.startThumbPostCompact',
						  'firstFeatured' => 'PostLayout.featuredPost',
						  'separator' => 'true',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'date' => 'true',
							'share' => 'true',
							'save' => 'true',
						  ),
						),
						1 =>
						array (
						  'title' => 'الأخبار المهمة',
						  'seeMore' =>
						  array (
							'name' => 'الأخبار المهمة',
							'url' => '/wp-json/wl/v1/posts?&categories=&offset=0&sort=latest',
						  ),
						  'url' => '/wp-json/wl/v1/posts?&categories=&offset=0&sort=latest&count=3',
						  'postLayout' => 'PostLayout.endThumbPost',
						  'separator' => 'true',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'save' => 'true',
						  ),
						),
						2 =>
						array (
						  'title' => 'أحداث مباشرة',
						  'seeMore' =>
						  array (
							'name' => 'أحداث مباشرة',
							'url' => '/wp-json/wl/v1/posts?&categories=&offset=0&sort=latest',
						  ),
						  'url' => '/wp-json/wl/v1/posts?&categories=&offset=0&sort=latest&count=4',
						  'postLayout' => 'PostLayout.relatedPost',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'save' => 'true',
						  ),
						),
						3 =>
						array (
						  'title' => 'أخبار السفر',
						  'seeMore' =>
						  array (
							'name' => 'أخبار السفر',
							'url' => '/wp-json/wl/v1/posts?&categories=&offset=0&sort=latest',
						  ),
						  'url' => '/wp-json/wl/v1/posts?&categories=&offset=0&sort=latest&count=3',
						  'postLayout' => 'PostLayout.startThumbPostCompact',
						  'options' =>
						  array (
							'category' => 'true',
							'readTime' => 'true',
							'save' => 'true',
						  ),
						),
					  ),
					),
					'archives' =>
					array (
					  'categories' =>
					  array (
						'layout' => 'CategoriesLayout.cat1',
						'url' => '/wp-json/wl/v1/categories',
					  ),
					  'single' =>
					  array (
						'answerButton' => 'true',
					  ),
					  'category' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'search' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					  'favorites' =>
					  array (
						'postLayout' => 'PostLayout.minimalPost',
						'url' => '/wp-json/wl/v1/posts?&ids=',
						'options' =>
						array (
						  'count' => '10',
						  'category' => 'true',
						  'readTime' => 'true',
						  'date' => 'true',
						  'save' => 'true',
						  'share' => 'true',
						),
					  ),
					),
					'styling' =>
					array (
					  'ThemeMode.light' =>
					  array (
						'scaffoldBackgroundColor' => '#FFFFFF',
						'primary' => '#f88c00',
						'secondary' => '#333739',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#FFFFFF',
						'appBarColor' => '#333739',
						'background' => '#FFFFFF',
						'sidemenutextcolor' => '#333739',
						'bottomBarBackgroundColor' => '#ffffff',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#333739',
						'tabBarBackgroundColor' => '#FFFFFF',
						'tabBarTextColor' => '#7f7f7f',
						'tabBarActiveTextColor' => '#f88c00',
						'tabBarIndicatorColor' => '#f88c00',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(0,0,0,0.05)',
						'inputsbackgroundcolor' => 'rgba(0,0,0,0.04)',
						'buttonsbackgroudcolor' => '#0080EE',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#FFFFFF',
						'settingTextColor' => '#000000',
						'errorColor' => '#FF0000',
						'successColor' => '#006900',
					  ),
					  'ThemeMode.dark' =>
					  array (
						'scaffoldBackgroundColor' => '#333739',
						'primary' => '#f88c00',
						'secondary' => '#FFFFFF',
						'secondaryVariant' => '#8A8A89',
						'appBarBackgroundColor' => '#333739',
						'appBarColor' => '#FFFFFF',
						'background' => '#333739',
						'sidemenutextcolor' => '#FFFFFF',
						'bottomBarBackgroundColor' => '#333739',
						'bottomBarInActiveColor' => '#8A8A89',
						'bottomBarActiveColor' => '#ffffff',
						'tabBarBackgroundColor' => '#333739',
						'tabBarTextColor' => '#7f7f7f',
						'tabBarActiveTextColor' => '#FFFFFF',
						'tabBarIndicatorColor' => '#f88c00',
						'shadowColor' => 'rgba(0,0,0,0.15)',
						'dividerColor' => 'rgba(255,255,255,0.13)',
						'inputsbackgroundcolor' => 'rgba(255,255,255,0.07)',
						'buttonsbackgroudcolor' => '#0080EE',
						'buttonTextColor' => '#FFFFFF',
						'settingBackgroundColor' => '#000000',
						'settingTextColor' => '#FFFFFF',
						'errorColor' => '#FF0000',
						'successColor' => '#006900',
					  ),
					),
					'settingsPage' =>
					array (
					  'textSize' => 'true',
					  'darkMode' => 'true',
					  'rateApp' => 'true',
					  'demos' => 'true',
					  'shareApp' =>
					  array (
						'title' => '',
						'image' => '',
						'android' => '',
						'ios' => '',
					  ),
					  'privacyPolicy' => '/wp-json/wl/v1/page?id=1039',
					  'termsAndConditions' => '/wp-json/wl/v1/page?id=1037',
					  'contactUs' => '/wp-json/wl/v1/contact-us',
					  'aboutApp' =>
					  array (
						'aboutLogoLight' => 'http://Array/2020/07/logo-demo-6-white.png',
						'aboutLogoDark' => 'http://Array/2020/07/logo-demo-6-white.png',
						'title' => 'My WordPress Website',
						'content' => 'Just another WordPress sitern',
					  ),
					  'devMode' =>
					  array (
						'time' => '6000',
						'count' => '3',
						'addUrl' => '/?edd_action=save_development_token',
						'removeUrl' => '/?edd_action=remove_development_token',
					  ),
					),
					'basicUrls' =>
					array (
					  'devMode' => 'wp-json/wl/v1/dev-mode',
					  'getPost' => '/wp-json/wl/v1/post',
					  'submitComment' => '/wp-json/wl/v1/add-comment',
					  'removeUrl' => '/?edd_action=remove_development_token',
					  'saveToken' => '/?edd_action=save_token',
					  'translations' => '/wp-json/wl/v1/translations',
					  'getPostWPJSON' => '/wp-json/wl/v1/post',
					  'getTags' => '/wp-json/wl/v1/posts?tags=',
					  'getTagsPosts' => '/wp-json/wl/v1/posts?tags=',
					  'login' => '/wp-json/wl/v1/login',
					  'selectDemo' => '/wp-json/wl/v1/selectDemo',
					  'demos' => '/wp-json/wl/v1/demos',
					),
					'baseUrl' => 'https://jannah.tielabs.com/appbear-rtl/',
					'defaultLayout' => 'Layout.standard',
					'searchApi' => '/wp-json/wl/v1/posts?s=',
					'commentsApi' => '/wp-json/wl/v1/comments?id=',
					'commentAdd' => '/wp-json/wl/v1/add-comment',
					'relatedPostsApi' => '/wp-json/wl/v1/posts?related_id=',
					'lang' => 'en',
					'copyrights' => 'https://appbear.io/',
					'validConfig' => 'true',
				);
			break;
		}

		return apply_filters( 'AppBear/API/Demos/data', $demo_data, $request['demo'] );
	}


}


new AppBear_Demos_Endpoints();
