<?php

/**
 * Styling
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


$options['styling']['ThemeMode.light']['bottomBarBackgroundColor'] = $data['styling-themeMode_light-bottomBarBackgroundColor'];
$options['styling']['ThemeMode.light']['scaffoldBackgroundColor'] = $data['styling-themeMode_light-scaffoldbackgroundcolor'];
$options['styling']['ThemeMode.light']['primary'] = $data['styling-themeMode_light-primary'];
$options['styling']['ThemeMode.light']['secondary'] = $data['styling-themeMode_light-secondary'];
$options['styling']['ThemeMode.light']['secondaryVariant'] = $data['styling-themeMode_light-secondaryvariant'];
$options['styling']['ThemeMode.light']['appBarBackgroundColor'] = $data['styling-themeMode_light-appBarBackgroundColor'];
$options['styling']['ThemeMode.light']['appBarColor'] = $data['styling-themeMode_light-appBarColor'];
$options['styling']['ThemeMode.light']['background'] = $data['styling-themeMode_light-background'];
$options['styling']['ThemeMode.light']['sidemenutextcolor'] = $data['styling-themeMode_light-sideMenuIconsTextColor'];
$options['styling']['ThemeMode.light']['bottomBarInActiveColor'] = $data['styling-themeMode_light-bottomBarInActiveColor'];
$options['styling']['ThemeMode.light']['bottomBarActiveColor'] = $data['styling-themeMode_light-bottomBarActiveColor'];
$options['styling']['ThemeMode.light']['tabBarBackgroundColor'] = $data['styling-themeMode_light-tabbarbackgroundcolor'];
$options['styling']['ThemeMode.light']['tabBarTextColor'] = $data['styling-themeMode_light-tabbartextcolor'];
$options['styling']['ThemeMode.light']['tabBarActiveTextColor'] = $data['styling-themeMode_light-tabbaractivetextcolor'];
$options['styling']['ThemeMode.light']['tabBarIndicatorColor'] = $data['styling-themeMode_light-tabbarindicatorcolor'];
$options['styling']['ThemeMode.light']['shadowColor'] = $data['styling-themeMode_light-shadowColor'];
$options['styling']['ThemeMode.light']['dividerColor'] = $data['styling-themeMode_light-dividerColor'];
$options['styling']['ThemeMode.light']['inputsbackgroundcolor'] = $data['styling-themeMode_light-inputsbackgroundcolor'];
$options['styling']['ThemeMode.light']['buttonsbackgroudcolor'] = $data['styling-themeMode_light-buttonsbackgroudcolor'];
$options['styling']['ThemeMode.light']['buttonTextColor'] = $data['styling-themeMode_light-buttonTextColor'];
$options['styling']['ThemeMode.light']['settingBackgroundColor'] = isset($data['styling-themeMode_light-settingBackgroundColor']) ? $data['styling-themeMode_light-settingBackgroundColor'] : '';
$options['styling']['ThemeMode.light']['settingTextColor'] = isset($data['styling-themeMode_light-settingTextColor']) ? $data['styling-themeMode_light-settingTextColor'] : '';
$options['styling']['ThemeMode.light']['errorColor'] =  $data['styling-themeMode_light-errorcolor'];
$options['styling']['ThemeMode.light']['successColor'] = $data['styling-themeMode_light-successcolor'];


if (isset($data['switch_theme_mode']) && $data['switch_theme_mode'] !== 'false') {
  $options['styling']['ThemeMode.dark']['bottomBarBackgroundColor'] = $data['styling-themeMode_dark-bottomBarBackgroundColor'];
  $options['styling']['ThemeMode.dark']['scaffoldBackgroundColor'] = $data['styling-themeMode_dark-scaffoldbackgroundcolor'];
  $options['styling']['ThemeMode.dark']['primary'] = $data['styling-themeMode_dark-primary'];
  $options['styling']['ThemeMode.dark']['secondary'] = $data['styling-themeMode_dark-secondary'];
  $options['styling']['ThemeMode.dark']['secondaryVariant'] = $data['styling-themeMode_dark-secondaryvariant'];
  $options['styling']['ThemeMode.dark']['appBarBackgroundColor'] = $data['styling-themeMode_dark-appBarBackgroundColor'];
  $options['styling']['ThemeMode.dark']['appBarColor'] = $data['styling-themeMode_dark-appBarColor'];
  $options['styling']['ThemeMode.dark']['background'] = $data['styling-themeMode_dark-background'];
  $options['styling']['ThemeMode.dark']['sidemenutextcolor'] = $data['styling-themeMode_dark-sideMenuIconsTextColor'];
  $options['styling']['ThemeMode.dark']['bottomBarInActiveColor'] = $data['styling-themeMode_dark-bottomBarInActiveColor'];
  $options['styling']['ThemeMode.dark']['bottomBarActiveColor'] = $data['styling-themeMode_dark-bottomBarActiveColor'];
  $options['styling']['ThemeMode.dark']['tabBarBackgroundColor'] = $data['styling-themeMode_dark-tabbarbackgroundcolor'];
  $options['styling']['ThemeMode.dark']['tabBarTextColor'] = $data['styling-themeMode_dark-tabbartextcolor'];
  $options['styling']['ThemeMode.dark']['tabBarActiveTextColor'] = $data['styling-themeMode_dark-tabbaractivetextcolor'];
  $options['styling']['ThemeMode.dark']['tabBarIndicatorColor'] = $data['styling-themeMode_dark-tabbarindicatorcolor'];
  $options['styling']['ThemeMode.dark']['shadowColor'] = $data['styling-themeMode_dark-shadowColor'];
  $options['styling']['ThemeMode.dark']['dividerColor'] = $data['styling-themeMode_dark-dividerColor'];
  $options['styling']['ThemeMode.dark']['inputsbackgroundcolor'] = $data['styling-themeMode_dark-inputsbackgroundcolor'];
  $options['styling']['ThemeMode.dark']['buttonsbackgroudcolor'] = $data['styling-themeMode_dark-buttonsbackgroudcolor'];
  $options['styling']['ThemeMode.dark']['buttonTextColor'] = $data['styling-themeMode_dark-buttonTextColor'];
  $options['styling']['ThemeMode.dark']['settingBackgroundColor'] = isset($data['styling-themeMode_dark-settingBackgroundColor']) ? $data['styling-themeMode_dark-settingBackgroundColor'] : '';
  $options['styling']['ThemeMode.dark']['settingTextColor'] = isset($data['styling-themeMode_dark-settingTextColor']) ? $data['styling-themeMode_dark-settingTextColor'] : '';
  $options['styling']['ThemeMode.dark']['errorColor'] = $data['styling-themeMode_dark-errorcolor'];
  $options['styling']['ThemeMode.dark']['successColor'] = $data['styling-themeMode_dark-successcolor'];
}
