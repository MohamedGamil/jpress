<?php namespace Appbear\Includes;

class Importer {
    private $appbear = null;
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
    public function __construct( $appbear, $data = array(), $settings ){
        $this->appbear = $appbear;
        $this->data = $data;
        $this->update_uploads_url = $settings['update_uploads_url'];
        $this->update_plugins_url = $settings['update_plugins_url'];
        if( $settings['show_authentication_fields'] ){
            $this->username = ! empty( $data['appbear-import-username'] ) ? $data['appbear-import-username'] : null;
            $this->password = ! empty( $data['appbear-import-password'] ) ? $data['appbear-import-password'] : null;
        }

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene los datos a importar
    |---------------------------------------------------------------------------------------------------
    */
    public function get_import_appbear_data(){
        $import_appbear_data = false;
        $json_appbear_data = false;
        $data = $this->data;
        $prefix = $this->appbear->arg( 'fields_prefix' );

        //name por defecto del campo es appbear-import-field que se agrega con el método add_import_field() en class-appbear-core.php
        //Puede ser from_file, from_url o una url json tomada del array items del campo import.
        //https://appbearframework.com/documentation/field-types/import-export/
        $import_from = $data[$prefix . 'appbear-import-field'];

        switch( $import_from ){
            case 'from_file':
                if( isset( $_FILES["appbear-import-file"] ) ){
                    $file_name = $_FILES['appbear-import-file']['name'];
                    if( Functions::ends_with( '.json', $file_name ) ){
                        $json_appbear_data = file_get_contents( $_FILES['appbear-import-file']['tmp_name'] );
                    }
                }
                break;

            case 'from_url':
                if( Functions::ends_with( '.json', $data['appbear-import-url'] ) ){
                    $json_appbear_data = $this->get_json_from_url( $data['appbear-import-url'] );
                }
                break;

            default:
                $import_source = $import_from;
                $import_wp_content = '';
                $import_wp_widget = '';
                $widget_cb = '';
                if( isset( $data['appbear-import-data'] ) ){
                    $sources = isset( $data['appbear-import-data'][$import_source] ) ? $data['appbear-import-data'][$import_source] : array();
                    $import_appbear = isset( $sources['import_appbear'] ) ? $sources['import_appbear'] : '';
                    $import_wp_content = isset( $sources['import_wp_content'] ) ? $sources['import_wp_content'] : '';
                    $import_wp_widget = isset( $sources['import_wp_widget'] ) ? $sources['import_wp_widget'] : '';
                    $widget_cb = isset( $sources['import_wp_widget_callback'] ) ? $sources['import_wp_widget_callback'] : '';
                } else {
                    $import_appbear = $import_source;
                }

                //Import appbear data
                //if( file_exists( $import_appbear ) || Functions::remote_file_exists( $import_appbear ) ){
                if( Functions::ends_with( '.json', $import_appbear ) ){//Remote file falla en sitios https
                    $json_appbear_data = $this->get_json_from_url( $import_appbear );
                }

                //Import Wp Content
                if( file_exists( $import_wp_content ) ){
                    echo '<h2>Importing wordpress data from local file, please wait ...</h2>';
                    $this->set_wp_content_data( $import_wp_content );
                } else if( Functions::remote_file_exists( $import_wp_content ) ){
                    $file_content = file_get_contents( $import_wp_content );
                    if( $file_content !== false ){
                        if( false !== file_put_contents( APPBEAR_DIR . 'wp-content-data.xml', $file_content ) ){
                            echo '<h2>Importing wordpress data from remote file, please wait ...</h2>';
                            //echo '<div class="wp-import-messages">';
                            $this->set_wp_content_data( APPBEAR_DIR . 'wp-content-data.xml' );
                            unlink( APPBEAR_DIR . 'wp-content-data.xml' );
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

        if( $json_appbear_data !== false ){
            //commented by essam for the array issue in top bar logo. on 19-11-2020 reported by emad
            // $json_appbear_data = $this->update_urls_from_data( $json_appbear_data );
            $import_appbear_data = json_decode( $json_appbear_data, true );
        }

        if( is_array( $import_appbear_data ) && ! empty( $import_appbear_data ) ){
            return $import_appbear_data;
        }

        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Importa contenido de wordpres
    |---------------------------------------------------------------------------------------------------
    */
    public function set_wp_content_data( $file ){
        if( ! defined( 'WP_LOAD_IMPORTERS' ) ) define( 'WP_LOAD_IMPORTERS', true );

        $importer_error = false;
        if( ! class_exists( '\WP_Import' ) ){
            $class_wp_import = APPBEAR_DIR . 'libs/wordpress-importer/wordpress-importer.php';
            if( file_exists( $class_wp_import ) ){
                require_once $class_wp_import;
            } else{
                $importer_error = true;
            }
        }

        if( $importer_error ){
            die( "Error on import" );
        } else{
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
        // $this->data = $import_appbear_data;
        // array_walk_recursive( $import_appbear_data, array( $this, 'replace_urls') );

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