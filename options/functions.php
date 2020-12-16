<?php

defined('ABSPATH') || exit; // Exit if accessed directly


/**
 * Get a Plugin Option
 *
 * @param string $name Option name
 * @param mixed $default Option default value
 */
function appbear_get_option($name, $default = false)
{
	$opts = get_option('appbear-settings');
	return isset($opts[$name]) && $opts[$name] ? $opts[$name] : $default;
}


/**
 * Get Time Format
 */
function appbear_get_time()
{
	$time_format = appbear_get_option('time_format');

	// Human Readable Post Dates
	if ($time_format == 'modern') {
		$time_now  = current_time('timestamp');
		$post_time = get_the_time('U');

		if ($post_time > ($time_now - MONTH_IN_SECONDS)) {
			// NOTE: Why use `TIELABS_TEXTDOMAIN` ?
			$since = sprintf(esc_html__('%s ago', TIELABS_TEXTDOMAIN), human_time_diff($post_time, $time_now));
		} else {
			$since = get_the_date();
		}
	}

	// Default date format
	else {
		$since = get_the_date();
	}

	return apply_filters('AppBear/API/Post/Post_Date', $since);
}


/**
 * Get Post Format
 *
 * @param int $post_id Post ID (Optional for current post ID)
 */
function appbear_post_format($post_id = null)
{
	if (( $post_id = $post_id ?? get_the_ID() ) === false) {
		return null;
	}

	// Default WordPress Core post format
	$post_format = get_post_format($post_id);
	$post_format = $post_format ? $post_format : 'standard';

	// Allow themes to chnage this and apply their custom post formats
	return apply_filters('AppBear/API/Post/Post_Format', $post_format, $post_id);
}


/**
 * Get the post gallery of a post by ID
 *
 * @param int $post_id Post ID (Optional for current post ID)
 */
function appbear_post_gallery($post_id = null)
{
	if (( $post_id = $post_id ?? get_the_ID() ) === false) {
		return null;
	}

	// TODO: Empty function default logic? may need work!

	// Allow themes to chnage this
	return apply_filters('AppBear/API/Post/Post_Gallery', array(), $post_id);
}


/**
 * Get the post video of a post by ID
 *
 * @param int $post_id Post ID (Optional for current post ID)
 */
function appbear_post_video($post_id = null)
{
	if (( $post_id = $post_id ?? get_the_ID() ) === false) {
		return null;
	}

	// TODO: Empty function default logic? may need work!

	// Allow themes to chnage this
	return apply_filters('AppBear/API/Post/Post_Video', '', $post_id);
}


/**
 * Get a Template File
 *
 * @param string $file Template File Path
 */
function appbear_get_template($templatePath, $vars = [])
{
	// NOTE: Should be substitued with a template file..
	$prefix = APPBEAR_DIR . 'templates';
	$templatePath = str_replace('.php', '', $templatePath);
  $path = $prefix . DIRECTORY_SEPARATOR . $templatePath . '.php';

  if (is_file($path)) {
    // Start a new output buffer
		ob_start();

    // Extract passed template vars if needed
    if (empty($vars) === false) {
      extract($vars);
    }

    // Include template in current output buffer
    include $path;

    // Get template output
    $getOutputBuffer = ob_get_clean();

    // Apply filters on template output before using it
    return apply_filters('AppBear/API/Template', $getOutputBuffer, $templatePath, $vars);
	}

  throw new Error("Template '{$templatePath}' not found!");
}


/**
 * Get public key
 */
function appbear_get_public_key()
{
  return trim( get_option( 'appbear_public_key' ) );
}


/**
 * Get license key
 */
function appbear_get_license_key()
{
  return trim( get_option( 'appbear_license_key' ) );
}


/**
 * Get deeplinking options
 */
function appbear_get_deeplinking_opts($asArray = false)
{
  $allOpts = get_option( 'appbear-settings' );

  $opts = (Object) array(
    'appid_ios' => $allOpts['appid_ios'],
    'name_ios' => $allOpts['bundle_name_ios'],
    'name_android' => $allOpts['bundle_name_android'],
    'widget_enabled' => $allOpts['deeplinking_widget_enabled'],
  );

  return $asArray ? (array) $opts : $opts;
}


/**
 * Check if dev mode is active
 */
function _appbear_is_dev_mode()
{
  return APPBEAR_ENABLE_LICENSE_DEBUG_MODE === true && in_array($_SERVER['REMOTE_ADDR'], [ '127.0.0.1', '::1' ]);
}


/**
 * Check current license validity
 */
