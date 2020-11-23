<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * AppBear_Endpoints Class
 *
 * This class handles all API Endpoints
 *
 *
 * @since 1.0
 */
class AppBear_Endpoints {


	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wl/v1';


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
			'posts',
			'post',
			'categories',
			'tabs',
			'page',
			'add-comment',
			'get-version',
			'contact-us',
			'language',
			'options',
			'dev-mode',
			'translations',
			'translations_ar',
			'comments',
			'svg',
			'flr',
			'register',
			'selectdemo',
			'demos'
		);

		foreach ( $get_routes as $route ) {
			$this->register_rest_route( $route, 'GET' );
		}

		// POST routes
		$post_routes = array(
			'login'
		);

		foreach ( $post_routes as $route ) {
			$this->register_rest_route( $route, 'POST' );
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

		$callback = 'get_'.str_replace( '-', '_', $route );

		if( method_exists( $this, $callback ) ){
			register_rest_route( $this->namespace, $route, array(
				'methods'  => $method,
				'callback' => array( $this, $callback )
			) );
		}
	}


	/**
	 * get_posts
	 *
	 * @access public
	 * @since 1.0
	 * @return void
		 */
	public function get_posts( $request ) {

		$args = array(
			'post_type'      => ! empty( $request['post'] )  ? $request['post']  : 'post',
			'posts_per_page' => ! empty( $request['count'] ) ? $request['count'] : -1,
		);

		// Pagination
		if ( isset($request['page'] ) ) {
			$args['paged'] = $request['page'];
		}

		// Search
		if ( isset( $request['s'] ) ) {
			$args['s'] = $request['s'];
		}

		else{

			// Categories
			if ( isset( $request['categories'] ) ) {
				$args['cat'] = $request['categories'];
			}
			else if ( isset( $request['category_id'] ) ) {
				$args['cat'] = $request['category_id'];
			}

			// Tags
			if ( isset( $request['tags'] ) ) {
				$args['tag'] = $request['tags'];
			}

			// Posts IDs
			if ( isset( $request['ids'] ) ) {
				$args['post__in'] = explode( ',', $request['ids'] );
			}

			// Exclude posts
			if ( isset( $request['exclude'] ) ) {
				$args['post__not_in'] = explode( ',', $request['exclude'] );
			}

			if ( isset( $request['offset'] ) ) {
				$args['offset'] = $request['offset'];
			}

			if ( isset( $request['sort'] ) ) {
				$args['order']   = '';
				$args['orderby'] = $request['sort'];
			}


			// What is the fuck is this ------
				if (isset($request['related_id'])) {
					$cats = wp_get_post_categories($request['related_id']);
					$tags_IDs = wp_get_post_tags($request['related_id'], array('fields' => 'ids'));

					$args['category__in'] = $cats;
					$args['tag__in'] = $tags;
				}
			// ----------
		}


		// The Query
		$posts = new WP_Query( $args );


		// The Loop
		if ( $posts->have_posts() ) {

			$data = array(
				'status' => true,
				'posts'  => array(),
			);



			// ------
			if ($request['count'] == '-1') {
				$data['count'] 		= (int)$posts->found_posts;
			} else {
				$data['count'] 		= (int)$request['count'];
			}
			$data['count_total'] 		= (int)$posts->found_posts;
			$data['pages'] 					= ($request['count'] == '-1') ? 1 : ceil($posts->found_posts / $request['count']);
			// -----



			while ( $posts->have_posts() ) {

				$posts->the_post();

				$post_id = get_the_ID();


				// Some SHIT
				if ( $post_id == $request['related_id']) {
					$data["count"] -= $data["count"];
					$data["count_total"] -= $data["count_total"];
					$data["pages"] = ($request['count'] == 0) ? 0 : ceil(($posts->found_posts - 1) / $request['count']);
					if ($data["count"] == 0)
						$data['posts'] = array();
				}


				if ( $post_id != $request['related_id']) {



					$this_post = array(
						'id'            => $post_id,
						'share'         => get_permalink(),
						'url'           => get_permalink(),
						'status'        => get_post_status(),
						'title'         => get_the_title(),
						'title_plain'   => the_title_attribute('echo=0'),
						'excerpt'       => get_the_excerpt(),
						'date'          => appbear_get_time(),
						'modified'      => get_post_modified_time(),
						'comment_count' => (int) get_comments_number(),
						'readtime'      => '1 min read',
						'author'        => array(
							'name' => get_the_author(),
						),
					);

					// Post Format
					$format = appbear_post_format();

					$this_post['post'] = $format; // change it later to format instead of post

					if( $format == 'gallery' ){
						$this_post['gallery'] = appbear_post_gallery();
					}
					elseif( $format == 'video' ){
						$this_post['video'] = appbear_post_video();
					}





					// To be checked
					$categories = get_the_category();
					$categories_list = array();
					foreach ($categories as $category) {
						$category_list = $category;
						$category_list->url = 'wp-json/wl/v1/posts?category_id=' . $category->term_id;
						$category_list->id = $category->term_id;
						$categories_list[] = $category_list;
					}
					$this_post['categories'] = $categories_list;
					// -----

					// --- Featured Image
					$thumbnail = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
					$this_post['thumbnail'] = $thumbnail;
					$this_post['featured_image'] = array(
						'thumbnail' => $thumbnail,
						'medium'    => get_the_post_thumbnail_url( $post_id, 'medium' ),
						'large'     => get_the_post_thumbnail_url( $post_id, 'large' ),
					);


					$data['posts'][] = $this_post;
				}
			}



		} else {

			$data = array(
				'status'      => false,
				'count'       => 0,
				'count_total' => 0,
				'pages'       => 0,
			);

		}


		return $data;
	}



	/**
	 * Output JSON
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function output_json( $type = false, $data ) {

		header( 'Content-Type: application/json' );

		if( isset( $type ) ) {

			if( $type == 'error' ) {
				$data = array(
					'status' => 0,
					'error'  => $data,
				);
			}
			elseif( $type == 'success' && is_array( $data ) ) {
				$data['status'] = 1;
			}
		}

		echo json_encode( map_deep( $data, array( $this, 'kses' ) ) );
		exit;
	}

}


new AppBear_Endpoints();





function wl_demos(){
	$array = array (
		array(
			'id'	=>	1,
			'name'	=>	'Jannah Games',
		),
		array(
			'id'	=>	2,
			'name'	=>	'The Road Dark',
		),
		array(
			'id'	=>	3,
			'name'	=>	'Journal',
		),
		array(
			'id'	=>	4,
			'name'	=>	'Jannah Hotels',
		),
		array(
			'id'	=>	5,
			'name'	=>	'J Videos',
		),
		array(
			'id'	=>	6,
			'name'	=>	'Jannah News',
		),
		array(
			'id'	=>	7,
			'name'	=>	'The Road Light',
		),
		array(
			'id'	=>	8,
			'name'	=>	'Sahifa',
		),
		array(
			'id'	=>	9,
			'name'	=>	'عربي',
		),
	);
	header('Content-type: application/json');
	$demo = json_encode($array);
	echo $demo;
}

function wl_selectdemo(){
	$params = $_GET;
	$array 	= '';

	switch($params['demo']){
		case 1:
			$array = array (
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
			$array = array (
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
			$array = array (
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
			$array = array (
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
			$array = array (
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
			$array = array (
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
			$array = array (
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
			$array = array (
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
			$array = array (
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

	header('Content-type: application/json');
	$demo = json_encode($array);
	echo $demo;
}

//deep linking
add_action('wp_enqueue_scripts', 'deeplink_custom_js');
function deeplink_custom_js()
{
	if (is_single()) {

		wp_register_script('browser-deeplink', APPBEAR_URL . 'js/browser-deeplink.js', array());
		wp_enqueue_script('browser-deeplink');

		/*
       * TODO: get appId & appName iOS and Android from settings
      */
		$deeplinking = get_option('appbear-settings')['deeplinking'];
		//$deeplinking['ios']['appid'];
		//$deeplinking['ios']['appname'];
		//$deeplinking['android']['appid'];
		wp_add_inline_script('browser-deeplink', '
      	deeplink.setup({
      		iOS: {
      			appId: "1525329429",
      			appName: "com.jannah.app"
      			},
      			android: {
      				appId: "com.jannah.app",
      			}
      			});
      			window.onload = function() {
      				deeplink.open("' . get_the_ID() . '");
      			}
      			');
	}
}






















