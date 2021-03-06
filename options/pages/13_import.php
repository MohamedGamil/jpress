<?php

/**
 * Import / Export Tab
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Import / Export Page
$settings->open_tab_item('import');
$settings->add_import_field(array(
  'name' => 'Select Demo',
  'default' => 'http://jpressframework.com/demos/blank.json',
  'desc' => 'Choose a demo, then click import button',
  'items' => array(
    JPRESS_URL . 'options/demos/demo1.json' => JPRESS_URL . 'options/img/demos/demo1.jpg',
    JPRESS_URL . 'options/demos/demo2.json' => JPRESS_URL . 'options/img/demos/demo2.jpg',
    JPRESS_URL . 'options/demos/demo3.json' => JPRESS_URL . 'options/img/demos/demo3.jpg',
    JPRESS_URL . 'options/demos/demo4.json' => JPRESS_URL . 'options/img/demos/demo4.jpg',
    JPRESS_URL . 'options/demos/demo5.json' => JPRESS_URL . 'options/img/demos/demo5.jpg',
    JPRESS_URL . 'options/demos/demo6.json' => JPRESS_URL . 'options/img/demos/demo6.jpg',
    JPRESS_URL . 'options/demos/demo7.json' => JPRESS_URL . 'options/img/demos/demo7.jpg',
    JPRESS_URL . 'options/demos/demo8.json' => JPRESS_URL . 'options/img/demos/demo8.jpg'
  ),
  'options' => array(
    'import_from_file' => false,
    'import_from_url' => false,
    'width' => '200px'
  )
));

$settings->add_export_field(array(
  'name' => 'Export',
  'desc' => 'Download and make a backup of your options.',
));
$settings->close_tab_item('import');
