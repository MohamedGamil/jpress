<?php
use Appbear\Includes\Metabox as Metabox;
use Appbear\Includes\AdminPage as AdminPage;


/*
|---------------------------------------------------------------------------------------------------
| Obtiene todas las instancias de Appbear
|---------------------------------------------------------------------------------------------------
*/
function jpress_get_all(){
  return Appbear::get_all_jpresss();
}

/*
|---------------------------------------------------------------------------------------------------
| Obtiene una instancia de Appbear
|---------------------------------------------------------------------------------------------------
*/
function jpress_get( $jpress_id ){
  return Appbear::get( $jpress_id );
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
  return Appbear::get_field_value( $jpress_id, $field_id, $default, $post_id );
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
| Nuevo formulario basado en Appbear
|---------------------------------------------------------------------------------------------------
*/
// function jpress_new_form( $jpress_id = '', $form_args = array(), $echo = false ){
//   return AdminPage::get_form( $jpress_id, $form_args, $echo );
// }

