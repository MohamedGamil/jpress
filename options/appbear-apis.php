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
			'page',
			'add-comment',
			'get-version',
			'contact-us',
			'options',
			'dev-mode',
			'translations',
			//'comments',
			//'register',
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
		else{

			// Categories
			if ( isset( $request['categories'] ) ) {
				$args['cat'] = $request['categories'];
			}

			// Tags
			if ( isset( $request['tags'] ) ) {
				$args['tag__in'] = explode( ',', $request['tags'] );
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
				$args['orderby'] = str_replace( 'Sort.', '', $request['sort'] );
			}

		}

		//var_dump( $args );

		// The Query
		$posts = new WP_Query( $args );

		// The Loop
		if ( $posts->have_posts() ) {

			$data = array(
				'status'      => true,
				'count'       => ( $args['posts_per_page'] == -1 ) ? (int) $posts->found_posts : (int) $args['posts_per_page'],
				'count_total' => (int)$posts->found_posts,
				'pages'       => ( $args['posts_per_page'] == -1 ) ? 1 : ceil( $posts->found_posts / $args['posts_per_page'] ),
				'posts'       => array(),
			);

			while ( $posts->have_posts() ) {
				$posts->the_post();
				$data['posts'][] = $this->get_post_data();
			}
		}

		// No Posts
		else {

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
			'share'         => get_permalink(),
			'url'           => get_permalink(),
			'status'        => get_post_status(),
			'title'         => get_the_title(),
			'title_plain'   => the_title_attribute('echo=0'),
			'excerpt'       => get_the_excerpt(),
			'date'          => appbear_get_time(),
			'modified'      => get_post_modified_time(),
			'comment_count' => (int) get_comments_number(),
			'read_time'     => '1 min read', // @fouad
			'author'        => array(
				'name' => get_the_author(),
			),
		);

		// Post Format
		$format = appbear_post_format();

		$the_post['format'] = $format;

		if( $format == 'gallery' ){
			$the_post['gallery'] = appbear_post_gallery();
		}
		elseif( $format == 'video' ){
			$the_post['video'] = appbear_post_video();
		}

		// Return only the first category
		// To do. create a function that work with cats. and tags
		$categories = get_the_category();
		$the_category = array();
		foreach ( $categories as $category ) {
			$the_category['term_id'] = $category->term_id;
			$the_category['name']    = $category->name;
			$the_category['url']     = 'wp-json/wl/v1/posts?categories='. $category->term_id;
			break;
		}

		$the_post['categories'] = array( $the_category );


		// Featured Image
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

		$this_post['content'] = appbear_shortcodes_parsing( $post->post_content );

		// Tags
		// To do. create a function that work with cats. and tags
		$this_post['tags'] = array();
		$tags = get_the_tags( $post->ID );

		if( ! empty( $tags ) ){
			foreach ( $tags as $single_tag ) {
				$this_post['tags'][] = array(
					'term_id' => $single_tag->term_id,
					'name'    => $single_tag->name,
				);
			}
		}

		// Add option later to enable disable
		$this_post['related_posts'] = array();


		// -----
		$comments = array();

		if( get_comments_number() < 100 ){

			$args = array(
				'post_id'       => $post->ID,
				'status'        => 'approve',
				'order'         => 'ASC',
				'type'          => 'comment',
				//'hierarchical'  => 'threaded',
			);

			$get_comments = get_comments( $args );


			foreach ( $get_comments as $comment ) {

				if ( $comment->comment_parent == 0 ) {

					// Set the avatar
					$comment = $this->prepare_comment( $comment );

					// Get Child replies
					$child_comments = get_comments( array(
						'post_id'       => $post->ID,
						'status'        => 'approve',
						'order'         => 'ASC',
						'type'          => 'comment',
						'parent'        => $comment['comment_ID']
					) );

					$replies = array();
					foreach ( $child_comments as $reply ) {
						$replies[] = $this->prepare_comment( $reply );
					}

					$comment['replies'] = $replies;

					// --
					$comments[] = $comment;
				}
			}
		}

		$this_post['comments'] = $comments;


		// ------
		return array( 'post' => $this_post );
	}


	/**
	 *
	 */
	private function prepare_comment( $comment ){

		return array(
			'comment_ID'         => $comment->comment_ID,
			'comment_author'     => $comment->comment_author,
			'comment_author_url' => $comment->comment_author_url,
			'comment_date'       => $comment->comment_date,
			'comment_content'    => $comment->comment_content,
			'comment_parent'     => $comment->comment_parent,
			'author_avatar'      => get_avatar_url( $comment->comment_author_email ),
			'replies'            => array(),
		);

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


	/*/
	 * do_register
	 * Need some checks before wp_create_user.
	 *
	 * @access public
	 * @since 1.0
	 * @return array()
	 *
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
			$response["success"]= true;ƒ
			$response["statusCode"]= 200;
			$response["code"]= "jwt_auth_valid_credential";
			$response["message"]= "Credential is valid";
			$response["token"]= $request['device_token'];
			$response["user"]=$user->data;
		}

		return $response;
	}
	*/

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
			$category->url = "wp-json/wl/v1/posts?categories=" . $category->term_id;
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





	function do_options() {
		return get_option('appbear-settings');
	}




	function do_translations() {
		return get_option('appbear-language');
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







