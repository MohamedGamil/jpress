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
  'name' => __( 'Font Family', 'textdomain' ),
  'id' => 'section-typography-fontfamily',
  'options' => array( 'toggle' => true )
));
$fontfamily->add_field( array(
  'id' => 'section-typography-fontfamily-heading',
  'name' => __( 'Headings Font Family',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => array(
    '' => __('Default', 'textdomain'),
    __( 'Web Safe Fonts',   'textdomain' ) => AppbearItems::web_safe_fonts(),
    __( 'Google Fonts',   'textdomain' ) => AppbearItems::dart_google_fonts()
  ),
  'options' => array(
    'search' => true, // NOTE: Displays an input to search items. Default: false
  )
));
$fontfamily->add_field( array(
  'id' => 'section-typography-fontfamily-body',
  'name' => __( 'Body Font Family',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => array(
    '' => __('Default', 'textdomain'),
    'Web Safe Fonts' => AppbearItems::web_safe_fonts(),
    'Google Fonts' => AppbearItems::dart_google_fonts()
  ),
  'options' => array(
    'search' => true, // NOTE: Displays an input to search items. Default: false
  )
));


$font = $settings->add_section( array(
  'name' => __( 'Font Sizes, Weights and Line Heights', 'textdomain' ),
  'id' => 'section-typography-font',
  'options' => array( 'toggle' => true )
));
$font->open_mixed_field(array('name' => __('Heading: H1', 'textdomain' )));
$font->add_field( array(
  'id' => 'section-typography-font-h1-size',
  'name' => __( 'Font Size',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h1-line_height',
  'name' => __( 'Line Height',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h1-weight',
  'name' => __( 'Font Weight',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h1-transform',
  'name' => __( 'Capitalization',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Heading: H2', 'textdomain' )));
$font->add_field( array(
  'id' => 'section-typography-font-h2-size',
  'name' => __( 'Font Size',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h2-line_height',
  'name' => __( 'Line Height',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h2-weight',
  'name' => __( 'Font Weight',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h2-transform',
  'name' => __( 'Capitalization',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Heading: H3', 'textdomain' ),'desc' => __( 'Example: Sections Title')));
$font->add_field( array(
  'id' => 'section-typography-font-h3-size',
  'name' => __( 'Font Size',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h3-line_height',
  'name' => __( 'Line Height',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h3-weight',
  'name' => __( 'Font Weight',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h3-transform',
  'name' => __( 'Capitalization',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Heading: H4', 'textdomain' ),'desc' => __( 'Example: Post Titles',   'textdomain' )));
$font->add_field( array(
  'id' => 'section-typography-font-h4-size',
  'name' => __( 'Font Size',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h4-line_height',
  'name' => __( 'Line Height',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h4-weight',
  'name' => __( 'Font Weight',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h4-transform',
  'name' => __( 'Capitalization',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Heading: H5', 'textdomain' )));
$font->add_field( array(
  'id' => 'section-typography-font-h5-size',
  'name' => __( 'Font Size',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h5-line_height',
  'name' => __( 'Line Height',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h5-weight',
  'name' => __( 'Font Weight',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h5-transform',
  'name' => __( 'Capitalization',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Heading: H6', 'textdomain' )));
$font->add_field( array(
  'id' => 'section-typography-font-h6-size',
  'name' => __( 'Font Size',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h6-line_height',
  'name' => __( 'Line Height',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h6-weight',
  'name' => __( 'Font Weight',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-h6-transform',
  'name' => __( 'Capitalization',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::text_transform(),
));
$font->close_mixed_field();
$font->open_mixed_field(array('name' => __('Subtitle 1', 'textdomain' ),'desc' => __( 'Example: Meta (tags, author, category, ...)',   'textdomain' ),));
$font->add_field( array(
  'id' => 'section-typography-font-subtitle1-size',
  'name' => __( 'Font Size',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_size(),
));
$font->add_field( array(
  'id' => 'section-typography-font-subtitle1-line_height',
  'name' => __( 'Line Height',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::line_height(),
));
$font->add_field( array(
  'id' => 'section-typography-font-subtitle1-weight',
  'name' => __( 'Font Weight',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_weight(),
));
$font->add_field( array(
  'id' => 'section-typography-font-subtitle1-transform',
  'name' => __( 'Capitalization',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::text_transform(),
));

$font->close_mixed_field();

$font->open_mixed_field(array('name' => __('Subtitle 2', 'textdomain' ),'desc' => __( 'Example: Bottom Bar and Home Page tabs Text')));

$font->add_field( array(
  'id' => 'section-typography-font-subtitle2-size',
  'name' => __( 'Font Size',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_size(),
));

$font->add_field( array(
  'id' => 'section-typography-font-subtitle2-line_height',
  'name' => __( 'Line Height',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::line_height(),
));

$font->add_field( array(
  'id' => 'section-typography-font-subtitle2-weight',
  'name' => __( 'Font Weight',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_weight(),
));

$font->add_field( array(
  'id' => 'section-typography-font-subtitle2-transform',
  'name' => __( 'Capitalization',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::text_transform(),
));

$font->close_mixed_field();

$font->open_mixed_field(array('name' => __('Body 1', 'textdomain' ),'desc' => __( 'Example: Page Titles')));

$font->add_field( array(
  'id' => 'section-typography-font-body1-size',
  'name' => __( 'Font Size',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_size(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body1-line_height',
  'name' => __( 'Line Height',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::line_height(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body1-weight',
  'name' => __( 'Font Weight',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_weight(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body1-transform',
  'name' => __( 'Capitalization',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::text_transform(),
));

$font->close_mixed_field();

$font->open_mixed_field(array('name' => __('Body 2', 'textdomain' )));

$font->add_field( array(
  'id' => 'section-typography-font-body2-size',
  'name' => __( 'Font Size',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_size(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body2-line_height',
  'name' => __( 'Line Height',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::line_height(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body2-weight',
  'name' => __( 'Font Weight',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::font_weight(),
));

$font->add_field( array(
  'id' => 'section-typography-font-body2-transform',
  'name' => __( 'Capitalization',   'textdomain' ),
  'type' => 'select',
  'default' => '',
  'items' => AppbearItems::text_transform(),
));

$font->close_mixed_field();

$settings->close_tab_item('typography');
