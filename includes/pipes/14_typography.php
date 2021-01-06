<?php

/**
 * Typography
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


if (isset($data['section-typography-fontfamily-heading']) && $data['section-typography-fontfamily-heading'] !== '') {
  $options['typography']['headline1']["fontFamily"]   = $data['section-typography-fontfamily-heading'];
  $options['typography']['headline2']["fontFamily"]   = $data['section-typography-fontfamily-heading'];
  $options['typography']['headline3']["fontFamily"]   = $data['section-typography-fontfamily-heading'];
  $options['typography']['headline4']["fontFamily"]   = $data['section-typography-fontfamily-heading'];
  $options['typography']['headline5']["fontFamily"]   = $data['section-typography-fontfamily-heading'];
}

if (isset($data['section-typography-fontfamily-heading']) && $data['section-typography-fontfamily-body'] !== '') {
  $options['typography']['subtitle1']["fontFamily"]   = $data['section-typography-fontfamily-body'];
  $options['typography']['subtitle2']["fontFamily"]   = $data['section-typography-fontfamily-body'];
  $options['typography']['bodyText1']["fontFamily"]   = $data['section-typography-fontfamily-body'];
  $options['typography']['bodyText2']["fontFamily"]   = $data['section-typography-fontfamily-body'];
}

if (isset($data['section-typography-font-h1-size']) && $data['section-typography-font-h1-size'] !== '') {
  $options['typography']['headline1']["fontSize"]     = $data['section-typography-font-h1-size'];
}

if (isset($data['section-typography-font-h1-line_height']) && $data['section-typography-font-h1-line_height'] !== '') {
  $options['typography']['headline1']["lineHeight"]   = $data['section-typography-font-h1-line_height'];
}

if (isset($data['section-typography-font-h1-weight']) && $data['section-typography-font-h1-weight'] !== '') {
  $options['typography']['headline1']["fontWeight"]   = $data['section-typography-font-h1-weight'];
}

if (isset($data['section-typography-font-h1-transform']) && $data['section-typography-font-h1-transform'] !== '') {
  $options['typography']['headline1']["fontTransform"]    = $data['section-typography-font-h1-transform'];
}

if (isset($data['section-typography-font-h2-size']) && $data['section-typography-font-h2-size'] !== '') {
  $options['typography']['headline2']["fontSize"]     = $data['section-typography-font-h2-size'];
}

if (isset($data['section-typography-font-h2-line_height']) && $data['section-typography-font-h2-line_height'] !== '') {
  $options['typography']['headline2']["lineHeight"]   = $data['section-typography-font-h2-line_height'];
}

if (isset($data['section-typography-font-h2-weight']) && $data['section-typography-font-h2-weight'] !== '') {
  $options['typography']['headline2']["fontWeight"]   = $data['section-typography-font-h2-weight'];
}

if (isset($data['section-typography-font-h2-transform']) && $data['section-typography-font-h2-transform'] !== '') {
  $options['typography']['headline2']["fontTransform"]    = $data['section-typography-font-h2-transform'];
}

if (isset($data['section-typography-font-h3-size']) && $data['section-typography-font-h3-size'] !== '') {
  $options['typography']['headline3']["fontSize"]     = $data['section-typography-font-h3-size'];
}

if (isset($data['section-typography-font-h3-line_height']) && $data['section-typography-font-h3-line_height'] !== '') {
  $options['typography']['headline3']["lineHeight"]   = $data['section-typography-font-h3-line_height'];
}

if (isset($data['section-typography-font-h3-weight']) && $data['section-typography-font-h3-weight'] !== '') {
  $options['typography']['headline3']["fontWeight"]   = $data['section-typography-font-h3-weight'];
}

if (isset($data['section-typography-font-h3-transform']) && $data['section-typography-font-h3-transform'] !== '') {
  $options['typography']['headline3']["fontTransform"]    = $data['section-typography-font-h3-transform'];
}

if (isset($data['section-typography-font-h4-size']) && $data['section-typography-font-h4-size'] !== '') {
  $options['typography']['headline4']["fontSize"]     = $data['section-typography-font-h4-size'];
}

if (isset($data['section-typography-font-h4-line_height']) && $data['section-typography-font-h4-line_height'] !== '') {
  $options['typography']['headline4']["lineHeight"]   = $data['section-typography-font-h4-line_height'];
}

if (isset($data['section-typography-font-h4-weight']) && $data['section-typography-font-h4-weight'] !== '') {
  $options['typography']['headline4']["fontWeight"]   = $data['section-typography-font-h4-weight'];
}

if (isset($data['section-typography-font-h4-transform']) && $data['section-typography-font-h4-transform'] !== '') {
  $options['typography']['headline4']["fontTransform"]    = $data['section-typography-font-h4-transform'];
}

if (isset($data['section-typography-font-h5-size']) && $data['section-typography-font-h5-size'] !== '') {
  $options['typography']['headline5']["fontSize"]     = $data['section-typography-font-h5-size'];
}

if (isset($data['section-typography-font-h5-line_height']) && $data['section-typography-font-h5-line_height'] !== '') {
  $options['typography']['headline5']["lineHeight"]   = $data['section-typography-font-h5-line_height'];
}

if (isset($data['section-typography-font-h5-weight']) && $data['section-typography-font-h5-weight'] !== '') {
  $options['typography']['headline5']["fontWeight"]   = $data['section-typography-font-h5-weight'];
}

if (isset($data['section-typography-font-h5-transform']) && $data['section-typography-font-h5-transform'] !== '') {
  $options['typography']['headline5']["fontTransform"]    = $data['section-typography-font-h5-transform'];
}

if (isset($data['section-typography-font-subtitle1-size']) && $data['section-typography-font-subtitle1-size'] !== '') {
  $options['typography']['subtitle1']["fontSize"]     = $data['section-typography-font-subtitle1-size'];
}

if (isset($data['section-typography-font-subtitle1-line_height']) && $data['section-typography-font-subtitle1-line_height'] !== '') {
  $options['typography']['subtitle1']["lineHeight"]   = $data['section-typography-font-subtitle1-line_height'];
}

if (isset($data['section-typography-font-subtitle1-weight']) && $data['section-typography-font-subtitle1-weight'] !== '') {
  $options['typography']['subtitle1']["fontWeight"]   = $data['section-typography-font-subtitle1-weight'];
}

if (isset($data['section-typography-font-subtitle1-transform']) && $data['section-typography-font-subtitle1-transform'] !== '') {
  $options['typography']['subtitle1']["fontTransform"]    = $data['section-typography-font-subtitle1-transform'];
}

if (isset($data['section-typography-font-subtitle2-size']) && $data['section-typography-font-subtitle2-size'] !== '') {
  $options['typography']['subtitle2']["fontSize"]     = $data['section-typography-font-subtitle2-size'];
}

if (isset($data['section-typography-font-subtitle2-size']) && $data['section-typography-font-subtitle2-line_height'] !== '') {
  $options['typography']['subtitle2']["lineHeight"]   = $data['section-typography-font-subtitle2-line_height'];
}

if (isset($data['section-typography-font-subtitle2-weight']) && $data['section-typography-font-subtitle2-weight'] !== '') {
  $options['typography']['subtitle2']["fontWeight"]   = $data['section-typography-font-subtitle2-weight'];
}

if (isset($data['section-typography-font-subtitle2-transform']) && $data['section-typography-font-subtitle2-transform'] !== '') {
  $options['typography']['subtitle2']["fontTransform"]    = $data['section-typography-font-subtitle2-transform'];
}

if (isset($data['section-typography-font-body1-size']) && $data['section-typography-font-body1-size'] !== '') {
  $options['typography']['bodyText1']["fontSize"]     = $data['section-typography-font-body1-size'];
}

if (isset($data['section-typography-font-body1-line_height']) && $data['section-typography-font-body1-line_height'] !== '') {
  $options['typography']['bodyText1']["lineHeight"]   = $data['section-typography-font-body1-line_height'];
}

if (isset($data['section-typography-font-body1-weight']) && $data['section-typography-font-body1-weight'] !== '') {
  $options['typography']['bodyText1']["fontWeight"]   = $data['section-typography-font-body1-weight'];
}

if (isset($data['section-typography-font-body1-transform']) && $data['section-typography-font-body1-transform'] !== '') {
  $options['typography']['bodyText1']["fontTransform"]    = $data['section-typography-font-body1-transform'];
}

if (isset($data['section-typography-font-body2-size']) && $data['section-typography-font-body2-size'] !== '') {
  $options['typography']['bodyText2']["fontSize"]     = $data['section-typography-font-body2-size'];
}

if (isset($data['section-typography-font-body2-line_height']) && $data['section-typography-font-body2-line_height'] !== '') {
  $options['typography']['bodyText2']["lineHeight"]   = $data['section-typography-font-body2-line_height'];
}

if (isset($data['section-typography-font-body2-weight']) && $data['section-typography-font-body2-weight'] !== '') {
  $options['typography']['bodyText2']["fontWeight"]   = $data['section-typography-font-body2-weight'];
}

if (isset($data['section-typography-font-body2-transform']) && $data['section-typography-font-body2-transform'] !== '') {
  $options['typography']['bodyText2']["fontTransform"]    = $data['section-typography-font-body2-transform'];
}
