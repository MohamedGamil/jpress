<?php

/**
 * Onboarding Slides
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


if (isset($data['onboarding']) && $data['onboarding'] !== 'false') {
  $options['onboardModels'] = array();

  foreach ($data['onboardmodels'] as $key => $slide) {
    if ($key === 1000) {
      continue;
    }

    unset($slide['onboardmodels_type']);
    unset($slide['onboardmodels_visibility']);
    unset($slide['image_id']);

    $slide['subTitle']  =   $slide['subtitle'];

    unset($slide['subtitle']);
    array_push($options['onboardModels'], $slide);
  }
}
