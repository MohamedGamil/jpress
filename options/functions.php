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
	return apply_filters( 'AppBear/API/Post/Post_Video', '', $post_id );
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



function appbear_shortcodes_parsing($content)
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

