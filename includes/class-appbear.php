<?php

use Appbear\Includes\AssetsLoader as AssetsLoader;
use Appbear\Includes\Ajax as Ajax;
use Appbear\Includes\AppbearCore as AppbearCore;
use Appbear\Includes\Functions as Functions;

class Appbear {
    public $version;
    private static $instance = null;
    private static $appbears = array();

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

        foreach( self::$appbears as $appbear ){
            if( is_a( $appbear, 'Appbear\Includes\Metabox' ) ){
                if( in_array( $screen->post_type, (array) $appbear->arg( 'post_types' ) ) ){
                    $load_scripts = true;
                }
            } else{
                if( false !== stripos( $screen->id, $appbear->id ) ){
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
    public static function new_appbear( $options = array() ){
        if( empty( $options['id'] ) ){
            return false;
        }

        $appbear = self::get( $options['id'] );
        if( $appbear ){
            return $appbear;
        }
        return new AppbearCore( $options );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene una instancia de Appbear
    |---------------------------------------------------------------------------------------------------
    */
    public static function get( $appbear_id ){
        $appbear_id = trim( $appbear_id );

        if( empty( $appbear_id ) ){
          return null;
        }

        if( Functions::is_empty( self::$appbears ) || ! isset( self::$appbears[$appbear_id] ) ){
          return null;
        }

        return self::$appbears[$appbear_id];
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene todos los appbear creados
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_all_appbears(){
        return self::$appbears;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega una instancia de Appbear
    |---------------------------------------------------------------------------------------------------
    */
    public static function add( $appbear ){
        if( is_a( $appbear, 'Appbear\Includes\AppbearCore' ) ){
            self::$appbears[$appbear->get_id()] = $appbear;
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Elimina una instancia de Appbear
    |---------------------------------------------------------------------------------------------------
    */
    public static function remove_appbear( $id ){
        if( isset( self::$appbears[$id] ) ){
            unset( self::$appbears[$id] );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el valor de una opción
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_field_value( $appbear_id, $field_id = '', $default = '', $post_id = '' ){
        $value = '';
        $appbear = self::get( $appbear_id );
        if( ! $appbear ){
            return false;
        }
        switch( $appbear->get_object_type() ){
            case 'metabox':
                $value = $appbear->get_field_value( $field_id, $post_id, $default );
                break;

            case 'admin-page':
                $value = $appbear->get_field_value( $field_id, $default );
                break;
        }
        if( Functions::is_empty( $value ) ){
            return $default;
        }
        return $value;
    }

}
