<?php

/**
 * Typography
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Typography Page
$settings->open_tab_item('typography');

$fontfamily = $settings->add_section( array(
  'name' => __( 'Font Family', 'jpress' ),
  'id' => 'section-typography-fontfamily',
  'options' => array( 'toggle' => true )
));
$fontfamily->add_field( array(
  'id' => 'section-typography-fontfamily-heading',
  'name' => __( 'Headings Font Family',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => array(
    '' => __('Default', 'jpress'),
    __( 'Web Safe Fonts',   'jpress' ) => JPressItems::web_safe_fonts(),
    __( 'Google Fonts',   'jpress' ) => JPressItems::dart_google_fonts()
  ),
  'options' => array(
    'search' => true, // NOTE: Displays an input to search items. Default: false
  )
));
$fontfamily->add_field( array(
  'id' => 'section-typography-fontfamily-body',
  'name' => __( 'Body Font Family',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => array(
    '' => __('Default', 'jpress'),
    'Web Safe Fonts' => JPressItems::web_safe_fonts(),
    'Google Fonts' => JPressItems::dart_google_fonts()
  ),
  'options' => array(
    'search' => true, // NOTE: Displays an input to search items. Default: false
  )
));


$font = $settings->add_section( array(
  'name' => __( 'Font Sizes, Weights and Line Heights', 'jpress' ),
  'id' => 'section-typography-font',
  'options' => array( 'toggle' => true )
));
$font->open_mixed_field(array('name' => __('Heading: H1', 'jpress' )));
$font->add_field( array(
  'id' => 'section-typography-font-h1-size',
  'name' => __( 'Font Size',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h1-line_height',
  'name' => __( 'Line Height',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h1-weight',
  'name' => __( 'Font Weight',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h1-transform',
  'name' => __( 'Capitalization',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Heading: H2', 'jpress' )));
$font->add_field( array(
  'id' => 'section-typography-font-h2-size',
  'name' => __( 'Font Size',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h2-line_height',
  'name' => __( 'Line Height',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h2-weight',
  'name' => __( 'Font Weight',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h2-transform',
  'name' => __( 'Capitalization',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Heading: H3', 'jpress' ),'desc' => __( 'Example: Sections Title')));
$font->add_field( array(
  'id' => 'section-typography-font-h3-size',
  'name' => __( 'Font Size',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h3-line_height',
  'name' => __( 'Line Height',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h3-weight',
  'name' => __( 'Font Weight',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h3-transform',
  'name' => __( 'Capitalization',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Heading: H4', 'jpress' ),'desc' => __( 'Example: Post Titles',   'jpress' )));
$font->add_field( array(
  'id' => 'section-typography-font-h4-size',
  'name' => __( 'Font Size',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h4-line_height',
  'name' => __( 'Line Height',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h4-weight',
  'name' => __( 'Font Weight',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h4-transform',
  'name' => __( 'Capitalization',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Heading: H5', 'jpress' )));
$font->add_field( array(
  'id' => 'section-typography-font-h5-size',
  'name' => __( 'Font Size',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h5-line_height',
  'name' => __( 'Line Height',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h5-weight',
  'name' => __( 'Font Weight',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h5-transform',
  'name' => __( 'Capitalization',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Heading: H6', 'jpress' )));
$font->add_field( array(
  'id' => 'section-typography-font-h6-size',
  'name' => __( 'Font Size',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h6-line_height',
  'name' => __( 'Line Height',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h6-weight',
  'name' => __( 'Font Weight',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h6-transform',
  'name' => __( 'Capitalization',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Subtitle 1', 'jpress' ),'desc' => __( 'Example: Meta (tags, author, category, ...)',   'jpress' ),));
$font->add_field( array(
  'id' => 'section-typography-font-subtitle1-size',
  'name' => __( 'Font Size',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-subtitle1-line_height',
  'name' => __( 'Line Height',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-subtitle1-weight',
  'name' => __( 'Font Weight',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-subtitle1-transform',
  'name' => __( 'Capitalization',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::text_transform(),
));

$font->close_mixed_field();

$font->open_mixed_field(array('name' => __('Subtitle 2', 'jpress' ),'desc' => __( 'Example: Bottom Bar and Home Page tabs Text')));

$font->add_field( array(
  'id' => 'section-typography-font-subtitle2-size',
  'name' => __( 'Font Size',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_size(),
));

$font->add_field( array(
  'id' => 'section-typography-font-subtitle2-line_height',
  'name' => __( 'Line Height',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::line_height(),
));

$font->add_field( array(
  'id' => 'section-typography-font-subtitle2-weight',
  'name' => __( 'Font Weight',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_weight(),
));

$font->add_field( array(
  'id' => 'section-typography-font-subtitle2-transform',
  'name' => __( 'Capitalization',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::text_transform(),
));

$font->close_mixed_field();

$font->open_mixed_field(array('name' => __('Body 1', 'jpress' ),'desc' => __( 'Example: Page Titles')));

$font->add_field( array(
  'id' => 'section-typography-font-body1-size',
  'name' => __( 'Font Size',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_size(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body1-line_height',
  'name' => __( 'Line Height',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::line_height(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body1-weight',
  'name' => __( 'Font Weight',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_weight(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body1-transform',
  'name' => __( 'Capitalization',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::text_transform(),
));

$font->close_mixed_field();

$font->open_mixed_field(array('name' => __('Body 2', 'jpress' )));

$font->add_field( array(
  'id' => 'section-typography-font-body2-size',
  'name' => __( 'Font Size',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_size(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body2-line_height',
  'name' => __( 'Line Height',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::line_height(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body2-weight',
  'name' => __( 'Font Weight',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::font_weight(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body2-transform',
  'name' => __( 'Capitalization',   'jpress' ),
  'type' => 'select',
  'default' => '',
  'items' => JPressItems::text_transform(),
));

$font->close_mixed_field();

$settings->close_tab_item('typography');
