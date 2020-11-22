<?php
use Appbear\Includes\Metabox as Metabox;
use Appbear\Includes\AdminPage as AdminPage;


/*
|---------------------------------------------------------------------------------------------------
| Obtiene todas las instancias de Appbear
|---------------------------------------------------------------------------------------------------
*/
function appbear_get_all(){
  return Appbear::get_all_appbears();
}

/*
|---------------------------------------------------------------------------------------------------
| Obtiene una instancia de Appbear
|---------------------------------------------------------------------------------------------------
*/
function appbear_get( $appbear_id ){
  return Appbear::get( $appbear_id );
}

/*
|---------------------------------------------------------------------------------------------------
| Nuevo metabox
|---------------------------------------------------------------------------------------------------
*/
function appbear_new_metabox( $options = array() ){
  return new Metabox( $options );
}

/*
|---------------------------------------------------------------------------------------------------
| Nueva página de opciones
|---------------------------------------------------------------------------------------------------
*/
function appbear_new_admin_page( $options = array() ){
  return new AdminPage( $options );
}

/*
|---------------------------------------------------------------------------------------------------
| Retorna el valor de una opción
|---------------------------------------------------------------------------------------------------
*/
function appbear_get_field_value( $appbear_id, $field_id = '', $default = '', $post_id = '' ){
  return Appbear::get_field_value( $appbear_id, $field_id, $default, $post_id );
}

/*
|---------------------------------------------------------------------------------------------------
| Código Corto que Retorna el valor de una opción
|---------------------------------------------------------------------------------------------------
*/
add_shortcode( 'appbear_get_field_value', 'appbear_get_field_value_shortcode' );
function appbear_get_field_value_shortcode( $atts ) {
    $a = shortcode_atts( array(
        'appbear_id' => null,
        'field_id' => '',
        'default' => '',
        'post_id' => '',
    ), $atts );
    return appbear_get_field_value( $a['appbear_id'], $a['field_id'], $a['default'], $a['post_id'] );
}


/*
|---------------------------------------------------------------------------------------------------
| Nuevo formulario basado en Appbear
|---------------------------------------------------------------------------------------------------
*/
// function appbear_new_form( $appbear_id = '', $form_args = array(), $echo = false ){
//   return AdminPage::get_form( $appbear_id, $form_args, $echo );
// }

