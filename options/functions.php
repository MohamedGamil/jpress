<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 *
 */
function appbear_get_option( $name, $default = false ){

	$get_options = get_option('appbear-settings');

	if( isset( $get_options[ $name ] ) ) {
		return $get_options[ $name ];
	}

	if( $default ) {
		return $default;
	}

	return false;
}



function appbear_get_time(){

	$time_format = appbear_get_option( 'time_format' );

	// Human Readable Post Dates
	if( $time_format == 'modern' ){

		$time_now  = current_time( 'timestamp' );
		$post_time = get_the_time( 'U' );

		if ( $post_time > ( $time_now - MONTH_IN_SECONDS ) ){
			$since = sprintf( esc_html__( '%s ago', TIELABS_TEXTDOMAIN ), human_time_diff( $post_time, $time_now ) );
		}
		else {
			$since = get_the_date();
		}
	}

	// Default date format
	else{
		$since = get_the_date();
	}

	return apply_filters( 'AppBear/API/Post/Post_Date', $since );
}


/**
 * appbear_post_format
 *
 * Get the post format of a post by ID
 */
function appbear_post_format( $post_id = null ) {

	if( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if( ! $post_id ) {
		return false;
	}

	// Default WordPress Core post format
	$post_format = get_post_format( $post_id );
	$post_format = $post_format ? $post_format : 'standard';

	// Allow themes to chnage this and apply their custom post formats
	return apply_filters( 'AppBear/API/Post/Post_Format', $post_format, $post_id );
}


/**
 * appbear_post_gallery
 *
 * Get the post gallery of a post by ID
 */
function appbear_post_gallery( $post_id = null ){

	if( ! $post_id ){
		$post_id = get_the_ID();
	}

	if( ! $post_id ){
		return false;
	}

	// Allow themes to chnage this
	return apply_filters( 'AppBear/API/Post/Post_Gallery', array(), $post_id );
}


/**
 * appbear_post_video
 *
 * Get the post gallery of a post by ID
 */
function appbear_post_video( $post_id = null ){

	if( ! $post_id ){
		$post_id = get_the_ID();
	}

	if( ! $post_id ){
		return false;
	}

	// Allow themes to chnage this
	return apply_filters( 'AppBear/API/Post/Post_Video', array(), $post_id );
}

