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
	 * do_posts
	 *
	 * @access public
	 * @since 1.0
	 * @return array()
	 */
	public function do_posts( $request ) {

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

		// Related
		if ( isset($request['related_id'] ) ) {
			$args['category__in'] = wp_get_post_categories( $request['related_id'] );

			//$tags_IDs = wp_get_post_tags($request['related_id'], array('fields' => 'ids'));
			//$args['tag__in'] = $tags;
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

					$this_post = $this->get_post_data();

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
	 * get_post_data
	 *
	 * @access public
	 * @since 1.0
	 * @return array()
	 */
	public function get_post_data(){

		$post_id = get_the_ID();

		$the_post = array(
			'id'            => $post_id,
			//'slug'					= $post->post_name;
			'share'         => get_permalink(),
			'url'           => get_permalink(),
			'status'        => get_post_status(),
			'title'         => get_the_title(),
			'title_plain'   => the_title_attribute('echo=0'),
			'excerpt'       => get_the_excerpt(),
			'date'          => appbear_get_time(),
			'modified'      => get_post_modified_time(),
			'comment_count' => (int) get_comments_number(),
			'read_time'     => '1 min read',
			'author'        => array(
				'name' => get_the_author(),
			),
		);

		// Post Format
		$format = appbear_post_format();

		$the_post['post'] = $format; // change it later to format instead of post

		if( $format == 'gallery' ){
			$the_post['gallery'] = appbear_post_gallery();
		}
		elseif( $format == 'video' ){
			$the_post['video'] = appbear_post_video();
		}

		// --- Featured Image
		$thumbnail = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
		$the_post['thumbnail'] = $thumbnail;
		$the_post['featured_image'] = array(
			'thumbnail' => $thumbnail,
			'medium'    => get_the_post_thumbnail_url( $post_id, 'medium' ),
			'large'     => get_the_post_thumbnail_url( $post_id, 'large' ),
		);

		return apply_filters( 'AppBear/API/Post/data', $the_post );
	}



	/**
	 * do_post
	 *
	 * @access public
	 * @since 1.0
	 * @return array()
	 */
	public function do_post( $request ) {

		// TODO - output json error
		if( empty( $request['id'] ) ){
			return false;
		}

		// Get the Post Object
		global $post;

		$post = get_post( $request['id'] );

		if( ! $post ){
			return false;
		}

		setup_postdata( $post );

		$this_post = $this->get_post_data();


		$get_comments = get_comments(	array(
			'post_id' => $post->ID,
			//'hierarchical' => 'threaded' // Get threaded
		) );

		$this_post['content']       = appbear_shortcodes_parsing($post->post_content);
		$this_post['tags']          = ((get_the_tags($post->ID) == false) ? array() : get_the_tags($post->ID));
		$this_post['category']      = get_the_category($post->ID);
		$this_post['related_posts'] = array();


		// -----
		$comments = array();

		foreach ( $get_comments as $comment ) {

			if ( $comment->comment_parent == 0 ) {

				// Set the avatar
				$comment->author_avatar = get_avatar_url( $comment->comment_author_email );

				// Get Child replies
				$child_comments = get_comments( array( 'post_id' => $post->ID, 'parent' => $comment->comment_ID ) );
				$replies = array();
				foreach ( $child_comments as $reply ) {
					$reply->author_avatar = get_avatar_url( $reply->comment_author_email );
					$replies[] = $reply;
				}
				$comment->replies = $replies;

				// --
				$comments[] = $comment;
			}
		}

		$this_post['comments'] = $comments;


		$this_post['author'] = get_the_author(); //// Need to be changed @fouad

		// ------
		return array( 'post' => $this_post );
	}



	/**
	 * do_get_version
	 *
	 * @access public
	 * @since 1.0
	 * @return array()
	 */
	public function do_get_version( $request ) {
		return array(
			'version' => get_option('appbear_version')
		);
	}


	/**
	 * do_register
	 * Need some checks before wp_create_user.
	 *
	 * @access public
	 * @since 1.0
	 * @return array()
	 */
	public function do_register( $request ) {

		$data = wp_create_user($request['username'], $request['user_pass'], $request['email'] );
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
			$response["token"]= $request['device_token'];
			$response["user"]=$user->data;
		}

		return $response;
	}


	/**
	 * do_login()
	 * Needs some tests
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function do_login( $request ) {

		$credentials = array();
		$token = $request['device_token'];
		$credentials['user_login'] = $request['username'];
		$credentials['user_password'] = $request['password'];
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


	/*
	function do_comments(){
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



	function do_tabs()
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

*/


	/**
	 * do_categories()
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	function do_categories() {

		$categories = get_categories( array(
			'orderby' => 'name',
			'order'   => 'ASC'
		) );

		$the_cats = array();

		foreach ( $categories as $category ) {
			$category->url = "wp-json/wl/v1/posts?category_id=" . $category->term_id;
			$category->image_url = get_term_meta( $category->term_id, 'cat_image', true );
			$the_cats[] = $category;
		}

		$data = array(
			'status'     => true,
			'categories' => $the_cats
		);

		return $data;
	}






	/**
	 * do_page()
	 *
	 * @access public
	 * @since 1.0
	 * @return array()
	 */
	function do_page( $request ) {

		if( ! empty( $request['id'] ) ){

			$page = get_post( $request['id'] );

			if( $page ){
				return apply_filters( 'AppBear/API/Page/data', array(
					'status' => true,
					'id'     => $page->ID,
					'post' => array(
						'slug'    => $page->post_name,
						'title'   => $page->post_title,
						'content' => appbear_shortcodes_parsing( $page->post_content ),
					),
				) );
			}
		}

		return array(
			'status'=> false,
		);
	}


	// A lot of work need to be done here
	function do_add_comment(){
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



	// Pure SHIT
	function do_contact_us() {
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





	function do_language()
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

	function do_options()
	{
		return get_option('appbear-settings');
	}

	function do_translations()
	{
		return get_option('appbear-language');
	}

	function do_translations_ar()
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





	function do_svg( $request ) {

		if( ! empty( $request['type'] ) ){
			return;
		}

		$styling = appbear_get_option( 'styling' );
		$skin    = appbear_get_option( 'themeMode', 'ThemeMode_light' );

		$color1 = '#' . ! empty( $styling[ $skin ]['primary'] ) ? $styling[ $skin ]['primary'] : '000';
		$color2 = '#' . ! empty( $styling[ $skin ]['secondaryVariant'] ) ? $styling[ $skin ]['secondaryVariant'] : '000';

		header('Content-type: image/svg+xml');

		if ( $request['type'] == 'loading' ) {
			echo '
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="800px" height="800px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
					<circle cx="75" cy="50" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.9166666666666666s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.9166666666666666s"></animate>
					</circle>
					<circle cx="71.65063509461098" cy="62.5" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.8333333333333334s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.8333333333333334s"></animate>
					</circle>
					<circle cx="62.5" cy="71.65063509461096" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.75s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.75s"></animate>
					</circle>
					<circle cx="50" cy="75" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.6666666666666666s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.6666666666666666s"></animate>
					</circle>
					<circle cx="37.50000000000001" cy="71.65063509461098" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.5833333333333334s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.5833333333333334s"></animate>
					</circle>
					<circle cx="28.34936490538903" cy="62.5" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.5s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.5s"></animate>
					</circle>
					<circle cx="25" cy="50" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.4166666666666667s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.4166666666666667s"></animate>
					</circle>
					<circle cx="28.34936490538903" cy="37.50000000000001" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.3333333333333333s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.3333333333333333s"></animate>
					</circle>
					<circle cx="37.499999999999986" cy="28.349364905389038" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.25s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.25s"></animate>
					</circle>
					<circle cx="49.99999999999999" cy="25" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.16666666666666666s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.16666666666666666s"></animate>
					</circle>
					<circle cx="62.5" cy="28.349364905389034" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="-0.08333333333333333s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="-0.08333333333333333s"></animate>
					</circle>
					<circle cx="71.65063509461096" cy="37.499999999999986" fill="' . $color2 . '" r="5">
						<animate attributeName="r" values="3;3;5;3;3" times="0;0.1;0.2;0.3;1" dur="1s" repeatCount="indefinite" begin="0s"></animate>
						<animate attributeName="fill" values="' . $color2 . ';' . $color2 . ';' . $color1 . ';' . $color2 . ';' . $color2 . '" repeatCount="indefinite" times="0;0.1;0.2;0.3;1" dur="1s" begin="0s"></animate>
					</circle>
				</svg>
			';
		}
		elseif ( $request['type'] == 'settings' ) {
			echo '
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="800px" height="800px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
					<g transform="translate(50 50)">  <g transform="translate(-19 -19) scale(0.6)"> <g>
					<animateTransform attributeName="transform" type="rotate" values="0;45" keyTimes="0;1" dur="1.7857142857142856s" begin="0s" repeatCount="indefinite"></animateTransform><path d="M31.359972760794346 21.46047782418268 L38.431040572659825 28.531545636048154 L28.531545636048154 38.431040572659825 L21.46047782418268 31.359972760794346 A38 38 0 0 1 7.0000000000000036 37.3496987939662 L7.0000000000000036 37.3496987939662 L7.000000000000004 47.3496987939662 L-6.999999999999999 47.3496987939662 L-7 37.3496987939662 A38 38 0 0 1 -21.46047782418268 31.35997276079435 L-21.46047782418268 31.35997276079435 L-28.531545636048154 38.431040572659825 L-38.43104057265982 28.531545636048158 L-31.359972760794346 21.460477824182682 A38 38 0 0 1 -37.3496987939662 7.000000000000007 L-37.3496987939662 7.000000000000007 L-47.3496987939662 7.000000000000008 L-47.3496987939662 -6.9999999999999964 L-37.3496987939662 -6.999999999999997 A38 38 0 0 1 -31.35997276079435 -21.460477824182675 L-31.35997276079435 -21.460477824182675 L-38.431040572659825 -28.531545636048147 L-28.53154563604818 -38.4310405726598 L-21.4604778241827 -31.35997276079433 A38 38 0 0 1 -6.999999999999992 -37.3496987939662 L-6.999999999999992 -37.3496987939662 L-6.999999999999994 -47.3496987939662 L6.999999999999977 -47.3496987939662 L6.999999999999979 -37.3496987939662 A38 38 0 0 1 21.460477824182686 -31.359972760794342 L21.460477824182686 -31.359972760794342 L28.531545636048158 -38.43104057265982 L38.4310405726598 -28.53154563604818 L31.35997276079433 -21.4604778241827 A38 38 0 0 1 37.3496987939662 -6.999999999999995 L37.3496987939662 -6.999999999999995 L47.3496987939662 -6.999999999999997 L47.349698793966205 6.999999999999973 L37.349698793966205 6.999999999999976 A38 38 0 0 1 31.359972760794346 21.460477824182686 M0 -23A23 23 0 1 0 0 23 A23 23 0 1 0 0 -23" fill="' . $color1 . '"></path></g></g> <g transform="translate(19 19) scale(0.6)"> <g>
					<animateTransform attributeName="transform" type="rotate" values="45;0" keyTimes="0;1" dur="1.7857142857142856s" begin="-0.8928571428571428s" repeatCount="indefinite"></animateTransform><path d="M-31.35997276079435 -21.460477824182675 L-38.431040572659825 -28.531545636048147 L-28.53154563604818 -38.4310405726598 L-21.4604778241827 -31.35997276079433 A38 38 0 0 1 -6.999999999999992 -37.3496987939662 L-6.999999999999992 -37.3496987939662 L-6.999999999999994 -47.3496987939662 L6.999999999999977 -47.3496987939662 L6.999999999999979 -37.3496987939662 A38 38 0 0 1 21.460477824182686 -31.359972760794342 L21.460477824182686 -31.359972760794342 L28.531545636048158 -38.43104057265982 L38.4310405726598 -28.53154563604818 L31.35997276079433 -21.4604778241827 A38 38 0 0 1 37.3496987939662 -6.999999999999995 L37.3496987939662 -6.999999999999995 L47.3496987939662 -6.999999999999997 L47.349698793966205 6.999999999999973 L37.349698793966205 6.999999999999976 A38 38 0 0 1 31.359972760794346 21.460477824182686 L31.359972760794346 21.460477824182686 L38.431040572659825 28.531545636048158 L28.53154563604818 38.4310405726598 L21.460477824182703 31.35997276079433 A38 38 0 0 1 6.9999999999999964 37.3496987939662 L6.9999999999999964 37.3496987939662 L6.999999999999995 47.3496987939662 L-7.000000000000009 47.3496987939662 L-7.000000000000007 37.3496987939662 A38 38 0 0 1 -21.46047782418263 31.359972760794385 L-21.46047782418263 31.359972760794385 L-28.531545636048097 38.43104057265987 L-38.431040572659796 28.531545636048186 L-31.35997276079433 21.460477824182703 A38 38 0 0 1 -37.34969879396619 7.000000000000032 L-37.34969879396619 7.000000000000032 L-47.34969879396619 7.0000000000000355 L-47.3496987939662 -7.000000000000002 L-37.3496987939662 -7.000000000000005 A38 38 0 0 1 -31.359972760794346 -21.46047782418268 M0 -23A23 23 0 1 0 0 23 A23 23 0 1 0 0 -23" fill="' . $color2 . '"></path></g></g></g>
				</svg>
			';
		}
	}





	function do_flr( $request ) {

		if( ! empty( $request['type'] ) ){
			return;
		}

		$styling = appbear_get_option( 'styling' );
		$skin    = appbear_get_option( 'themeMode', 'ThemeMode_light' );

		$color1 = '#' . ! empty( $styling[ $skin ]['primary'] ) ? $styling[ $skin ]['primary'] : '000';
		$color2 = '#' . ! empty( $styling[ $skin ]['secondaryVariant'] ) ? $styling[ $skin ]['secondaryVariant'] : '000';

		if ( $request['type'] == 'loading' ) {
			echo '
				{
					"version":24,
					"artboards":[
						{
							"name":"Artboard",
							"translation":[
								-62.89067840576172,
								-261.20843505859375
							],
							"width":48,
							"height":48,
							"origin":[
								0,
								0
							],
							"clipContents":true,
							"color":[
								0,
								0,
								0,
								0
							],
							"nodes":[
								{
									"name":"Ellipse",
									"translation":[
										24,
										24
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"isVisible":true,
									"blendMode":3,
									"drawOrder":1,
									"transformAffectsStroke":true,
									"type":"shape"
								},
								{
									"name":"Ellipse Path",
									"parent":0,
									"translation":[
										0,
										0
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"bones":[

									],
									"isVisible":true,
									"isClosed":true,
									"points":[
										{
											"pointType":2,
											"translation":[
												0,
												-17.5
											],
											"in":[
												-9.664982795715332,
												-17.5
											],
											"out":[
												9.664982795715332,
												-17.5
											]
										},
										{
											"pointType":2,
											"translation":[
												17.5,
												0
											],
											"in":[
												17.5,
												-9.664982795715332
											],
											"out":[
												17.5,
												9.664982795715332
											]
										},
										{
											"pointType":2,
											"translation":[
												0,
												17.5
											],
											"in":[
												9.664982795715332,
												17.5
											],
											"out":[
												-9.664982795715332,
												17.5
											]
										},
										{
											"pointType":2,
											"translation":[
												-17.5,
												0
											],
											"in":[
												-17.5,
												9.664982795715332
											],
											"out":[
												-17.5,
												-9.664982795715332
											]
										}
									],
									"type":"path"
								},
								{
									"name":"Color",
									"parent":0,
									"opacity":1,
									"color":[
										0.9215686321258545,
										0,
										0.24705882370471954,
										1
									],
									"width":1.5,
									"cap":1,
									"join":0,
									"trim":1,
									"start":0,
									"end":-0.01,
									"offset":0,
									"type":"colorStroke"
								},
								{
									"name":"Success",
									"translation":[
										15,
										24
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"isVisible":true,
									"blendMode":3,
									"drawOrder":2,
									"transformAffectsStroke":false,
									"type":"shape"
								},
								{
									"name":"Path",
									"parent":3,
									"translation":[
										0,
										0
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"bones":[

									],
									"isVisible":true,
									"isClosed":false,
									"points":[
										{
											"pointType":0,
											"translation":[
												0,
												0
											],
											"radius":0
										},
										{
											"pointType":0,
											"translation":[
												7.5,
												7.5
											],
											"radius":0
										},
										{
											"pointType":0,
											"translation":[
												19.5,
												-6
											],
											"radius":0
										}
									],
									"type":"path"
								},
								{
									"name":"Path",
									"parent":3,
									"translation":[
										0,
										0
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"bones":[

									],
									"isVisible":true,
									"isClosed":false,
									"points":[
										{
											"pointType":0,
											"translation":[
												184,
												-34.201171875
											],
											"radius":0
										}
									],
									"type":"path"
								},
								{
									"name":"Color",
									"parent":3,
									"opacity":1,
									"color":[
										0.9215686321258545,
										0,
										0.24705882370471954,
										1
									],
									"width":1.5,
									"cap":1,
									"join":1,
									"trim":1,
									"start":0,
									"end":1,
									"offset":0,
									"type":"colorStroke"
								},
								{
									"name":"X One",
									"translation":[
										7,
										10.048828125
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"isVisible":true,
									"blendMode":3,
									"drawOrder":3,
									"transformAffectsStroke":false,
									"type":"shape"
								},
								{
									"name":"Path",
									"parent":7,
									"translation":[
										9.5,
										6.451171875
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"bones":[

									],
									"isVisible":true,
									"isClosed":false,
									"points":[
										{
											"pointType":0,
											"translation":[
												0,
												0
											],
											"radius":0
										},
										{
											"pointType":0,
											"translation":[
												15,
												15
											],
											"radius":0
										}
									],
									"type":"path"
								},
								{
									"name":"Color",
									"parent":7,
									"opacity":1,
									"color":[
										0.9215686321258545,
										0,
										0.24705882370471954,
										1
									],
									"width":1.5,
									"cap":1,
									"join":1,
									"trim":1,
									"start":0,
									"end":1,
									"offset":0,
									"type":"colorStroke"
								},
								{
									"name":"X Two",
									"translation":[
										16.5,
										16.5
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"isVisible":true,
									"blendMode":3,
									"drawOrder":4,
									"transformAffectsStroke":false,
									"type":"shape"
								},
								{
									"name":"Path",
									"parent":10,
									"translation":[
										0,
										0
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"bones":[

									],
									"isVisible":true,
									"isClosed":false,
									"points":[
										{
											"pointType":0,
											"translation":[
												0,
												15
											],
											"radius":0
										},
										{
											"pointType":0,
											"translation":[
												15,
												0
											],
											"radius":0
										}
									],
									"type":"path"
								},
								{
									"name":"Color",
									"parent":10,
									"opacity":1,
									"color":[
										0.9215686321258545,
										0,
										0.24705882370471954,
										1
									],
									"width":1.5,
									"cap":1,
									"join":1,
									"trim":1,
									"start":0,
									"end":1,
									"offset":0,
									"type":"colorStroke"
								}
							],
							"animations":[
								{
									"name":"Loading 1",
									"fps":80,
									"duration":0.75,
									"isLooping":true,
									"keyed":[
										{
											"component":0
										},
										{
											"component":2,
											"strokeStart":[
												[
													{
														"time":0,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													},
													{
														"time":0.75,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													}
												]
											],
											"strokeEnd":[
												[
													{
														"time":0,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													},
													{
														"time":0.75,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":1
													}
												]
											],
											"strokeOffset":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":0.75,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":6.5,
														"interpolatorType":1,
														"value":0
													}
												]
											],
											"strokeColor":[
												[
													{
														"time":0.6116599999950267,
														"interpolatorType":1,
														"value":[
															0.9725490212440491,
															0.5490196347236633,
															0,
															1
														]
													},
													{
														"time":0.75,
														"interpolatorType":1,
														"value":[
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
											"component":3
										},
										{
											"component":6,
											"strokeOpacity":[
												[
													{
														"time":0.75,
														"interpolatorType":1,
														"value":0
													}
												]
											],
											"strokeColor":[
												[
													{
														"time":0.75,
														"interpolatorType":1,
														"value":[
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
											"component":7
										},
										{
											"component":9,
											"strokeOpacity":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													}
												]
											]
										},
										{
											"component":10
										},
										{
											"component":12,
											"strokeOpacity":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													}
												]
											]
										}
									],
									"animationStart":0,
									"animationEnd":6.5,
									"type":"animation"
								},
								{
									"name":"Loading 2",
									"fps":60,
									"duration":0.75,
									"isLooping":false,
									"keyed":[
										{
											"component":0
										},
										{
											"component":2,
											"strokeStart":[
												[
													{
														"time":0,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													},
													{
														"time":0.75,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													}
												]
											],
											"strokeEnd":[
												[
													{
														"time":0,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													},
													{
														"time":0.75,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":1
													}
												]
											],
											"strokeOffset":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":0.75,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":6.5,
														"interpolatorType":1,
														"value":0
													}
												]
											],
											"strokeColor":[
												[
													{
														"time":0.75,
														"interpolatorType":1,
														"value":[
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
											"component":3
										},
										{
											"component":6,
											"strokeOpacity":[
												[
													{
														"time":0.75,
														"interpolatorType":1,
														"value":0
													}
												]
											],
											"strokeColor":[
												[
													{
														"time":0.75,
														"interpolatorType":1,
														"value":[
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
											"component":7
										},
										{
											"component":9,
											"strokeOpacity":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													}
												]
											]
										},
										{
											"component":10
										},
										{
											"component":12,
											"strokeOpacity":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													}
												]
											]
										}
									],
									"animationStart":0,
									"animationEnd":6.5,
									"type":"animation"
								},
								{
									"name":"Loading 3",
									"fps":60,
									"duration":0.75,
									"isLooping":false,
									"keyed":[
										{
											"component":0
										},
										{
											"component":2,
											"strokeStart":[
												[
													{
														"time":0,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													},
													{
														"time":0.75,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													}
												]
											],
											"strokeEnd":[
												[
													{
														"time":0,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													},
													{
														"time":0.75,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":1
													}
												]
											],
											"strokeOffset":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":0.75,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":6.5,
														"interpolatorType":1,
														"value":0
													}
												]
											],
											"strokeColor":[
												[
													{
														"time":0.75,
														"interpolatorType":1,
														"value":[
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
											"component":3
										},
										{
											"component":6,
											"strokeOpacity":[
												[
													{
														"time":0.75,
														"interpolatorType":1,
														"value":0
													}
												]
											],
											"strokeColor":[
												[
													{
														"time":0.75,
														"interpolatorType":1,
														"value":[
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
											"component":7
										},
										{
											"component":9,
											"strokeOpacity":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													}
												]
											]
										},
										{
											"component":10
										},
										{
											"component":12,
											"strokeOpacity":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													}
												]
											]
										}
									],
									"animationStart":0,
									"animationEnd":6.5,
									"type":"animation"
								},
								{
									"name":"Success",
									"fps":60,
									"duration":0.5,
									"isLooping":false,
									"keyed":[
										{
											"component":0
										},
										{
											"component":2,
											"strokeEnd":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":1
													},
													{
														"time":0.5,
														"interpolatorType":1,
														"value":1
													}
												]
											],
											"strokeColor":[
												[
													{
														"time":0.5,
														"interpolatorType":1,
														"value":[
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
											"component":3
										},
										{
											"component":6,
											"strokeEnd":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":0.5,
														"interpolatorType":1,
														"value":1
													},
													{
														"time":5.75,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													},
													{
														"time":6.5,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":1
													}
												]
											],
											"strokeOpacity":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":1
													}
												]
											],
											"strokeColor":[
												[
													{
														"time":0.5,
														"interpolatorType":1,
														"value":[
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
											"component":7
										},
										{
											"component":9,
											"strokeOpacity":[
												[
													{
														"time":0.5,
														"interpolatorType":1,
														"value":0
													}
												]
											]
										},
										{
											"component":10
										},
										{
											"component":12,
											"strokeOpacity":[
												[
													{
														"time":0.5,
														"interpolatorType":1,
														"value":0
													}
												]
											]
										}
									],
									"animationStart":0,
									"animationEnd":6.5,
									"type":"animation"
								},
								{
									"name":"Error",
									"fps":60,
									"duration":0.5,
									"isLooping":false,
									"keyed":[
										{
											"component":0
										},
										{
											"component":2,
											"strokeEnd":[
												[
													{
														"time":0.5,
														"interpolatorType":1,
														"value":1
													},
													{
														"time":1,
														"interpolatorType":1,
														"value":1
													}
												]
											]
										},
										{
											"component":3
										},
										{
											"component":6,
											"strokeOpacity":[
												[
													{
														"time":0.5,
														"interpolatorType":1,
														"value":0
													}
												]
											]
										},
										{
											"component":7
										},
										{
											"component":9,
											"strokeEnd":[
												[
													{
														"time":0,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													},
													{
														"time":0.25,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":1
													},
													{
														"time":9.516666666666667,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":1
													}
												]
											],
											"strokeOpacity":[
												[
													{
														"time":0,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":1
													}
												]
											]
										},
										{
											"component":10
										},
										{
											"component":12,
											"strokeOpacity":[
												[
													{
														"time":0.25,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":1
													}
												]
											],
											"strokeEnd":[
												[
													{
														"time":0,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													},
													{
														"time":0.25,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":0
													},
													{
														"time":0.5,
														"interpolatorType":2,
														"cubicX1":0.42,
														"cubicY1":0,
														"cubicX2":0.58,
														"cubicY2":1,
														"value":1
													}
												]
											]
										}
									],
									"animationStart":0,
									"animationEnd":9.516666666666667,
									"type":"animation"
								}
							],
							"type":"artboard"
						}
					]
				}
			';
		}

		elseif ( $request['type'] == 'settings' ) {
			echo '
				{
					"version":24,
					"artboards":[
						{
							"name":"Artboard",
							"translation":[
								116.39401245117188,
								97.28765869140625
							],
							"width":960,
							"height":960,
							"origin":[
								0,
								0
							],
							"clipContents":true,
							"color":[
								0.19607843458652496,
								0.25882354378700256,
								0.364705890417099,
								0
							],
							"nodes":[
								{
									"name":"Capa_1",
									"translation":[
										43.010009765625,
										-81.55313110351562
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"type":"node"
								},
								{
									"name":"Node",
									"parent":0,
									"translation":[
										0,
										0
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"type":"node"
								},
								{
									"name":"Shape",
									"parent":1,
									"translation":[
										256.8004455566406,
										361.2919921875
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"isVisible":true,
									"blendMode":3,
									"drawOrder":1,
									"transformAffectsStroke":false,
									"type":"shape"
								},
								{
									"name":"Color",
									"parent":2,
									"opacity":1,
									"color":[
										0.9725490212440491,
										0.5490196347236633,
										0,
										1
									],
									"fillRule":1,
									"type":"colorFill"
								},
								{
									"name":"Path",
									"parent":2,
									"translation":[
										-0.624755859375,
										1.0994873046875
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"bones":[

									],
									"isVisible":true,
									"isClosed":true,
									"points":[
										{
											"pointType":2,
											"translation":[
												-215.0504913330078,
												60.60098648071289
											],
											"in":[
												-215.0504913330078,
												60.60098648071289
											],
											"out":[
												-210.15048217773438,
												77.40098571777344
											]
										},
										{
											"pointType":2,
											"translation":[
												-194.75048828125,
												108.80098724365234
											],
											"in":[
												-203.35049438476562,
												93.60099029541016
											],
											"out":[
												-194.75048828125,
												108.80098724365234
											]
										},
										{
											"pointType":2,
											"translation":[
												-219.25048828125,
												139.70098876953125
											],
											"in":[
												-219.25048828125,
												139.70098876953125
											],
											"out":[
												-227.25048828125,
												149.80099487304688
											]
										},
										{
											"pointType":2,
											"translation":[
												-217.35049438476562,
												173.30099487304688
											],
											"in":[
												-226.35049438476562,
												164.20098876953125
											],
											"out":[
												-217.35049438476562,
												173.30099487304688
											]
										},
										{
											"pointType":2,
											"translation":[
												-175.15048217773438,
												215.50099182128906
											],
											"in":[
												-175.15048217773438,
												215.50099182128906
											],
											"out":[
												-166.0504913330078,
												224.60098266601562
											]
										},
										{
											"pointType":2,
											"translation":[
												-141.5504913330078,
												217.39999389648438
											],
											"in":[
												-151.65048217773438,
												225.39999389648438
											],
											"out":[
												-141.5504913330078,
												217.39999389648438
											]
										},
										{
											"pointType":2,
											"translation":[
												-110.8504867553711,
												193.09999084472656
											],
											"in":[
												-110.8504867553711,
												193.09999084472656
											],
											"out":[
												-95.05049133300781,
												202.20098876953125
											]
										},
										{
											"pointType":2,
											"translation":[
												-60.75048828125,
												214.29998779296875
											],
											"in":[
												-78.25048828125,
												209.29998779296875
											],
											"out":[
												-60.75048828125,
												214.29998779296875
											]
										},
										{
											"pointType":2,
											"translation":[
												-56.150489807128906,
												253.79998779296875
											],
											"in":[
												-56.150489807128906,
												253.79998779296875
											],
											"out":[
												-54.650489807128906,
												266.5999755859375
											]
										},
										{
											"pointType":2,
											"translation":[
												-31.050487518310547,
												276.1999816894531
											],
											"in":[
												-43.850486755371094,
												276.1999816894531
											],
											"out":[
												-31.050487518310547,
												276.1999816894531
											]
										},
										{
											"pointType":2,
											"translation":[
												28.649511337280273,
												276.1999816894531
											],
											"in":[
												28.649511337280273,
												276.1999816894531
											],
											"out":[
												41.44951248168945,
												276.1999816894531
											]
										},
										{
											"pointType":2,
											"translation":[
												53.74951171875,
												253.79998779296875
											],
											"in":[
												52.24951171875,
												266.5989990234375
											],
											"out":[
												53.74951171875,
												253.79998779296875
											]
										},
										{
											"pointType":2,
											"translation":[
												58.149513244628906,
												215.69998168945312
											],
											"in":[
												58.149513244628906,
												215.69998168945312
											],
											"out":[
												76.94950866699219,
												210.79998779296875
											]
										},
										{
											"pointType":2,
											"translation":[
												111.8495101928711,
												193.99998474121094
											],
											"in":[
												94.94950866699219,
												203.49998474121094
											],
											"out":[
												111.8495101928711,
												193.99998474121094
											]
										},
										{
											"pointType":2,
											"translation":[
												141.5495147705078,
												217.49998474121094
											],
											"in":[
												141.5495147705078,
												217.49998474121094
											],
											"out":[
												151.64950561523438,
												225.49998474121094
											]
										},
										{
											"pointType":2,
											"translation":[
												175.14950561523438,
												215.59999084472656
											],
											"in":[
												166.0495147705078,
												224.59999084472656
											],
											"out":[
												175.14950561523438,
												215.59999084472656
											]
										},
										{
											"pointType":2,
											"translation":[
												217.34951782226562,
												173.39999389648438
											],
											"in":[
												217.34951782226562,
												173.39999389648438
											],
											"out":[
												226.4495086669922,
												164.29998779296875
											]
										},
										{
											"pointType":2,
											"translation":[
												219.24951171875,
												139.79998779296875
											],
											"in":[
												227.24951171875,
												149.89999389648438
											],
											"out":[
												219.24951171875,
												139.79998779296875
											]
										},
										{
											"pointType":2,
											"translation":[
												196.14950561523438,
												110.49998474121094
											],
											"in":[
												196.14950561523438,
												110.49998474121094
											],
											"out":[
												205.74951171875,
												93.89898681640625
											]
										},
										{
											"pointType":2,
											"translation":[
												218.24951171875,
												57.699989318847656
											],
											"in":[
												213.24951171875,
												76.19998931884766
											],
											"out":[
												218.24951171875,
												57.699989318847656
											]
										},
										{
											"pointType":2,
											"translation":[
												253.84951782226562,
												53.5999870300293
											],
											"in":[
												253.84951782226562,
												53.5999870300293
											],
											"out":[
												266.6505126953125,
												52.0999870300293
											]
										},
										{
											"pointType":2,
											"translation":[
												276.24951171875,
												28.499988555908203
											],
											"in":[
												276.24951171875,
												41.29998779296875
											],
											"out":[
												276.24951171875,
												28.499988555908203
											]
										},
										{
											"pointType":2,
											"translation":[
												276.24951171875,
												-31.20001220703125
											],
											"in":[
												276.24951171875,
												-31.20001220703125
											],
											"out":[
												276.24951171875,
												-44.0000114440918
											]
										},
										{
											"pointType":2,
											"translation":[
												253.84951782226562,
												-56.300010681152344
											],
											"in":[
												266.6495056152344,
												-54.800010681152344
											],
											"out":[
												253.84951782226562,
												-56.300010681152344
											]
										},
										{
											"pointType":2,
											"translation":[
												218.74951171875,
												-60.4000129699707
											],
											"in":[
												218.74951171875,
												-60.4000129699707
											],
											"out":[
												213.94851684570312,
												-78.70001220703125
											]
										},
										{
											"pointType":2,
											"translation":[
												197.55050659179688,
												-112.60001373291016
											],
											"in":[
												206.74951171875,
												-96.20001220703125
											],
											"out":[
												197.55050659179688,
												-112.60001373291016
											]
										},
										{
											"pointType":2,
											"translation":[
												219.1505126953125,
												-139.90000915527344
											],
											"in":[
												219.1505126953125,
												-139.90000915527344
											],
											"out":[
												227.1505126953125,
												-150.00001525878906
											]
										},
										{
											"pointType":2,
											"translation":[
												217.25051879882812,
												-173.50001525878906
											],
											"in":[
												226.25051879882812,
												-164.40000915527344
											],
											"out":[
												217.25051879882812,
												-173.50001525878906
											]
										},
										{
											"pointType":2,
											"translation":[
												175.1505126953125,
												-215.60000610351562
											],
											"in":[
												175.1505126953125,
												-215.60000610351562
											],
											"out":[
												166.05050659179688,
												-224.70001220703125
											]
										},
										{
											"pointType":2,
											"translation":[
												141.55050659179688,
												-217.50001525878906
											],
											"in":[
												151.6505126953125,
												-225.50001525878906
											],
											"out":[
												141.55050659179688,
												-217.50001525878906
											]
										},
										{
											"pointType":2,
											"translation":[
												115.0505142211914,
												-196.50001525878906
											],
											"in":[
												115.0505142211914,
												-196.50001525878906
											],
											"out":[
												97.85050964355469,
												-206.60000610351562
											]
										},
										{
											"pointType":2,
											"translation":[
												60.1505126953125,
												-219.50001525878906
											],
											"in":[
												79.44950866699219,
												-214.30001831054688
											],
											"out":[
												60.1505126953125,
												-219.50001525878906
											]
										},
										{
											"pointType":2,
											"translation":[
												56.1505126953125,
												-253.80001831054688
											],
											"in":[
												56.1505126953125,
												-253.80001831054688
											],
											"out":[
												54.6505126953125,
												-266.6000061035156
											]
										},
										{
											"pointType":2,
											"translation":[
												31.050512313842773,
												-276.20001220703125
											],
											"in":[
												43.85051345825195,
												-276.20001220703125
											],
											"out":[
												31.050512313842773,
												-276.20001220703125
											]
										},
										{
											"pointType":2,
											"translation":[
												-28.64948844909668,
												-276.20001220703125
											],
											"in":[
												-28.64948844909668,
												-276.20001220703125
											],
											"out":[
												-41.44948959350586,
												-276.20001220703125
											]
										},
										{
											"pointType":2,
											"translation":[
												-53.749488830566406,
												-253.80001831054688
											],
											"in":[
												-52.249488830566406,
												-266.6000061035156
											],
											"out":[
												-53.749488830566406,
												-253.80001831054688
											]
										},
										{
											"pointType":2,
											"translation":[
												-57.749488830566406,
												-219.50001525878906
											],
											"in":[
												-57.749488830566406,
												-219.50001525878906
											],
											"out":[
												-77.54949188232422,
												-214.20001220703125
											]
										},
										{
											"pointType":2,
											"translation":[
												-114.04949188232422,
												-195.70001220703125
											],
											"in":[
												-96.4494857788086,
												-206.20001220703125
											],
											"out":[
												-114.04949188232422,
												-195.70001220703125
											]
										},
										{
											"pointType":2,
											"translation":[
												-141.5494842529297,
												-217.50001525878906
											],
											"in":[
												-141.5494842529297,
												-217.50001525878906
											],
											"out":[
												-151.6494903564453,
												-225.50001525878906
											]
										},
										{
											"pointType":2,
											"translation":[
												-175.1494903564453,
												-215.60000610351562
											],
											"in":[
												-166.0494842529297,
												-224.60000610351562
											],
											"out":[
												-175.1494903564453,
												-215.60000610351562
											]
										},
										{
											"pointType":2,
											"translation":[
												-217.3494873046875,
												-173.40000915527344
											],
											"in":[
												-217.3494873046875,
												-173.40000915527344
											],
											"out":[
												-226.44949340820312,
												-164.30001831054688
											]
										},
										{
											"pointType":2,
											"translation":[
												-219.24948120117188,
												-139.80001831054688
											],
											"in":[
												-227.24948120117188,
												-149.90000915527344
											],
											"out":[
												-219.24948120117188,
												-139.80001831054688
											]
										},
										{
											"pointType":2,
											"translation":[
												-196.24948120117188,
												-110.70001220703125
											],
											"in":[
												-196.24948120117188,
												-110.70001220703125
											],
											"out":[
												-205.44949340820312,
												-94.10001373291016
											]
										},
										{
											"pointType":2,
											"translation":[
												-217.0494842529297,
												-58.0000114440918
											],
											"in":[
												-212.44949340820312,
												-76.40000915527344
											],
											"out":[
												-217.0494842529297,
												-58.0000114440918
											]
										},
										{
											"pointType":2,
											"translation":[
												-253.8494873046875,
												-53.800010681152344
											],
											"in":[
												-253.8494873046875,
												-53.800010681152344
											],
											"out":[
												-266.64947509765625,
												-52.300010681152344
											]
										},
										{
											"pointType":2,
											"translation":[
												-276.2494812011719,
												-28.70001220703125
											],
											"in":[
												-276.2494812011719,
												-41.5000114440918
											],
											"out":[
												-276.2494812011719,
												-28.70001220703125
											]
										},
										{
											"pointType":2,
											"translation":[
												-276.2494812011719,
												30.999988555908203
											],
											"in":[
												-276.2494812011719,
												30.999988555908203
											],
											"out":[
												-276.2494812011719,
												43.79998779296875
											]
										},
										{
											"pointType":2,
											"translation":[
												-253.8494873046875,
												56.0999870300293
											],
											"in":[
												-266.64947509765625,
												54.5999870300293
											],
											"out":[
												-253.8494873046875,
												56.0999870300293
											]
										}
									],
									"type":"path"
								},
								{
									"name":"Path",
									"parent":2,
									"translation":[
										0.624755859375,
										-1.099517822265625
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"bones":[

									],
									"isVisible":true,
									"isClosed":true,
									"points":[
										{
											"pointType":1,
											"translation":[
												0,
												-98.70000457763672
											],
											"in":[
												-54.400001525878906,
												-98.70000457763672
											],
											"out":[
												54.400001525878906,
												-98.70000457763672
											]
										},
										{
											"pointType":1,
											"translation":[
												98.69999694824219,
												-0.000006591797045985004
											],
											"in":[
												98.69999694824219,
												-54.40000534057617
											],
											"out":[
												98.69999694824219,
												54.399993896484375
											]
										},
										{
											"pointType":1,
											"translation":[
												0,
												98.69999694824219
											],
											"in":[
												54.400001525878906,
												98.69999694824219
											],
											"out":[
												-54.39899826049805,
												98.69999694824219
											]
										},
										{
											"pointType":1,
											"translation":[
												-98.69999694824219,
												-0.000006591797045985004
											],
											"in":[
												-98.69999694824219,
												54.399993896484375
											],
											"out":[
												-98.69999694824219,
												-54.40000534057617
											]
										}
									],
									"type":"path"
								},
								{
									"name":"Shape",
									"parent":1,
									"translation":[
										678.37353515625,
										618.989501953125
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"isVisible":true,
									"blendMode":3,
									"drawOrder":2,
									"transformAffectsStroke":false,
									"type":"shape"
								},
								{
									"name":"Color",
									"parent":6,
									"opacity":1,
									"color":[
										0.5372549295425415,
										0.5372549295425415,
										0.5372549295425415,
										1
									],
									"fillRule":1,
									"type":"colorFill"
								},
								{
									"name":"Path",
									"parent":6,
									"translation":[
										-0.47503662109375,
										0.92529296875
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"bones":[

									],
									"isVisible":true,
									"isClosed":true,
									"points":[
										{
											"pointType":2,
											"translation":[
												162.64901733398438,
												-158.94949340820312
											],
											"in":[
												172.35000610351562,
												-150.74949645996094
											],
											"out":[
												162.64901733398438,
												-158.94949340820312
											]
										},
										{
											"pointType":2,
											"translation":[
												131.14901733398438,
												-185.54949951171875
											],
											"in":[
												131.14901733398438,
												-185.54949951171875
											],
											"out":[
												121.45001220703125,
												-193.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												97.95001220703125,
												-184.64950561523438
											],
											"in":[
												107.14900970458984,
												-193.34950256347656
											],
											"out":[
												97.95001220703125,
												-184.64950561523438
											]
										},
										{
											"pointType":2,
											"translation":[
												80.55001068115234,
												-168.34950256347656
											],
											"in":[
												80.55001068115234,
												-168.34950256347656
											],
											"out":[
												65.85101318359375,
												-175.44949340820312
											]
										},
										{
											"pointType":2,
											"translation":[
												34.1500129699707,
												-183.34950256347656
											],
											"in":[
												50.25101089477539,
												-180.44949340820312
											],
											"out":[
												34.1500129699707,
												-183.34950256347656
											]
										},
										{
											"pointType":2,
											"translation":[
												29.252012252807617,
												-207.34950256347656
											],
											"in":[
												29.252012252807617,
												-207.34950256347656
											],
											"out":[
												26.752012252807617,
												-219.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												2.650012254714966,
												-227.34950256347656
											],
											"in":[
												15.252012252807617,
												-228.34950256347656
											],
											"out":[
												2.650012254714966,
												-227.34950256347656
											]
										},
										{
											"pointType":2,
											"translation":[
												-38.449989318847656,
												-223.84950256347656
											],
											"in":[
												-38.449989318847656,
												-223.84950256347656
											],
											"out":[
												-51.04998779296875,
												-222.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												-61.3499870300293,
												-199.74949645996094
											],
											"in":[
												-60.949989318847656,
												-212.44949340820312
											],
											"out":[
												-61.3499870300293,
												-199.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												-62.14898681640625,
												-175.34950256347656
											],
											"in":[
												-62.14898681640625,
												-175.34950256347656
											],
											"out":[
												-77.94998931884766,
												-169.64950561523438
											]
										},
										{
											"pointType":2,
											"translation":[
												-106.44998931884766,
												-152.04949951171875
											],
											"in":[
												-92.84999084472656,
												-161.84950256347656
											],
											"out":[
												-106.44998931884766,
												-152.04949951171875
											]
										},
										{
											"pointType":2,
											"translation":[
												-127.24898529052734,
												-165.84950256347656
											],
											"in":[
												-127.24898529052734,
												-165.84950256347656
											],
											"out":[
												-137.85098266601562,
												-172.84950256347656
											]
										},
										{
											"pointType":2,
											"translation":[
												-160.14898681640625,
												-161.14950561523438
											],
											"in":[
												-151.94998168945312,
												-170.84950256347656
											],
											"out":[
												-160.14898681640625,
												-161.14950561523438
											]
										},
										{
											"pointType":2,
											"translation":[
												-186.74899291992188,
												-129.44949340820312
											],
											"in":[
												-186.74899291992188,
												-129.44949340820312
											],
											"out":[
												-194.94998168945312,
												-119.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												-185.85098266601562,
												-96.24949645996094
											],
											"in":[
												-194.54998779296875,
												-105.44950103759766
											],
											"out":[
												-185.85098266601562,
												-96.24949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												-167.64999389648438,
												-76.85050201416016
											],
											"in":[
												-167.64999389648438,
												-76.85050201416016
											],
											"out":[
												-173.95098876953125,
												-62.6505012512207
											]
										},
										{
											"pointType":2,
											"translation":[
												-181.04998779296875,
												-32.45050048828125
											],
											"in":[
												-178.45098876953125,
												-47.7495002746582
											],
											"out":[
												-181.04998779296875,
												-32.45050048828125
											]
										},
										{
											"pointType":2,
											"translation":[
												-207.04998779296875,
												-27.15049934387207
											],
											"in":[
												-207.04998779296875,
												-27.15049934387207
											],
											"out":[
												-219.44998168945312,
												-24.65049934387207
											]
										},
										{
											"pointType":2,
											"translation":[
												-227.04998779296875,
												-0.5494999885559082
											],
											"in":[
												-228.04998779296875,
												-13.150500297546387
											],
											"out":[
												-227.04998779296875,
												-0.5494999885559082
											]
										},
										{
											"pointType":2,
											"translation":[
												-223.54998779296875,
												40.550498962402344
											],
											"in":[
												-223.54998779296875,
												40.550498962402344
											],
											"out":[
												-222.44998168945312,
												53.1505012512207
											]
										},
										{
											"pointType":2,
											"translation":[
												-199.44998168945312,
												63.45050048828125
											],
											"in":[
												-212.14999389648438,
												63.050498962402344
											],
											"out":[
												-199.44998168945312,
												63.45050048828125
											]
										},
										{
											"pointType":2,
											"translation":[
												-171.34999084472656,
												64.34950256347656
											],
											"in":[
												-171.34999084472656,
												64.34950256347656
											],
											"out":[
												-166.24798583984375,
												77.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												-151.44998168945312,
												102.34950256347656
											],
											"in":[
												-159.54898071289062,
												90.45050048828125
											],
											"out":[
												-151.44998168945312,
												102.34950256347656
											]
										},
										{
											"pointType":2,
											"translation":[
												-167.14898681640625,
												126.04949951171875
											],
											"in":[
												-167.14898681640625,
												126.04949951171875
											],
											"out":[
												-174.14898681640625,
												136.64950561523438
											]
										},
										{
											"pointType":2,
											"translation":[
												-162.44998168945312,
												158.94949340820312
											],
											"in":[
												-172.14898681640625,
												150.74949645996094
											],
											"out":[
												-162.44998168945312,
												158.94949340820312
											]
										},
										{
											"pointType":2,
											"translation":[
												-130.94998168945312,
												185.54949951171875
											],
											"in":[
												-130.94998168945312,
												185.54949951171875
											],
											"out":[
												-121.24898529052734,
												193.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												-97.74898529052734,
												184.64950561523438
											],
											"in":[
												-106.94998931884766,
												193.34950256347656
											],
											"out":[
												-97.74898529052734,
												184.64950561523438
											]
										},
										{
											"pointType":2,
											"translation":[
												-77.14898681640625,
												165.34950256347656
											],
											"in":[
												-77.14898681640625,
												165.34950256347656
											],
											"out":[
												-63.64898681640625,
												171.64950561523438
											]
										},
										{
											"pointType":2,
											"translation":[
												-34.8499870300293,
												179.14950561523438
											],
											"in":[
												-49.449989318847656,
												176.34950256347656
											],
											"out":[
												-34.8499870300293,
												179.14950561523438
											]
										},
										{
											"pointType":2,
											"translation":[
												-29.148988723754883,
												207.34950256347656
											],
											"in":[
												-29.148988723754883,
												207.34950256347656
											],
											"out":[
												-26.648988723754883,
												219.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												-2.548987865447998,
												227.34950256347656
											],
											"in":[
												-15.148987770080566,
												228.34950256347656
											],
											"out":[
												-2.548987865447998,
												227.34950256347656
											]
										},
										{
											"pointType":2,
											"translation":[
												38.5510139465332,
												223.84950256347656
											],
											"in":[
												38.5510139465332,
												223.84950256347656
											],
											"out":[
												51.1510124206543,
												222.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												61.451011657714844,
												199.74949645996094
											],
											"in":[
												61.0510139465332,
												212.45050048828125
											],
											"out":[
												61.451011657714844,
												199.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												62.35101318359375,
												172.14849853515625
											],
											"in":[
												62.35101318359375,
												172.14849853515625
											],
											"out":[
												77.35101318359375,
												166.84849548339844
											]
										},
										{
											"pointType":2,
											"translation":[
												104.65000915527344,
												150.74949645996094
											],
											"in":[
												91.55001068115234,
												159.64849853515625
											],
											"out":[
												104.65000915527344,
												150.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												127.35101318359375,
												165.74949645996094
											],
											"in":[
												127.35101318359375,
												165.74949645996094
											],
											"out":[
												137.95101928710938,
												172.74949645996094
											]
										},
										{
											"pointType":2,
											"translation":[
												160.25100708007812,
												161.04949951171875
											],
											"in":[
												152.05001831054688,
												170.74949645996094
											],
											"out":[
												160.25100708007812,
												161.04949951171875
											]
										},
										{
											"pointType":2,
											"translation":[
												186.85101318359375,
												129.54949951171875
											],
											"in":[
												186.85101318359375,
												129.54949951171875
											],
											"out":[
												195.05001831054688,
												119.84950256347656
											]
										},
										{
											"pointType":2,
											"translation":[
												185.95101928710938,
												96.34950256347656
											],
											"in":[
												194.65000915527344,
												105.54949951171875
											],
											"out":[
												185.95101928710938,
												96.34950256347656
											]
										},
										{
											"pointType":2,
											"translation":[
												167.65000915527344,
												76.95050048828125
											],
											"in":[
												167.65000915527344,
												76.95050048828125
											],
											"out":[
												174.35101318359375,
												62.7504997253418
											]
										},
										{
											"pointType":2,
											"translation":[
												182.05001831054688,
												32.3494987487793
											],
											"in":[
												179.25201416015625,
												47.7504997253418
											],
											"out":[
												182.05001831054688,
												32.3494987487793
											]
										},
										{
											"pointType":2,
											"translation":[
												207.05001831054688,
												27.249500274658203
											],
											"in":[
												207.05001831054688,
												27.249500274658203
											],
											"out":[
												219.45001220703125,
												24.749500274658203
											]
										},
										{
											"pointType":2,
											"translation":[
												227.05001831054688,
												0.6485000252723694
											],
											"in":[
												228.05001831054688,
												13.249500274658203
											],
											"out":[
												227.05001831054688,
												0.6485000252723694
											]
										},
										{
											"pointType":2,
											"translation":[
												223.55001831054688,
												-40.451499938964844
											],
											"in":[
												223.55001831054688,
												-40.451499938964844
											],
											"out":[
												222.45001220703125,
												-53.05149841308594
											]
										},
										{
											"pointType":2,
											"translation":[
												199.45001220703125,
												-63.35150146484375
											],
											"in":[
												212.15000915527344,
												-62.951499938964844
											],
											"out":[
												199.45001220703125,
												-63.35150146484375
											]
										},
										{
											"pointType":2,
											"translation":[
												174.35000610351562,
												-64.15149688720703
											],
											"in":[
												174.35000610351562,
												-64.15149688720703
											],
											"out":[
												169.14901733398438,
												-78.75150299072266
											]
										},
										{
											"pointType":2,
											"translation":[
												153.45001220703125,
												-105.35150146484375
											],
											"in":[
												162.14901733398438,
												-92.55049896240234
											],
											"out":[
												153.45001220703125,
												-105.35150146484375
											]
										},
										{
											"pointType":2,
											"translation":[
												167.14901733398438,
												-125.95149993896484
											],
											"in":[
												167.14901733398438,
												-125.95149993896484
											],
											"out":[
												174.35000610351562,
												-136.54949951171875
											]
										}
									],
									"type":"path"
								},
								{
									"name":"Path",
									"parent":6,
									"translation":[
										0.4749755859375,
										-0.92523193359375
									],
									"rotation":0,
									"scale":[
										1,
										1
									],
									"opacity":1,
									"isCollapsed":false,
									"clips":[

									],
									"bones":[

									],
									"isVisible":true,
									"isClosed":true,
									"points":[
										{
											"pointType":2,
											"translation":[
												6.801000118255615,
												80.5000228881836
											],
											"in":[
												51.19900131225586,
												76.801025390625
											],
											"out":[
												-37.5989990234375,
												84.301025390625
											]
										},
										{
											"pointType":2,
											"translation":[
												-80.5,
												6.801024913787842
											],
											"in":[
												-76.8010025024414,
												51.20002365112305
											],
											"out":[
												-84.3010025024414,
												-37.598976135253906
											]
										},
										{
											"pointType":2,
											"translation":[
												-6.801000118255615,
												-80.4999771118164
											],
											"in":[
												-51.19900131225586,
												-76.79997253417969
											],
											"out":[
												37.5989990234375,
												-84.29997253417969
											]
										},
										{
											"pointType":2,
											"translation":[
												80.5,
												-6.7999749183654785
											],
											"in":[
												76.8010025024414,
												-51.198974609375
											],
											"out":[
												84.3010025024414,
												37.60102462768555
											]
										}
									],
									"type":"path"
								}
							],
							"animations":[
								{
									"name":"Loading 2",
									"fps":60,
									"duration":1,
									"isLooping":true,
									"keyed":[
										{
											"component":2,
											"rotation":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":1,
														"interpolatorType":1,
														"value":0.7853981633974483
													}
												]
											],
											"posY":[
												[
													{
														"time":1,
														"interpolatorType":1,
														"value":361.2919921875
													}
												]
											],
											"posX":[
												[
													{
														"time":1,
														"interpolatorType":1,
														"value":261.7046813964844
													}
												]
											]
										},
										{
											"component":6,
											"rotation":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":1,
														"interpolatorType":1,
														"value":0.7853981633974483
													}
												]
											],
											"posX":[
												[
													{
														"time":1,
														"interpolatorType":1,
														"value":675.9214477539062
													}
												]
											]
										}
									],
									"animationStart":0,
									"animationEnd":1,
									"type":"animation"
								},
								{
									"name":"Loading 1",
									"fps":60,
									"duration":1,
									"isLooping":true,
									"keyed":[
										{
											"component":2,
											"rotation":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":1,
														"interpolatorType":1,
														"value":0.7853981633974483
													}
												]
											],
											"posY":[
												[
													{
														"time":1,
														"interpolatorType":1,
														"value":361.2919921875
													}
												]
											],
											"posX":[
												[
													{
														"time":1,
														"interpolatorType":1,
														"value":261.7046813964844
													}
												]
											]
										},
										{
											"component":6,
											"rotation":[
												[
													{
														"time":0,
														"interpolatorType":1,
														"value":0
													},
													{
														"time":1,
														"interpolatorType":1,
														"value":0.7853981633974483
													}
												]
											],
											"posX":[
												[
													{
														"time":1,
														"interpolatorType":1,
														"value":675.9214477539062
													}
												]
											]
										}
									],
									"animationStart":0,
									"animationEnd":1,
									"type":"animation"
								}
							],
							"type":"artboard"
						}
					]
				}

			';
		}
	}





	function do_dev_mode( $request ){

		if( ! empty( $request['action'] ) ){

			switch ( $request['action'] ) {
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
	}


}


new AppBear_Endpoints();