function appbear_check_license()
{
  return ( get_option( 'appbear_license_status' ) === 'valid' && empty(appbear_get_public_key()) === false ) || _appbear_is_dev_mode();
}


// FIXME: Missing docs comment
function appbear_shortcodes_parsing($content)
{

	// NOTE: A couple of things needs to be done here:
	//            1) Revise each replacement
	//            2) A better optimized way to replace strings
	//            3) A more dynamic approach to handle replacement cases


	// $pattern = '@(?<=)\[tie_list type="(.*?)(?=)"](?=)(.*?)\[/tie_list](?=)@sm';
	// $replacement = '
	// <div class="tie_list $1">
	// 	$2
	// </div>
	// ';
	// $string = preg_replace($pattern, $replacement, $string);


	// $pattern = '/\[gallery [\s\S]*\]/i';
  // preg_match_all($pattern, $content, $matches);
  // dd($matches, $content);

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

	$pattern = '/\[one\_[_a-zA-Z]+\]/i';
	$string = preg_replace($pattern, "<div>", $string);
	$pattern = '/\[two\_[_a-zA-Z]+\]/i';
	$string = preg_replace($pattern, "<div>", $string);
	$pattern = '/\[\/one\_[_a-zA-Z]+\]/i';
	$string = preg_replace($pattern, "</div>", $string);
	$pattern = '/\[\/two\_[_a-zA-Z]+\]/i';
	$string = preg_replace($pattern, "</div>", $string);
	$pattern = '/\[three\_[_a-zA-Z]+\]/i';
	$string = preg_replace($pattern, "<div>", $string);
	$pattern = '/\[\/three\_[_a-zA-Z]\]/i';
	$string = preg_replace($pattern, "</div>", $string);
	$pattern = '/\[five\_[_a-zA-Z]\]/i';
	$string = preg_replace($pattern, "<div>", $string);
	$pattern = '/\[\/five\_[_a-zA-Z]\]/i';
	$string = preg_replace($pattern, "</div>", $string);
	// $pattern = '/[[0-9]+\/[0-9]+\]/i';
	// $string = preg_replace($pattern, "", $string);


	// $string = str_replace('<p>', "<div>", $string);
	// $string = str_replace('</p>', "</div>", $string);


	// Gallery Start
	$pos = strpos($string, "ids=\"");
	$new_string  = substr($string, $pos + 5);
	$second_pos = strpos($new_string, "\"");
	$ids = substr($new_string, 0, $second_pos);
	$ids = explode(",", $ids);
	$output = "";
	$attr = array();
	$attr['includes'] = $ids;
	$html5 = current_theme_supports('html5', 'gallery');
	$atts  = shortcode_atts(
		array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,
			'itemtag'    => $html5 ? 'figure' : 'dl',
			'icontag'    => $html5 ? 'div' : 'dt',
			'captiontag' => $html5 ? 'figcaption' : 'dd',
			'columns'    => 3,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => '',
			'link'       => '',
		),
		$attr,
		'gallery'
	);
	foreach ($ids as &$value) {
		$_attachments = get_posts(
			array(
				'include'        => $atts['include'],
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
			)
		);

		$attachments = array();
		foreach ($_attachments as $key => $val) {
			$attachments[$val->ID] = $_attachments[$key];
		}
		$output = "\n";
		foreach ($attachments as $att_id => $attachment) {
			if (!empty($atts['link'])) {
				if ('none' === $atts['link']) {
					$output .= wp_get_attachment_image($att_id, $atts['size'], false, $attr);
				} else {
					$output .= wp_get_attachment_link($att_id, $atts['size'], false);
				}
			} else {
				$output .= wp_get_attachment_link($att_id, $atts['size'], true);
			}
			$output .= "\n";
		}
		if (apply_filters('use_default_gallery_style', !$html5)) {
			$type_attr = current_theme_supports('html5', 'style') ? '' : ' type="text/css"';
			$gallery_style = "
			               <style{$type_attr}>
			                       #{$selector} {
			                               margin: auto;
			                       }
			                       #{$selector} .gallery-item {
			                               float: {$float};
			                               margin-top: 10px;
			                               text-align: center;
			                               width: {$itemwidth}%;
			                       }
			                     #{$selector} img {
			                               border: 2px solid #cfcfcf;
			                       }
			                       #{$selector} .gallery-caption {
			                               margin-left: 0;
			                       }
			                       /* see gallery_shortcode() in wp-includes/media.php */
			               </style>\n\t\t";
		}
		$size_class  = sanitize_html_class($atts['size']);
		$gallery_div = "<div id='1' class='gallery galleryid-1 gallery-columns-4 gallery-size-{$size_class}'>";
		$output = apply_filters('gallery_style', $gallery_style . $gallery_div);
		$i = 0;
		foreach ($attachments as $id => $attachment) {
			$attr = (trim($attachment->post_excerpt)) ? array('aria-describedby' => "$selector-$id") : '';
			if (!empty($atts['link']) && 'file' === $atts['link']) {
				$image_output = wp_get_attachment_link($id, $atts['size'], false, false, false, $attr);
			} elseif (!empty($atts['link']) && 'none' === $atts['link']) {
				$image_output = wp_get_attachment_image($id, $atts['size'], false, $attr);
			} else {
				$image_output = wp_get_attachment_link($id, $atts['size'], true, false, false, $attr);
			}
			$image_meta = wp_get_attachment_metadata($id);
			$orientation = '';
			if (isset($image_meta['height'], $image_meta['width'])) {
				$orientation = ($image_meta['height'] > $image_meta['width']) ? 'portrait' : 'landscape';
			}
			$output .= "<{$itemtag} class='gallery-item'>";
			$output .= "
                       <{$icontag} class='gallery-icon {$orientation}'>
                               $image_output
                       </{$icontag}>";
			if ($captiontag && trim($attachment->post_excerpt)) {
				$output .= "
                               <{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
                               " . wptexturize($attachment->post_excerpt) . "
                               </{$captiontag}>";
			}
			$output .= "</{$itemtag}>";
			if (!$html5 && $columns > 0 && 0 === ++$i % $columns) {
				$output .= '<br style="clear: both" />';
			}
		}
		if (!$html5 && $columns > 0 && 0 !== $i % $columns) {
			$output .= "
                       <br style='clear: both' />";
		}
		$output .= "
               </div>\n";
	}
	// print_r($images);
	// echo"</pre>";

  // DEPRECATED: The following line applies the above logic to the string while omiting the rest of the string,
  //                    this behavior introduce unwanted effect in the mobile app resulting in a missing post content.
  //                    Plus it looks like the gallery shortcode is already pre-applied in the post content, thus the
  //                    entirety of this code block needs proper refactoring.
	// $pattern = '/\[gallery [\s\S]*\]/i';
  // $string = preg_replace($pattern, $output, $string);


	// exit();
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

	$string = str_replace("[tie_full_img]", "<img>", $string);
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

		<form name="registerform" action="' . get_home_url() . '/wp-login.php" method="post">
			<input type="text" name="log" title="Username" placeholder="Username">
			<div class="pass-container">
				<input type="password" name="pwd" title="Password" placeholder="Password">
				<a class="forget-text" href="' . get_home_url() . '/wp-login.php?action=lostpassword&redirect_to=' . get_home_url() . '">Forget?</a>
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

	// $string = apply_filters( 'the_content', $string );

	$string = str_replace(" ]", ">", $string);
	$string = str_replace("\"]", "\">", $string);

	return $string;
}


