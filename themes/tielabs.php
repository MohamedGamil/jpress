<?php



add_filter( 'AppBear/API/Post/Post_Format', 'appbear_theme_tielabs_post_format', 10, 2 );
function appbear_theme_tielabs_post_format( $format, $post_id ){

  $get_format = get_post_meta( $post_id, 'tie_post_head', true );

  if( ! empty( $get_format ) ){

    if( $get_format == 'slider' ){
      $format = 'gallery';
    }
    elseif( $get_format == 'video' ){
      $format = 'video';
    }
  }

  return $format;
}



add_filter( 'AppBear/API/Post/Post_Gallery', 'appbear_theme_tielabs_post_format_gallery', 10, 2 );
function appbear_theme_tielabs_post_format_gallery( $images, $post_id ){

  $get_images = get_post_meta( $post_id, 'tie_post_gallery', true );

  if( ! empty( $get_images ) && is_array( $get_images ) ){

    foreach( $get_images as $single_image ){
      $image = wp_get_attachment_image_src( $single_image['id'], 'large' );
      $images[] = $image[0];
    }
  }

  return $images;
}




add_filter( 'AppBear/API/Post/Post_Video', 'appbear_theme_tielabs_post_format_video', 10, 2 );
function appbear_theme_tielabs_post_format_video( $video, $post_id ){

  return get_post_meta( $post_id, 'tie_video_url', true );
}
