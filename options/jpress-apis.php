<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * JPress_Endpoints Class
 *
 * This class handles all API Endpoints
 *
 *
 * @since 0.0.1
 */
class JPress_Endpoints {
  const DEFAULT_POSTS_PER_PAGE_COUNT = 10;
  const AD_SECTION_KEYWORD = 'advert';

  /**
   * Endpoint namespace.
   *
   * @var string
   */
  protected $namespace = 'jpress/v1';

  /**
   * Internal initilization state &
   * internal singlton instance.
   *
   * @var boolean
   */
  static protected $_didInit = false;
  static protected $_localInstance = null;


  /**
   * Run hooks initilization
   */
  static public function run() {
    if (static::$_didInit === true && is_null(static::$_localInstance) === false) {
      return;
    }

    static::$_localInstance = new JPress_Endpoints();
    static::$_didInit = true;
  }


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
      'posts',
      'post',
      'categories',
      'page',
      'add-comment',
      'get-version',
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
      'login',
      'contact-us',
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

		if ( method_exists( $this, $callback ) ) {
			register_rest_route( $this->namespace, $route, array(
				'methods'  => $method,
				'callback' => array( $this, $callback ),
				'permission_callback' => '__return_true', // Required since WordPress 5.5
			));
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
    $sections = isset($request['sections']) ? $request['sections'] : array();

    if (empty($sections) === false) {
      $response = [];

      foreach ($sections as $section) {
        $params = [];
        $isAdvert = true;

        if ( $section !== static::AD_SECTION_KEYWORD ) {
          $isAdvert = false;
          @parse_str( $section, $params );
        }

        $response[] = $this->_queryPosts($params, $isAdvert);
      }

      return array(
        'sections' => $response,
      );
    }

    return $this->_queryPosts($request);
	}


  /**
   * get_post_data
   *
   * @access public
   * @since 1.0
   * @return array()
   */
  public function get_post_data() {
    $post_id = get_the_ID();

    $the_post = array(
      'id'            => $post_id,
      'share'         => get_permalink(),
      'url'           => get_permalink(),
      'status'        => get_post_status(),
      'title'         => get_the_title(),
      'title_plain'   => the_title_attribute('echo=0'),
      'excerpt'       => get_the_excerpt(),
      'date'          => jpress_get_time(),
      'modified'      => get_post_modified_time(),
      'comment_count' => (int) get_comments_number(),
      'read_time'     => jpress_get_read_time(), // @fouad
      'author'        => array(
        'name' => get_the_author(),
      ),
    );

    // Post Format
    $format = jpress_post_format();

    $the_post['format'] = $format;

    if ( $format == 'gallery' ) {
      $the_post['gallery'] = jpress_post_gallery();
    }
    elseif ( $format == 'video' ) {
      $the_post['video'] = jpress_post_video();
    }

    // Return only the first category
    // To do. create a function that work with cats. and tags
    $categories = get_the_category();
    $the_category = array();

    // NOTE: Bad code?
    foreach ( $categories as $category ) {
      $the_category['term_id'] = $category->term_id;
      $the_category['name']    = $category->name;
      $the_category['url']     = 'wp-json/jpress/v1/posts?categories='. $category->term_id;
      break;
    }

    $the_post['categories'] = array( $the_category );


    // Featured Image
    $thumbnail = get_the_post_thumbnail_url( $post_id, 'thumbnail' );

    if ($thumbnail != false) {
      $the_post['thumbnail'] = $thumbnail;
      $the_post['featured_image'] = array(
        'thumbnail' => $thumbnail,
        'medium'    => get_the_post_thumbnail_url( $post_id, 'medium' ),
        'large'     => get_the_post_thumbnail_url( $post_id, 'large' ),
      );
    }

    return apply_filters( 'JPress/API/Post/data', $the_post );
  }


