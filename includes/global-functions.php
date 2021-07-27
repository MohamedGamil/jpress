<?php
use JPress\Includes\Metabox as Metabox;
use JPress\Includes\AdminPage as AdminPage;


/*
|---------------------------------------------------------------------------------------------------
| Obtiene todas las instancias de JPress
|---------------------------------------------------------------------------------------------------
*/
function jpress_get_all(){
  return JPress::get_all_jpresss();
}

/*
|---------------------------------------------------------------------------------------------------
| Obtiene una instancia de JPress
|---------------------------------------------------------------------------------------------------
*/
function jpress_get( $jpress_id ){
  return JPress::get( $jpress_id );
}

/*
|---------------------------------------------------------------------------------------------------
| Nuevo metabox
|---------------------------------------------------------------------------------------------------
*/
function jpress_new_metabox( $options = array() ){
  return new Metabox( $options );
}

/*
|---------------------------------------------------------------------------------------------------
| Nueva página de opciones
|---------------------------------------------------------------------------------------------------
*/
function jpress_new_admin_page( $options = array() ){
  return new AdminPage( $options );
}

/*
|---------------------------------------------------------------------------------------------------
| Retorna el valor de una opción
|---------------------------------------------------------------------------------------------------
*/
function jpress_get_field_value( $jpress_id, $field_id = '', $default = '', $post_id = '' ){
  return JPress::get_field_value( $jpress_id, $field_id, $default, $post_id );
}

/*
|---------------------------------------------------------------------------------------------------
| Código Corto que Retorna el valor de una opción
|---------------------------------------------------------------------------------------------------
*/
add_shortcode( 'jpress_get_field_value', 'jpress_get_field_value_shortcode' );
function jpress_get_field_value_shortcode( $atts ) {
    $a = shortcode_atts( array(
        'jpress_id' => null,
        'field_id' => '',
        'default' => '',
        'post_id' => '',
    ), $atts );
    return jpress_get_field_value( $a['jpress_id'], $a['field_id'], $a['default'], $a['post_id'] );
}


/*
|---------------------------------------------------------------------------------------------------
| Nuevo formulario basado en JPress
|---------------------------------------------------------------------------------------------------
*/
// function jpress_new_form( $jpress_id = '', $form_args = array(), $echo = false ){
//   return AdminPage::get_form( $jpress_id, $form_args, $echo );
// }

