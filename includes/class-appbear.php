<?php

use Appbear\Includes\AssetsLoader as AssetsLoader;
use Appbear\Includes\Ajax as Ajax;
use Appbear\Includes\AppbearCore as AppbearCore;
use Appbear\Includes\Functions as Functions;

class Appbear {
    public $version;
    private static $instance = null;
    private static $jpresss = array();

    private function __construct( $version = '1.0.0' ){
        $this->version = $version;
        add_action( 'current_screen', array( $this, 'load_assets' ) );
        $this->ajax();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Singleton
    |---------------------------------------------------------------------------------------------------
    */
    private function __clone(){
    }//Stopping Clonning of Object

    private function __wakeup(){
    }//Stopping unserialize of object

    public static function init( $version = '1.0.0' ){
        if( null === self::$instance ){
            self::$instance = new self( $version );
        }
        return self::$instance;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Carga de scripts y estilos.
    |---------------------------------------------------------------------------------------------------
    */
    public function load_assets(){
        $load_scripts = false;
        $screen = get_current_screen();

        foreach( self::$jpresss as $jpress ){
            if( is_a( $jpress, 'Appbear\Includes\Metabox' ) ){
                if( in_array( $screen->post_type, (array) $jpress->arg( 'post_types' ) ) ){
                    $load_scripts = true;
                }
            } else{
                if( false !== stripos( $screen->id, $jpress->id ) ){
                    $load_scripts = true;
                }
            }
        }
        //Los scripts también se incluyen en la lista de cada post_type, para futuras características

        if( $load_scripts ){
            new AssetsLoader( $this->version );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Ajax
    |---------------------------------------------------------------------------------------------------
    */
    public function ajax(){
        new Ajax();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Crear un Appbear
    |---------------------------------------------------------------------------------------------------
    */
    public static function new_jpress( $options = array() ){
        if( empty( $options['id'] ) ){
            return false;
        }

        $jpress = self::get( $options['id'] );
        if( $jpress ){
            return $jpress;
        }
        return new AppbearCore( $options );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene una instancia de Appbear
    |---------------------------------------------------------------------------------------------------
    */
    public static function get( $jpress_id ){
        $jpress_id = trim( $jpress_id );

        if( empty( $jpress_id ) ){
          return null;
        }

        if( Functions::is_empty( self::$jpresss ) || ! isset( self::$jpresss[$jpress_id] ) ){
          return null;
        }

        return self::$jpresss[$jpress_id];
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene todos los jpress creados
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_all_jpresss(){
        return self::$jpresss;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega una instancia de Appbear
    |---------------------------------------------------------------------------------------------------
    */
    public static function add( $jpress ){
        if( is_a( $jpress, 'Appbear\Includes\AppbearCore' ) ){
            self::$jpresss[$jpress->get_id()] = $jpress;
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Elimina una instancia de Appbear
    |---------------------------------------------------------------------------------------------------
    */
    public static function remove_jpress( $id ){
        if( isset( self::$jpresss[$id] ) ){
            unset( self::$jpresss[$id] );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el valor de una opción
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_field_value( $jpress_id, $field_id = '', $default = '', $post_id = '' ){
        $value = '';
        $jpress = self::get( $jpress_id );
        if( ! $jpress ){
            return false;
        }
        switch( $jpress->get_object_type() ){
            case 'metabox':
                $value = $jpress->get_field_value( $field_id, $post_id, $default );
                break;

            case 'admin-page':
                $value = $jpress->get_field_value( $field_id, $default );
                break;
        }
        if( Functions::is_empty( $value ) ){
            return $default;
        }
        return $value;
    }

}
