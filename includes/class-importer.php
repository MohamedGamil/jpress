<?php

namespace JPress\Includes;

class Importer {
    private $jpress = null;
    private $data = array();
    private $update_uploads_url = false;
    private $update_plugins_url = true;
    private $username = null;
    private $password = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor de la clase
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $jpress, $data = array(), $settings ){
        $this->jpress = $jpress;
        $this->data = $data;
        $this->update_uploads_url = $settings['update_uploads_url'];
        $this->update_plugins_url = $settings['update_plugins_url'];
        if( $settings['show_authentication_fields'] ){
            $this->username = ! empty( $data['jpress-import-username'] ) ? $data['jpress-import-username'] : null;
            $this->password = ! empty( $data['jpress-import-password'] ) ? $data['jpress-import-password'] : null;
        }

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene los datos a importar
    |---------------------------------------------------------------------------------------------------
    */
    public function get_import_jpress_data(){
        $import_jpress_data = false;
        $json_jpress_data = false;
        $data = $this->data;
        $prefix = $this->jpress->arg( 'fields_prefix' );

        //name por defecto del campo es jpress-import-field que se agrega con el método add_import_field() en class-jpress-core.php
        //Puede ser from_file, from_url o una url json tomada del array items del campo import.
        //https://jpressframework.com/documentation/field-types/import-export/
        $import_from = $data[$prefix . 'jpress-import-field'];

        switch( $import_from ){
            case 'from_file':
                if( isset( $_FILES["jpress-import-file"] ) ){
                    $file_name = $_FILES['jpress-import-file']['name'];
                    if( Functions::ends_with( '.json', $file_name ) ){
                        $json_jpress_data = file_get_contents( $_FILES['jpress-import-file']['tmp_name'] );
                    }
                }
                break;

            case 'from_url':
                if( Functions::ends_with( '.json', $data['jpress-import-url'] ) ){
                    $json_jpress_data = $this->get_json_from_url( $data['jpress-import-url'] );
                }
                break;

            default:
                $import_source = $import_from;
                $import_wp_content = '';
                $import_wp_widget = '';
                $widget_cb = '';
                if( isset( $data['jpress-import-data'] ) ){
                    $sources = isset( $data['jpress-import-data'][$import_source] ) ? $data['jpress-import-data'][$import_source] : array();
                    $import_jpress = isset( $sources['import_jpress'] ) ? $sources['import_jpress'] : '';
                    $import_wp_content = isset( $sources['import_wp_content'] ) ? $sources['import_wp_content'] : '';
                    $import_wp_widget = isset( $sources['import_wp_widget'] ) ? $sources['import_wp_widget'] : '';
                    $widget_cb = isset( $sources['import_wp_widget_callback'] ) ? $sources['import_wp_widget_callback'] : '';
                } else {
                    $import_jpress = $import_source;
                }

                //Import jpress data
                //if( file_exists( $import_jpress ) || Functions::remote_file_exists( $import_jpress ) ){
                if( Functions::ends_with( '.json', $import_jpress ) ){//Remote file falla en sitios https
                    $json_jpress_data = $this->get_json_from_url( $import_jpress );
                }

                //Import Wp Content
                if( file_exists( $import_wp_content ) ){
                  echo '<h2>Importing wordpress data from local file, please wait ...</h2>';
                  $this->set_wp_content_data( $import_wp_content );
                }
                elseif ( ($file_content = Functions::remote_file_exists( $import_wp_content )) ) {
                  if ( $file_content !== false ) {
                      if( false !== file_put_contents( JPRESS_DIR . 'wp-content-data.xml', $file_content ) ){
                          echo '<h2>Importing wordpress data from remote file, please wait ...</h2>';
                          //echo '<div class="wp-import-messages">';
                          $this->set_wp_content_data( JPRESS_DIR . 'wp-content-data.xml' );
                          unlink( JPRESS_DIR . 'wp-content-data.xml' );
                          //echo '</div>';
                      }
                  }
                }

                //Import Wp Widget
                if( file_exists( $import_wp_widget ) || Functions::remote_file_exists( $import_wp_widget ) ){
                    if( is_callable( $widget_cb ) ){
                        call_user_func( $widget_cb, $import_wp_widget );
                    }
                }
                break;
        }

        if( $json_jpress_data !== false ){
            //commented by essam for the array issue in top bar logo. on 19-11-2020 reported by emad
            // $json_jpress_data = $this->update_urls_from_data( $json_jpress_data );
            $import_jpress_data = json_decode( $json_jpress_data, true );
        }

        if( is_array( $import_jpress_data ) && ! empty( $import_jpress_data ) ){
            return $import_jpress_data;
        }

        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Importa contenido de wordpres
    |---------------------------------------------------------------------------------------------------
    */
    public function set_wp_content_data( $file ){
      if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
        define( 'WP_LOAD_IMPORTERS', true );
      }

      $importer_error = ! class_exists( '\WP_Import' );

      if ( $importer_error ) {
        die( "Error on import" );
      }
      else {
        if( is_file( $file ) && class_exists( '\WP_Import' ) ){
          $wp_import = new \WP_Import();
          $wp_import->fetch_attachments = true;
          $wp_import->import( $file );
        } else{
          echo "The XML file containing the dummy content is not available or could not be read .. You might want to try to set the file permission to chmod 755.<br/>If this doesn't work please use the Wordpress importer and import the XML file (should be located in your download .zip: Sample Content folder) manually";
        }
      }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Actualiza las urls de los datos
    |---------------------------------------------------------------------------------------------------
    */
    public function update_urls_from_data( $json_data ){
        // $this->data = $import_jpress_data;
        // array_walk_recursive( $import_jpress_data, array( $this, 'replace_urls') );

        $data = json_decode( $json_data, true );
        $json_data = str_replace( '\\/', '/', $json_data );
        if( $this->update_uploads_url && isset( $data['wp_upload_dir'] ) ){
            $json_data = str_replace( $data['wp_upload_dir'], wp_upload_dir(), $json_data );
        }
        if( $this->update_plugins_url && isset( $data['plugins_url'] ) ){
            $json_data = str_replace( $data['plugins_url'], plugins_url(), $json_data );
        }
        return $json_data;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna un string json desde una url
    |---------------------------------------------------------------------------------------------------
    */
    private function get_json_from_url( $url ){
        $json = file_get_contents( $url );
        $json_decode = json_decode( $json );
        if( $json_decode === null ){
            $options = array();
            if( ! empty( $this->username ) && ! empty( $this->password ) ){
                $options = array(
                    'headers' => array(
                        'Authorization' => "Basic ". base64_encode("$this->username:$this->password")
                    ),
                );
            }

            $response = wp_remote_get( $url, $options );
            if( is_wp_error( $response ) ){
                $options['sslverify'] = false;
                $response = wp_remote_get( $url, $options );
            }
            if( is_wp_error( $response ) ){
                return false;
            } else{
                $json = wp_remote_retrieve_body( $response );
            }
        }
        return $json;
    }

}
