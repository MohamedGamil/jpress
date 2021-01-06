<?php

/**
 * Logos
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


if (isset($data['logo-light']) === true) {
  $options['logo']['light'] = $data['logo-light'];
}

if (isset($data['switch_theme_mode']) && $data['switch_theme_mode'] !== 'false') {
  $options['logo']['dark'] = $data['logo-dark'];
}