  /**
   * do_post
   *
   * @access public
   * @since 1.0
   * @return array()
   */
  public function do_post( $request ) {
    // Get the Post Object
    global $post;

    // TODO: Output json error
    if ( empty( $request['id'] ) ) {
      return false;
    }

    $post = get_post( $request['id'] );

    if ( ! $post ) {
      return false;
    }

    setup_postdata( $post );

    $this_post = $this->get_post_data();

    // $this_post['content'] = apply_filters( 'the_content', $post->post_content );
    $this_post['content'] = jpress_shortcodes_parsing( $post->post_content );
    $this_post['content'] = apply_filters( 'the_content', $this_post['content'] );

    // Tags
    // To do. create a function that work with cats. and tags
    $this_post['tags'] = array();
    $postID = $this_post['id'];
    $tags = get_the_tags( $postID );

    if ( ! empty( $tags ) ) {
      foreach ( $tags as $single_tag ) {
        $this_post['tags'][] = array(
          'term_id' => $single_tag->term_id,
          'name'    => $single_tag->name,
        );
      }
    }

    // Add option later to enable disable
    $this_post['related_posts'] = $this->do_posts(
      array(
        'categories' => wp_get_post_categories(),
        'exclude'    => $request['id'], // Exclude current post
        'count'      => 5,
      )
    );

    $comments = array();

    $args = array(
      'post_id' => $postID,
      'status' => 'approve',
      'order' => 'ASC',
      'post_status' => 'publish',
      'type' => 'comment',

      // 'lang' => get_locale(),
      // 'hierarchical' => 'threaded',
    );

    $get_comments = get_comments( $args );

    // NOTE: May require a limit parameter

    foreach ( $get_comments as $comment ) {
      if ( absint($comment->comment_parent) === 0 ) {
        // Set the avatar
        $comment = $this->_prepare_comment( $comment );

        // Get Child replies
        $child_comments = get_comments( array(
          'post_id'       => $postID,
          'status'        => 'approve',
          'order'         => 'ASC',
          'type'          => 'comment',
          'parent'        => $comment['comment_ID']
        ));

        $replies = array();

        foreach ( $child_comments as $reply ) {
          $replies[] = $this->_prepare_comment( $reply );
        }

        $comment['replies'] = $replies;
        $comments[] = $comment;
      }
    }

    // NOTE: Debug line
    // dd( 2, $comments );

    $this_post['comments'] = $comments;

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
      'version' => get_option('jpress-version')
    );
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