if (!function_exists('dd')):

/**
 * Simple debugging helper functions
 *
 * @since      0.0.2
 * @package    App_Bear
 * @subpackage App_Bear/options
 */
function dd() {
  if (!APPBEAR_ENABLE_DEBUG_HELPERS) return;

  $args = func_get_args();
  $newLine = "\n\n------------------%s------------------\n\n\n";

  @ob_get_clean();
  @ob_flush();

  header('Content-Type: text/html; charset=UTF-8');
  echo "<body style='background: #1c1c1c; color: #FFF'><pre>\n";

  foreach($args as $k => $arg) {
      $kk = $k + 1;
      $argTitle = " Argument #{$kk} ";
      print('<span style="color:#888">');
      printf($newLine, $argTitle);
      print('</span>');

      switch(TRUE) {
          case (is_bool($arg)) === TRUE:
          case (is_string($arg)) === TRUE:
          case (is_numeric($arg)) === TRUE:
              var_dump($arg);
              break;
          default:
              print_r($arg);
              break;
      }

      if ($k !== (count($args) -1)) {
          $sep = sprintf($newLine, str_repeat('-', strlen($argTitle)));
          print('<span style="color:#555">');
          print( str_replace('-', '_', $sep) . "\n" );
          print('</span>');
      }
  }

  echo "\n</pre></body>";
  die;
}

function ddjson() {
  if (!APPBEAR_ENABLE_DEBUG_HELPERS) return;

  header('Content-Type: application/json; charset=UTF-8');
  $args = func_get_args();
  echo json_encode($args);
  die;
}

endif;