function shortcodes_parsing($content)
{



	// $pattern = '@(?<=)\[tie_list type="(.*?)(?=)"](?=)(.*?)\[/tie_list](?=)@sm';
	// $replacement = '
	// <div class="tie_list $1">
	// 	$2
	// </div>
	// ';
	// $string = preg_replace($pattern, $replacement, $string);

	$pattern = "/\[tie_list type=\"checklist\"\]\n<ul>\n/i";
	$string = preg_replace($pattern, "<div class=\"tie_list checklist\">", $content);
	$pattern = "/\[tie_list type=\"heart\"\]\n<ul>\n/i";
	$string = preg_replace($pattern, "<div class=\"tie_list heart\">", $string);
	$pattern = "/\[tie_list type=\"starlist\"\]\n<ul>\n/i";
	$string = preg_replace($pattern, "<div class=\"tie_list starlist\">", $string);
	$pattern = "/\[tie_list type=\"plus\"\]\n<ul>\n/i";
	$string = preg_replace($pattern, "<div class=\"tie_list plus\">", $string);
	$pattern = "/\[tie_list type=\"minus\"\]\n<ul>\n/i";
	$string = preg_replace($pattern, "<div class=\"tie_list minus\">", $string);
	$pattern = "/\[tie_list type=\"cons\"\]\n<ul>\n/i";
	$string = preg_replace($pattern, "<div class=\"tie_list cons\">", $string);
	$pattern = "/\[tie_list type=\"thumbdown\"\]\n<ul>\n/i";
	$string = preg_replace($pattern, "<div class=\"tie_list thumbdown\">", $string);
	$pattern = "/\[tie_list type=\"lightbulb\"\]\n<ul>\n/i";
	$string = preg_replace($pattern, "<div class=\"tie_list lightbulb\">", $string);
	$pattern = "/\[tie_list type=\"thumbup\"\]\n<ul>\n/i";
	$string = preg_replace($pattern, "<div class=\"tie_list thumbup\">", $string);

	$string = str_replace("[/tie_list]", "</div>", $string);

	$pattern = '/\[one\_[a_zA-Z]+\]/i';
	$string = preg_replace($pattern, "<div>", $string);
	$pattern = '/\[two\_[a_zA-Z]+\]/i';
	$string = preg_replace($pattern, "<div>", $string);
	$pattern = '/\[\/one\_[a_zA-Z]+\]/i';
	$string = preg_replace($pattern, "</div>", $string);
	$pattern = '/\[\/two\_[a_zA-Z]+\]/i';
	$string = preg_replace($pattern, "</div>", $string);
	$pattern = '/\[three\_[a_zA-Z]+\_[a_zA-Z]+\]/i';
	$string = preg_replace($pattern, "<div>", $string);
	$pattern = '/\[\/three\_[a_zA-Z]+\_[a_zA-Z]+\]/i';
	$string = preg_replace($pattern, "</div>", $string);
	$pattern = '/\[five\_[a_zA-Z]+\_[a_zA-Z]+\]/i';
	$string = preg_replace($pattern, "<div>", $string);
	$pattern = '/\[\/five\_[a_zA-Z]+\_[a_zA-Z]+\]/i';
	$string = preg_replace($pattern, "</div>", $string);
	// $pattern = '/[[0-9]+\/[0-9]+\]/i';
	// $string = preg_replace($pattern, "", $string);


	$string = str_replace('<p>', "<div>", $string);
	$string = str_replace('</p>', "</div>", $string);

	//button
	$pattern = '/\[button/i';
	$string = preg_replace($pattern, "<a class=\"shortc-button\" ", $string);

	$pattern = '/\[\/button\]/i';
	$string = preg_replace($pattern, "</a>", $string);

	$pattern = '/\[highlight/i';
	$string = preg_replace($pattern, "<span class=\"tie-highlight\"", $string);

	$pattern = '/\[\/highlight\]/i';
	$string = preg_replace($pattern, "</span>", $string);

	$pattern = '/\[tooltip/i';
	$string = preg_replace($pattern, "<a data-toggle=\"tooltip\" data-placement=\"top\" class=\"post-tooltip tooltip-top\"", $string);

	$pattern = '/gravity=\"[a-zA-Z]+\"\]/i';
	$string = preg_replace($pattern, "data-original-title=\"", $string);

	$pattern = '/\[\/tooltip\]/i';
	$string = preg_replace($pattern, "\"></a>", $string);

	//Slideshow
	$pattern = '/\[tie_slideshow\]/i';
	$string = preg_replace($pattern, "<div class=\"post-content-slideshow-outer\">
	<div class=\"post-content-slideshow\" style=\"min-height: auto;\">
	<div class=\"tie-slick-slider slick-initialized slick-slider slick-dotted\" role=\"toolbar\" style=\"display: block;\">
	<div aria-live=\"polite\" class=\"slick-list draggable\" style=\"height: 941px;\">
	<div class=\"slick-track\" style=\"opacity: 1; width: 1725px; transform: translate3d(-1035px, 0px, 0px);\" role=\"listbox\">", $string);

	$pattern = '/\[\/tie_slideshow\]/i';
	$string = preg_replace($pattern, "
				</div>
				</div>
				</div>
				<div class=\"slider-nav-wrapper\">
				<ul class=\"tie-slider-nav\">
				<li class=\"slick-arrow\" style=\"display: list-item;\"><span class=\"tie-icon-angle-left\"></span></li>
				<li class=\"slick-arrow\" style=\"display: list-item;\"><span class=\"tie-icon-angle-right\"></span></li>
				</ul>
				</div>
				 <ul class=\"tie-slick-dots\" style=\"display: block;\"><li class=\"\" aria-hidden=\"true\" aria-selected=\"true\" aria-controls=\"navigation20\" id=\"slick-slide20\">
				 <button type=\"button\" data-role=\"none\" tabindex=\"0\" role=\"button\">1</button></li>
				 <li aria-hidden=\"true\" aria-selected=\"false\" aria-controls=\"navigation21\" id=\"slick-slide21\" class=\"\"><button type=\"button\" data-role=\"none\" tabindex=\"0\" role=\"button\">2</button></li>
				 <li aria-hidden=\"false\" aria-selected=\"false\" aria-controls=\"navigation22\" id=\"slick-slide22\" class=\"slick-active\"><button type=\"button\" data-role=\"none\" tabindex=\"0\" role=\"button\">3</button></li>
				 </ul>
				 </div>
				 </div>", $string);

	$pattern = '/\[tie_slide\]/i';
	$string = preg_replace($pattern, "<div class=\"slide post-content-slide slick-slide slick-current slick-active\" data-slick-index=\"0\" aria-hidden=\"false\" style=\"width: 780px;\" tabindex=\"-1\" role=\"option\" data-aria-describedby=\"slick-slide10\">", $string);

	// $pattern = '/\[\/tie_slide\]/i';
	// $string = preg_replace($pattern, "<\div>", $string);
	$string = str_replace("[/tie_slide]", "</div>", $string);


	$pattern = '/\[lightbox full/i';
	$string = preg_replace($pattern, "<a class=\"lightbox-enabled\" href", $string);

	$pattern = '/\[\/lightbox\]/i';
	$string = preg_replace($pattern, "</a>", $string);

	//Toggle
	//in case of closed state
	$pattern = '/" state="[a-zA-Z]+" \]/i';
	$string = preg_replace($pattern, "<span class=\"fa fa-angle-down\" aria-hidden=\"true\"></span></h3><div class=\"toggle-content\" style=\"display: none;\">", $string);

	$pattern = '/\[toggle title="/i';
	$string = preg_replace($pattern, "<div class=\"toggle tie-sc-close\"> <h3 class=\"toggle-head\">", $string);

	// //in case of opened state
	// $pattern = '/\[toggle title="[a-zA-Z0-9 ]+" state="open" \]/i';
	// $string = preg_replace($pattern,"<div class=\"toggle tie-sc-open\"> <h3 class=\"toggle-head\">".$title."<span class=\"fa fa-angle-down\" aria-hidden=\"true\"></span></h3><div class=\"toggle-content\" style=\"display: block;\">", $string);

	$string = str_replace("[/toggle]", "</div></div>", $string);

	$string = str_replace("[tie_full_img]", "</img>", $string);
	$string = str_replace("[/tie_full_img]", "</img>", $string);


	$pattern = '/\[box type=\"success\" align=\"\" class=\"\" width=\"\"\]/i';
	$string = preg_replace($pattern, "<div class=\"box success\"><div class=\"box-inner-block\"><span class=\"fa tie-shortcode-boxicon\"></span>", $string);

	$pattern = '/\[box type=\"download\" align=\"\" class=\"\" width=\"\"\]/i';
	$string = preg_replace($pattern, "<div class=\"box download\"><div class=\"box-inner-block\"><span class=\"fa tie-shortcode-boxicon\"></span>", $string);

	$pattern = '/\[box type=\"warning\" align=\"\" class=\"\" width=\"\"\]/i';
	$string = preg_replace($pattern, "<div class=\"box warning\"><div class=\"box-inner-block\"><span class=\"fa tie-shortcode-boxicon\"></span>", $string);


	$pattern = '/\[box type=\"note\" align=\"\" class=\"\" width=\"\"\]/i';
	$string = preg_replace($pattern, "<div class=\"box note\"><div class=\"box-inner-block\"><span class=\"fa tie-shortcode-boxicon\"></span>", $string);


	$pattern = '/\[box type=\"info\" align=\"\" class=\"\" width=\"\"\]/i';
	$string = preg_replace($pattern, "<div class=\"box info\"><div class=\"box-inner-block\"><span class=\"fa tie-shortcode-boxicon\"></span>", $string);


	$pattern = '/\[box type=\"error\" align=\"\" class=\"\" width=\"\"\]/i';
	$string = preg_replace($pattern, "<div class=\"box error\"><div class=\"box-inner-block\"><span class=\"fa tie-shortcode-boxicon\"></span>", $string);


	$pattern = '/\[box type=\"shadow\" align=\"\" class=\"\" width=\"\"\]/i';
	$string = preg_replace($pattern, "<div class=\"box shadow\"><div class=\"box-inner-block\"><span class=\"fa tie-shortcode-boxicon\"></span>", $string);

	$string = str_replace("[/box]", "</div></div>", $string);


	$pattern = '/\[tabs type=\"horizontal\"\]/i';
	$string = preg_replace($pattern, "<div class=\"tabs-shortcode tabs-wrapper container-wrapper tabs-horizontal\">", $string);

	$pattern = '/\[tabs type=\"vertical\"\]/i';
	$string = preg_replace($pattern, "<div class=\"tabs-shortcode tabs-wrapper container-wrapper tabs-vertical\">", $string);

	$pattern = '/\[tab\]/i';
	$string = preg_replace($pattern, "<div class=\"tab-content\"><div class=\"tab-content-wrap\">", $string);

	$pattern = '/\[tab_title\]/i';
	$string = preg_replace($pattern, "<li>", $string);

	$pattern = '/\[tabs_head\]/i';
	$string = preg_replace($pattern, "<ul class=\"tabs\">", $string);

	$string = str_replace("[/tab_title]", "</li>", $string);
	$string = str_replace("[/tabs_head]", "</ul>", $string);
	$string = str_replace("[/tabs]", "</div>", $string);
	$string = str_replace("[/tab]", "</div></div>", $string);

	// divider

	$pattern = '/\[divider /i';
	$string = preg_replace($pattern, "<hr ", $string);
	$pattern = '/<hr style=\"/i';
	$string = preg_replace($pattern, "<hr class=\"divider divider-", $string);

	//padding
	// [padding right=\"5%\" left=\"5%\">
	// <div class="tie-padding  has-padding-left has-padding-right" style="padding-left:20%; padding-right:20%; padding-top:0; padding-bottom:0;">

	$pattern = '/\[padding /i';
	$string = preg_replace($pattern, "<div class=\"tie-padding  has-padding-left has-padding-right\" ", $string);
	$string = str_replace("[/padding]", "</div>", $string);

	//dropcap
	// <span class="tie-dropcap">s</span>
	// [dropcap]s[/dropcap]
	$string = str_replace("[dropcap]", "<span class=\"tie-dropcap\">", $string);
	$string = str_replace("[/dropcap]", "</span>", $string);

	//audio
	// [audio mp3=\"https://jannah.tielabs.com/jannah/wp-content/uploads/sites/8/2016/05/short-news.mp3\">
	$pattern = '/\[audio mp3/i';
	$string = preg_replace($pattern, '<div id="mep_0" class="mejs-container wp-audio-shortcode mejs-audio" tabindex="0" role="application" aria-label="Audio Player" style="width: 780px; height: 40px; min-width: 241px;"><div class="mejs-inner"><div class="mejs-mediaelement"><mediaelementwrapper id="audio-5092-1"><audio class="wp-audio-shortcode" id="audio-5092-1_html5" preload="none" style="width: 100%; height: 100%;"><source type="audio/mpeg"><a href', $string);

	$pattern = '/\.mp3"]/i';
	$string = preg_replace($pattern, '.mp3\"></audio></mediaelementwrapper></div><div class="mejs-layers"><div class="mejs-poster mejs-layer" style="display: none; width: 100%; height: 100%;"></div></div><div class="mejs-controls"><div class="mejs-button mejs-playpause-button mejs-play"><button type="button" aria-controls="mep_0" title="Play" aria-label="Play" tabindex="0"></button></div><div class="mejs-time mejs-currenttime-container" role="timer" aria-live="off"><span class="mejs-currenttime">00:00</span></div><div class="mejs-time-rail"><span class="mejs-time-total mejs-time-slider" role="slider" tabindex="0" aria-label="Time Slider" aria-valuemin="0" aria-valuemax="0" aria-valuenow="0" aria-valuetext="00:00"><span class="mejs-time-buffering" style="display: none;"></span><span class="mejs-time-loaded"></span><span class="mejs-time-current"></span><span class="mejs-time-hovered no-hover"></span><span class="mejs-time-handle"><span class="mejs-time-handle-content"></span></span><span class="mejs-time-float" style="display: none; left: 0px;"><span class="mejs-time-float-current">00:00</span><span class="mejs-time-float-corner"></span></span></span></div><div class="mejs-time mejs-duration-container"><span class="mejs-duration">00:00</span></div><div class="mejs-button mejs-volume-button mejs-mute"><button type="button" aria-controls="mep_0" title="Mute" aria-label="Mute" tabindex="0"></button></div><a class="mejs-horizontal-volume-slider" href="javascript:void(0);" aria-label="Volume Slider" aria-valuemin="0" aria-valuemax="100" aria-valuenow="100" role="slider"><span class="mejs-offscreen">Use Up/Down Arrow keys to increase or decrease volume.</span><div class="mejs-horizontal-volume-total"><div class="mejs-horizontal-volume-current" style="left: 0px; width: 100%;"></div><div class="mejs-horizontal-volume-handle" style="left: 100%;"></div></div></a></div></div></div>', $string);

	$string = str_replace("[tie_login]", '<div class="login-form">

		<form name="registerform" action="'.get_site_url().'/wp-login.php" method="post">
			<input type="text" name="log" title="Username" placeholder="Username">
			<div class="pass-container">
				<input type="password" name="pwd" title="Password" placeholder="Password">
				<a class="forget-text" href="'.get_site_url().'/wp-login.php?action=lostpassword&redirect_to='.get_site_url().'">Forget?</a>
			</div>

			<input type="hidden" name="redirect_to" value="/shortcode-test-test-fouad-hi/"/>
			<label for="rememberme" class="rememberme">
				<input id="rememberme" name="rememberme" type="checkbox" checked="checked" value="forever" /> Remember me			</label>



			<button type="submit" class="button fullwidth login-submit">Log In</button>

					</form>


	</div>', $string);

	$pattern = '@(?<=)\[googlemap src="(.*?)(?=)"](?=)@sm';
	$replacement = '
	<div class="google-map">
		<iframe width="100%" height="200" frameborder="0" title="Map" src="$1" async></iframe>
	</div>
	';
	$string = preg_replace($pattern, $replacement, $string);

	$pattern = '@(?<=)\[author title="(.*?)(?=)" image="(.*?)"](?=)(.*?)\[/author](?=)@sm';
	$replacement = '
	<div class="about-author about-author-box container-wrapper">
		<div class="author-avatar">
			<img src="$2" alt="">
		</div>
		<div class="author-info">
			<h4>$1</h4>$3
		</div>
	</div>
	';
	$string = preg_replace($pattern, $replacement, $string);

	//video
	$pattern = '/\[embed width=\"\" height=\"\"\]/i';
	$string = preg_replace($pattern, '<div style="width: 640px;" class="wp-video"><!--[if lt IE 9]><script>document.createElement(\'video\');</script><![endif]-->// <span class="mejs-offscreen">Video Player</span><div id="mep_1" class="mejs-container mejs-container-keyboard-inactive wp-video-shortcode mejs-video" tabindex="0" role="application" aria-label="Video Player" style="width: 345px; height: 194.062px; min-width: 217px;"><div class="mejs-inner"><div class="mejs-mediaelement"><mediaelementwrapper id="video-5092-1"><video class="wp-video-shortcode" id="video-5092-1_html5" width="640" height="360" preload="metadata" style="width: 345px; height: 194.062px;"><source type="video/mp4" src="', $string);

	$pattern = '/\[\/embed\]/i';
	$string = preg_replace($pattern, '"></video></mediaelementwrapper></div><div class="mejs-layers"><div class="mejs-poster mejs-layer" style="display: none; width: 100%; height: 100%;"></div><div class="mejs-overlay mejs-layer" style="width: 100%; height: 100%; display: none;"><div class="mejs-overlay-loading"><span class="mejs-overlay-loading-bg-img"></span></div></div><div class="mejs-overlay mejs-layer" style="display: none; width: 100%; height: 100%;"><div class="mejs-overlay-error"></div></div><div class="mejs-overlay mejs-layer mejs-overlay-play" style="width: 100%; height: 100%;"><div class="mejs-overlay-button" role="button" tabindex="0" aria-label="Play" aria-pressed="false"></div></div></div><div class="mejs-controls"><div class="mejs-button mejs-playpause-button mejs-play"><button type="button" aria-controls="mep_1" title="Play" aria-label="Play" tabindex="0"></button></div><div class="mejs-time mejs-currenttime-container" role="timer" aria-live="off"><span class="mejs-currenttime">00:00</span></div><div class="mejs-time-rail"><span class="mejs-time-total mejs-time-slider" role="slider" tabindex="0" aria-label="Time Slider" aria-valuemin="0" aria-valuemax="60.095011" aria-valuenow="0" aria-valuetext="00:00"><span class="mejs-time-buffering" style="display: none;"></span><span class="mejs-time-loaded" style="transform: scaleX(0.0594559);"></span><span class="mejs-time-current" style="transform: scaleX(0);"></span><span class="mejs-time-hovered no-hover"></span><span class="mejs-time-handle" style="transform: translateX(0px);"><span class="mejs-time-handle-content"></span></span><span class="mejs-time-float"><span class="mejs-time-float-current">00:00</span><span class="mejs-time-float-corner"></span></span></span></div><div class="mejs-time mejs-duration-container"><span class="mejs-duration">01:00</span></div><div class="mejs-button mejs-volume-button mejs-mute"><button type="button" aria-controls="mep_1" title="Mute" aria-label="Mute" tabindex="0"></button><a href="javascript:void(0);" class="mejs-volume-slider" aria-label="Volume Slider" aria-valuemin="0" aria-valuemax="100" role="slider" aria-orientation="vertical" aria-valuenow="80" aria-valuetext="80%"><span class="mejs-offscreen">Use Up/Down Arrow keys to increase or decrease volume.</span><div class="mejs-volume-total"><div class="mejs-volume-current" style="bottom: 0px; height: 80%;"></div><div class="mejs-volume-handle" style="bottom: 80%; margin-bottom: -3px;"></div></div></a></div><div class="mejs-button mejs-fullscreen-button"><button type="button" aria-controls="mep_1" title="Fullscreen" aria-label="Fullscreen" tabindex="0"></button></div></div></div></div></div>', $string);


	$pattern = '/\[caption/i';
	$string = preg_replace($pattern, '<shortcaption', $string);
	$pattern = '/\[\/caption\]/i';
	$string = preg_replace($pattern, '</shortcaption>', $string);


	$string = str_replace(" ]", ">", $string);
	$string = str_replace("\"]", "\">", $string);

	return $string;
}

function wl_register(){
	$params = $_GET;
	$data = wp_create_user($params['username'], $params['user_pass'], $params['email'] );
	$response = array();
	if(is_wp_error($data)){
		$response["status"]= false;
		$response["success"]= false;
		$response["message"]= "Register Failuer";
	}
	else{
		$user = get_user_by('id', $data);
		$response["status"]= true;
		$response["success"]= true;
		$response["statusCode"]= 200;
		$response["code"]= "jwt_auth_valid_credential";
		$response["message"]= "Credential is valid";
		$response["token"]= $params['device_token'];
		$response["user"]=$user->data;
	}

	return $response;
}

function wl_login()
{
	$params = $_REQUEST;
	$credentials = array();
	$token = $params['device_token'];
	$credentials['user_login']=$params['username'];
	$credentials['user_password']=$params['password'];
	$response = array();
	$data = wp_signon($credentials);
	if(is_wp_error($data)){
		$response["status"]= false;
		$response["success"]= false;
		$response["message"]= "Credential is not valid";
	}
	else{
		$response["status"]= true;
		$response["success"]= true;
		$response["statusCode"]= 200;
		$response["code"]= "jwt_auth_valid_credential";
		$response["message"]= "Credential is valid";
		$response["token"]= $token;
		$response["user"]['id']	=	(int)$data->data->ID;
		$response["user"]['username']	=	$data->data->user_login;
		$response["user"]['nicename']	=	$data->data->user_nicename;
		$response["user"]['email']	=	$data->data->user_email;
		$response["user"]['registered']	=	$data->data->user_registered;
		$response["user"]['displayname']	=	$data->data->display_name;
	}
	return $response;
}



function wl_post()
{
	// $args = [
	// 	'name' => $slug['slug'],
	// 	'post_type' => 'post'
	// ];
	$param = $_GET;
	$post = get_post($param['id']);

	$options_date_style = get_option('appbear-settings')['time_format'];

	$comments_args = array(
		'post_id' => $post->ID,
	);
	$new_comments = array();
	$comments = get_comments($comments_args);
	$i = 0;
	foreach ($comments as $comment) {
		if ($comment->comment_parent == 0) {
			$new_comments[$i] = $comment;
			$new_comments[$i]->author_avatar = get_avatar_url(get_the_author_meta($comment->user_id));

			// echo $comment->comment_ID;
			// echo "<br>";
			$comments_args = array(
				'post_id' => $post->ID,
				'parent' => $comment->comment_ID
			);
			$child_comments = get_comments($comments_args);
			$replies = array();
			$s = 0;
			// print_r($child_comments);
			foreach ($child_comments as $comment) {
				$replies[$s] = $comment;
				$replies[$s]->author_avatar = get_avatar_url(get_the_author_meta($comment->user_id));
				$s++;
			}
			$new_comments[$i]->replies = $replies;
			$i++;
		}
	}
	// exit;
	$data['id'] = $post->ID;
	$data['title'] = $post->post_title;

	$data['post'] = appbear_post_format($post->ID);
	switch (appbear_post_format($post->ID)) {
		case 'gallery':
			$data['gallery'] = appbear_post_gallery($post->ID);
			break;
		case 'video':
			$data['video'] = appbear_post_video($post->ID);
			break;
	}
	// $data['content'] =  html_styling( '<div>'. mobile_kses_stip( $post->post_content ) . '</div>');
	$data['content'] =  shortcodes_parsing($post->post_content);
	$data['content'] = $data['content'];
	// exit();
	// $data['content'] =  '<ul class="tie_list checklist"><li>test</li><li> test 2</li><li> test 2</li><li> test 2</li><li> test 2</li></ul>';

	$data['slug'] = $post->post_name;
	if (get_the_post_thumbnail_url($post->ID, 'thumbnail') == false) {
		$data['thumbnail'] = '';
		$data['featured_image']['thumbnail'] = '';
		$data['featured_image']['medium'] = '';
		$data['featured_image']['large'] = '';
	} else {
		$response = wp_remote_get(get_the_post_thumbnail_url($post->ID, 'thumbnail'));
		if($response['response']['code']==200)
			$data['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
		else
			$data['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'full');
		$response = wp_remote_get(get_the_post_thumbnail_url($post->ID, 'thumbnail'));
		if($response['response']['code']==200)
			$data['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
		else
			$data['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'full');
		$response = wp_remote_get(get_the_post_thumbnail_url($post->ID, 'medium'));
		if($response['response']['code']==200)
			$data['featured_image']['medium'] = get_the_post_thumbnail_url($post->ID, 'medium');
		else
			$data['featured_image']['medium'] = get_the_post_thumbnail_url($post->ID, 'full');
		$response = wp_remote_get(get_the_post_thumbnail_url($post->ID, 'large'));
		if($response['response']['code']==200)
			$data['featured_image']['large'] = get_the_post_thumbnail_url($post->ID, 'large');
		else
			$data['featured_image']['large'] = get_the_post_thumbnail_url($post->ID, 'full');
	}
	$data['read_time'] = "2 minutes";
	$data['author'] = get_the_author_meta('nicename', $post->post_author);
	if ($options_date_style == 'traditional')
		$data['date']  =  get_the_date('', $post->ID);
	elseif ($options_date_style == 'modern')
		$data['date']  =  human_time_diff(strtotime(get_the_date('', $post->ID))) . ' ago';
	$data['tags'] = ((get_the_tags($post->ID) == false) ? array() : get_the_tags($post->ID));
	$data['comments'] = $new_comments;
	$data['category'] = get_the_category($post->ID);
	$data['related_posts'] = array();
	$data['share'] = get_permalink($post->ID);

	return array('post' => $data);
}



function wl_comments()
{
	$param = $_GET;


	$options_date_style = get_option('appbear-settings')['time_format'];

	$comments_args = array(
		'post_id' => $param['id'],
		'offset' => (--$param['page']) * $param['count'],
		'number' => $param['count'],
	);
	$new_comments = array();
	$comments = get_comments($comments_args);
	$i = 0;
	foreach ($comments as $comment) {
		if ($comment->comment_parent == 0) {
			$new_comments[$i] = $comment;
			$new_comments[$i]->author_avatar = get_avatar_url(get_the_author_meta($comment->user_id));

			if ($options_date_style == 'traditional')
				$new_comments[$i]->comment_date  =  get_comment_date('', $comment->comment_ID);
			elseif ($options_date_style == 'modern')
				$new_comments[$i]->comment_date  =  human_time_diff(strtotime(get_comment_date('', $comment->comment_ID))) . ' ago';

			$comments_args = array(
				'post_id' =>  $param['id'],
				'parent' => $comment->comment_ID
			);
			$child_comments = get_comments($comments_args);
			$replies = array();
			$s = 0;
			foreach ($child_comments as $comment) {
				$replies[$s] = $comment;
				$replies[$s]->author_avatar = get_avatar_url(get_the_author_meta($comment->user_id));

				if ($options_date_style == 'traditional')
					$replies[$s]->comment_date  =  get_comment_date('', $comment->comment_ID);
				elseif ($options_date_style == 'modern')
					$replies[$s]->comment_date  =  human_time_diff(strtotime(get_comment_date('', $comment->comment_ID))) . ' ago';
				$s++;
			}
			$new_comments[$i]->replies = $replies;
			$i++;
		}
	}
	// $data['comments'] = $new_comments;
	return $new_comments;
}

function wl_categories()
{
	$categories = get_categories(array(
		'orderby' => 'name',
		'order'   => 'ASC'
	));

	$data = array();
	// The Loop
	$i = 0;
	$data['status'] = 'ok';
	foreach ($categories as $category) {
		$data['categories'][$i] 				= $category;
		$data['categories'][$i]->url = "wp-json/wl/v1/posts?category_id=" . $category->term_id;
		$data['categories'][$i]->image_url = get_term_meta( $category->term_id,"cat_image",true) ;
		$i++;
	}
	/* Restore original Post Data */
	wp_reset_postdata();

	return $data;
}




function wl_tabs()
{

	$data['status'] = 'ok';
	$data['tabs'][0]['title'] = "Top news";
	$data['tabs'][0]['json'] = "wp-json/wl/v1/posts?page=1&count=3&category_id=16";
	$data['tabs'][0]['post_type'] = "post";
	$data['tabs'][1]['title'] = "World";
	$data['tabs'][1]['json'] = "wp-json/wl/v1/posts?page=1&count=3&category_id=17";
	$data['tabs'][1]['post_type'] = "post";
	$data['tabs'][2]['title'] = "Fashion";
	$data['tabs'][2]['json'] = "wp-json/wl/v1/posts?page=1&count=3&category_id=19";
	$data['tabs'][2]['post_type'] = "post";
	$data['tabs'][3]['title'] = "Videos";
	$data['tabs'][3]['json'] = "wp-json/wl/v1/posts?page=1&count=3&category_id=21";
	$data['tabs'][3]['post_type'] = "post";
	$data['tabs'][4]['title'] = "Sports";
	$data['tabs'][4]['json'] = "wp-json/wl/v1/posts?page=1&count=3&category_id=11";
	$data['tabs'][4]['post_type'] = "post";
	$data['tabs'][5]['title'] = "Tech";
	$data['tabs'][5]['json'] = "wp-json/wl/v1/posts?page=1&count=3&category_id=16";
	$data['tabs'][5]['post_type'] = "post";
	$data['tabs'][6]['title'] = "Local";
	$data['tabs'][6]['json'] = "wp-json/wl/v1/posts?page=1&count=3&category_id=17";
	$data['tabs'][6]['post_type'] = "post";

	return $data;
}





function wl_page()
{
	$param = $_GET;
	$page = get_post($param['id']);
	$data['status'] 				= true;
	$data['post']['id'] = $page->ID;
	$data['post']['title'] = $page->post_title;

	// $data['post']['content'] =  html_styling('<div>' . mobile_kses_stip($page->post_content) . '</div>');
	$data['post']['content'] =  shortcodes_parsing($page->post_content);
	// $data['post']['content'] =  $page->post_content;

	$data['post']['slug'] = $page->post_name;

	return $data;
}





function wl_add_comment()
{
	$param = $_GET;
	$data = array();
	if (isset($param['post_id']))
		$data['comment_post_ID'] = $param['post_id'];
	if (isset($param['comment_content']))
		$data['comment_content'] = $param['comment_content'];
	if (isset($param['comment_author']))
		$data['comment_author'] = $param['comment_author'];
	if (isset($param['comment_parent']))
		$data['comment_parent'] = $param['comment_parent'];
	if (isset($param['comment_author_email']))
		$data['comment_author_email'] = $param['comment_author_email'];


	$result = wp_insert_comment($data);
	if ($result > 0) {
		$respones = array();
		$response['status'] = true;
		$response['message'] = "Comment added successfuly";
		return $response;
	} else {
		$respones = array();
		$response['status'] = fasle;
		$response['message'] = "failuer";
		return $response;
	}
}




function wl_get_version()
{
	$param = $_GET;
	$response = array();
	$response['version'] = get_option('appbear_version');

	return $response;
}




function wl_contact_us()
{
	$param = $_GET;
	$data = array();
	$data['name'] = $param['name'];
	$data['email'] = $param['email'];
	$data['message'] = $param['message'];
	$to = get_option('appbear-settings')['local-settingspage-contactus'];
	$subject = 'Contact Us Message';
	$logo = "<h1 style='line-height: inherit; display: block; height: auto; width: 100%; border: 0; text-align:center' >" . get_bloginfo('name') . "</h1>";
	if (isset(get_option('appbear-settings')['logo'])) {
		if (isset(get_option('appbear-settings')['thememode']) && get_option('appbear-settings')['thememode'] == "themeMode_light" && isset(get_option('appbear-settings')['logo-light'])) {
			$logo = "<img src='" . str_replace(" ", "", get_option('appbear-settings')['logo-light']) . "' alt='' style='line-height: inherit; display: block; height: auto; width: 100%; border: 0; max-width: 85px;' width='85' />";
		} elseif (isset(get_option('appbear-settings')['logo-dark'])) {
			$logo = "<img src='" . str_replace(" ", "", get_option('appbear-settings')['logo-dark']) . "' alt='' style='line-height: inherit; display: block; height: auto; width: 100%; border: 0; max-width: 85px;' width='85' />";
		}
	}

	$body = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><!--[if IE]><html xmlns='http://www.w3.org/1999/xhtml' class='ie'><![endif]--><!--[if !IE]><!--><html style='line-height: inherit; margin: 0; padding: 0;' xmlns='http://www.w3.org/1999/xhtml'><!--<![endif]--><head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<title></title>
	<!--[if !mso]><!--><meta http-equiv='X-UA-Compatible' content='IE=edge'><!--<![endif]-->
	<meta name='viewport' content='width=device-width'><style type='text/css'>
	@media only screen and (min-width: 620px) {
		.wrapper {
			min-width: 600px !important;
		}
		.wrapper h1 {  }
		.wrapper h1 {
			font-size: 26px !important;
			line-height: 34px !important;
		}
		.wrapper h2 {  }
		.wrapper h3 {  }
		.column {  }
		.wrapper .size-8 {
			font-size: 8px !important;
			line-height: 14px !important;
		}
		.wrapper .size-9 {
			font-size: 9px !important;
			line-height: 16px !important;
		}
		.wrapper .size-10 {
			font-size: 10px !important;
			line-height: 18px !important;
		}
		.wrapper .size-11 {
			font-size: 11px !important;
			line-height: 19px !important;
		}
		.wrapper .size-12 {
			font-size: 12px !important;
			line-height: 19px !important;
		}
		.wrapper .size-13 {
			font-size: 13px !important;
			line-height: 21px !important;
		}
		.wrapper .size-14 {
			font-size: 14px !important;
			line-height: 21px !important;
		}
		.wrapper .size-15 {
			font-size: 15px !important;
			line-height: 23px !important;
		}
		.wrapper .size-16 {
			font-size: 16px !important;
			line-height: 24px !important;
		}
		.wrapper .size-17 {
			font-size: 17px !important;
			line-height: 26px
			!important;
		}
		.wrapper .size-18 {
			font-size: 18px !important;
			line-height: 26px !important;
		}
		.wrapper .size-20 {
			font-size: 20px !important;
			line-height: 28px !important;
		}
		.wrapper .size-22 {
			font-size: 22px !important;
			line-height: 31px !important;
		}
		.wrapper .size-24 {
			font-size: 24px !important;
			line-height: 32px !important;
		}
		.wrapper .size-26 {
			font-size: 26px !important;
			line-height: 34px !important;
		}
		.wrapper .size-28 {
			font-size: 28px !important;
			line-height: 36px !important;
		}
		.wrapper .size-30 {
			font-size: 30px !important;
			line-height: 38px !important;
		}
		.wrapper .size-32 {
			font-size: 32px !important;
			line-height: 40px !important;
		}
		.wrapper .size-34 {
			font-size: 34px !important;
			line-height: 43px !important;
		}
		.wrapper .size-36 {
			font-size: 36px !important;
			line-height: 43px !important;
		}
		.wrapper .size-40 {
			font-size: 40px !important;
			line-height: 47px !important;
		}
		.wrapper .size-44 {
			font-size: 44px !important;
			line-height: 50px !important;
		}
		.wrapper
		.size-48 {
			font-size: 48px !important;
			line-height: 54px !important;
		}
		.wrapper .size-56 {
			font-size: 56px !important;
			line-height: 60px !important;
		}
		.wrapper .size-64 {
			font-size: 64px !important;
			line-height: 63px !important;
		}
	}
	</style>
	<meta name='x-apple-disable-message-reformatting'>
	<style type='text/css'>
	@media only screen and (min-width: 620px) {
		.column,
		.gutter {
			display: table-cell;
			Float: none !important;
			vertical-align: top;
		}
		div.preheader,
		.email-footer {
			max-width: 560px !important;
			width: 560px !important;
		}
		.snippet,
		.webversion {
			width: 280px !important;
		}
		div.header,
		.layout,
		.one-col .column {
			max-width: 600px !important;
			width: 600px !important;
		}
		.fixed-width.has-border,
		.fixed-width.x_has-border,
		.has-gutter.has-border,
		.has-gutter.x_has-border {
			max-width: 602px !important;
			width: 602px !important;
		}
		.two-col .column {
			max-width: 300px !important;
			width: 300px !important;
		}
		.three-col .column,
		.column.narrow,
		.column.x_narrow {
			max-width: 200px !important;
			width: 200px !important;
		}
		.column.wide,
		.column.x_wide {
			width: 400px !important;
		}
		.two-col.has-gutter .column,
		.two-col.x_has-gutter .column {
			max-width: 290px !important;
			width: 290px !important;
		}
		.three-col.has-gutter .column,
		.three-col.x_has-gutter .column,
		.has-gutter .narrow {
			max-width: 188px !important;
			width: 188px !important;
		}
		.has-gutter .wide {
			max-width: 394px !important;
			width: 394px !important;
		}
		.two-col.has-gutter.has-border .column,
		.two-col.x_has-gutter.x_has-border .column {
			max-width: 292px !important;
			width: 292px !important;
		}
		.three-col.has-gutter.has-border .column,
		.three-col.x_has-gutter.x_has-border .column,
		.has-gutter.has-border .narrow,
		.has-gutter.x_has-border .narrow {
			max-width: 190px !important;
			width: 190px !important;
		}
		.has-gutter.has-border .wide,
		.has-gutter.x_has-border .wide {
			max-width: 396px !important;
			width: 396px !important;
		}
	}
	</style>

	<!--[if !mso]><!--><link href='https://fonts.googleapis.com/css?family=PT+Serif:400,700,400italic,700italic|Ubuntu:400,700,400italic,700italic' rel='stylesheet' type='text/css'><!--<![endif]--><meta name='robots' content='noindex,nofollow'>
	<meta property='og:title' content='My First Campaign'>
	</head>
	<!--[if mso]>
	<body class='mso'>
	<![endif]-->
	<!--[if !mso]><!-->
	<body class='full-padding' style='line-height: inherit; margin: 0; padding: 0; -webkit-text-size-adjust: 100%;'>
	<!--<![endif]-->
	<table class='wrapper' style='line-height: inherit; border-collapse: collapse; table-layout: fixed; min-width: 320px; width: 100%; background-color: #F6F6F6;' cellpadding='0' cellspacing='0' role='presentation' width='100%' bgcolor='#ededf1'><tbody style='line-height: inherit;'><tr style='line-height: inherit;'><td style='line-height: inherit;'>
	<div role='banner' style='line-height: inherit;'>
	<div class='preheader' style='line-height: inherit; transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out; Margin: 0 auto; min-width: 280px; max-width: 360px; -fallback-width: 90%; width: calc(100% - 60px);'>
	<div style='line-height: inherit; border-collapse: collapse; display: table; width: 100%;'>
	<!--[if (mso)|(IE)]><table align='center' class='preheader' cellpadding='0' cellspacing='0' role='presentation'><tr><td style='width: 280px' valign='top'><![endif]-->
	<div class='snippet' style='display: table-cell; font-size: 12px; line-height: 19px; max-width: 280px; min-width: 140px; padding: 10px 0 5px 0; color: #7c7e7f; font-family: Ubuntu,sans-serif; Float: none; width: 50%;'>

	</div>
	<!--[if (mso)|(IE)]></td><td style='width: 280px' valign='top'><![endif]-->
	<div class='webversion' style='display: table-cell; font-size: 12px; line-height: 19px; max-width: 280px; min-width: 139px; padding: 10px 0 5px 0; text-align: right; color: #7c7e7f; font-family: Ubuntu,sans-serif; Float: none; width: 50%;'>

	</div>
	<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
	</div>
	</div>
	<div class='header' style='line-height: inherit; transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out; Margin: 0 auto; min-width: 320px; max-width: 400px; -fallback-width: 95%; width: calc(100% - 20px);' id='emb-email-header-container'>
	<!--[if (mso)|(IE)]><table align='center' class='header' cellpadding='0' cellspacing='0' role='presentation'><tr><td style='width: 600px'><![endif]-->
	<div class='logo emb-logo-margin-box' style='font-size: 26px; line-height: 32px; Margin-top: 6px; Margin-bottom: 20px; color: #c3ced9; font-family: Roboto,Tahoma,sans-serif; Margin-left: 20px; Margin-right: 20px;' align='center'>
	<div class='logo-center' align='center' id='emb-email-header' style='line-height: inherit;'>" . $logo . "</div>
	</div>
	<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
	</div>
	</div>
	<div style='line-height: inherit;'>
	<div class='layout one-col fixed-width stack' style='line-height: inherit; transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out; Margin: 0 auto; min-width: 320px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; max-width: 400px; -fallback-width: 95%; width: calc(100% - 20px);'>
	<div class='layout__inner' style='line-height: inherit; border-collapse: collapse; display: table; width: 100%; background-color: #ffffff;'>
	<!--[if (mso)|(IE)]><table align='center' cellpadding='0' cellspacing='0' role='presentation'><tr class='layout-fixed-width' style='background-color: #ffffff;'><td style='width: 600px' class='w560'><![endif]-->
	<div class='column' style='transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out; text-align: left; color: #7c7e7f; font-size: 14px; line-height: 21px; font-family: PT Serif,Georgia,serif; max-width: 400px; width: 100%;'>

	<div style='line-height: inherit; Margin-left: 20px; Margin-right: 20px; Margin-top: 24px;'>
	<div style='mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;'>&nbsp;</div>
	</div>

	<div style='line-height: inherit; Margin-left: 20px; Margin-right: 20px;'>
	<div style='line-height: inherit; mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;'>
	<h3 style='Margin-top: 0; Margin-bottom: 0; font-style: normal; font-weight: normal; color: #788991; font-size: 16px; line-height: 24px; text-align: center;'>AN ANNOUNCEMENT:</h3><h1 style='Margin-top: 12px; Margin-bottom: 0; font-style: normal; font-weight: normal; color: #3e4751; font-size: 22px; line-height: 31px; font-family: Ubuntu,sans-serif; text-align: center;'>Your have a new contact us message</h1><p style='line-height: inherit; Margin-top: 20px; Margin-bottom: 20px;'><span style='line-height: inherit;'>Hello,<br/><br />You got a new contact us message, here's the message details:</span><table><tr><td width='80px'>Name</td><td>" . $data['name'] . "</td></tr><tr><td>Email</td><td>" . $data['email'] . "</td></tr><tr><td>Message</td><td>" . $data['message'] . "</td></tr></table></p>
	</div>
	</div>

	<div style='line-height: inherit; Margin-left: 20px; Margin-right: 20px;'>
	<div class='divider' style='display: block; font-size: 2px; line-height: 2px; Margin-left: auto; Margin-right: auto; width: 40px; background-color: #b4b4c4; Margin-bottom: 20px;'>&nbsp;</div>
	</div>

	<div style='line-height: inherit; Margin-left: 20px; Margin-right: 20px;'>
	<div style='mso-line-height-rule: exactly; line-height: 5px; font-size: 1px;'>&nbsp;</div>
	</div>



	<div style='line-height: inherit; Margin-left: 20px; Margin-right: 20px; Margin-bottom: 24px;'>
	<div style='line-height: inherit; mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;'>
	<p style='line-height: inherit; Margin-top: 0; Margin-bottom: 0;'>Kind regards,</p>
	</div>
	</div>

	</div>
	<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
	</div>
	</div>

	<div style='mso-line-height-rule: exactly; line-height: 20px; font-size: 20px;'>&nbsp;</div>


	<div style='line-height: 40px; font-size: 40px;'>&nbsp;</div>
	</div></td></tr></tbody></table>

	</body></html>";
	// echo $body;
	// exit;
	$headers = array('Content-Type: text/html; charset=UTF-8');
	$result = wp_mail($to, $subject, $body, $headers);
	$response = array();
	if ($result == true) {
		$respone['status'] = true;
		$respone['message'] = 'Your message was sent successfuly';
		return $respone;
	} else {
		$respone['status'] = false;
		$respone['error'] = 'There are missing fields';
		return $respone;
	}
}





function wl_language()
{
	$param = $_GET;
	if ($param['language'] == "en") {
		$response = '{
			"credit": "Credit",
			"skip": "SKIP",
			"next": "Next",
			"done": "Done",
			"contactUs": "Contact Us",
			"contactUsTitle": "Let\'s talk",
			"contactUsSubTitle": "Sahifa is your news entertainment music fashion website. We provide you with the latest breaking news and videos straight from entertainment industry world.",
			"yourName": "Your Name",
			"yourEmail": "Your Email",
			"yourMessage": "Your Message",
			"send": "Send",
			"settings": "Settings",
			"aboutUs": "About Us",
			"layout": "Layout",
			"textSize": "Text Size",
			"aA": "Aa",
			"pullScreen": "Pull the screen",
			"pullScreenSubtitle": "Swipe left or right to move between articles",
			"darkMode": "Dark Mode",
			"rateApp": "Rate this app",
			"shareApp": "Share the app",
			"privacyPolicy": "Privacy policy",
			"termsAndConditions": "Terms and Conditions",
			"poweredBy": "Powered by",
			"logout": "Logout",
			"dissmis": "Dissmis",
			"notifications": "Notifications",
			"faq": "Faq",
			"signUpLogin": "Sign up / Login",
			"followers": "Followers",
			"email": "Email",
			"password": "Password",
			"login": "Login",
			"forgotPassword": "Forgot Password?",
			"needAnAccount": "Need a account?",
			"createAnAccount": " Create an account",
			"invalid": "invalid data",
			"username": "username",
			"register": "register",
			"asked": "Asked at",
			"answerPlus": "+ Answer",
			"answers": "Answers",
			"check": "check terms and privacy policy",
			"publish": "Publish Your Question",
			"exploreCategories": "Explore Categories",
			"relatedPosts": "RELATED POSTS",
			"leaveComment": "LEAVE A COMMENT",
			"commentsCount": "COMMENTS",
			"reply": "Reply",
			"replyTo": "Reply to",
			"By": "By",
			"cancel": "Cancel",
			"questionTitle": "Question Title",
			"questionDesc": "Please choose an appropriate title for the question so it can be answered easier.",
			"categoryTitle": "Category",
			"categoryDesc": "Please choose the appropriate section so question can be searched easier.",
			"tagsTitle": "Tags",
			"tagsDesc": "Please choose the appropriate section so question can be searched easier.",
			"pollTitle": "This question is a poll?",
			"pollDesc": "If you want to be doing a poll click here.",
			"imagePoll": "Image poll?",
			"addAnswer": "Add Answer",
			"answer": "Answer",
			"addMoreAnswers": "Add More +",
			"featuredImage": "Featured image",
			"detailsTitle": "Details",
			"detailsDesc": "Type the description thoroughly and in details.",
			"videoTitle": "Video ID",
			"youtube": "YouTube",
			"vimeo": "Vimeo",
			"daily": "DailyMotion",
			"facebook": "Facebook",
			"videoDescYouTube": "Put here the video id : https://www.youtube.com/watch?v=sdUUx5FdySs Ex: \'sdUUx5FdySs\'.",
			"videoDescVimeo": "Put here the video id : https://www.vimeo.com/watch?v=sdUUx5FdySs Ex: \'sdUUx5FdySs\'.",
			"videoDescDaily": "Put here the video id : https://www.dailymotion.com/watch?v=sdUUx5FdySs Ex: \'sdUUx5FdySs\'.",
			"videoDescFacebook": "Put here the video id : https://www.facebook.com/watch?v=sdUUx5FdySs Ex: \'sdUUx5FdySs\'.",
			"anonBox": "Ask Anonymously",
			"anonBox2": "Anonymous asks",
			"videoBox": "Add a video to describe the problem better.",
			"privateBox": "This question is a private question?",
			"notificationsBox": "Get notification by email when someone answers this question.",
			"termsBox1": "By asking your question, you agreed to the ",
			"addQuestion": "Add question",
			"termsBox2": "Terms of Service ",
			"termsBox3": "and ",
			"termsBox4": "Privacy Policy",
			"answered": "Answered",
			"poll": "Poll",
			"submit": "Submit",
			"result": "result",
			"leaveAnAnswer": "LEAVE AN ANSWER",
			"bestAnswer": "Best Answer",
			"answerOn": "Answer On",
			"comment": "Comment",
			"name": "Name",
			"postComment": "Post Comment",
			"postReply": "Post Reply",
			"forgot1": "Forgot your Password?",
			"forgot2": "Enter the email address associated with your account",
			"resetPassword": "Reset Password",
			"answerDesc": "Type your answer thoroughly and in details.",
			"attachments": "Attachments",
			"anonAnswer": "Answer Anonymously",
			"anonAnswer2": "Anonymously answer",
			"follow": "Follow",
			"unFollow": "Unfollow",
			"ask": "Ask",
			"following": "Following",
			"selectCat1": "Select ",
			"selectCat2": "Categories",
			"selectCat3": " which you like to follow:",
			"people": "People",
			"lets": "Let\'s go",
			"points": "Points",
			"questions": "Questions",
			"baseUrl":"Your website url",
			"baseUrlTitle":"Your website title",
			"baseUrlDesc":"Your website description",
			"emptyBaseUrl":"Empty url",
			"alreadyBaseUrl":"It\'s already using that url",
			"loadingUpdates":"Receiving updates from server..."
		}';
	}
	echo $response;
}



function wl_options()
{
	return get_option('appbear-settings');
}



function wl_translations()
{
	return get_option('appbear-language');
}




function wl_translations_ar()
{
	$respone = '{
		"back": "الرجوع",
		"skip": "التخطي",
		"done": "تم",
		"contactUs": "تواصل معنا",
		"loadingUpdates": "الحصول علي التحديثات من الخادم...",
		"baseUrl": "الرابط الاساسي",
		"baseUrlTitle": "تغيير الرابط الاساسي",
		"baseUrlDesc": "قم بتغيير عنوان الرابط الذي تأتي منه البيانات.",
		"emptyBaseUrl": "لا يجب ان يكون الرابط خاليا",
		"alreadyBaseUrl": "هذا الراب موجود بالفعل",
		"contactUsTitle": "هيا بنا نتحدث",
		"contactUsSubTitle": "AppBear هو موقع ويب للأزياء والموسيقى الإخبارية والترفيهية. نقدم لك أحدث الأخبار ومقاطع الفيديو مباشرة من عالم صناعة الترفيه.",
		"yourName": "اسمك",
		"yourEmail": "بريدك الالكتروني",
		"yourMessage": "رسالتك",
		"send": "إرسال",
		"settings": "الاعدادات",
		"aboutUs": "عنا",
		"layout": "الإطار",
		"textSize": "حجم الخط",
		"aA": "Aa",
		"darkMode": "الوضع المظلم",
		"rateApp": "قيم هذا التطبيق",
		"shareApp": "شارك هذا التطبيق",
		"privacyPolicy": "سياسة الخصوصية",
		"termsAndConditions": "الشروط و الاحكام",
		"poweredBy": "مشغل بواسطة",
		"logout": "الخروج",
		"relatedPosts": "المنشورات ذات الصلة",
		"leaveComment": "اترك تعليق",
		"commentsCount": "التعلقيات",
		"reply": "الرد",
		"replyTo": "الرد علي",
		"By": "بواسطة",
		"cancel": "الغاء",
		"submit": "إرسال",
		"comment": "تعليق",
		"name": "اسم",
		"postComment": "تعليق علي منشور",
		"postReply": "رد علي منشور",
		"lets": "هيا بنا",
		"noFav": "لا مختارات حتي الان",
		"noPosts": "لا توجد منشورات",
		"mustNotBeEmpty": "لا يجب ان يكون خاليا",
		"loadingMore": "الحصول علي المزيد...",
		"loadingMoreQuestions": "حمل المزيد",
		"someThingWentWrong": "هناك خطأ ما",
		"search": "ابحث",
		"noMore": "لا توجد عناصر اخري",
		"removedToFav": "تمت إزالته من المفضلة",
		"addedToFav": "أضيف إلي المفضلة",
		"typeToSearch": "اكتب للبحث",
		"version": "النسخة ",
		"yourVersionUpToDate": "إصدار التطبيق الخاص بك محدث ",
		"yourVersionNotUpToDate": "قم بتحميل أحدث إصدار ",
		"upgradeHint": "استمر في الضغط لتنشيط وضع التطوير",
		"aboutApp": "عن التطبيق",
		"tapsLeft": "Taps left",
		"devModeActive": "وضع التطوير نشط",
		"noResults": "لا نتيجة",
		"noSections": "الرجاء إضافة أقسام الصحفة الرئيسية من لوحة الإدارة",
		"noMainPage": "يجب إضافة صفحة رئيسية واحدة على الأقل من لوحة الإدارة",
		"noBoards": "No boarding slides",
		"errorPageTitle": "حدث خطأ ما",
		"retry": "إعادة المحاولة",
		"noInternet": "لا يوجد اتصال بالإنترنت",
		"checkInternet": "يرجى التحقق من اتصالك بالإنترنت وحاول مرة أخرى",
		"noComments": "لا تعليقات",
		"seeMore": "اظهار الكل",
		"confirmDemoTitle": "حدد العرض",
		"confirmDemoMessage": "هل تريد تحديد هذا العرض؟",
		"chooseYourDemo": "اختر العرض الخاص بك",
		"confirmResetTitle": "إعادة تعيين العرض",
		"confirmResetMessage": "تأكيد إعادة الرسالة",
		"yes": "نعم",
		"reset": "أعادة",
		"customDemo": "عرض مخصص",
		"customDemoTitle": "عرض مخصص",
		"customDemoBody": "قم بتغيير عنوان الرابط الذي تأتي منه البيانات ",
		"confirmCustomDemoTitle": "اختار العرض المخصص",
		"confirmCustomDemoMessage": "هل تريد تحديد عرض توضيحي مخصص؟",
		"getOur": "يرجى الحصول على",
		"appBear": "AppBear",
		"plugin": "plugin"
	}';
	echo $respone;
}




//
// function wl_save_token()
// {
// 	global $wpdb;
// 	$default = array(
// 									 'token' => $_GET['token'],
// 									 'site_url' => get_bloginfo('url'),
// 								 );
// 	$item = shortcode_atts( $default, $_REQUEST );
//
// 	$wpdb->insert( 'wp_appbear_tokens', $item );
// }




function wl_renderSVG()
{


	$options_date_style = get_option('appbear_options')['styling'][get_option('appbear_options')['themeMode']];

	$color1 = '#' . $options_date_style['primary'];
	$color2 = '#' . $options_date_style['secondaryVariant'];

	header('Content-type: image/svg+xml');


	$param = $_GET;

	if ($param['type'] == 'loading') {
		echo '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="800px" height="800px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
		<circle cx="75" cy="50" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.9166666666666666s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.9166666666666666s"></animate>
		</circle><circle cx="71.65063509461098" cy="62.5" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.8333333333333334s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.8333333333333334s"></animate>
		</circle><circle cx="62.5" cy="71.65063509461096" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.75s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.75s"></animate>
		</circle><circle cx="50" cy="75" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.6666666666666666s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.6666666666666666s"></animate>
		</circle><circle cx="37.50000000000001" cy="71.65063509461098" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.5833333333333334s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.5833333333333334s"></animate>
		</circle><circle cx="28.34936490538903" cy="62.5" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.5s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.5s"></animate>
		</circle><circle cx="25" cy="50" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.4166666666666667s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.4166666666666667s"></animate>
		</circle><circle cx="28.34936490538903" cy="37.50000000000001" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.3333333333333333s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.3333333333333333s"></animate>
		</circle><circle cx="37.499999999999986" cy="28.349364905389038" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.25s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.25s"></animate>
		</circle><circle cx="49.99999999999999" cy="25" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.16666666666666666s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.16666666666666666s"></animate>
		</circle><circle cx="62.5" cy="28.349364905389034" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.08333333333333333s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.08333333333333333s"></animate>
		</circle><circle cx="71.65063509461096" cy="37.499999999999986" fill="' . $color2 . '" r="5">
		<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="0s"></animate>
		<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="0s"></animate>
		</circle>
		</svg>';
	} elseif ($param['type'] == 'settings') {
		echo '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="800px" height="800px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
		<g transform="translate(50 50)">  <g transform="translate(-19 -19) scale(0.6)"> <g>
		<animateTransform attributeName="transform" type="rotate" values="0;45" keyTimes="0;1" dur="1.7857142857142856s" begin="0s" repeatCount="indefinite"></animateTransform><path d="M31.359972760794346 21.46047782418268 L38.431040572659825 28.531545636048154 L28.531545636048154 38.431040572659825 L21.46047782418268 31.359972760794346 A38 38 0 0 1 7.0000000000000036 37.3496987939662 L7.0000000000000036 37.3496987939662 L7.000000000000004 47.3496987939662 L-6.999999999999999 47.3496987939662 L-7 37.3496987939662 A38 38 0 0 1 -21.46047782418268 31.35997276079435 L-21.46047782418268 31.35997276079435 L-28.531545636048154 38.431040572659825 L-38.43104057265982 28.531545636048158 L-31.359972760794346 21.460477824182682 A38 38 0 0 1 -37.3496987939662 7.000000000000007 L-37.3496987939662 7.000000000000007 L-47.3496987939662 7.000000000000008 L-47.3496987939662 -6.9999999999999964 L-37.3496987939662 -6.999999999999997 A38 38 0 0 1 -31.35997276079435 -21.460477824182675 L-31.35997276079435 -21.460477824182675 L-38.431040572659825 -28.531545636048147 L-28.53154563604818 -38.4310405726598 L-21.4604778241827 -31.35997276079433 A38 38 0 0 1 -6.999999999999992 -37.3496987939662 L-6.999999999999992 -37.3496987939662 L-6.999999999999994 -47.3496987939662 L6.999999999999977 -47.3496987939662 L6.999999999999979 -37.3496987939662 A38 38 0 0 1 21.460477824182686 -31.359972760794342 L21.460477824182686 -31.359972760794342 L28.531545636048158 -38.43104057265982 L38.4310405726598 -28.53154563604818 L31.35997276079433 -21.4604778241827 A38 38 0 0 1 37.3496987939662 -6.999999999999995 L37.3496987939662 -6.999999999999995 L47.3496987939662 -6.999999999999997 L47.349698793966205 6.999999999999973 L37.349698793966205 6.999999999999976 A38 38 0 0 1 31.359972760794346 21.460477824182686 M0 -23A23 23 0 1 0 0 23 A23 23 0 1 0 0 -23" fill="' . $color1 . '"></path></g></g> <g transform="translate(19 19) scale(0.6)"> <g>
		<animateTransform attributeName="transform" type="rotate" values="45;0" keyTimes="0;1" dur="1.7857142857142856s" begin="-0.8928571428571428s" repeatCount="indefinite"></animateTransform><path d="M-31.35997276079435 -21.460477824182675 L-38.431040572659825 -28.531545636048147 L-28.53154563604818 -38.4310405726598 L-21.4604778241827 -31.35997276079433 A38 38 0 0 1 -6.999999999999992 -37.3496987939662 L-6.999999999999992 -37.3496987939662 L-6.999999999999994 -47.3496987939662 L6.999999999999977 -47.3496987939662 L6.999999999999979 -37.3496987939662 A38 38 0 0 1 21.460477824182686 -31.359972760794342 L21.460477824182686 -31.359972760794342 L28.531545636048158 -38.43104057265982 L38.4310405726598 -28.53154563604818 L31.35997276079433 -21.4604778241827 A38 38 0 0 1 37.3496987939662 -6.999999999999995 L37.3496987939662 -6.999999999999995 L47.3496987939662 -6.999999999999997 L47.349698793966205 6.999999999999973 L37.349698793966205 6.999999999999976 A38 38 0 0 1 31.359972760794346 21.460477824182686 L31.359972760794346 21.460477824182686 L38.431040572659825 28.531545636048158 L28.53154563604818 38.4310405726598 L21.460477824182703 31.35997276079433 A38 38 0 0 1 6.9999999999999964 37.3496987939662 L6.9999999999999964 37.3496987939662 L6.999999999999995 47.3496987939662 L-7.000000000000009 47.3496987939662 L-7.000000000000007 37.3496987939662 A38 38 0 0 1 -21.46047782418263 31.359972760794385 L-21.46047782418263 31.359972760794385 L-28.531545636048097 38.43104057265987 L-38.431040572659796 28.531545636048186 L-31.35997276079433 21.460477824182703 A38 38 0 0 1 -37.34969879396619 7.000000000000032 L-37.34969879396619 7.000000000000032 L-47.34969879396619 7.0000000000000355 L-47.3496987939662 -7.000000000000002 L-37.3496987939662 -7.000000000000005 A38 38 0 0 1 -31.359972760794346 -21.46047782418268 M0 -23A23 23 0 1 0 0 23 A23 23 0 1 0 0 -23" fill="' . $color2 . '"></path></g></g></g>
		</svg>';
	}
}



function wl_renderFalre()
{


	$options_date_style = get_option('appbear_options')['styling'][get_option('appbear_options')['themeMode']];

	$color1 = '#' . $options_date_style['primary'];
	$color2 = '#' . $options_date_style['secondaryVariant'];

	// header('Content-type: image/svg+xml');


	$param = $_GET;

	if ($param['type'] == 'loading') {
		echo '{
			"version": 24,
			"artboards": [
			{
				"name": "Artboard",
				"translation": [
				-62.89067840576172,
				-261.20843505859375
				],
				"width": 48,
				"height": 48,
				"origin": [
				0,
				0
				],
				"clipContents": true,
				"color": [
				0,
				0,
				0,
				0
				],
				"nodes": [
				{
					"name": "Ellipse",
					"translation": [
					24,
					24
					],
					"rotation": 0,
					"scale": [
					1,
					1
					],
					"opacity": 1,
					"isCollapsed": false,
					"clips": [],
					"isVisible": true,
					"blendMode": 3,
					"drawOrder": 1,
					"transformAffectsStroke": true,
					"type": "shape"
					},
					{
						"name": "Ellipse Path",
						"parent": 0,
						"translation": [
						0,
						0
						],
						"rotation": 0,
						"scale": [
						1,
						1
						],
						"opacity": 1,
						"isCollapsed": false,
						"clips": [],
						"bones": [],
						"isVisible": true,
						"isClosed": true,
						"points": [
						{
							"pointType": 2,
							"translation": [
							0,
							-17.5
							],
							"in": [
							-9.664982795715332,
							-17.5
							],
							"out": [
							9.664982795715332,
							-17.5
							]
							},
							{
								"pointType": 2,
								"translation": [
								17.5,
								0
								],
								"in": [
								17.5,
								-9.664982795715332
								],
								"out": [
								17.5,
								9.664982795715332
								]
								},
								{
									"pointType": 2,
									"translation": [
									0,
									17.5
									],
									"in": [
									9.664982795715332,
									17.5
									],
									"out": [
									-9.664982795715332,
									17.5
									]
									},
									{
										"pointType": 2,
										"translation": [
										-17.5,
										0
										],
										"in": [
										-17.5,
										9.664982795715332
										],
										"out": [
										-17.5,
										-9.664982795715332
										]
									}
									],
									"type": "path"
									},
									{
										"name": "Color",
										"parent": 0,
										"opacity": 1,
										"color": [
										0.9215686321258545,
										0,
										0.24705882370471954,
										1
										],
										"width": 1.5,
										"cap": 1,
										"join": 0,
										"trim": 1,
										"start": 0,
										"end": -0.01,
										"offset": 0,
										"type": "colorStroke"
										},
										{
											"name": "Success",
											"translation": [
											15,
											24
											],
											"rotation": 0,
											"scale": [
											1,
											1
											],
											"opacity": 1,
											"isCollapsed": false,
											"clips": [],
											"isVisible": true,
											"blendMode": 3,
											"drawOrder": 2,
											"transformAffectsStroke": false,
											"type": "shape"
											},
											{
												"name": "Path",
												"parent": 3,
												"translation": [
												0,
												0
												],
												"rotation": 0,
												"scale": [
												1,
												1
												],
												"opacity": 1,
												"isCollapsed": false,
												"clips": [],
												"bones": [],
												"isVisible": true,
												"isClosed": false,
												"points": [
												{
													"pointType": 0,
													"translation": [
													0,
													0
													],
													"radius": 0
													},
													{
														"pointType": 0,
														"translation": [
														7.5,
														7.5
														],
														"radius": 0
														},
														{
															"pointType": 0,
															"translation": [
															19.5,
															-6
															],
															"radius": 0
														}
														],
														"type": "path"
														},
														{
															"name": "Path",
															"parent": 3,
															"translation": [
															0,
															0
															],
															"rotation": 0,
															"scale": [
															1,
															1
															],
															"opacity": 1,
															"isCollapsed": false,
															"clips": [],
															"bones": [],
															"isVisible": true,
															"isClosed": false,
															"points": [
															{
																"pointType": 0,
																"translation": [
																184,
																-34.201171875
																],
																"radius": 0
															}
															],
															"type": "path"
															},
															{
																"name": "Color",
																"parent": 3,
																"opacity": 1,
																"color": [
																0.9215686321258545,
																0,
																0.24705882370471954,
																1
																],
																"width": 1.5,
																"cap": 1,
																"join": 1,
																"trim": 1,
																"start": 0,
																"end": 1,
																"offset": 0,
																"type": "colorStroke"
																},
																{
																	"name": "X One",
																	"translation": [
																	7,
																	10.048828125
																	],
																	"rotation": 0,
																	"scale": [
																	1,
																	1
																	],
																	"opacity": 1,
																	"isCollapsed": false,
																	"clips": [],
																	"isVisible": true,
																	"blendMode": 3,
																	"drawOrder": 3,
																	"transformAffectsStroke": false,
																	"type": "shape"
																	},
																	{
																		"name": "Path",
																		"parent": 7,
																		"translation": [
																		9.5,
																		6.451171875
																		],
																		"rotation": 0,
																		"scale": [
																		1,
																		1
																		],
																		"opacity": 1,
																		"isCollapsed": false,
																		"clips": [],
																		"bones": [],
																		"isVisible": true,
																		"isClosed": false,
																		"points": [
																		{
																			"pointType": 0,
																			"translation": [
																			0,
																			0
																			],
																			"radius": 0
																			},
																			{
																				"pointType": 0,
																				"translation": [
																				15,
																				15
																				],
																				"radius": 0
																			}
																			],
																			"type": "path"
																			},
																			{
																				"name": "Color",
																				"parent": 7,
																				"opacity": 1,
																				"color": [
																				0.9215686321258545,
																				0,
																				0.24705882370471954,
																				1
																				],
																				"width": 1.5,
																				"cap": 1,
																				"join": 1,
																				"trim": 1,
																				"start": 0,
																				"end": 1,
																				"offset": 0,
																				"type": "colorStroke"
																				},
																				{
																					"name": "X Two",
																					"translation": [
																					16.5,
																					16.5
																					],
																					"rotation": 0,
																					"scale": [
																					1,
																					1
																					],
																					"opacity": 1,
																					"isCollapsed": false,
																					"clips": [],
																					"isVisible": true,
																					"blendMode": 3,
																					"drawOrder": 4,
																					"transformAffectsStroke": false,
																					"type": "shape"
																					},
																					{
																						"name": "Path",
																						"parent": 10,
																						"translation": [
																						0,
																						0
																						],
																						"rotation": 0,
																						"scale": [
																						1,
																						1
																						],
																						"opacity": 1,
																						"isCollapsed": false,
																						"clips": [],
																						"bones": [],
																						"isVisible": true,
																						"isClosed": false,
																						"points": [
																						{
																							"pointType": 0,
																							"translation": [
																							0,
																							15
																							],
																							"radius": 0
																							},
																							{
																								"pointType": 0,
																								"translation": [
																								15,
																								0
																								],
																								"radius": 0
																							}
																							],
																							"type": "path"
																							},
																							{
																								"name": "Color",
																								"parent": 10,
																								"opacity": 1,
																								"color": [
																								0.9215686321258545,
																								0,
																								0.24705882370471954,
																								1
																								],
																								"width": 1.5,
																								"cap": 1,
																								"join": 1,
																								"trim": 1,
																								"start": 0,
																								"end": 1,
																								"offset": 0,
																								"type": "colorStroke"
																							}
																							],
																							"animations": [
																							{
																								"name": "Loading 1",
																								"fps": 80,
																								"duration": 0.75,
																								"isLooping": true,
																								"keyed": [
																								{
																									"component": 0
																									},
																									{
																										"component": 2,
																										"strokeStart": [
																										[
																										{
																											"time": 0,
																											"interpolatorType": 2,
																											"cubicX1": 0.42,
																											"cubicY1": 0,
																											"cubicX2": 0.58,
																											"cubicY2": 1,
																											"value": 0
																											},
																											{
																												"time": 0.75,
																												"interpolatorType": 2,
																												"cubicX1": 0.42,
																												"cubicY1": 0,
																												"cubicX2": 0.58,
																												"cubicY2": 1,
																												"value": 0
																											}
																											]
																											],
																											"strokeEnd": [
																											[
																											{
																												"time": 0,
																												"interpolatorType": 2,
																												"cubicX1": 0.42,
																												"cubicY1": 0,
																												"cubicX2": 0.58,
																												"cubicY2": 1,
																												"value": 0
																												},
																												{
																													"time": 0.75,
																													"interpolatorType": 2,
																													"cubicX1": 0.42,
																													"cubicY1": 0,
																													"cubicX2": 0.58,
																													"cubicY2": 1,
																													"value": 1
																												}
																												]
																												],
																												"strokeOffset": [
																												[
																												{
																													"time": 0,
																													"interpolatorType": 1,
																													"value": 0
																													},
																													{
																														"time": 0.75,
																														"interpolatorType": 1,
																														"value": 0
																														},
																														{
																															"time": 6.5,
																															"interpolatorType": 1,
																															"value": 0
																														}
																														]
																														],
																														"strokeColor": [
																														[
																														{
																															"time": 0.6116599999950267,
																															"interpolatorType": 1,
																															"value": [
																															0.9725490212440491,
																															0.5490196347236633,
																															0,
																															1
																															]
																															},
																															{
																																"time": 0.75,
																																"interpolatorType": 1,
																																"value": [
																																0.572549045085907,
																																0.572549045085907,
																																0.572549045085907,
																																1
																																]
																															}
																															]
																															]
																															},
																															{
																																"component": 3
																																},
																																{
																																	"component": 6,
																																	"strokeOpacity": [
																																	[
																																	{
																																		"time": 0.75,
																																		"interpolatorType": 1,
																																		"value": 0
																																	}
																																	]
																																	],
																																	"strokeColor": [
																																	[
																																	{
																																		"time": 0.75,
																																		"interpolatorType": 1,
																																		"value": [
																																		0.3225489854812622,
																																		0.9215686321258545,
																																		0,
																																		1
																																		]
																																	}
																																	]
																																	]
																																	},
																																	{
																																		"component": 7
																																		},
																																		{
																																			"component": 9,
																																			"strokeOpacity": [
																																			[
																																			{
																																				"time": 0,
																																				"interpolatorType": 1,
																																				"value": 0
																																			}
																																			]
																																			]
																																			},
																																			{
																																				"component": 10
																																				},
																																				{
																																					"component": 12,
																																					"strokeOpacity": [
																																					[
																																					{
																																						"time": 0,
																																						"interpolatorType": 1,
																																						"value": 0
																																					}
																																					]
																																					]
																																				}
																																				],
																																				"animationStart": 0,
																																				"animationEnd": 6.5,
																																				"type": "animation"
																																				},
																																				{
																																					"name": "Loading 2",
																																					"fps": 60,
																																					"duration": 0.75,
																																					"isLooping": false,
																																					"keyed": [
																																					{
																																						"component": 0
																																						},
																																						{
																																							"component": 2,
																																							"strokeStart": [
																																							[
																																							{
																																								"time": 0,
																																								"interpolatorType": 2,
																																								"cubicX1": 0.42,
																																								"cubicY1": 0,
																																								"cubicX2": 0.58,
																																								"cubicY2": 1,
																																								"value": 0
																																								},
																																								{
																																									"time": 0.75,
																																									"interpolatorType": 2,
																																									"cubicX1": 0.42,
																																									"cubicY1": 0,
																																									"cubicX2": 0.58,
																																									"cubicY2": 1,
																																									"value": 0
																																								}
																																								]
																																								],
																																								"strokeEnd": [
																																								[
																																								{
																																									"time": 0,
																																									"interpolatorType": 2,
																																									"cubicX1": 0.42,
																																									"cubicY1": 0,
																																									"cubicX2": 0.58,
																																									"cubicY2": 1,
																																									"value": 0
																																									},
																																									{
																																										"time": 0.75,
																																										"interpolatorType": 2,
																																										"cubicX1": 0.42,
																																										"cubicY1": 0,
																																										"cubicX2": 0.58,
																																										"cubicY2": 1,
																																										"value": 1
																																									}
																																									]
																																									],
																																									"strokeOffset": [
																																									[
																																									{
																																										"time": 0,
																																										"interpolatorType": 1,
																																										"value": 0
																																										},
																																										{
																																											"time": 0.75,
																																											"interpolatorType": 1,
																																											"value": 0
																																											},
																																											{
																																												"time": 6.5,
																																												"interpolatorType": 1,
																																												"value": 0
																																											}
																																											]
																																											],
																																											"strokeColor": [
																																											[
																																											{
																																												"time": 0.75,
																																												"interpolatorType": 1,
																																												"value": [
																																												0.6000000238418579,
																																												1,
																																												0.6392157077789307,
																																												1
																																												]
																																											}
																																											]
																																											]
																																											},
																																											{
																																												"component": 3
																																												},
																																												{
																																													"component": 6,
																																													"strokeOpacity": [
																																													[
																																													{
																																														"time": 0.75,
																																														"interpolatorType": 1,
																																														"value": 0
																																													}
																																													]
																																													],
																																													"strokeColor": [
																																													[
																																													{
																																														"time": 0.75,
																																														"interpolatorType": 1,
																																														"value": [
																																														0.3225489854812622,
																																														0.9215686321258545,
																																														0,
																																														1
																																														]
																																													}
																																													]
																																													]
																																													},
																																													{
																																														"component": 7
																																														},
																																														{
																																															"component": 9,
																																															"strokeOpacity": [
																																															[
																																															{
																																																"time": 0,
																																																"interpolatorType": 1,
																																																"value": 0
																																															}
																																															]
																																															]
																																															},
																																															{
																																																"component": 10
																																																},
																																																{
																																																	"component": 12,
																																																	"strokeOpacity": [
																																																	[
																																																	{
																																																		"time": 0,
																																																		"interpolatorType": 1,
																																																		"value": 0
																																																	}
																																																	]
																																																	]
																																																}
																																																],
																																																"animationStart": 0,
																																																"animationEnd": 6.5,
																																																"type": "animation"
																																																},
																																																{
																																																	"name": "Loading 3",
																																																	"fps": 60,
																																																	"duration": 0.75,
																																																	"isLooping": false,
																																																	"keyed": [
																																																	{
																																																		"component": 0
																																																		},
																																																		{
																																																			"component": 2,
																																																			"strokeStart": [
																																																			[
																																																			{
																																																				"time": 0,
																																																				"interpolatorType": 2,
																																																				"cubicX1": 0.42,
																																																				"cubicY1": 0,
																																																				"cubicX2": 0.58,
																																																				"cubicY2": 1,
																																																				"value": 0
																																																				},
																																																				{
																																																					"time": 0.75,
																																																					"interpolatorType": 2,
																																																					"cubicX1": 0.42,
																																																					"cubicY1": 0,
																																																					"cubicX2": 0.58,
																																																					"cubicY2": 1,
																																																					"value": 0
																																																				}
																																																				]
																																																				],
																																																				"strokeEnd": [
																																																				[
																																																				{
																																																					"time": 0,
																																																					"interpolatorType": 2,
																																																					"cubicX1": 0.42,
																																																					"cubicY1": 0,
																																																					"cubicX2": 0.58,
																																																					"cubicY2": 1,
																																																					"value": 0
																																																					},
																																																					{
																																																						"time": 0.75,
																																																						"interpolatorType": 2,
																																																						"cubicX1": 0.42,
																																																						"cubicY1": 0,
																																																						"cubicX2": 0.58,
																																																						"cubicY2": 1,
																																																						"value": 1
																																																					}
																																																					]
																																																					],
																																																					"strokeOffset": [
																																																					[
																																																					{
																																																						"time": 0,
																																																						"interpolatorType": 1,
																																																						"value": 0
																																																						},
																																																						{
																																																							"time": 0.75,
																																																							"interpolatorType": 1,
																																																							"value": 0
																																																							},
																																																							{
																																																								"time": 6.5,
																																																								"interpolatorType": 1,
																																																								"value": 0
																																																							}
																																																							]
																																																							],
																																																							"strokeColor": [
																																																							[
																																																							{
																																																								"time": 0.75,
																																																								"interpolatorType": 1,
																																																								"value": [
																																																								0.9215686321258545,
																																																								0,
																																																								0.24705882370471954,
																																																								1
																																																								]
																																																							}
																																																							]
																																																							]
																																																							},
																																																							{
																																																								"component": 3
																																																								},
																																																								{
																																																									"component": 6,
																																																									"strokeOpacity": [
																																																									[
																																																									{
																																																										"time": 0.75,
																																																										"interpolatorType": 1,
																																																										"value": 0
																																																									}
																																																									]
																																																									],
																																																									"strokeColor": [
																																																									[
																																																									{
																																																										"time": 0.75,
																																																										"interpolatorType": 1,
																																																										"value": [
																																																										0.3225489854812622,
																																																										0.9215686321258545,
																																																										0,
																																																										1
																																																										]
																																																									}
																																																									]
																																																									]
																																																									},
																																																									{
																																																										"component": 7
																																																										},
																																																										{
																																																											"component": 9,
																																																											"strokeOpacity": [
																																																											[
																																																											{
																																																												"time": 0,
																																																												"interpolatorType": 1,
																																																												"value": 0
																																																											}
																																																											]
																																																											]
																																																											},
																																																											{
																																																												"component": 10
																																																												},
																																																												{
																																																													"component": 12,
																																																													"strokeOpacity": [
																																																													[
																																																													{
																																																														"time": 0,
																																																														"interpolatorType": 1,
																																																														"value": 0
																																																													}
																																																													]
																																																													]
																																																												}
																																																												],
																																																												"animationStart": 0,
																																																												"animationEnd": 6.5,
																																																												"type": "animation"
																																																												},
																																																												{
																																																													"name": "Success",
																																																													"fps": 60,
																																																													"duration": 0.5,
																																																													"isLooping": false,
																																																													"keyed": [
																																																													{
																																																														"component": 0
																																																														},
																																																														{
																																																															"component": 2,
																																																															"strokeEnd": [
																																																															[
																																																															{
																																																																"time": 0,
																																																																"interpolatorType": 1,
																																																																"value": 1
																																																																},
																																																																{
																																																																	"time": 0.5,
																																																																	"interpolatorType": 1,
																																																																	"value": 1
																																																																}
																																																																]
																																																																],
																																																																"strokeColor": [
																																																																[
																																																																{
																																																																	"time": 0.5,
																																																																	"interpolatorType": 1,
																																																																	"value": [
																																																																	0.6000000238418579,
																																																																	1,
																																																																	0.6392157077789307,
																																																																	1
																																																																	]
																																																																}
																																																																]
																																																																]
																																																																},
																																																																{
																																																																	"component": 3
																																																																	},
																																																																	{
																																																																		"component": 6,
																																																																		"strokeEnd": [
																																																																		[
																																																																		{
																																																																			"time": 0,
																																																																			"interpolatorType": 1,
																																																																			"value": 0
																																																																			},
																																																																			{
																																																																				"time": 0.5,
																																																																				"interpolatorType": 1,
																																																																				"value": 1
																																																																				},
																																																																				{
																																																																					"time": 5.75,
																																																																					"interpolatorType": 2,
																																																																					"cubicX1": 0.42,
																																																																					"cubicY1": 0,
																																																																					"cubicX2": 0.58,
																																																																					"cubicY2": 1,
																																																																					"value": 0
																																																																					},
																																																																					{
																																																																						"time": 6.5,
																																																																						"interpolatorType": 2,
																																																																						"cubicX1": 0.42,
																																																																						"cubicY1": 0,
																																																																						"cubicX2": 0.58,
																																																																						"cubicY2": 1,
																																																																						"value": 1
																																																																					}
																																																																					]
																																																																					],
																																																																					"strokeOpacity": [
																																																																					[
																																																																					{
																																																																						"time": 0,
																																																																						"interpolatorType": 1,
																																																																						"value": 1
																																																																					}
																																																																					]
																																																																					],
																																																																					"strokeColor": [
																																																																					[
																																																																					{
																																																																						"time": 0.5,
																																																																						"interpolatorType": 1,
																																																																						"value": [
																																																																						0.6000000238418579,
																																																																						1,
																																																																						0.6392157077789307,
																																																																						1
																																																																						]
																																																																					}
																																																																					]
																																																																					]
																																																																					},
																																																																					{
																																																																						"component": 7
																																																																						},
																																																																						{
																																																																							"component": 9,
																																																																							"strokeOpacity": [
																																																																							[
																																																																							{
																																																																								"time": 0.5,
																																																																								"interpolatorType": 1,
																																																																								"value": 0
																																																																							}
																																																																							]
																																																																							]
																																																																							},
																																																																							{
																																																																								"component": 10
																																																																								},
																																																																								{
																																																																									"component": 12,
																																																																									"strokeOpacity": [
																																																																									[
																																																																									{
																																																																										"time": 0.5,
																																																																										"interpolatorType": 1,
																																																																										"value": 0
																																																																									}
																																																																									]
																																																																									]
																																																																								}
																																																																								],
																																																																								"animationStart": 0,
																																																																								"animationEnd": 6.5,
																																																																								"type": "animation"
																																																																								},
																																																																								{
																																																																									"name": "Error",
																																																																									"fps": 60,
																																																																									"duration": 0.5,
																																																																									"isLooping": false,
																																																																									"keyed": [
																																																																									{
																																																																										"component": 0
																																																																										},
																																																																										{
																																																																											"component": 2,
																																																																											"strokeEnd": [
																																																																											[
																																																																											{
																																																																												"time": 0.5,
																																																																												"interpolatorType": 1,
																																																																												"value": 1
																																																																												},
																																																																												{
																																																																													"time": 1,
																																																																													"interpolatorType": 1,
																																																																													"value": 1
																																																																												}
																																																																												]
																																																																												]
																																																																												},
																																																																												{
																																																																													"component": 3
																																																																													},
																																																																													{
																																																																														"component": 6,
																																																																														"strokeOpacity": [
																																																																														[
																																																																														{
																																																																															"time": 0.5,
																																																																															"interpolatorType": 1,
																																																																															"value": 0
																																																																														}
																																																																														]
																																																																														]
																																																																														},
																																																																														{
																																																																															"component": 7
																																																																															},
																																																																															{
																																																																																"component": 9,
																																																																																"strokeEnd": [
																																																																																[
																																																																																{
																																																																																	"time": 0,
																																																																																	"interpolatorType": 2,
																																																																																	"cubicX1": 0.42,
																																																																																	"cubicY1": 0,
																																																																																	"cubicX2": 0.58,
																																																																																	"cubicY2": 1,
																																																																																	"value": 0
																																																																																	},
																																																																																	{
																																																																																		"time": 0.25,
																																																																																		"interpolatorType": 2,
																																																																																		"cubicX1": 0.42,
																																																																																		"cubicY1": 0,
																																																																																		"cubicX2": 0.58,
																																																																																		"cubicY2": 1,
																																																																																		"value": 1
																																																																																		},
																																																																																		{
																																																																																			"time": 9.516666666666667,
																																																																																			"interpolatorType": 2,
																																																																																			"cubicX1": 0.42,
																																																																																			"cubicY1": 0,
																																																																																			"cubicX2": 0.58,
																																																																																			"cubicY2": 1,
																																																																																			"value": 1
																																																																																		}
																																																																																		]
																																																																																		],
																																																																																		"strokeOpacity": [
																																																																																		[
																																																																																		{
																																																																																			"time": 0,
																																																																																			"interpolatorType": 2,
																																																																																			"cubicX1": 0.42,
																																																																																			"cubicY1": 0,
																																																																																			"cubicX2": 0.58,
																																																																																			"cubicY2": 1,
																																																																																			"value": 1
																																																																																		}
																																																																																		]
																																																																																		]
																																																																																		},
																																																																																		{
																																																																																			"component": 10
																																																																																			},
																																																																																			{
																																																																																				"component": 12,
																																																																																				"strokeOpacity": [
																																																																																				[
																																																																																				{
																																																																																					"time": 0.25,
																																																																																					"interpolatorType": 2,
																																																																																					"cubicX1": 0.42,
																																																																																					"cubicY1": 0,
																																																																																					"cubicX2": 0.58,
																																																																																					"cubicY2": 1,
																																																																																					"value": 1
																																																																																				}
																																																																																				]
																																																																																				],
																																																																																				"strokeEnd": [
																																																																																				[
																																																																																				{
																																																																																					"time": 0,
																																																																																					"interpolatorType": 2,
																																																																																					"cubicX1": 0.42,
																																																																																					"cubicY1": 0,
																																																																																					"cubicX2": 0.58,
																																																																																					"cubicY2": 1,
																																																																																					"value": 0
																																																																																					},
																																																																																					{
																																																																																						"time": 0.25,
																																																																																						"interpolatorType": 2,
																																																																																						"cubicX1": 0.42,
																																																																																						"cubicY1": 0,
																																																																																						"cubicX2": 0.58,
																																																																																						"cubicY2": 1,
																																																																																						"value": 0
																																																																																						},
																																																																																						{
																																																																																							"time": 0.5,
																																																																																							"interpolatorType": 2,
																																																																																							"cubicX1": 0.42,
																																																																																							"cubicY1": 0,
																																																																																							"cubicX2": 0.58,
																																																																																							"cubicY2": 1,
																																																																																							"value": 1
																																																																																						}
																																																																																						]
																																																																																						]
																																																																																					}
																																																																																					],
																																																																																					"animationStart": 0,
																																																																																					"animationEnd": 9.516666666666667,
																																																																																					"type": "animation"
																																																																																				}
																																																																																				],
																																																																																				"type": "artboard"
																																																																																			}
																																																																																			]
																																																																																		}';
	} elseif ($param['type'] == 'settings') {
		echo '{
																																																																																			"version": 24,
																																																																																			"artboards": [
																																																																																			{
																																																																																				"name": "Artboard",
																																																																																				"translation": [
																																																																																				116.39401245117188,
																																																																																				97.28765869140625
																																																																																				],
																																																																																				"width": 960,
																																																																																				"height": 960,
																																																																																				"origin": [
																																																																																				0,
																																																																																				0
																																																																																				],
																																																																																				"clipContents": true,
																																																																																				"color": [
																																																																																				0.19607843458652496,
																																																																																				0.25882354378700256,
																																																																																				0.364705890417099,
																																																																																				0
																																																																																				],
																																																																																				"nodes": [
																																																																																				{
																																																																																					"name": "Capa_1",
																																																																																					"translation": [
																																																																																					43.010009765625,
																																																																																					-81.55313110351562
																																																																																					],
																																																																																					"rotation": 0,
																																																																																					"scale": [
																																																																																					1,
																																																																																					1
																																																																																					],
																																																																																					"opacity": 1,
																																																																																					"isCollapsed": false,
																																																																																					"clips": [],
																																																																																					"type": "node"
																																																																																					},
																																																																																					{
																																																																																						"name": "Node",
																																																																																						"parent": 0,
																																																																																						"translation": [
																																																																																						0,
																																																																																						0
																																																																																						],
																																																																																						"rotation": 0,
																																																																																						"scale": [
																																																																																						1,
																																																																																						1
																																																																																						],
																																																																																						"opacity": 1,
																																																																																						"isCollapsed": false,
																																																																																						"clips": [],
																																																																																						"type": "node"
																																																																																						},
																																																																																						{
																																																																																							"name": "Shape",
																																																																																							"parent": 1,
																																																																																							"translation": [
																																																																																							256.8004455566406,
																																																																																							361.2919921875
																																																																																							],
																																																																																							"rotation": 0,
																																																																																							"scale": [
																																																																																							1,
																																																																																							1
																																																																																							],
																																																																																							"opacity": 1,
																																																																																							"isCollapsed": false,
																																																																																							"clips": [],
																																																																																							"isVisible": true,
																																																																																							"blendMode": 3,
																																																																																							"drawOrder": 1,
																																																																																							"transformAffectsStroke": false,
																																																																																							"type": "shape"
																																																																																							},
																																																																																							{
																																																																																								"name": "Color",
																																																																																								"parent": 2,
																																																																																								"opacity": 1,
																																																																																								"color": [
																																																																																								0.9725490212440491,
																																																																																								0.5490196347236633,
																																																																																								0,
																																																																																								1
																																																																																								],
																																																																																								"fillRule": 1,
																																																																																								"type": "colorFill"
																																																																																								},
																																																																																								{
																																																																																									"name": "Path",
																																																																																									"parent": 2,
																																																																																									"translation": [
																																																																																									-0.624755859375,
																																																																																									1.0994873046875
																																																																																									],
																																																																																									"rotation": 0,
																																																																																									"scale": [
																																																																																									1,
																																																																																									1
																																																																																									],
																																																																																									"opacity": 1,
																																																																																									"isCollapsed": false,
																																																																																									"clips": [],
																																																																																									"bones": [],
																																																																																									"isVisible": true,
																																																																																									"isClosed": true,
																																																																																									"points": [
																																																																																									{
																																																																																										"pointType": 2,
																																																																																										"translation": [
																																																																																										-215.0504913330078,
																																																																																										60.60098648071289
																																																																																										],
																																																																																										"in": [
																																																																																										-215.0504913330078,
																																																																																										60.60098648071289
																																																																																										],
																																																																																										"out": [
																																																																																										-210.15048217773438,
																																																																																										77.40098571777344
																																																																																										]
																																																																																										},
																																																																																										{
																																																																																											"pointType": 2,
																																																																																											"translation": [
																																																																																											-194.75048828125,
																																																																																											108.80098724365234
																																																																																											],
																																																																																											"in": [
																																																																																											-203.35049438476562,
																																																																																											93.60099029541016
																																																																																											],
																																																																																											"out": [
																																																																																											-194.75048828125,
																																																																																											108.80098724365234
																																																																																											]
																																																																																											},
																																																																																											{
																																																																																												"pointType": 2,
																																																																																												"translation": [
																																																																																												-219.25048828125,
																																																																																												139.70098876953125
																																																																																												],
																																																																																												"in": [
																																																																																												-219.25048828125,
																																																																																												139.70098876953125
																																																																																												],
																																																																																												"out": [
																																																																																												-227.25048828125,
																																																																																												149.80099487304688
																																																																																												]
																																																																																												},
																																																																																												{
																																																																																													"pointType": 2,
																																																																																													"translation": [
																																																																																													-217.35049438476562,
																																																																																													173.30099487304688
																																																																																													],
																																																																																													"in": [
																																																																																													-226.35049438476562,
																																																																																													164.20098876953125
																																																																																													],
																																																																																													"out": [
																																																																																													-217.35049438476562,
																																																																																													173.30099487304688
																																																																																													]
																																																																																													},
																																																																																													{
																																																																																														"pointType": 2,
																																																																																														"translation": [
																																																																																														-175.15048217773438,
																																																																																														215.50099182128906
																																																																																														],
																																																																																														"in": [
																																																																																														-175.15048217773438,
																																																																																														215.50099182128906
																																																																																														],
																																																																																														"out": [
																																																																																														-166.0504913330078,
																																																																																														224.60098266601562
																																																																																														]
																																																																																														},
																																																																																														{
																																																																																															"pointType": 2,
																																																																																															"translation": [
																																																																																															-141.5504913330078,
																																																																																															217.39999389648438
																																																																																															],
																																																																																															"in": [
																																																																																															-151.65048217773438,
																																																																																															225.39999389648438
																																																																																															],
																																																																																															"out": [
																																																																																															-141.5504913330078,
																																																																																															217.39999389648438
																																																																																															]
																																																																																															},
																																																																																															{
																																																																																																"pointType": 2,
																																																																																																"translation": [
																																																																																																-110.8504867553711,
																																																																																																193.09999084472656
																																																																																																],
																																																																																																"in": [
																																																																																																-110.8504867553711,
																																																																																																193.09999084472656
																																																																																																],
																																																																																																"out": [
																																																																																																-95.05049133300781,
																																																																																																202.20098876953125
																																																																																																]
																																																																																																},
																																																																																																{
																																																																																																	"pointType": 2,
																																																																																																	"translation": [
																																																																																																	-60.75048828125,
																																																																																																	214.29998779296875
																																																																																																	],
																																																																																																	"in": [
																																																																																																	-78.25048828125,
																																																																																																	209.29998779296875
																																																																																																	],
																																																																																																	"out": [
																																																																																																	-60.75048828125,
																																																																																																	214.29998779296875
																																																																																																	]
																																																																																																	},
																																																																																																	{
																																																																																																		"pointType": 2,
																																																																																																		"translation": [
																																																																																																		-56.150489807128906,
																																																																																																		253.79998779296875
																																																																																																		],
																																																																																																		"in": [
																																																																																																		-56.150489807128906,
																																																																																																		253.79998779296875
																																																																																																		],
																																																																																																		"out": [
																																																																																																		-54.650489807128906,
																																																																																																		266.5999755859375
																																																																																																		]
																																																																																																		},
																																																																																																		{
																																																																																																			"pointType": 2,
																																																																																																			"translation": [
																																																																																																			-31.050487518310547,
																																																																																																			276.1999816894531
																																																																																																			],
																																																																																																			"in": [
																																																																																																			-43.850486755371094,
																																																																																																			276.1999816894531
																																																																																																			],
																																																																																																			"out": [
																																																																																																			-31.050487518310547,
																																																																																																			276.1999816894531
																																																																																																			]
																																																																																																			},
																																																																																																			{
																																																																																																				"pointType": 2,
																																																																																																				"translation": [
																																																																																																				28.649511337280273,
																																																																																																				276.1999816894531
																																																																																																				],
																																																																																																				"in": [
																																																																																																				28.649511337280273,
																																																																																																				276.1999816894531
																																																																																																				],
																																																																																																				"out": [
																																																																																																				41.44951248168945,
																																																																																																				276.1999816894531
																																																																																																				]
																																																																																																				},
																																																																																																				{
																																																																																																					"pointType": 2,
																																																																																																					"translation": [
																																																																																																					53.74951171875,
																																																																																																					253.79998779296875
																																																																																																					],
																																																																																																					"in": [
																																																																																																					52.24951171875,
																																																																																																					266.5989990234375
																																																																																																					],
																																																																																																					"out": [
																																																																																																					53.74951171875,
																																																																																																					253.79998779296875
																																																																																																					]
																																																																																																					},
																																																																																																					{
																																																																																																						"pointType": 2,
																																																																																																						"translation": [
																																																																																																						58.149513244628906,
																																																																																																						215.69998168945312
																																																																																																						],
																																																																																																						"in": [
																																																																																																						58.149513244628906,
																																																																																																						215.69998168945312
																																																																																																						],
																																																																																																						"out": [
																																																																																																						76.94950866699219,
																																																																																																						210.79998779296875
																																																																																																						]
																																																																																																						},
																																																																																																						{
																																																																																																							"pointType": 2,
																																																																																																							"translation": [
																																																																																																							111.8495101928711,
																																																																																																							193.99998474121094
																																																																																																							],
																																																																																																							"in": [
																																																																																																							94.94950866699219,
																																																																																																							203.49998474121094
																																																																																																							],
																																																																																																							"out": [
																																																																																																							111.8495101928711,
																																																																																																							193.99998474121094
																																																																																																							]
																																																																																																							},
																																																																																																							{
																																																																																																								"pointType": 2,
																																																																																																								"translation": [
																																																																																																								141.5495147705078,
																																																																																																								217.49998474121094
																																																																																																								],
																																																																																																								"in": [
																																																																																																								141.5495147705078,
																																																																																																								217.49998474121094
																																																																																																								],
																																																																																																								"out": [
																																																																																																								151.64950561523438,
																																																																																																								225.49998474121094
																																																																																																								]
																																																																																																								},
																																																																																																								{
																																																																																																									"pointType": 2,
																																																																																																									"translation": [
																																																																																																									175.14950561523438,
																																																																																																									215.59999084472656
																																																																																																									],
																																																																																																									"in": [
																																																																																																									166.0495147705078,
																																																																																																									224.59999084472656
																																																																																																									],
																																																																																																									"out": [
																																																																																																									175.14950561523438,
																																																																																																									215.59999084472656
																																																																																																									]
																																																																																																									},
																																																																																																									{
																																																																																																										"pointType": 2,
																																																																																																										"translation": [
																																																																																																										217.34951782226562,
																																																																																																										173.39999389648438
																																																																																																										],
																																																																																																										"in": [
																																																																																																										217.34951782226562,
																																																																																																										173.39999389648438
																																																																																																										],
																																																																																																										"out": [
																																																																																																										226.4495086669922,
																																																																																																										164.29998779296875
																																																																																																										]
																																																																																																										},
																																																																																																										{
																																																																																																											"pointType": 2,
																																																																																																											"translation": [
																																																																																																											219.24951171875,
																																																																																																											139.79998779296875
																																																																																																											],
																																																																																																											"in": [
																																																																																																											227.24951171875,
																																																																																																											149.89999389648438
																																																																																																											],
																																																																																																											"out": [
																																																																																																											219.24951171875,
																																																																																																											139.79998779296875
																																																																																																											]
																																																																																																											},
																																																																																																											{
																																																																																																												"pointType": 2,
																																																																																																												"translation": [
																																																																																																												196.14950561523438,
																																																																																																												110.49998474121094
																																																																																																												],
																																																																																																												"in": [
																																																																																																												196.14950561523438,
																																																																																																												110.49998474121094
																																																																																																												],
																																																																																																												"out": [
																																																																																																												205.74951171875,
																																																																																																												93.89898681640625
																																																																																																												]
																																																																																																												},
																																																																																																												{
																																																																																																													"pointType": 2,
																																																																																																													"translation": [
																																																																																																													218.24951171875,
																																																																																																													57.699989318847656
																																																																																																													],
																																																																																																													"in": [
																																																																																																													213.24951171875,
																																																																																																													76.19998931884766
																																																																																																													],
																																																																																																													"out": [
																																																																																																													218.24951171875,
																																																																																																													57.699989318847656
																																																																																																													]
																																																																																																													},
																																																																																																													{
																																																																																																														"pointType": 2,
																																																																																																														"translation": [
																																																																																																														253.84951782226562,
																																																																																																														53.5999870300293
																																																																																																														],
																																																																																																														"in": [
																																																																																																														253.84951782226562,
																																																																																																														53.5999870300293
																																																																																																														],
																																																																																																														"out": [
																																																																																																														266.6505126953125,
																																																																																																														52.0999870300293
																																																																																																														]
																																																																																																														},
																																																																																																														{
																																																																																																															"pointType": 2,
																																																																																																															"translation": [
																																																																																																															276.24951171875,
																																																																																																															28.499988555908203
																																																																																																															],
																																																																																																															"in": [
																																																																																																															276.24951171875,
																																																																																																															41.29998779296875
																																																																																																															],
																																																																																																															"out": [
																																																																																																															276.24951171875,
																																																																																																															28.499988555908203
																																																																																																															]
																																																																																																															},
																																																																																																															{
																																																																																																																"pointType": 2,
																																																																																																																"translation": [
																																																																																																																276.24951171875,
																																																																																																																-31.20001220703125
																																																																																																																],
																																																																																																																"in": [
																																																																																																																276.24951171875,
																																																																																																																-31.20001220703125
																																																																																																																],
																																																																																																																"out": [
																																																																																																																276.24951171875,
																																																																																																																-44.0000114440918
																																																																																																																]
																																																																																																																},
																																																																																																																{
																																																																																																																	"pointType": 2,
																																																																																																																	"translation": [
																																																																																																																	253.84951782226562,
																																																																																																																	-56.300010681152344
																																																																																																																	],
																																																																																																																	"in": [
																																																																																																																	266.6495056152344,
																																																																																																																	-54.800010681152344
																																																																																																																	],
																																																																																																																	"out": [
																																																																																																																	253.84951782226562,
																																																																																																																	-56.300010681152344
																																																																																																																	]
																																																																																																																	},
																																																																																																																	{
																																																																																																																		"pointType": 2,
																																																																																																																		"translation": [
																																																																																																																		218.74951171875,
																																																																																																																		-60.4000129699707
																																																																																																																		],
																																																																																																																		"in": [
																																																																																																																		218.74951171875,
																																																																																																																		-60.4000129699707
																																																																																																																		],
																																																																																																																		"out": [
																																																																																																																		213.94851684570312,
																																																																																																																		-78.70001220703125
																																																																																																																		]
																																																																																																																		},
																																																																																																																		{
																																																																																																																			"pointType": 2,
																																																																																																																			"translation": [
																																																																																																																			197.55050659179688,
																																																																																																																			-112.60001373291016
																																																																																																																			],
																																																																																																																			"in": [
																																																																																																																			206.74951171875,
																																																																																																																			-96.20001220703125
																																																																																																																			],
																																																																																																																			"out": [
																																																																																																																			197.55050659179688,
																																																																																																																			-112.60001373291016
																																																																																																																			]
																																																																																																																			},
																																																																																																																			{
																																																																																																																				"pointType": 2,
																																																																																																																				"translation": [
																																																																																																																				219.1505126953125,
																																																																																																																				-139.90000915527344
																																																																																																																				],
																																																																																																																				"in": [
																																																																																																																				219.1505126953125,
																																																																																																																				-139.90000915527344
																																																																																																																				],
																																																																																																																				"out": [
																																																																																																																				227.1505126953125,
																																																																																																																				-150.00001525878906
																																																																																																																				]
																																																																																																																				},
																																																																																																																				{
																																																																																																																					"pointType": 2,
																																																																																																																					"translation": [
																																																																																																																					217.25051879882812,
																																																																																																																					-173.50001525878906
																																																																																																																					],
																																																																																																																					"in": [
																																																																																																																					226.25051879882812,
																																																																																																																					-164.40000915527344
																																																																																																																					],
																																																																																																																					"out": [
																																																																																																																					217.25051879882812,
																																																																																																																					-173.50001525878906
																																																																																																																					]
																																																																																																																					},
																																																																																																																					{
																																																																																																																						"pointType": 2,
																																																																																																																						"translation": [
																																																																																																																						175.1505126953125,
																																																																																																																						-215.60000610351562
																																																																																																																						],
																																																																																																																						"in": [
																																																																																																																						175.1505126953125,
																																																																																																																						-215.60000610351562
																																																																																																																						],
																																																																																																																						"out": [
																																																																																																																						166.05050659179688,
																																																																																																																						-224.70001220703125
																																																																																																																						]
																																																																																																																						},
																																																																																																																						{
																																																																																																																							"pointType": 2,
																																																																																																																							"translation": [
																																																																																																																							141.55050659179688,
																																																																																																																							-217.50001525878906
																																																																																																																							],
																																																																																																																							"in": [
																																																																																																																							151.6505126953125,
																																																																																																																							-225.50001525878906
																																																																																																																							],
																																																																																																																							"out": [
																																																																																																																							141.55050659179688,
																																																																																																																							-217.50001525878906
																																																																																																																							]
																																																																																																																							},
																																																																																																																							{
																																																																																																																								"pointType": 2,
																																																																																																																								"translation": [
																																																																																																																								115.0505142211914,
																																																																																																																								-196.50001525878906
																																																																																																																								],
																																																																																																																								"in": [
																																																																																																																								115.0505142211914,
																																																																																																																								-196.50001525878906
																																																																																																																								],
																																																																																																																								"out": [
																																																																																																																								97.85050964355469,
																																																																																																																								-206.60000610351562
																																																																																																																								]
																																																																																																																								},
																																																																																																																								{
																																																																																																																									"pointType": 2,
																																																																																																																									"translation": [
																																																																																																																									60.1505126953125,
																																																																																																																									-219.50001525878906
																																																																																																																									],
																																																																																																																									"in": [
																																																																																																																									79.44950866699219,
																																																																																																																									-214.30001831054688
																																																																																																																									],
																																																																																																																									"out": [
																																																																																																																									60.1505126953125,
																																																																																																																									-219.50001525878906
																																																																																																																									]
																																																																																																																									},
																																																																																																																									{
																																																																																																																										"pointType": 2,
																																																																																																																										"translation": [
																																																																																																																										56.1505126953125,
																																																																																																																										-253.80001831054688
																																																																																																																										],
																																																																																																																										"in": [
																																																																																																																										56.1505126953125,
																																																																																																																										-253.80001831054688
																																																																																																																										],
																																																																																																																										"out": [
																																																																																																																										54.6505126953125,
																																																																																																																										-266.6000061035156
																																																																																																																										]
																																																																																																																										},
																																																																																																																										{
																																																																																																																											"pointType": 2,
																																																																																																																											"translation": [
																																																																																																																											31.050512313842773,
																																																																																																																											-276.20001220703125
																																																																																																																											],
																																																																																																																											"in": [
																																																																																																																											43.85051345825195,
																																																																																																																											-276.20001220703125
																																																																																																																											],
																																																																																																																											"out": [
																																																																																																																											31.050512313842773,
																																																																																																																											-276.20001220703125
																																																																																																																											]
																																																																																																																											},
																																																																																																																											{
																																																																																																																												"pointType": 2,
																																																																																																																												"translation": [
																																																																																																																												-28.64948844909668,
																																																																																																																												-276.20001220703125
																																																																																																																												],
																																																																																																																												"in": [
																																																																																																																												-28.64948844909668,
																																																																																																																												-276.20001220703125
																																																																																																																												],
																																																																																																																												"out": [
																																																																																																																												-41.44948959350586,
																																																																																																																												-276.20001220703125
																																																																																																																												]
																																																																																																																												},
																																																																																																																												{
																																																																																																																													"pointType": 2,
																																																																																																																													"translation": [
																																																																																																																													-53.749488830566406,
																																																																																																																													-253.80001831054688
																																																																																																																													],
																																																																																																																													"in": [
																																																																																																																													-52.249488830566406,
																																																																																																																													-266.6000061035156
																																																																																																																													],
																																																																																																																													"out": [
																																																																																																																													-53.749488830566406,
																																																																																																																													-253.80001831054688
																																																																																																																													]
																																																																																																																													},
																																																																																																																													{
																																																																																																																														"pointType": 2,
																																																																																																																														"translation": [
																																																																																																																														-57.749488830566406,
																																																																																																																														-219.50001525878906
																																																																																																																														],
																																																																																																																														"in": [
																																																																																																																														-57.749488830566406,
																																																																																																																														-219.50001525878906
																																																																																																																														],
																																																																																																																														"out": [
																																																																																																																														-77.54949188232422,
																																																																																																																														-214.20001220703125
																																																																																																																														]
																																																																																																																														},
																																																																																																																														{
																																																																																																																															"pointType": 2,
																																																																																																																															"translation": [
																																																																																																																															-114.04949188232422,
																																																																																																																															-195.70001220703125
																																																																																																																															],
																																																																																																																															"in": [
																																																																																																																															-96.4494857788086,
																																																																																																																															-206.20001220703125
																																																																																																																															],
																																																																																																																															"out": [
																																																																																																																															-114.04949188232422,
																																																																																																																															-195.70001220703125
																																																																																																																															]
																																																																																																																															},
																																																																																																																															{
																																																																																																																																"pointType": 2,
																																																																																																																																"translation": [
																																																																																																																																-141.5494842529297,
																																																																																																																																-217.50001525878906
																																																																																																																																],
																																																																																																																																"in": [
																																																																																																																																-141.5494842529297,
																																																																																																																																-217.50001525878906
																																																																																																																																],
																																																																																																																																"out": [
																																																																																																																																-151.6494903564453,
																																																																																																																																-225.50001525878906
																																																																																																																																]
																																																																																																																																},
																																																																																																																																{
																																																																																																																																	"pointType": 2,
																																																																																																																																	"translation": [
																																																																																																																																	-175.1494903564453,
																																																																																																																																	-215.60000610351562
																																																																																																																																	],
																																																																																																																																	"in": [
																																																																																																																																	-166.0494842529297,
																																																																																																																																	-224.60000610351562
																																																																																																																																	],
																																																																																																																																	"out": [
																																																																																																																																	-175.1494903564453,
																																																																																																																																	-215.60000610351562
																																																																																																																																	]
																																																																																																																																	},
																																																																																																																																	{
																																																																																																																																		"pointType": 2,
																																																																																																																																		"translation": [
																																																																																																																																		-217.3494873046875,
																																																																																																																																		-173.40000915527344
																																																																																																																																		],
																																																																																																																																		"in": [
																																																																																																																																		-217.3494873046875,
																																																																																																																																		-173.40000915527344
																																																																																																																																		],
																																																																																																																																		"out": [
																																																																																																																																		-226.44949340820312,
																																																																																																																																		-164.30001831054688
																																																																																																																																		]
																																																																																																																																		},
																																																																																																																																		{
																																																																																																																																			"pointType": 2,
																																																																																																																																			"translation": [
																																																																																																																																			-219.24948120117188,
																																																																																																																																			-139.80001831054688
																																																																																																																																			],
																																																																																																																																			"in": [
																																																																																																																																			-227.24948120117188,
																																																																																																																																			-149.90000915527344
																																																																																																																																			],
																																																																																																																																			"out": [
																																																																																																																																			-219.24948120117188,
																																																																																																																																			-139.80001831054688
																																																																																																																																			]
																																																																																																																																			},
																																																																																																																																			{
																																																																																																																																				"pointType": 2,
																																																																																																																																				"translation": [
																																																																																																																																				-196.24948120117188,
																																																																																																																																				-110.70001220703125
																																																																																																																																				],
																																																																																																																																				"in": [
																																																																																																																																				-196.24948120117188,
																																																																																																																																				-110.70001220703125
																																																																																																																																				],
																																																																																																																																				"out": [
																																																																																																																																				-205.44949340820312,
																																																																																																																																				-94.10001373291016
																																																																																																																																				]
																																																																																																																																				},
																																																																																																																																				{
																																																																																																																																					"pointType": 2,
																																																																																																																																					"translation": [
																																																																																																																																					-217.0494842529297,
																																																																																																																																					-58.0000114440918
																																																																																																																																					],
																																																																																																																																					"in": [
																																																																																																																																					-212.44949340820312,
																																																																																																																																					-76.40000915527344
																																																																																																																																					],
																																																																																																																																					"out": [
																																																																																																																																					-217.0494842529297,
																																																																																																																																					-58.0000114440918
																																																																																																																																					]
																																																																																																																																					},
																																																																																																																																					{
																																																																																																																																						"pointType": 2,
																																																																																																																																						"translation": [
																																																																																																																																						-253.8494873046875,
																																																																																																																																						-53.800010681152344
																																																																																																																																						],
																																																																																																																																						"in": [
																																																																																																																																						-253.8494873046875,
																																																																																																																																						-53.800010681152344
																																																																																																																																						],
																																																																																																																																						"out": [
																																																																																																																																						-266.64947509765625,
																																																																																																																																						-52.300010681152344
																																																																																																																																						]
																																																																																																																																						},
																																																																																																																																						{
																																																																																																																																							"pointType": 2,
																																																																																																																																							"translation": [
																																																																																																																																							-276.2494812011719,
																																																																																																																																							-28.70001220703125
																																																																																																																																							],
																																																																																																																																							"in": [
																																																																																																																																							-276.2494812011719,
																																																																																																																																							-41.5000114440918
																																																																																																																																							],
																																																																																																																																							"out": [
																																																																																																																																							-276.2494812011719,
																																																																																																																																							-28.70001220703125
																																																																																																																																							]
																																																																																																																																							},
																																																																																																																																							{
																																																																																																																																								"pointType": 2,
																																																																																																																																								"translation": [
																																																																																																																																								-276.2494812011719,
																																																																																																																																								30.999988555908203
																																																																																																																																								],
																																																																																																																																								"in": [
																																																																																																																																								-276.2494812011719,
																																																																																																																																								30.999988555908203
																																																																																																																																								],
																																																																																																																																								"out": [
																																																																																																																																								-276.2494812011719,
																																																																																																																																								43.79998779296875
																																																																																																																																								]
																																																																																																																																								},
																																																																																																																																								{
																																																																																																																																									"pointType": 2,
																																																																																																																																									"translation": [
																																																																																																																																									-253.8494873046875,
																																																																																																																																									56.0999870300293
																																																																																																																																									],
																																																																																																																																									"in": [
																																																																																																																																									-266.64947509765625,
																																																																																																																																									54.5999870300293
																																																																																																																																									],
																																																																																																																																									"out": [
																																																																																																																																									-253.8494873046875,
																																																																																																																																									56.0999870300293
																																																																																																																																									]
																																																																																																																																								}
																																																																																																																																								],
																																																																																																																																								"type": "path"
																																																																																																																																								},
																																																																																																																																								{
																																																																																																																																									"name": "Path",
																																																																																																																																									"parent": 2,
																																																																																																																																									"translation": [
																																																																																																																																									0.624755859375,
																																																																																																																																									-1.099517822265625
																																																																																																																																									],
																																																																																																																																									"rotation": 0,
																																																																																																																																									"scale": [
																																																																																																																																									1,
																																																																																																																																									1
																																																																																																																																									],
																																																																																																																																									"opacity": 1,
																																																																																																																																									"isCollapsed": false,
																																																																																																																																									"clips": [],
																																																																																																																																									"bones": [],
																																																																																																																																									"isVisible": true,
																																																																																																																																									"isClosed": true,
																																																																																																																																									"points": [
																																																																																																																																									{
																																																																																																																																										"pointType": 1,
																																																																																																																																										"translation": [
																																																																																																																																										0,
																																																																																																																																										-98.70000457763672
																																																																																																																																										],
																																																																																																																																										"in": [
																																																																																																																																										-54.400001525878906,
																																																																																																																																										-98.70000457763672
																																																																																																																																										],
																																																																																																																																										"out": [
																																																																																																																																										54.400001525878906,
																																																																																																																																										-98.70000457763672
																																																																																																																																										]
																																																																																																																																										},
																																																																																																																																										{
																																																																																																																																											"pointType": 1,
																																																																																																																																											"translation": [
																																																																																																																																											98.69999694824219,
																																																																																																																																											-0.000006591797045985004
																																																																																																																																											],
																																																																																																																																											"in": [
																																																																																																																																											98.69999694824219,
																																																																																																																																											-54.40000534057617
																																																																																																																																											],
																																																																																																																																											"out": [
																																																																																																																																											98.69999694824219,
																																																																																																																																											54.399993896484375
																																																																																																																																											]
																																																																																																																																											},
																																																																																																																																											{
																																																																																																																																												"pointType": 1,
																																																																																																																																												"translation": [
																																																																																																																																												0,
																																																																																																																																												98.69999694824219
																																																																																																																																												],
																																																																																																																																												"in": [
																																																																																																																																												54.400001525878906,
																																																																																																																																												98.69999694824219
																																																																																																																																												],
																																																																																																																																												"out": [
																																																																																																																																												-54.39899826049805,
																																																																																																																																												98.69999694824219
																																																																																																																																												]
																																																																																																																																												},
																																																																																																																																												{
																																																																																																																																													"pointType": 1,
																																																																																																																																													"translation": [
																																																																																																																																													-98.69999694824219,
																																																																																																																																													-0.000006591797045985004
																																																																																																																																													],
																																																																																																																																													"in": [
																																																																																																																																													-98.69999694824219,
																																																																																																																																													54.399993896484375
																																																																																																																																													],
																																																																																																																																													"out": [
																																																																																																																																													-98.69999694824219,
																																																																																																																																													-54.40000534057617
																																																																																																																																													]
																																																																																																																																												}
																																																																																																																																												],
																																																																																																																																												"type": "path"
																																																																																																																																												},
																																																																																																																																												{
																																																																																																																																													"name": "Shape",
																																																																																																																																													"parent": 1,
																																																																																																																																													"translation": [
																																																																																																																																													678.37353515625,
																																																																																																																																													618.989501953125
																																																																																																																																													],
																																																																																																																																													"rotation": 0,
																																																																																																																																													"scale": [
																																																																																																																																													1,
																																																																																																																																													1
																																																																																																																																													],
																																																																																																																																													"opacity": 1,
																																																																																																																																													"isCollapsed": false,
																																																																																																																																													"clips": [],
																																																																																																																																													"isVisible": true,
																																																																																																																																													"blendMode": 3,
																																																																																																																																													"drawOrder": 2,
																																																																																																																																													"transformAffectsStroke": false,
																																																																																																																																													"type": "shape"
																																																																																																																																													},
																																																																																																																																													{
																																																																																																																																														"name": "Color",
																																																																																																																																														"parent": 6,
																																																																																																																																														"opacity": 1,
																																																																																																																																														"color": [
																																																																																																																																														0.5372549295425415,
																																																																																																																																														0.5372549295425415,
																																																																																																																																														0.5372549295425415,
																																																																																																																																														1
																																																																																																																																														],
																																																																																																																																														"fillRule": 1,
																																																																																																																																														"type": "colorFill"
																																																																																																																																														},
																																																																																																																																														{
																																																																																																																																															"name": "Path",
																																																																																																																																															"parent": 6,
																																																																																																																																															"translation": [
																																																																																																																																															-0.47503662109375,
																																																																																																																																															0.92529296875
																																																																																																																																															],
																																																																																																																																															"rotation": 0,
																																																																																																																																															"scale": [
																																																																																																																																															1,
																																																																																																																																															1
																																																																																																																																															],
																																																																																																																																															"opacity": 1,
																																																																																																																																															"isCollapsed": false,
																																																																																																																																															"clips": [],
																																																																																																																																															"bones": [],
																																																																																																																																															"isVisible": true,
																																																																																																																																															"isClosed": true,
																																																																																																																																															"points": [
																																																																																																																																															{
																																																																																																																																																"pointType": 2,
																																																																																																																																																"translation": [
																																																																																																																																																162.64901733398438,
																																																																																																																																																-158.94949340820312
																																																																																																																																																],
																																																																																																																																																"in": [
																																																																																																																																																172.35000610351562,
																																																																																																																																																-150.74949645996094
																																																																																																																																																],
																																																																																																																																																"out": [
																																																																																																																																																162.64901733398438,
																																																																																																																																																-158.94949340820312
																																																																																																																																																]
																																																																																																																																																},
																																																																																																																																																{
																																																																																																																																																	"pointType": 2,
																																																																																																																																																	"translation": [
																																																																																																																																																	131.14901733398438,
																																																																																																																																																	-185.54949951171875
																																																																																																																																																	],
																																																																																																																																																	"in": [
																																																																																																																																																	131.14901733398438,
																																																																																																																																																	-185.54949951171875
																																																																																																																																																	],
																																																																																																																																																	"out": [
																																																																																																																																																	121.45001220703125,
																																																																																																																																																	-193.74949645996094
																																																																																																																																																	]
																																																																																																																																																	},
																																																																																																																																																	{
																																																																																																																																																		"pointType": 2,
																																																																																																																																																		"translation": [
																																																																																																																																																		97.95001220703125,
																																																																																																																																																		-184.64950561523438
																																																																																																																																																		],
																																																																																																																																																		"in": [
																																																																																																																																																		107.14900970458984,
																																																																																																																																																		-193.34950256347656
																																																																																																																																																		],
																																																																																																																																																		"out": [
																																																																																																																																																		97.95001220703125,
																																																																																																																																																		-184.64950561523438
																																																																																																																																																		]
																																																																																																																																																		},
																																																																																																																																																		{
																																																																																																																																																			"pointType": 2,
																																																																																																																																																			"translation": [
																																																																																																																																																			80.55001068115234,
																																																																																																																																																			-168.34950256347656
																																																																																																																																																			],
																																																																																																																																																			"in": [
																																																																																																																																																			80.55001068115234,
																																																																																																																																																			-168.34950256347656
																																																																																																																																																			],
																																																																																																																																																			"out": [
																																																																																																																																																			65.85101318359375,
																																																																																																																																																			-175.44949340820312
																																																																																																																																																			]
																																																																																																																																																			},
																																																																																																																																																			{
																																																																																																																																																				"pointType": 2,
																																																																																																																																																				"translation": [
																																																																																																																																																				34.1500129699707,
																																																																																																																																																				-183.34950256347656
																																																																																																																																																				],
																																																																																																																																																				"in": [
																																																																																																																																																				50.25101089477539,
																																																																																																																																																				-180.44949340820312
																																																																																																																																																				],
																																																																																																																																																				"out": [
																																																																																																																																																				34.1500129699707,
																																																																																																																																																				-183.34950256347656
																																																																																																																																																				]
																																																																																																																																																				},
																																																																																																																																																				{
																																																																																																																																																					"pointType": 2,
																																																																																																																																																					"translation": [
																																																																																																																																																					29.252012252807617,
																																																																																																																																																					-207.34950256347656
																																																																																																																																																					],
																																																																																																																																																					"in": [
																																																																																																																																																					29.252012252807617,
																																																																																																																																																					-207.34950256347656
																																																																																																																																																					],
																																																																																																																																																					"out": [
																																																																																																																																																					26.752012252807617,
																																																																																																																																																					-219.74949645996094
																																																																																																																																																					]
																																																																																																																																																					},
																																																																																																																																																					{
																																																																																																																																																						"pointType": 2,
																																																																																																																																																						"translation": [
																																																																																																																																																						2.650012254714966,
																																																																																																																																																						-227.34950256347656
																																																																																																																																																						],
																																																																																																																																																						"in": [
																																																																																																																																																						15.252012252807617,
																																																																																																																																																						-228.34950256347656
																																																																																																																																																						],
																																																																																																																																																						"out": [
																																																																																																																																																						2.650012254714966,
																																																																																																																																																						-227.34950256347656
																																																																																																																																																						]
																																																																																																																																																						},
																																																																																																																																																						{
																																																																																																																																																							"pointType": 2,
																																																																																																																																																							"translation": [
																																																																																																																																																							-38.449989318847656,
																																																																																																																																																							-223.84950256347656
																																																																																																																																																							],
																																																																																																																																																							"in": [
																																																																																																																																																							-38.449989318847656,
																																																																																																																																																							-223.84950256347656
																																																																																																																																																							],
																																																																																																																																																							"out": [
																																																																																																																																																							-51.04998779296875,
																																																																																																																																																							-222.74949645996094
																																																																																																																																																							]
																																																																																																																																																							},
																																																																																																																																																							{
																																																																																																																																																								"pointType": 2,
																																																																																																																																																								"translation": [
																																																																																																																																																								-61.3499870300293,
																																																																																																																																																								-199.74949645996094
																																																																																																																																																								],
																																																																																																																																																								"in": [
																																																																																																																																																								-60.949989318847656,
																																																																																																																																																								-212.44949340820312
																																																																																																																																																								],
																																																																																																																																																								"out": [
																																																																																																																																																								-61.3499870300293,
																																																																																																																																																								-199.74949645996094
																																																																																																																																																								]
																																																																																																																																																								},
																																																																																																																																																								{
																																																																																																																																																									"pointType": 2,
																																																																																																																																																									"translation": [
																																																																																																																																																									-62.14898681640625,
																																																																																																																																																									-175.34950256347656
																																																																																																																																																									],
																																																																																																																																																									"in": [
																																																																																																																																																									-62.14898681640625,
																																																																																																																																																									-175.34950256347656
																																																																																																																																																									],
																																																																																																																																																									"out": [
																																																																																																																																																									-77.94998931884766,
																																																																																																																																																									-169.64950561523438
																																																																																																																																																									]
																																																																																																																																																									},
																																																																																																																																																									{
																																																																																																																																																										"pointType": 2,
																																																																																																																																																										"translation": [
																																																																																																																																																										-106.44998931884766,
																																																																																																																																																										-152.04949951171875
																																																																																																																																																										],
																																																																																																																																																										"in": [
																																																																																																																																																										-92.84999084472656,
																																																																																																																																																										-161.84950256347656
																																																																																																																																																										],
																																																																																																																																																										"out": [
																																																																																																																																																										-106.44998931884766,
																																																																																																																																																										-152.04949951171875
																																																																																																																																																										]
																																																																																																																																																										},
																																																																																																																																																										{
																																																																																																																																																											"pointType": 2,
																																																																																																																																																											"translation": [
																																																																																																																																																											-127.24898529052734,
																																																																																																																																																											-165.84950256347656
																																																																																																																																																											],
																																																																																																																																																											"in": [
																																																																																																																																																											-127.24898529052734,
																																																																																																																																																											-165.84950256347656
																																																																																																																																																											],
																																																																																																																																																											"out": [
																																																																																																																																																											-137.85098266601562,
																																																																																																																																																											-172.84950256347656
																																																																																																																																																											]
																																																																																																																																																											},
																																																																																																																																																											{
																																																																																																																																																												"pointType": 2,
																																																																																																																																																												"translation": [
																																																																																																																																																												-160.14898681640625,
																																																																																																																																																												-161.14950561523438
																																																																																																																																																												],
																																																																																																																																																												"in": [
																																																																																																																																																												-151.94998168945312,
																																																																																																																																																												-170.84950256347656
																																																																																																																																																												],
																																																																																																																																																												"out": [
																																																																																																																																																												-160.14898681640625,
																																																																																																																																																												-161.14950561523438
																																																																																																																																																												]
																																																																																																																																																												},
																																																																																																																																																												{
																																																																																																																																																													"pointType": 2,
																																																																																																																																																													"translation": [
																																																																																																																																																													-186.74899291992188,
																																																																																																																																																													-129.44949340820312
																																																																																																																																																													],
																																																																																																																																																													"in": [
																																																																																																																																																													-186.74899291992188,
																																																																																																																																																													-129.44949340820312
																																																																																																																																																													],
																																																																																																																																																													"out": [
																																																																																																																																																													-194.94998168945312,
																																																																																																																																																													-119.74949645996094
																																																																																																																																																													]
																																																																																																																																																													},
																																																																																																																																																													{
																																																																																																																																																														"pointType": 2,
																																																																																																																																																														"translation": [
																																																																																																																																																														-185.85098266601562,
																																																																																																																																																														-96.24949645996094
																																																																																																																																																														],
																																																																																																																																																														"in": [
																																																																																																																																																														-194.54998779296875,
																																																																																																																																																														-105.44950103759766
																																																																																																																																																														],
																																																																																																																																																														"out": [
																																																																																																																																																														-185.85098266601562,
																																																																																																																																																														-96.24949645996094
																																																																																																																																																														]
																																																																																																																																																														},
																																																																																																																																																														{
																																																																																																																																																															"pointType": 2,
																																																																																																																																																															"translation": [
																																																																																																																																																															-167.64999389648438,
																																																																																																																																																															-76.85050201416016
																																																																																																																																																															],
																																																																																																																																																															"in": [
																																																																																																																																																															-167.64999389648438,
																																																																																																																																																															-76.85050201416016
																																																																																																																																																															],
																																																																																																																																																															"out": [
																																																																																																																																																															-173.95098876953125,
																																																																																																																																																															-62.6505012512207
																																																																																																																																																															]
																																																																																																																																																															},
																																																																																																																																																															{
																																																																																																																																																																"pointType": 2,
																																																																																																																																																																"translation": [
																																																																																																																																																																-181.04998779296875,
																																																																																																																																																																-32.45050048828125
																																																																																																																																																																],
																																																																																																																																																																"in": [
																																																																																																																																																																-178.45098876953125,
																																																																																																																																																																-47.7495002746582
																																																																																																																																																																],
																																																																																																																																																																"out": [
																																																																																																																																																																-181.04998779296875,
																																																																																																																																																																-32.45050048828125
																																																																																																																																																																]
																																																																																																																																																																},
																																																																																																																																																																{
																																																																																																																																																																	"pointType": 2,
																																																																																																																																																																	"translation": [
																																																																																																																																																																	-207.04998779296875,
																																																																																																																																																																	-27.15049934387207
																																																																																																																																																																	],
																																																																																																																																																																	"in": [
																																																																																																																																																																	-207.04998779296875,
																																																																																																																																																																	-27.15049934387207
																																																																																																																																																																	],
																																																																																																																																																																	"out": [
																																																																																																																																																																	-219.44998168945312,
																																																																																																																																																																	-24.65049934387207
																																																																																																																																																																	]
																																																																																																																																																																	},
																																																																																																																																																																	{
																																																																																																																																																																		"pointType": 2,
																																																																																																																																																																		"translation": [
																																																																																																																																																																		-227.04998779296875,
																																																																																																																																																																		-0.5494999885559082
																																																																																																																																																																		],
																																																																																																																																																																		"in": [
																																																																																																																																																																		-228.04998779296875,
																																																																																																																																																																		-13.150500297546387
																																																																																																																																																																		],
																																																																																																																																																																		"out": [
																																																																																																																																																																		-227.04998779296875,
																																																																																																																																																																		-0.5494999885559082
																																																																																																																																																																		]
																																																																																																																																																																		},
																																																																																																																																																																		{
																																																																																																																																																																			"pointType": 2,
																																																																																																																																																																			"translation": [
																																																																																																																																																																			-223.54998779296875,
																																																																																																																																																																			40.550498962402344
																																																																																																																																																																			],
																																																																																																																																																																			"in": [
																																																																																																																																																																			-223.54998779296875,
																																																																																																																																																																			40.550498962402344
																																																																																																																																																																			],
																																																																																																																																																																			"out": [
																																																																																																																																																																			-222.44998168945312,
																																																																																																																																																																			53.1505012512207
																																																																																																																																																																			]
																																																																																																																																																																			},
																																																																																																																																																																			{
																																																																																																																																																																				"pointType": 2,
																																																																																																																																																																				"translation": [
																																																																																																																																																																				-199.44998168945312,
																																																																																																																																																																				63.45050048828125
																																																																																																																																																																				],
																																																																																																																																																																				"in": [
																																																																																																																																																																				-212.14999389648438,
																																																																																																																																																																				63.050498962402344
																																																																																																																																																																				],
																																																																																																																																																																				"out": [
																																																																																																																																																																				-199.44998168945312,
																																																																																																																																																																				63.45050048828125
																																																																																																																																																																				]
																																																																																																																																																																				},
																																																																																																																																																																				{
																																																																																																																																																																					"pointType": 2,
																																																																																																																																																																					"translation": [
																																																																																																																																																																					-171.34999084472656,
																																																																																																																																																																					64.34950256347656
																																																																																																																																																																					],
																																																																																																																																																																					"in": [
																																																																																																																																																																					-171.34999084472656,
																																																																																																																																																																					64.34950256347656
																																																																																																																																																																					],
																																																																																																																																																																					"out": [
																																																																																																																																																																					-166.24798583984375,
																																																																																																																																																																					77.74949645996094
																																																																																																																																																																					]
																																																																																																																																																																					},
																																																																																																																																																																					{
																																																																																																																																																																						"pointType": 2,
																																																																																																																																																																						"translation": [
																																																																																																																																																																						-151.44998168945312,
																																																																																																																																																																						102.34950256347656
																																																																																																																																																																						],
																																																																																																																																																																						"in": [
																																																																																																																																																																						-159.54898071289062,
																																																																																																																																																																						90.45050048828125
																																																																																																																																																																						],
																																																																																																																																																																						"out": [
																																																																																																																																																																						-151.44998168945312,
																																																																																																																																																																						102.34950256347656
																																																																																																																																																																						]
																																																																																																																																																																						},
																																																																																																																																																																						{
																																																																																																																																																																							"pointType": 2,
																																																																																																																																																																							"translation": [
																																																																																																																																																																							-167.14898681640625,
																																																																																																																																																																							126.04949951171875
																																																																																																																																																																							],
																																																																																																																																																																							"in": [
																																																																																																																																																																							-167.14898681640625,
																																																																																																																																																																							126.04949951171875
																																																																																																																																																																							],
																																																																																																																																																																							"out": [
																																																																																																																																																																							-174.14898681640625,
																																																																																																																																																																							136.64950561523438
																																																																																																																																																																							]
																																																																																																																																																																							},
																																																																																																																																																																							{
																																																																																																																																																																								"pointType": 2,
																																																																																																																																																																								"translation": [
																																																																																																																																																																								-162.44998168945312,
																																																																																																																																																																								158.94949340820312
																																																																																																																																																																								],
																																																																																																																																																																								"in": [
																																																																																																																																																																								-172.14898681640625,
																																																																																																																																																																								150.74949645996094
																																																																																																																																																																								],
																																																																																																																																																																								"out": [
																																																																																																																																																																								-162.44998168945312,
																																																																																																																																																																								158.94949340820312
																																																																																																																																																																								]
																																																																																																																																																																								},
																																																																																																																																																																								{
																																																																																																																																																																									"pointType": 2,
																																																																																																																																																																									"translation": [
																																																																																																																																																																									-130.94998168945312,
																																																																																																																																																																									185.54949951171875
																																																																																																																																																																									],
																																																																																																																																																																									"in": [
																																																																																																																																																																									-130.94998168945312,
																																																																																																																																																																									185.54949951171875
																																																																																																																																																																									],
																																																																																																																																																																									"out": [
																																																																																																																																																																									-121.24898529052734,
																																																																																																																																																																									193.74949645996094
																																																																																																																																																																									]
																																																																																																																																																																									},
																																																																																																																																																																									{
																																																																																																																																																																										"pointType": 2,
																																																																																																																																																																										"translation": [
																																																																																																																																																																										-97.74898529052734,
																																																																																																																																																																										184.64950561523438
																																																																																																																																																																										],
																																																																																																																																																																										"in": [
																																																																																																																																																																										-106.94998931884766,
																																																																																																																																																																										193.34950256347656
																																																																																																																																																																										],
																																																																																																																																																																										"out": [
																																																																																																																																																																										-97.74898529052734,
																																																																																																																																																																										184.64950561523438
																																																																																																																																																																										]
																																																																																																																																																																										},
																																																																																																																																																																										{
																																																																																																																																																																											"pointType": 2,
																																																																																																																																																																											"translation": [
																																																																																																																																																																											-77.14898681640625,
																																																																																																																																																																											165.34950256347656
																																																																																																																																																																											],
																																																																																																																																																																											"in": [
																																																																																																																																																																											-77.14898681640625,
																																																																																																																																																																											165.34950256347656
																																																																																																																																																																											],
																																																																																																																																																																											"out": [
																																																																																																																																																																											-63.64898681640625,
																																																																																																																																																																											171.64950561523438
																																																																																																																																																																											]
																																																																																																																																																																											},
																																																																																																																																																																											{
																																																																																																																																																																												"pointType": 2,
																																																																																																																																																																												"translation": [
																																																																																																																																																																												-34.8499870300293,
																																																																																																																																																																												179.14950561523438
																																																																																																																																																																												],
																																																																																																																																																																												"in": [
																																																																																																																																																																												-49.449989318847656,
																																																																																																																																																																												176.34950256347656
																																																																																																																																																																												],
																																																																																																																																																																												"out": [
																																																																																																																																																																												-34.8499870300293,
																																																																																																																																																																												179.14950561523438
																																																																																																																																																																												]
																																																																																																																																																																												},
																																																																																																																																																																												{
																																																																																																																																																																													"pointType": 2,
																																																																																																																																																																													"translation": [
																																																																																																																																																																													-29.148988723754883,
																																																																																																																																																																													207.34950256347656
																																																																																																																																																																													],
																																																																																																																																																																													"in": [
																																																																																																																																																																													-29.148988723754883,
																																																																																																																																																																													207.34950256347656
																																																																																																																																																																													],
																																																																																																																																																																													"out": [
																																																																																																																																																																													-26.648988723754883,
																																																																																																																																																																													219.74949645996094
																																																																																																																																																																													]
																																																																																																																																																																													},
																																																																																																																																																																													{
																																																																																																																																																																														"pointType": 2,
																																																																																																																																																																														"translation": [
																																																																																																																																																																														-2.548987865447998,
																																																																																																																																																																														227.34950256347656
																																																																																																																																																																														],
																																																																																																																																																																														"in": [
																																																																																																																																																																														-15.148987770080566,
																																																																																																																																																																														228.34950256347656
																																																																																																																																																																														],
																																																																																																																																																																														"out": [
																																																																																																																																																																														-2.548987865447998,
																																																																																																																																																																														227.34950256347656
																																																																																																																																																																														]
																																																																																																																																																																														},
																																																																																																																																																																														{
																																																																																																																																																																															"pointType": 2,
																																																																																																																																																																															"translation": [
																																																																																																																																																																															38.5510139465332,
																																																																																																																																																																															223.84950256347656
																																																																																																																																																																															],
																																																																																																																																																																															"in": [
																																																																																																																																																																															38.5510139465332,
																																																																																																																																																																															223.84950256347656
																																																																																																																																																																															],
																																																																																																																																																																															"out": [
																																																																																																																																																																															51.1510124206543,
																																																																																																																																																																															222.74949645996094
																																																																																																																																																																															]
																																																																																																																																																																															},
																																																																																																																																																																															{
																																																																																																																																																																																"pointType": 2,
																																																																																																																																																																																"translation": [
																																																																																																																																																																																61.451011657714844,
																																																																																																																																																																																199.74949645996094
																																																																																																																																																																																],
																																																																																																																																																																																"in": [
																																																																																																																																																																																61.0510139465332,
																																																																																																																																																																																212.45050048828125
																																																																																																																																																																																],
																																																																																																																																																																																"out": [
																																																																																																																																																																																61.451011657714844,
																																																																																																																																																																																199.74949645996094
																																																																																																																																																																																]
																																																																																																																																																																																},
																																																																																																																																																																																{
																																																																																																																																																																																	"pointType": 2,
																																																																																																																																																																																	"translation": [
																																																																																																																																																																																	62.35101318359375,
																																																																																																																																																																																	172.14849853515625
																																																																																																																																																																																	],
																																																																																																																																																																																	"in": [
																																																																																																																																																																																	62.35101318359375,
																																																																																																																																																																																	172.14849853515625
																																																																																																																																																																																	],
																																																																																																																																																																																	"out": [
																																																																																																																																																																																	77.35101318359375,
																																																																																																																																																																																	166.84849548339844
																																																																																																																																																																																	]
																																																																																																																																																																																	},
																																																																																																																																																																																	{
																																																																																																																																																																																		"pointType": 2,
																																																																																																																																																																																		"translation": [
																																																																																																																																																																																		104.65000915527344,
																																																																																																																																																																																		150.74949645996094
																																																																																																																																																																																		],
																																																																																																																																																																																		"in": [
																																																																																																																																																																																		91.55001068115234,
																																																																																																																																																																																		159.64849853515625
																																																																																																																																																																																		],
																																																																																																																																																																																		"out": [
																																																																																																																																																																																		104.65000915527344,
																																																																																																																																																																																		150.74949645996094
																																																																																																																																																																																		]
																																																																																																																																																																																		},
																																																																																																																																																																																		{
																																																																																																																																																																																			"pointType": 2,
																																																																																																																																																																																			"translation": [
																																																																																																																																																																																			127.35101318359375,
																																																																																																																																																																																			165.74949645996094
																																																																																																																																																																																			],
																																																																																																																																																																																			"in": [
																																																																																																																																																																																			127.35101318359375,
																																																																																																																																																																																			165.74949645996094
																																																																																																																																																																																			],
																																																																																																																																																																																			"out": [
																																																																																																																																																																																			137.95101928710938,
																																																																																																																																																																																			172.74949645996094
																																																																																																																																																																																			]
																																																																																																																																																																																			},
																																																																																																																																																																																			{
																																																																																																																																																																																				"pointType": 2,
																																																																																																																																																																																				"translation": [
																																																																																																																																																																																				160.25100708007812,
																																																																																																																																																																																				161.04949951171875
																																																																																																																																																																																				],
																																																																																																																																																																																				"in": [
																																																																																																																																																																																				152.05001831054688,
																																																																																																																																																																																				170.74949645996094
																																																																																																																																																																																				],
																																																																																																																																																																																				"out": [
																																																																																																																																																																																				160.25100708007812,
																																																																																																																																																																																				161.04949951171875
																																																																																																																																																																																				]
																																																																																																																																																																																				},
																																																																																																																																																																																				{
																																																																																																																																																																																					"pointType": 2,
																																																																																																																																																																																					"translation": [
																																																																																																																																																																																					186.85101318359375,
																																																																																																																																																																																					129.54949951171875
																																																																																																																																																																																					],
																																																																																																																																																																																					"in": [
																																																																																																																																																																																					186.85101318359375,
																																																																																																																																																																																					129.54949951171875
																																																																																																																																																																																					],
																																																																																																																																																																																					"out": [
																																																																																																																																																																																					195.05001831054688,
																																																																																																																																																																																					119.84950256347656
																																																																																																																																																																																					]
																																																																																																																																																																																					},
																																																																																																																																																																																					{
																																																																																																																																																																																						"pointType": 2,
																																																																																																																																																																																						"translation": [
																																																																																																																																																																																						185.95101928710938,
																																																																																																																																																																																						96.34950256347656
																																																																																																																																																																																						],
																																																																																																																																																																																						"in": [
																																																																																																																																																																																						194.65000915527344,
																																																																																																																																																																																						105.54949951171875
																																																																																																																																																																																						],
																																																																																																																																																																																						"out": [
																																																																																																																																																																																						185.95101928710938,
																																																																																																																																																																																						96.34950256347656
																																																																																																																																																																																						]
																																																																																																																																																																																						},
																																																																																																																																																																																						{
																																																																																																																																																																																							"pointType": 2,
																																																																																																																																																																																							"translation": [
																																																																																																																																																																																							167.65000915527344,
																																																																																																																																																																																							76.95050048828125
																																																																																																																																																																																							],
																																																																																																																																																																																							"in": [
																																																																																																																																																																																							167.65000915527344,
																																																																																																																																																																																							76.95050048828125
																																																																																																																																																																																							],
																																																																																																																																																																																							"out": [
																																																																																																																																																																																							174.35101318359375,
																																																																																																																																																																																							62.7504997253418
																																																																																																																																																																																							]
																																																																																																																																																																																							},
																																																																																																																																																																																							{
																																																																																																																																																																																								"pointType": 2,
																																																																																																																																																																																								"translation": [
																																																																																																																																																																																								182.05001831054688,
																																																																																																																																																																																								32.3494987487793
																																																																																																																																																																																								],
																																																																																																																																																																																								"in": [
																																																																																																																																																																																								179.25201416015625,
																																																																																																																																																																																								47.7504997253418
																																																																																																																																																																																								],
																																																																																																																																																																																								"out": [
																																																																																																																																																																																								182.05001831054688,
																																																																																																																																																																																								32.3494987487793
																																																																																																																																																																																								]
																																																																																																																																																																																								},
																																																																																																																																																																																								{
																																																																																																																																																																																									"pointType": 2,
																																																																																																																																																																																									"translation": [
																																																																																																																																																																																									207.05001831054688,
																																																																																																																																																																																									27.249500274658203
																																																																																																																																																																																									],
																																																																																																																																																																																									"in": [
																																																																																																																																																																																									207.05001831054688,
																																																																																																																																																																																									27.249500274658203
																																																																																																																																																																																									],
																																																																																																																																																																																									"out": [
																																																																																																																																																																																									219.45001220703125,
																																																																																																																																																																																									24.749500274658203
																																																																																																																																																																																									]
																																																																																																																																																																																									},
																																																																																																																																																																																									{
																																																																																																																																																																																										"pointType": 2,
																																																																																																																																																																																										"translation": [
																																																																																																																																																																																										227.05001831054688,
																																																																																																																																																																																										0.6485000252723694
																																																																																																																																																																																										],
																																																																																																																																																																																										"in": [
																																																																																																																																																																																										228.05001831054688,
																																																																																																																																																																																										13.249500274658203
																																																																																																																																																																																										],
																																																																																																																																																																																										"out": [
																																																																																																																																																																																										227.05001831054688,
																																																																																																																																																																																										0.6485000252723694
																																																																																																																																																																																										]
																																																																																																																																																																																										},
																																																																																																																																																																																										{
																																																																																																																																																																																											"pointType": 2,
																																																																																																																																																																																											"translation": [
																																																																																																																																																																																											223.55001831054688,
																																																																																																																																																																																											-40.451499938964844
																																																																																																																																																																																											],
																																																																																																																																																																																											"in": [
																																																																																																																																																																																											223.55001831054688,
																																																																																																																																																																																											-40.451499938964844
																																																																																																																																																																																											],
																																																																																																																																																																																											"out": [
																																																																																																																																																																																											222.45001220703125,
																																																																																																																																																																																											-53.05149841308594
																																																																																																																																																																																											]
																																																																																																																																																																																											},
																																																																																																																																																																																											{
																																																																																																																																																																																												"pointType": 2,
																																																																																																																																																																																												"translation": [
																																																																																																																																																																																												199.45001220703125,
																																																																																																																																																																																												-63.35150146484375
																																																																																																																																																																																												],
																																																																																																																																																																																												"in": [
																																																																																																																																																																																												212.15000915527344,
																																																																																																																																																																																												-62.951499938964844
																																																																																																																																																																																												],
																																																																																																																																																																																												"out": [
																																																																																																																																																																																												199.45001220703125,
																																																																																																																																																																																												-63.35150146484375
																																																																																																																																																																																												]
																																																																																																																																																																																												},
																																																																																																																																																																																												{
																																																																																																																																																																																													"pointType": 2,
																																																																																																																																																																																													"translation": [
																																																																																																																																																																																													174.35000610351562,
																																																																																																																																																																																													-64.15149688720703
																																																																																																																																																																																													],
																																																																																																																																																																																													"in": [
																																																																																																																																																																																													174.35000610351562,
																																																																																																																																																																																													-64.15149688720703
																																																																																																																																																																																													],
																																																																																																																																																																																													"out": [
																																																																																																																																																																																													169.14901733398438,
																																																																																																																																																																																													-78.75150299072266
																																																																																																																																																																																													]
																																																																																																																																																																																													},
																																																																																																																																																																																													{
																																																																																																																																																																																														"pointType": 2,
																																																																																																																																																																																														"translation": [
																																																																																																																																																																																														153.45001220703125,
																																																																																																																																																																																														-105.35150146484375
																																																																																																																																																																																														],
																																																																																																																																																																																														"in": [
																																																																																																																																																																																														162.14901733398438,
																																																																																																																																																																																														-92.55049896240234
																																																																																																																																																																																														],
																																																																																																																																																																																														"out": [
																																																																																																																																																																																														153.45001220703125,
																																																																																																																																																																																														-105.35150146484375
																																																																																																																																																																																														]
																																																																																																																																																																																														},
																																																																																																																																																																																														{
																																																																																																																																																																																															"pointType": 2,
																																																																																																																																																																																															"translation": [
																																																																																																																																																																																															167.14901733398438,
																																																																																																																																																																																															-125.95149993896484
																																																																																																																																																																																															],
																																																																																																																																																																																															"in": [
																																																																																																																																																																																															167.14901733398438,
																																																																																																																																																																																															-125.95149993896484
																																																																																																																																																																																															],
																																																																																																																																																																																															"out": [
																																																																																																																																																																																															174.35000610351562,
																																																																																																																																																																																															-136.54949951171875
																																																																																																																																																																																															]
																																																																																																																																																																																														}
																																																																																																																																																																																														],
																																																																																																																																																																																														"type": "path"
																																																																																																																																																																																														},
																																																																																																																																																																																														{
																																																																																																																																																																																															"name": "Path",
																																																																																																																																																																																															"parent": 6,
																																																																																																																																																																																															"translation": [
																																																																																																																																																																																															0.4749755859375,
																																																																																																																																																																																															-0.92523193359375
																																																																																																																																																																																															],
																																																																																																																																																																																															"rotation": 0,
																																																																																																																																																																																															"scale": [
																																																																																																																																																																																															1,
																																																																																																																																																																																															1
																																																																																																																																																																																															],
																																																																																																																																																																																															"opacity": 1,
																																																																																																																																																																																															"isCollapsed": false,
																																																																																																																																																																																															"clips": [],
																																																																																																																																																																																															"bones": [],
																																																																																																																																																																																															"isVisible": true,
																																																																																																																																																																																															"isClosed": true,
																																																																																																																																																																																															"points": [
																																																																																																																																																																																															{
																																																																																																																																																																																																"pointType": 2,
																																																																																																																																																																																																"translation": [
																																																																																																																																																																																																6.801000118255615,
																																																																																																																																																																																																80.5000228881836
																																																																																																																																																																																																],
																																																																																																																																																																																																"in": [
																																																																																																																																																																																																51.19900131225586,
																																																																																																																																																																																																76.801025390625
																																																																																																																																																																																																],
																																																																																																																																																																																																"out": [
																																																																																																																																																																																																-37.5989990234375,
																																																																																																																																																																																																84.301025390625
																																																																																																																																																																																																]
																																																																																																																																																																																																},
																																																																																																																																																																																																{
																																																																																																																																																																																																	"pointType": 2,
																																																																																																																																																																																																	"translation": [
																																																																																																																																																																																																	-80.5,
																																																																																																																																																																																																	6.801024913787842
																																																																																																																																																																																																	],
																																																																																																																																																																																																	"in": [
																																																																																																																																																																																																	-76.8010025024414,
																																																																																																																																																																																																	51.20002365112305
																																																																																																																																																																																																	],
																																																																																																																																																																																																	"out": [
																																																																																																																																																																																																	-84.3010025024414,
																																																																																																																																																																																																	-37.598976135253906
																																																																																																																																																																																																	]
																																																																																																																																																																																																	},
																																																																																																																																																																																																	{
																																																																																																																																																																																																		"pointType": 2,
																																																																																																																																																																																																		"translation": [
																																																																																																																																																																																																		-6.801000118255615,
																																																																																																																																																																																																		-80.4999771118164
																																																																																																																																																																																																		],
																																																																																																																																																																																																		"in": [
																																																																																																																																																																																																		-51.19900131225586,
																																																																																																																																																																																																		-76.79997253417969
																																																																																																																																																																																																		],
																																																																																																																																																																																																		"out": [
																																																																																																																																																																																																		37.5989990234375,
																																																																																																																																																																																																		-84.29997253417969
																																																																																																																																																																																																		]
																																																																																																																																																																																																		},
																																																																																																																																																																																																		{
																																																																																																																																																																																																			"pointType": 2,
																																																																																																																																																																																																			"translation": [
																																																																																																																																																																																																			80.5,
																																																																																																																																																																																																			-6.7999749183654785
																																																																																																																																																																																																			],
																																																																																																																																																																																																			"in": [
																																																																																																																																																																																																			76.8010025024414,
																																																																																																																																																																																																			-51.198974609375
																																																																																																																																																																																																			],
																																																																																																																																																																																																			"out": [
																																																																																																																																																																																																			84.3010025024414,
																																																																																																																																																																																																			37.60102462768555
																																																																																																																																																																																																			]
																																																																																																																																																																																																		}
																																																																																																																																																																																																		],
																																																																																																																																																																																																		"type": "path"
																																																																																																																																																																																																	}
																																																																																																																																																																																																	],
																																																																																																																																																																																																	"animations": [
																																																																																																																																																																																																	{
																																																																																																																																																																																																		"name": "Loading 2",
																																																																																																																																																																																																		"fps": 60,
																																																																																																																																																																																																		"duration": 1,
																																																																																																																																																																																																		"isLooping": true,
																																																																																																																																																																																																		"keyed": [
																																																																																																																																																																																																		{
																																																																																																																																																																																																			"component": 2,
																																																																																																																																																																																																			"rotation": [
																																																																																																																																																																																																			[
																																																																																																																																																																																																			{
																																																																																																																																																																																																				"time": 0,
																																																																																																																																																																																																				"interpolatorType": 1,
																																																																																																																																																																																																				"value": 0
																																																																																																																																																																																																				},
																																																																																																																																																																																																				{
																																																																																																																																																																																																					"time": 1,
																																																																																																																																																																																																					"interpolatorType": 1,
																																																																																																																																																																																																					"value": 0.7853981633974483
																																																																																																																																																																																																				}
																																																																																																																																																																																																				]
																																																																																																																																																																																																				],
																																																																																																																																																																																																				"posY": [
																																																																																																																																																																																																				[
																																																																																																																																																																																																				{
																																																																																																																																																																																																					"time": 1,
																																																																																																																																																																																																					"interpolatorType": 1,
																																																																																																																																																																																																					"value": 361.2919921875
																																																																																																																																																																																																				}
																																																																																																																																																																																																				]
																																																																																																																																																																																																				],
																																																																																																																																																																																																				"posX": [
																																																																																																																																																																																																				[
																																																																																																																																																																																																				{
																																																																																																																																																																																																					"time": 1,
																																																																																																																																																																																																					"interpolatorType": 1,
																																																																																																																																																																																																					"value": 261.7046813964844
																																																																																																																																																																																																				}
																																																																																																																																																																																																				]
																																																																																																																																																																																																				]
																																																																																																																																																																																																				},
																																																																																																																																																																																																				{
																																																																																																																																																																																																					"component": 6,
																																																																																																																																																																																																					"rotation": [
																																																																																																																																																																																																					[
																																																																																																																																																																																																					{
																																																																																																																																																																																																						"time": 0,
																																																																																																																																																																																																						"interpolatorType": 1,
																																																																																																																																																																																																						"value": 0
																																																																																																																																																																																																						},
																																																																																																																																																																																																						{
																																																																																																																																																																																																							"time": 1,
																																																																																																																																																																																																							"interpolatorType": 1,
																																																																																																																																																																																																							"value": 0.7853981633974483
																																																																																																																																																																																																						}
																																																																																																																																																																																																						]
																																																																																																																																																																																																						],
																																																																																																																																																																																																						"posX": [
																																																																																																																																																																																																						[
																																																																																																																																																																																																						{
																																																																																																																																																																																																							"time": 1,
																																																																																																																																																																																																							"interpolatorType": 1,
																																																																																																																																																																																																							"value": 675.9214477539062
																																																																																																																																																																																																						}
																																																																																																																																																																																																						]
																																																																																																																																																																																																						]
																																																																																																																																																																																																					}
																																																																																																																																																																																																					],
																																																																																																																																																																																																					"animationStart": 0,
																																																																																																																																																																																																					"animationEnd": 1,
																																																																																																																																																																																																					"type": "animation"
																																																																																																																																																																																																					},
																																																																																																																																																																																																					{
																																																																																																																																																																																																						"name": "Loading 1",
																																																																																																																																																																																																						"fps": 60,
																																																																																																																																																																																																						"duration": 1,
																																																																																																																																																																																																						"isLooping": true,
																																																																																																																																																																																																						"keyed": [
																																																																																																																																																																																																						{
																																																																																																																																																																																																							"component": 2,
																																																																																																																																																																																																							"rotation": [
																																																																																																																																																																																																							[
																																																																																																																																																																																																							{
																																																																																																																																																																																																								"time": 0,
																																																																																																																																																																																																								"interpolatorType": 1,
																																																																																																																																																																																																								"value": 0
																																																																																																																																																																																																								},
																																																																																																																																																																																																								{
																																																																																																																																																																																																									"time": 1,
																																																																																																																																																																																																									"interpolatorType": 1,
																																																																																																																																																																																																									"value": 0.7853981633974483
																																																																																																																																																																																																								}
																																																																																																																																																																																																								]
																																																																																																																																																																																																								],
																																																																																																																																																																																																								"posY": [
																																																																																																																																																																																																								[
																																																																																																																																																																																																								{
																																																																																																																																																																																																									"time": 1,
																																																																																																																																																																																																									"interpolatorType": 1,
																																																																																																																																																																																																									"value": 361.2919921875
																																																																																																																																																																																																								}
																																																																																																																																																																																																								]
																																																																																																																																																																																																								],
																																																																																																																																																																																																								"posX": [
																																																																																																																																																																																																								[
																																																																																																																																																																																																								{
																																																																																																																																																																																																									"time": 1,
																																																																																																																																																																																																									"interpolatorType": 1,
																																																																																																																																																																																																									"value": 261.7046813964844
																																																																																																																																																																																																								}
																																																																																																																																																																																																								]
																																																																																																																																																																																																								]
																																																																																																																																																																																																								},
																																																																																																																																																																																																								{
																																																																																																																																																																																																									"component": 6,
																																																																																																																																																																																																									"rotation": [
																																																																																																																																																																																																									[
																																																																																																																																																																																																									{
																																																																																																																																																																																																										"time": 0,
																																																																																																																																																																																																										"interpolatorType": 1,
																																																																																																																																																																																																										"value": 0
																																																																																																																																																																																																										},
																																																																																																																																																																																																										{
																																																																																																																																																																																																											"time": 1,
																																																																																																																																																																																																											"interpolatorType": 1,
																																																																																																																																																																																																											"value": 0.7853981633974483
																																																																																																																																																																																																										}
																																																																																																																																																																																																										]
																																																																																																																																																																																																										],
																																																																																																																																																																																																										"posX": [
																																																																																																																																																																																																										[
																																																																																																																																																																																																										{
																																																																																																																																																																																																											"time": 1,
																																																																																																																																																																																																											"interpolatorType": 1,
																																																																																																																																																																																																											"value": 675.9214477539062
																																																																																																																																																																																																										}
																																																																																																																																																																																																										]
																																																																																																																																																																																																										]
																																																																																																																																																																																																									}
																																																																																																																																																																																																									],
																																																																																																																																																																																																									"animationStart": 0,
																																																																																																																																																																																																									"animationEnd": 1,
																																																																																																																																																																																																									"type": "animation"
																																																																																																																																																																																																								}
																																																																																																																																																																																																								],
																																																																																																																																																																																																								"type": "artboard"
																																																																																																																																																																																																							}
																																																																																																																																																																																																							]
																																																																																																																																																																																																						}';
	}
}




function wl_development_mode()
{

	$param = $_GET;

	switch ($param['action']) {
		case 'add':
			return true;
			break;
		case 'remove':
			return true;
			break;
		case 'check':
			return false;
			break;
	}
}




// HTML tags
function mobile_html_tags()
{
	global $allowedposttags, $allowedtags;
	$allowedtags['img'] = array('src' => true);
	$allowedtags['a'] = array('href' => true);
	$allowedtags['br'] = array();
	$allowedtags['ul'] = array();
	$allowedtags['ol'] = array();
	$allowedtags['li'] = array();
	$allowedtags['dl'] = array();
	$allowedtags['dt'] = array();
	$allowedtags['dd'] = array();
	$allowedtags['table'] = array();
	$allowedtags['td'] = array();
	$allowedtags['tr'] = array();
	$allowedtags['th'] = array();
	$allowedtags['thead'] = array();
	$allowedtags['tbody'] = array();
	$allowedtags['h1'] = array();
	$allowedtags['h2'] = array();
	$allowedtags['h3'] = array();
	$allowedtags['h4'] = array();
	$allowedtags['h5'] = array();
	$allowedtags['h6'] = array();
	$allowedtags['cite'] = array();
	$allowedtags['em'] = array();
	$allowedtags['address'] = array();
	$allowedtags['big'] = array();
	$allowedtags['ins'] = array();
	$allowedtags['span'] = array();
	$allowedtags['sub'] = array();
	$allowedtags['sup'] = array();
	$allowedtags['tt'] = array();
	$allowedtags['var'] = array();
	$allowedtags['p'] = array();
	$allowedtags['blockquote'] = array();
	$allowedtags['figure'] = array();
	$allowedtags['figcaption'] = array();
	$allowedtags['caption'] = array();
	// $allowedtags['iframe'] = array('src' => true);
	$allowedtags['dropcap'] = array();
	$allowedtags['tie_full_img'] = array();
	$allowedposttags['blockquote'] = array();
	// $allowedposttags['iframe'] = array('src' => true);
	$allowedposttags['br'] = array();
	$allowedposttags['p'] = array();
	$allowedposttags['img'] = array('src' => true);
	$allowedposttags['a'] = array('href' => true);
	$allowedposttags['tie_full_img'] = array();
	$allowedposttags['figure'] = array();
	$allowedposttags['figcaption'] = array();
	$allowedposttags['caption'] = array();
	$allowedposttags['dropcap'] = array();
	$allowedposttags['tie_list'] = array();
	error_log(print_r($allowedposttags, true));
	error_log(print_r($allowedtags, true));
}

// Kses stip
function mobile_kses_stip($value)
{
	$value = str_ireplace(array("\n"), "<br>", $value);
	$value = str_ireplace(array("<!-- wp:paragraph -->", "<!-- /wp:paragraph -->"), "", $value);
	$value = mobile_deslash($value);
	$value = replace_shortcodes($value);
	return wp_kses($value, mobile_html_tags());
}

function replace_shortcodes($value)
{
	$value = str_replace(array("[", "]"), array("<", ">"), $value);
	return $value;
}

function html_styling($value)
{
	$last_tag_opened = "";
	$last_tag_closed = "";

	$value = str_replace(
		array(
			"<p>",
			"<li>",
			"<ul>",
			"<blockquote>",
			"<ol>",
			"<figure>",
			"<h1>",
			"<h2>",
			"<h3>",
			"<h4>",
			"<h5>",
			"<h6>",
			"<img>",
			"<cite>",
			"<caption>",
			"<figcaption>",
			"<fig>",
		),
		array(
			"<p >",
			"<li >",
			"<ul >",
			"<blockquote >",
			"<ol >",
			"<figure >",
			"<h1 >",
			"<h2 >",
			"<h3 >",
			"<h4 >",
			"<h5 >",
			"<h6 >",
			"<img >",
			"<cite >",
			"<caption >",
			"<figcaption >",
			"<dropcap >",
		),
		$value
	);


	$pattern =
		array(
			'<a ',
			'<abbr ',
			'<address ',
			'<area ',
			'<article ',
			'<aside ',
			'<audio ',
			'<b ',
			'<base ',
			'<bdi ',
			'<bdo ',
			'<blockquote',
			'<body ',
			'<br ',
			'<button ',
			'<canvas ',
			'<caption ',
			'<figcaption ',
			'<cite ',
			'<code ',
			'<col ',
			'<colgroup ',
			'<data ',
			'<datalist ',
			'<dd ',
			'<del ',
			'<details ',
			'<dfn ',
			'<dialog ',
			'<div ',
			'<dl ',
			'<dt ',
			'<em ',
			'<embed ',
			'<fieldset ',
			'<figure ',
			'<footer ',
			'<form ',
			'<h1 ',
			'<h2 ',
			'<h3 ',
			'<h4 ',
			'<h5 ',
			'<h6 ',
			'<head ',
			'<header ',
			'<hgroup ',
			'<hr ',
			'<html ',
			'<i ',
			'<iframe ',
			'<img ',
			'<input ',
			'<ins ',
			'<kbd ',
			'<keygen ',
			'<label ',
			'<legend ',
			'<li ',
			'<main ',
			'<map ',
			'<mark ',
			'<menu ',
			'<menuitem ',
			'<meta ',
			'<meter ',
			'<nav ',
			'<noscript ',
			'<object ',
			'<ol ',
			'<optgroup ',
			'<option ',
			'<output ',
			'<p ',
			'<param ',
			'<pre ',
			'<progress ',
			'<q ',
			'<rb ',
			'<rp ',
			'<rt ',
			'<rtc ',
			'<ruby ',
			'<s ',
			'<samp ',
			'<script ',
			'<section ',
			'<select ',
			'<small ',
			'<source ',
			'<span ',
			'<strong ',
			'<style ',
			'<sub ',
			'<summary ',
			'<sup ',
			'<table ',
			'<tbody ',
			'<td ',
			'<template ',
			'<textarea ',
			'<tfoot ',
			'<th ',
			'<thead ',
			'<time ',
			'<title ',
			'<tr ',
			'<track ',
			'<u ',
			'<ul ',
			'<var ',
			'<video ',
			'<wbr ',
			//Short codes
			'<dropcap'
		);
	$replacement =
		array(
			'<a style="text-decoration: none;" ',
			'<abbr style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<address style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<area style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<article style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<aside style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<audio style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<b style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<base style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<bdi style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<bdo style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',

			'<blockquote style="font-size: 21px; line-height: 26px; font-weight: 600; margin-top: 24px; margin-bottom: 24px; margin-left: 0; margin-right: 0;" ',

			'<body style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<br style="margin-bottom:5px" ',
			'<button style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<canvas style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',

			'<caption style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 400; font-size: 13px; line-height: 19px; margin-top: 5px; margin-bottom: 20px; font-style: italic;" ',

			'<figcaption style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 400; font-size: 11px; line-height: 19px; margin-top: 7px; margin-bottom: 0px; font-style: italic;" ',

			'<cite style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 400; font-size: 13px; line-height: 19px; display: block; clear: both; margin-top: 6px;text-align: center;" ',


			'<code style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<col style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<colgroup style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<data style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<datalist style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<dd style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<del style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<details style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<dfn style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<dialog style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<div style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 400; font-size: 15px; line-height: 40px; margin-bottom: 24px;" ',
			'<dl style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<dt style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<em style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<embed style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<fieldset style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',

			'<figure style="margin-top: 24px; margin-left: 0px; margin-right: 0px; margin-bottom: 24px;" ',

			'<footer style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<form style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',

			'<h1 style="font-family: ' . get_option('appbear-settings')['section-typography-font-h1-size'] . ';font-size: ' . get_option('appbear-settings')['section-typography-font-h1-size'] . '; line-height: ' . get_option('appbear-settings')['section-typography-font-h1-line_height'] . '; font-weight: ' . get_option('appbear-settings')['section-typography-font-h1-weight'] . '; margin-bottom: 1px;" ',
			'<h2 style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-heading'] . ';font-size: ' . get_option('appbear-settings')['section-typography-font-h2-size'] . '; line-height: ' . get_option('appbear-settings')['section-typography-font-h2-line_height'] . '; font-weight: ' . get_option('appbear-settings')['section-typography-font-h2-weight'] . '; margin-bottom: 1px;" ',
			'<h3 style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-heading'] . ';font-size: ' . get_option('appbear-settings')['section-typography-font-h3-size'] . '; line-height: ' . get_option('appbear-settings')['section-typography-font-h3-line_height'] . '; font-weight: ' . get_option('appbear-settings')['section-typography-font-h3-weight'] . '; margin-bottom: 1px;" ',

			'<h4 style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-heading'] . ';font-size: ' . get_option('appbear-settings')['section-typography-font-h4-size'] . '; line-height: ' . get_option('appbear-settings')['section-typography-font-h4-line_height'] . '; font-weight: ' . get_option('appbear-settings')['section-typography-font-h4-weight'] . '; margin-bottom: 1px;" ',
			'<h5 style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-heading'] . ';font-size: ' . get_option('appbear-settings')['section-typography-font-h5-size'] . '; line-height: ' . get_option('appbear-settings')['section-typography-font-h5-line_height'] . '; font-weight: ' . get_option('appbear-settings')['section-typography-font-h5-weight'] . '; margin-bottom: 1px;" ',
			'<h6 style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-heading'] . ';font-size: ' . get_option('appbear-settings')['section-typography-font-h6-size'] . '; line-height: ' . get_option('appbear-settings')['section-typography-font-h6-line_height'] . '; font-weight: ' . get_option('appbear-settings')['section-typography-font-h6-weight'] . '; margin-bottom: 1px;" ',


			'<head style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<header style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<hgroup style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<hr style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<html style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<i style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<iframe style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px; margin-bottom: 20px" ',
			'<img style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px; margin-bottom: 20px" ',
			'<input style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<ins style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<kbd style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<keygen style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<label style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<legend style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',

			'<li style="display: block; list-style: disc; list-style-image: none; margin-bottom: 10px; font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 400; font-size: 13px; line-height: 28px;" ',

			'<main style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<map style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<mark style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<menu style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<menuitem style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<meta style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<meter style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<nav style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<noscript style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<object style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<ol style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<optgroup style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<option style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<output style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',

			'<p style="display:block; font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 400; font-size: 15px; line-height: 40px; margin-bottom: 24px;" ',

			'<param style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<pre style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<progress style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<q style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<rb style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<rp style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<rt style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<rtc style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<ruby style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<s style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<samp style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<script style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<section style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<select style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<small style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<source style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<span style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<strong style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<style style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<sub style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<summary style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<sup style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<table style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<tbody style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<td style="line-height: 1.4;" ',
			'<template style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<textarea style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<tfoot style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<th style="line-height: 1.4; font-weight: 700;" ',
			'<thead style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<time style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<title style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<tr style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<track style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<u style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',

			'<ul style="display: block; margin-bottom: 24px;" ',

			'<var style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<video style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',
			'<wbr style="font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 16px; line-height: 24px;" ',

			//Short codes
			'<dropcap style="display: inline-block; font-family: ' . get_option('appbear-settings')['section-typography-fontfamily-body'] . '; font-weight: 600; font-size: 25px; line-height: 20px; margin-right: 2px; padding-top: 20px;" '
		);
	$value = str_ireplace($pattern, $replacement, $value);


	return $value;
}

// deslash
function mobile_deslash($content)
{
	$content = preg_replace("/\\\+'/", "'", $content);
	$content = preg_replace('/\\\+"/', '"', $content);
	return $content;
}