    if (is_wp_error($data)) {
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


  /**
   * do_categories()
   *
   * @access public
   * @since 1.0
   * @return void
   */
  public function do_categories() {
    $metadata = JPress_Categories::get_metadata();

    foreach( $metadata as $key => &$cat ) {
      $cat = isset($cat['image']) && $cat['image'] ? $cat['image'] : '';
    }

    $categories = get_categories( array(
      'orderby' => 'name',
      'order'   => 'ASC'
    ));

    $the_cats = array();

    foreach ( $categories as $category ) {
      // $category->image_url = get_term_meta( $category->term_id, 'cat_image', true );

      $imageId = isset($metadata[$category->term_id]) ? $metadata[$category->term_id] : '';
      $imageUrl = wp_get_attachment_url( $imageId, 'thumbnail' );
      $category->image_id = $imageId;

      if (empty($imageUrl) === false) {
        $category->image_url =  $imageUrl;
      }

      $category->url = "wp-json/jpress/v1/posts?categories=" . $category->term_id;

      $the_cats[] = (array) $category;
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
  public function do_page( $request ) {
    if ( ! empty( $request['id'] ) ) {

      $page = get_post( $request['id'] );

      if ( $page ) {
        return apply_filters( 'JPress/API/Page/data', array(
          'status' => true,
          'id'     => $page->ID,
          'post' => array(
            'title'   => $page->post_title,
            'content' => jpress_shortcodes_parsing( $page->post_content ),
          ),
        ));
      }
    }

    return array(
      'status'=> false,
    );
  }


  /**
   * do_add_comment()
   *
   * @access public
   * @since 1.0
   * @return array()
   */
	public function do_add_comment() {
		$param = $_GET;
    $data = array();

		if (isset($param['post_id'])) {
      $data['comment_post_ID'] = $param['post_id'];
    }

		if (isset($param['comment_content'])) {
      $data['comment_content'] = $param['comment_content'];
    }

		if (isset($param['comment_author'])) {
      $data['comment_author'] = $param['comment_author'];
    }

		if (isset($param['comment_parent'])) {
      $data['comment_parent'] = $param['comment_parent'];
    }

		if (isset($param['comment_author_email'])) {
      $data['comment_author_email'] = $param['comment_author_email'];
    }

    $result = wp_insert_comment($data);

		if ($result > 0) {
			$response['status'] = true;
			$response['message'] = 'Comment added successfuly';
			return $response;
    }
    else {
			$response['status'] = false;
			$response['message'] = 'failure';
			return $response;
		}
  }


  /*
   * Contact us action
   */
  public function do_contact_us() {
    $data = [];
    $response = [];
    $error = false;
    $errorMessages = [];

    $to = jpress_get_option('local-settingspage-contactus');
    $subject = __( 'Contact Us Message', 'jpress' );

    foreach (['name', 'email', 'message'] as $field) {
      // NOTE: Should use $_POST instead?
      $data[$field] = trim($_GET[$field]);
      $capitalizedField = ucfirst($field);

      if (empty($data[$field])) {
        $error = true;
        $errorMessages[] = __( "'{$capitalizedField}' input is empty!", 'jpress' );
      }
    }

    // Validate email address input
    if ($error === false && filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
      $error = true;
      $errorMessages[] = __( 'Invalid Email Address!', 'jpress' );
    }

    // Validate owner/admin email address setting
    if (!$to) {
      $error = true;
      $errorMessages[] = __( 'Missing admin email address setting!', 'jpress' );
    }

    // Proceed if passing empty fields checks..
    if ($error === false) {
      $defaultLogo = jpress_get_template('contact_us/contact_us_logo');
      $customLogo = jpress_get_option('logo');
      $themeMode = jpress_get_option('thememode');
      $logoLight = jpress_get_option('logo-light');
      $logoDark = jpress_get_option('logo-dark');

      if ($customLogo) {
        foreach ( ['light', 'dark' ] as $logoMode ) {
          $varKey = 'logo'. ucfirst($logoMode);
          $$varKey = str_replace(' ', '', $$varKey);
        }

        $logoImg = $themeMode === 'themeMode_light' && $logoLight ? $logoLight : $logoDark;
        $logo = "<img src='{$logoImg}' alt='' style='line-height: inherit; display: block; height: auto; width: 100%; border: 0; max-width: 85px;' width='85' />";
      } else {
        $logo = $defaultLogo;
      }

      $body = jpress_get_template('contact_us/contact_us', compact('data'));
      $headers = ['Content-Type: text/html; charset=UTF-8'];
      $result = @wp_mail($to, $subject, $body, $headers);

      if ($result === false) {
        $error = true;
        $errorMessages[] = __( 'Error! Unable to send email message!', 'jpress' );
      }
    }

    if ($error === true) {
      $response['status'] = false;

      // NOTE: FIXME: This should be returned as array for use in a better user-experience
      $response['error'] = implode("\n", $errorMessages);
    } else {
      $response['status'] = true;
      $response['message'] = __( 'Your message was sent successfuly.', 'jpress' );
    }

    return $response;
  }


  /*
   * Options
   */
  public function do_options() {
    return get_option('jpress-options');
  }


  /*
   * Translations
   */
  public function do_translations() {
    return get_option('jpress-language');
  }


  /*
   * Dev Mode
   */
  public function do_dev_mode( $request ) {
    if ( ! empty( $request['action'] ) ) {
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


  /**
   * Query Posts
   *
   * @param array $request Request parameters
   * @param boolean $forceEmpty Force an empty response (Like in case of a query on a home page advert section)
   * @return array
   */
  protected function _queryPosts($request, $forceEmpty = false) {
    $args = array(
      'post_type'      => ! empty( $request['post'] )  ? $request['post']  : 'post',
      'posts_per_page' => ! empty( $request['count'] ) ? $request['count'] : -1,
    );

    $argsPairs = array(
      'paged' => 'page',
      'cat' => 'categories',
      'offset' => 'offset',
      's' => 's',
    );

    $arrayPairs = array(
      'tag__in' => 'tags',
      'post__in' => 'ids',
      'post__not_in' => 'exclude',
    );

    // NOTE: Arguments Pairs [ "Pagination", "Categories", "Tags", "Inclusive / Exclusive Posts by IDs", "Offsetting", "Search" ]
    foreach ( $argsPairs as $key => $requestKey ) {
      if ( isset($request[$requestKey]) ) {
        $args[ $key ] = $request[ $requestKey ];
      }
    }

    foreach ( $arrayPairs as $key => $requestKey ) {
      if ( isset($request[$requestKey]) ) {
        $args[ $key ] = explode( ',', $request[ $requestKey ] );
      }
    }

    // NOTE: Introduce default value for count parameter to prevent pagination issues
    if (isset($args['paged']) && $args['posts_per_page'] === -1) {
      $args['posts_per_page'] = static::DEFAULT_POSTS_PER_PAGE_COUNT;
    }

    // NOTE: Adjust offset parameter to play nice with pagination
    if ( isset($args['paged'], $args['offset']) ) {
      $args['offset'] = ( $args['paged'] - 1 ) * $args['posts_per_page'] + $args['offset'];
    }

    // Sorting
    if ( isset( $request['sort'] ) ) {
      $args['order']   = '';
      $args['orderby'] = str_replace( 'Sort.', '', $request['sort'] );
    }

    if ($forceEmpty === false) {
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

        return $data;
      }
    }

    return array(
      'status'      => false,
      'count'       => 0,
      'count_total' => 0,
      'pages'       => 0,
    );
  }


  /**
   * Prepare comment
   *
   * @param WP_Comment $comment Comment Object
   * @return array Processed Comment Array
   */
  private function _prepare_comment( $comment ) {
    return array(
      'comment_ID' => $comment->comment_ID,
      'comment_author' => $comment->comment_author,
      'comment_author_url' => $comment->comment_author_url,
      'comment_date' => $comment->comment_date,
      'comment_content' => $comment->comment_content,
      'comment_parent' => $comment->comment_parent,
      'author_avatar' => get_avatar_url( $comment->comment_author_email ),
      'replies' => array(),
    );
  }


  /**
   * DEPRECATED: Get Single Category Options
   *
   * @deprecated 0.2.0 Will be removed!
   * @return array
   */
  private function _getSingleCategoryOptions() {
    $jpressOpts = jpress_get_option('%ALL%');
    $linkType = $jpressOpts['single_cat_ad_image_link_type'];
    $linkValue = $jpressOpts['single_cat_ad_image_link_url'];

    switch ($linkType) {
      case 'NavigationType.category':
        $category = get_category_by_slug($jpressOpts['single_cat_ad_image_link_category']);

        if (empty($category) === false) {
          $linkValue = '/wp-json/jpress/v1/posts?categories=' . $category->term_id;
        }
        break;

      case 'NavigationType.page':
        $post = get_post($jpressOpts['single_cat_ad_image_link_page']);

        if ($post) {
          $linkValue = '/wp-json/jpress/v1/page?id=' . $post->ID;
        }
        break;

      case 'NavigationType.main':
        $linkValue = $jpressOpts['single_cat_ad_image_link_main'];
        break;
    }

    $imageAd = array(
      'img' => $jpressOpts['single_cat_ad_image_file'],
      'type' => $linkType,
      'value' => $linkValue,
    );

    $singleCatOptions = array(
      'adType' => str_replace('PostLayout.', '', $jpressOpts['local_ads_single_cat_type']),
      'htmlAd' => array(
        'content' => $jpressOpts['single_cat_ad_section_html'],
      ),
      'imageAd' => $imageAd,
    );

    // NOTE: Debug line
    // dd($singleCatOptions);

    return $singleCatOptions;
  }
}
