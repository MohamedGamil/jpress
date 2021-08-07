<?php

/**
 * Onboarding
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: User Guide Page (Onboarding)
$settings->open_tab_item('user_guide');

$section_header_2 = $settings->add_section( array(
  'name' => __( 'User Guide Slides', 'jpress' ),
  'id' => 'local-section_userguide_slides',
  'desc' => __( 'Slides which your clients will see when they first start your application', 'jpress' ),
  'options' => array( 'toggle' => true )
));

$section_header_2->add_field(array(
  'name' => __( 'Enabled', 'jpress' ),
  'id' => 'onboarding',
  'type' => 'switcher',
  'default'	=>	'false',
  'options' => array(
    'on_value' => 'true',
    'off_value' => 'false'
  )
));

$slides = $section_header_2->add_group( array(
  'name' => __('User Guide Slides', 'jpress'),
  'id' => 'onboardmodels',
  'options' => array(
  'add_item_text' => __('New Slide', 'jpress'),
    'show_if' => array('onboarding', '=', 'true'),
  ),
  'controls' => array(
  'name' =>  __('Slide', 'jpress').' #',
  'readonly_name' => false,
  'images' => true,
  'default_image' => JPRESS_URL . 'assets/img/transparent.png',
  'image_field_id' => 'image',
  'height' => '190px',
  ),
));

$slides->add_field(array(
  'id' => 'title',
  'name' => __('Slide Title', 'jpress'),
  'type' => 'text',
  'grid' => '3-of-6',
));

$slides->add_field(array(
  'id' => 'subTitle',
  'name' => __('SubTitle', 'jpress'),
  'type' => 'text',
'grid' => '3-of-6'
));

$slides->add_field(array(
  'id' => 'image',
  'name' => __( 'Image', 'jpress' ),
  'type' => 'file',
));

$settings->close_tab_item('user_guide');
