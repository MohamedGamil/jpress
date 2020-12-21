<?php

namespace Appbear\Includes;

class FieldTypes {
    protected $field = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor de la clase
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $field = null ) {
        $this->field = $field;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Función por defecto, permite contruir un tipo de campo inexsistente
    |---------------------------------------------------------------------------------------------------
    */
    public function __call( $field_type, $arguments ) {
        ob_start();
        do_action( "appbear_build_{$field_type}", $this->field->get_appbear(), $this->field, $this->field->get_value(), $this );
        return ob_get_clean();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el campo
    |---------------------------------------------------------------------------------------------------
    */
    public function build() {
        $type = $this->field->arg( 'type' );
        return $this->{$type}( $type );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: button
    |---------------------------------------------------------------------------------------------------
    */
    public function button( $type = '' ) {
        $return = '';
        $options = $this->field->arg( 'options' );
        $attributes = $this->field->arg( 'attributes' );
        $content = $this->field->get_result_callback( 'content' );

        $default_attributes = array(
            'name' => $this->field->get_name(),
            'id' => Functions::get_id_attribute_by_name( $this->field->get_name() ),
            'class' => "appbear-element appbear-btn appbear-btn-{$options['size']} appbear-btn-{$options['color']}"
        );
        if ( $options['tag'] != 'a' ) {
            $default_attributes['type'] = 'button';
        }
        $attributes = Functions::nice_array_merge(
            $default_attributes,
            $attributes,
            array( 'name', 'id' ),
            array( 'class' => ' ' )
        );
        $attributes = $this->join_attributes( $attributes );
        $content = $content == '' ? $this->field->arg( 'default' ) : $content;
        $content = $options['icon'] . $content;

        if ( $options['tag'] == 'a' ) {
            $return .= "<a {$attributes}>{$content}</a>";
        } else if ( $options['tag'] == 'input' ) {
            $return .= "<input {$attributes} value='{$content}'>";
        } else if ( $options['tag'] == 'button' ) {
            $return .= "<button {$attributes}>{$content}</button>";
        }
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: textarea
    |---------------------------------------------------------------------------------------------------
    */
    public function code_editor( $type = '' ) {
        $return = '';
        $value = $this->field->get_value( true, 'esc_textarea' );
        $id = Functions::get_id_attribute_by_name( $this->field->get_name() );
        $language = $this->field->arg( 'options', 'language' );
        $theme = $this->field->arg( 'options', 'theme' );
        $height = $this->field->arg( 'options', 'height' );
        $return .= "<div class='appbear-code-editor' id='{$id}-ace' data-language='$language' data-theme='$theme' style='height: $height'>$value</div>";
        $return .= $this->build_textarea( 'code_editor' );
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: colorpicker
    |---------------------------------------------------------------------------------------------------
    */
    public function colorpicker( $type = '' ) {
        $value = $this->field->get_value();
        $value = $this->field->validate_colorpicker( $value );
        $return = '';
        if ( $this->field->arg( 'options', 'show_default_button' ) ) {
            $return .= "<div class='appbear-colorpicker-default-btn' title='" . __( 'Set default color', 'appbear' ) . "'>";
            $return .= "<i class='appbear-icon appbear-icon-eraser'></i>";
            $return .= "</div>";
        }
        $return .= $this->build_input( 'text', $value );
        $return .= "<div class='appbear-colorpicker-preview'>";
        $return .= "<span class='appbear-colorpicker-color' value='$value'></span>";
        $return .= "</div>";

        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: tagsinput
    |---------------------------------------------------------------------------------------------------
    */
    public function tagsinput( $type = '' ) {
        $value = $this->field->get_value();
        $return = '<div class="input-group">';
        $return .= $this->build_input( 'tagsinput', $value );
        if ( $this->field->arg( 'options', 'show_default_button' ) ) {
            $return .= '<div class="input-group-btn">';
            $return .= '<button class="btn btn-tiffany" type="button">' . __( 'Add', 'appbear' ) .'</button>';
            $return .= '</div>';
        }
        $return .= "</div>";

        return $return;
    }

    public function typography( $type = '' ) {
        $value = $this->field->get_value();
        $return = '<div class="input-group">';
        $return .= $this->build_input( 'tagsinput', $value );
        if ( $this->field->arg( 'options', 'show_default_button' ) ) {
            $return .= '<div class="input-group-btn">';
            $return .= '<button class="btn btn-tiffany" type="button">' . __( 'Add', 'appbear' ) .'</button>';
            $return .= '</div>';
        }
        $return .= "</div>";

        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: checkbox
    |---------------------------------------------------------------------------------------------------
    */
    public function checkbox( $type = '' ) {
        return $this->radio( 'checkbox' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: date
    |---------------------------------------------------------------------------------------------------
    */
    public function date( $type = '' ) {
        return $this->build_input( 'date' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: time
    |---------------------------------------------------------------------------------------------------
    */
    public function time( $type = '' ) {
        return $this->build_input( 'time' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: file
    |---------------------------------------------------------------------------------------------------
    */
    public function file( $type = '' ) {
        $return = '';
        $name = $this->field->get_name();
        $value = $this->field->get_value( true );
        $options = $this->field->arg( 'options' );
        $preview_size = $options['preview_size'];
        $data_preview_size = json_encode( $preview_size );
        $attachment_field = $this->field->get_parent()->get_field( $this->field->id . '_id' );
        $attachment_name = $attachment_field->get_name( $this->field->index );

        $btn_class = "appbear-btn-input appbear-btn appbear-btn-icon appbear-btn-small appbear-btn-teal appbear-upload-file {$options['upload_file_class']}";
        $wrap_class = "appbear-wrap-preview appbear-wrap-preview-file";

        if ( $options['multiple'] === true ) {
            $btn_class .= " appbear-btn-preview-multiple";
            $wrap_class .= " appbear-wrap-preview-multiple";
        } else {
            $return .= $this->build_input( 'text' );
        }

        $full_width = Functions::ends_with( '100%', $preview_size['width'] ) ? 'appbear-video-full-width' : '';

        $return .= "<a class='$btn_class' data-field-name='$name' title='{$options['upload_file_text']}'><i class='appbear-icon appbear-icon-upload'></i></a>";
        $return .= "<ul class='$wrap_class $full_width appbear-clearfix' data-field-name='$attachment_name' data-preview-size='$data_preview_size' data-synchronize-selector='{$options['synchronize_selector']}'>";

        if ( ! Functions::is_empty( $value ) ) {
            if ( $options['multiple'] === true ) {
                foreach( $value as $index => $val ) {
                    $return .= $this->build_file_item( $preview_size, $val, $options['multiple'], $attachment_field, $index );
                }
            } else {
                $return .= $this->build_file_item( $preview_size, $value, $options['multiple'], $attachment_field, null );
            }
        }
        $return .= "</ul>";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build item file
    |---------------------------------------------------------------------------------------------------
    */
    private function build_file_item( $preview_size, $value, $multiple, $attachment_field, $index = null ) {
        $return = '';
        $mime_types = (array) $this->field->arg( 'options', 'mime_types' );
        if ( ! Functions::is_empty( $mime_types ) ) {
            $extension = Functions::get_file_extension( $value );
            if ( ! $extension || ! in_array( $extension, $mime_types ) ) {
                return '';
            }
        }

        $attachment_name = $attachment_field->get_name( $this->field->index );
        $attachment_id = $attachment_field->get_value( true, 'esc_attr', $this->field->index );

        if ( $multiple === true && ! empty( $attachment_id ) ) {
            $attachment_id = isset( $attachment_id[$index] ) ? $attachment_id[$index] : false;
        }

        if ( empty( $attachment_id ) ) {
            $attachment_id = Functions::get_attachment_id_by_url( $value );
        }

        $item_class = 'appbear-preview-item appbear-preview-file';
        $item_body = '';
        $inputs = $multiple == true ? $this->build_input( 'hidden', $value, array(), 'esc_attr', array( 'id' ) ) : '';
        $inputs .= "<input type='hidden' name='{$attachment_name}' value='{$attachment_id}' class='appbear-attachment-id'>";

        if ( $this->is_image_file( $value ) ) {
            $item_class .= ' appbear-preview-image';
            if ( ! empty( $attachment_id ) ) {
                $width = (int) $preview_size['width'];
                $height = ( $preview_size['height'] == 'auto' ) ? $width : (int) $preview_size['height'];
                //array( $width, $height ) add custom size added by "add_image_size"
                $item_body = wp_get_attachment_image( $attachment_id, array( $width, $height ), false, array( 'class' => 'appbear-image appbear-preview-handler' ) );
            }
            if ( empty( $attachment_id ) || empty( $item_body ) ) {
                $item_body = "<img src='$value' style='width: {$preview_size['width']}; height: {$preview_size['height']}' class='appbear-image appbear-preview-handler'>";
            }
        } else if ( $this->is_video_file( $value ) ) {
            $item_class .= ' appbear-preview-video';
            $extension = Functions::get_file_extension( $value );
            $item_body = "<div class='appbear-video'>";
            $item_body .= "<video controls style='width: {$preview_size['width']}; height: {$preview_size['height']}'>";
            $item_body .= "<source src='$value' type='video/$extension'>";
            $item_body .= "</video>";
            $item_body .= "</div>";
        } else {
            $file_link = $value;
            $file_mime = 'aplication';
            $file_name = 'Filename';
            $file_icon_url = wp_mime_type_icon();
            if ( $file = get_post( $attachment_id, ARRAY_A ) ) {
                $file_link = isset( $file['guid'] ) ? $file['guid'] : $file_link;
                $file_mime = isset( $file['post_mime_type'] ) ? $file['post_mime_type'] : $file_mime;
                $file_name = wp_basename( get_attached_file( $attachment_id ) );
                $file_icon_url = wp_mime_type_icon( $attachment_id );
            }
            $item_body = "<img src='$file_icon_url' class='appbear-preview-icon-file appbear-preview-handler'><a href='$file_link' class='appbear-preview-download-link'>$file_name</a><span class='appbear-preview-mime appbear-preview-handler'>$file_mime</span>";
        }

        $return .= "<li class='{$item_class}'>";
        $return .= $inputs;
        $return .= $item_body;
        $return .= "<a class='appbear-btn appbear-btn-iconize appbear-btn-small appbear-btn-red appbear-remove-preview'><i class='appbear-icon appbear-icon-times-circle'></i></a>";
        $return .= "</li>";

        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: hidden
    |---------------------------------------------------------------------------------------------------
    */
    public function hidden( $type = '' ) {
        return $this->build_input( 'hidden' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: html
    |---------------------------------------------------------------------------------------------------
    */
    public function html( $type = '' ) {
        return $this->field->get_result_callback( 'content' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: icon_seletor
    |---------------------------------------------------------------------------------------------------
    */
    public function icon_selector( $type = '' ) {
      $items = $this->field->arg( 'items' );
      $options = $this->field->arg( 'options' );
      $value = $this->field->get_value();

      $return = '';
      $return .= $this->build_input( 'hidden' );
      $return .= "<div class='appbear-icon-actions appbear-clearfix'>";
      $return .= "<div class='appbear-icon-active appbear-item-icon-selector'>";

      if ( Functions::ends_with( '.svg', $value ) ) {
        $return .= "<img src='$value'>";
      }
      else {
        $return .= "<i class='".Functions::get_icon($value )."'></i>";
      }

      $return .= "</div>";

      if ( false === $options['hide_search'] ) {
        $return .= "<input type='text' class='appbear-search-icon' placeholder='Search icon...'>";
      }

      // BUG: Buttons are not working as intended!
      if ( false === $options['hide_buttons'] ) {
        $return .= "<a class='appbear-btn appbear-btn-small appbear-btn-teal' data-search='all'>All</a>";
        $return .= "<a class='appbear-btn appbear-btn-small appbear-btn-teal' data-search='font'>Icon font</a>";
        $return .= "<a class='appbear-btn appbear-btn-small appbear-btn-teal' data-search='.svg'>SVG</a>";
      }

      $return .= "</div>";

      $data = json_encode( $options );
      $itemsJson = json_encode( $this->_prepareIconsItems($items) );
      $return .= "<script>APPBEAR_JS._field_icons['{$this->field->id}'] = $itemsJson;</script>";
      $return .= "<div class='appbear-icons-wrap appbear-clearfix' data-options='{$data}' style='height:{$options['wrap_height']} '>";

      /*
      $icons_html = '';

      if ( false === $options['load_with_ajax'] ) {
        foreach( $items as $k => $icon ) {
          $key = 'font ' . $k;
          $type = 'icon font';

          if ( Functions::ends_with( '.svg', $k ) ) {
            $type = 'svg';
            $key = explode( '/', $k );
            $key = end( $key );
            $font_size = 'inherit';
          }
          else {
            // 14 = padding vertical + border vertical
            $font_size = ( intval( $options['size'] ) - 14 ) . 'px';
            $icon = preg_replace( '/(<i\b[^><]*)>/i', '$1 style="">', $icon );
          }

          $icons_html .= "<div class='appbear-item-icon-selector' data-value='$k' data-key='$key' data-search='". Functions::get_icon( $k ) ."' data-type='$type' style='width: {$options['size']}; height: {$options['size']}; font-size: {$font_size}'>";
          $icons_html .= $icon;
          $icons_html .= "</div>";
        }
      }

      $return .= $icons_html;
      */

      $return .= "</div>";

      return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: image
    |---------------------------------------------------------------------------------------------------
    */
    public function image( $type = '' ) {
        $return = '';
        $value = $this->field->get_value();
        $image_class = 'appbear-element-image ' . $this->field->arg( 'options', 'image_class' );

        if ( $this->field->arg( 'options', 'hide_input' ) ) {
            $return .= $this->build_input( 'hidden' );
        } else {
            $return .= $this->build_input( 'text' );
            $return .= "<a class='appbear-btn-input appbear-btn appbear-btn-icon appbear-btn-small appbear-btn-teal appbear-get-image' title='Preview'><i class='appbear-icon appbear-icon-refresh'></i></a>";
        }

        $return .= "<ul class='appbear-wrap-preview appbear-wrap-image appbear-clearfix' data-image-class='{$image_class}'>";
        $return .= "<li class='appbear-preview-item appbear-preview-image'>";
        $return .= "<img src='{$value}' class='{$image_class}'";
        if ( empty( $value ) ) {
            $return .= " style='display: none;'";
        }
        $return .= ">";
        $return .= "<a class='appbear-btn appbear-btn-iconize appbear-btn-small appbear-btn-red appbear-remove-preview'";
        if ( empty( $value ) ) {
            $return .= " style='display: none;'";
        }
        $return .= "><i class='appbear-icon appbear-icon-times-circle'></i></a>";
        $return .= "</li>";
        $return .= "</ul>";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: image_seletor
    |---------------------------------------------------------------------------------------------------
    */
    public function import( $type = '' ) {
        $return = '';
        $items = $this->field->arg( 'items' );
        $items_desc = $this->field->arg( 'items_desc' );
        $options = $this->field->arg( 'options' );
        $import_settings = $this->field->get_appbear()->arg( 'import_settings' );
        if ( ! Functions::is_empty( $items ) ) {
            $has_images = false;
            foreach( $items as $item_key => $item_val ) {
                if ( Functions::get_file_extension( $item_val ) ) {
                    $has_images = true;
                }
            }
            if ( $has_images ) {
                $return .= $this->image_selector();
            } else {
                $return .= $this->radio( 'radio' );
            }
        }

        if ( ! Functions::is_empty( $items_desc ) ) {
            foreach( $items_desc as $item_key => $import_data ) {
                if ( is_array( $import_data ) ) {
                    foreach( $import_data as $import_key => $import_val ) {
                        if ( Functions::starts_with( 'import_', $import_key ) ) {
                            $return .= "<input type='hidden' name='appbear-import-data[$item_key][$import_key]' value='$import_val'>";
                        }
                    }
                }
            }
        }

        $return .= "<div class='appbear-wrap-import-inputs'></div>";

        if ( $options['import_from_file'] ) {
            $return .= "<div class='appbear-wrap-input-file'>";
            $return .= "<input type='file' name='appbear-import-file'>";
            $return .= "</div>";
        }
        if ( $options['import_from_url'] ) {
            $return .= "<div class='appbear-wrap-input-url'>";
            $placeholder = __( 'Enter a valid json url', 'appbear' );
            $return .= "<input type='text' name='appbear-import-url' placeholder='$placeholder'>";
            $return .= "</div>";
        }

        if ( $import_settings['show_authentication_fields'] ) {
            $auth_fields = '
<div class="appbear-row appbear-clearfix appbear-type-mixed appbear-show" style="margin-left: -25px; margin-bottom: 15px;">
    <div class="appbear-label"><label class="appbear-element-label">'.$options["label_text_auth_fields"].'</label>
        <div class="appbear-field-description">'.$options["desc_text_auth_fields"].'</div>
    </div>
    <div class="appbear-content appbear-clearfix">
        <div class="appbear-wrap-mixed appbear-clearfix">
            <div class="appbear-row appbear-clearfix appbear-type-text appbear-row-mixed appbear-grid appbear-col-2-of-8 appbear-row-id-appbear-import-username appbear-show">
                <div class="appbear-label-mixed"><label class="appbear-element-label">Username</label></div>
                <div class="appbear-content-mixed appbear-clearfix">
                    <div class="appbear-field appbear-field-id-appbear-import-username">
                        <input type="text" name="appbear-import-username" class="appbear-element-text">
                    </div>
                </div>
            </div>
            <div class="appbear-row appbear-clearfix appbear-type-text appbear-row-mixed appbear-grid appbear-col-2-of-8 appbear-row-id-appbear-import-password appbear-show">
                <div class="appbear-label-mixed"><label class="appbear-element-label">Password</label></div>
                <div class="appbear-content-mixed appbear-clearfix">
                    <div class="appbear-field appbear-field-id-appbear-import-password ">
                        <input type="password" name="appbear-import-password" class="appbear-element-text">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
            $return .= $auth_fields;
        }

        $return .= "<input type='button' name='appbear-import' id='appbear-import' class='appbear-btn appbear-btn-{$this->field->get_appbear()->arg( 'skin' )}' value='{$options['import_button_text']}'>";

        return $return;
    }

    public function export( $type = '' ) {
        $return = '';
        $options = $this->field->arg( 'options' );
        $file_base_name = $options['export_file_name'];
        $data = $this->field->get_appbear()->get_fields_data( 'json' );
        $file_name = $file_base_name . '-' . date( 'd-m-Y' ) . '.json';
        $return .= "<textarea>$data</textarea>";

        $dir = APPBEAR_DIR;
        if ( is_dir( $dir . 'backups' ) ) {
            $dir = $dir . 'backups/';
        } else {
            if ( mkdir( $dir . 'backups', 0777, true ) ) {
                $dir = $dir . 'backups/';
            }
        }
        $opendir = opendir( $dir );
        while( $file = readdir( $opendir ) ) {
            if ( preg_match( "/^({$file_base_name}-.*.json)/i", $file, $name ) ) {
                if ( isset( $name[0] ) && is_writable( $dir . $name[0] ) ) {
                    @unlink( $dir . $name[0] );
                }
            }
        }

        if ( ! is_writable( $dir ) ) {
            return $return;
        }

        if ( false !== file_put_contents( $dir . $file_name, $data ) ) {
            $file_url = APPBEAR_URL . $file_name;
            if ( stripos( $dir, 'backups' ) !== false ) {
                $file_url = APPBEAR_URL . 'backups/' . $file_name;
            }
            $return .= "<a href='$file_url' id='appbear-export-btn' class='appbear-btn appbear-btn-{$this->field->get_appbear()->arg( 'skin' )}' target='_blank' download>{$options['export_button_text']}</a>";
        }
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: image_seletor
    |---------------------------------------------------------------------------------------------------
    */
    public function image_selector( $type = '' ) {
        $items = $this->field->arg( 'items' );
        if ( Functions::is_empty( $items ) ) {
            return '';
        }
        $items_desc = $this->field->arg( 'items_desc' );
        $options = $this->field->arg( 'options' );
        $wrap_class = 'appbear-radiochecks init-image-selector';
        if ( $this->field->arg( 'options', 'in_line' ) == false ) {
            $wrap_class .= ' appbear-vertical';
        }
        $data_image_chooser = json_encode( $options );
        $return = "<div class='$wrap_class' data-image-selector='$data_image_chooser'>";
        foreach( $items as $key => $image ) {
            $item_class = "appbear-item-image-selector item-key-{$key}";
            if ( ( $key == 'from_file' || $key == 'from_url' ) && ( $options['import_from_file'] || $options['import_from_url'] ) ) {
                $item_class .= " appbear-block";
            }
            $return .= "<div class='$item_class' style='width: {$options['width']}'>";
            $label_class = "";
            if ( ! Functions::get_file_extension( $image ) ) {
                $label_class .= "no-image";
            }
            $return .= "<label class='$label_class'>";
            $return .= $this->build_input( $options['like_checkbox'] ? 'checkbox' : 'radio', $key, array( 'data-image' => $image ) );
            $return .= "<span>$image</span>";
            $return .= "</label>";
            if ( isset( $items_desc[$key] ) ) {
                $return .= "<div class='appbear-item-desc'>";
                if ( is_array( $items_desc[$key] ) ) {
                    if ( isset( $items_desc[$key]['title'] ) ) {
                        $return .= "<div class='appbear-item-desc-title'>{$items_desc[$key]['title']}</div>";
                    }
                    if ( isset( $items_desc[$key]['content'] ) ) {
                        $return .= "<div class='appbear-item-desc-content'>{$items_desc[$key]['content']}</div>";
                    }
                } else {
                    $return .= "<div class='appbear-item-desc'>{$items_desc[$key]}</div>";
                }
                $return .= "</div>";
            }
            $return .= "</div>";
        }
        $return .= "</div>";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: number
    |---------------------------------------------------------------------------------------------------
    */
    public function number( $type = '' ) {
        $attributes = $this->field->arg( 'attributes' );
        $options = $this->field->arg( 'options' );
        if ( ! Functions::is_empty( $attributes ) ) {
            foreach( $attributes as $attr => $val ) {
                if ( in_array( $attr, array( 'min', 'max', 'step', 'precision' ) ) ) {
                    $this->field->args['attributes']['data-' . $attr] = $val;
                }
            }
        }
        $unit_picker = (array) $options['unit_picker'];
        $has_unit_picker = is_array( $unit_picker ) && count( $unit_picker ) > 0 ? true : false;
        $unit_field = $this->field->get_parent()->get_field( $this->field->id . '_unit' );
        $unit_field_name = $unit_field->get_name( $this->field->index );
        $unit_value = $unit_field->get_value( true, 'esc_attr', $this->field->index );
        $has_unit_picker = $has_unit_picker && isset( $unit_picker[$unit_value] );
        if ( ! $has_unit_picker ) {
            $unit_value = $options['unit'];
        }
        $return = $this->build_input( 'text', '', array( 'data-default-unit' => $options['unit'] ), 'esc_attr', array( 'min', 'max', 'step', 'precision' ) );
        $return .= "<div class='appbear-unit appbear-noselect appbear-unit-has-picker-{$has_unit_picker}' data-default-unit='{$options['unit']}'>";

        $return .= "<input type='hidden' name='{$unit_field_name}' value='{$unit_value}' class='appbear-unit-number'>";
        if ( $options['show_unit'] ) {
            $unit_text = $has_unit_picker ? $unit_picker[$unit_value] : $unit_value;
            //$title = $unit_text == '#' ? 'Without unit' : '';
            $return .= "<span>{$unit_text}</span>";
        }
        if ( $has_unit_picker && $options['show_unit'] ) {
            $return .= "<i class='appbear-icon appbear-icon-caret-down appbear-unit-picker'></i>";
            $return .= "<div class='appbear-units-dropdown'>";
            foreach( $unit_picker as $unit => $display ) {
                //$title = $display == '#' ? 'Without unit' : '';
                $return .= "<div class='appbear-unit-item' data-value='$unit'>$display</div>";
            }
            $return .= "</div>";
        }
        $return .= "<a href='javascript:;' class='appbear-spinner-control' data-spin='up'><i class='appbear-icon appbear-icon-caret-up appbear-spinner-handler'></i></a>";
        $return .= "<a href='javascript:;' class='appbear-spinner-control' data-spin='down'><i class='appbear-icon appbear-icon-caret-down appbear-spinner-handler'></i></a>";
        $return .= "</div>";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: oembed
    |---------------------------------------------------------------------------------------------------
    */
    public function oembed( $type = '' ) {
        global $post, $wp_embed;
        $return = '';
        $oembed_url = $this->field->get_value();
        $oembed_class = 'appbear-element-oembed ' . $this->field->arg( 'options', 'oembed_class' );
        $preview_size = $this->field->arg( 'options', 'preview_size' );
        $data_preview_size = json_encode( $preview_size );
        $return .= $this->build_input( 'text' );
        $return .= "<a class='appbear-btn-input appbear-btn appbear-btn-icon appbear-btn-small appbear-btn-teal appbear-get-oembed' title='{$this->field->arg( 'options', 'get_preview_text' )}'><i class='appbear-icon appbear-icon-refresh'></i></a>";
        $full_width = Functions::ends_with( '100%', $preview_size['width'] ) ? 'appbear-oembed-full-width' : '';

        $return .= "<ul class='appbear-wrap-preview appbear-wrap-oembed $full_width appbear-clearfix' data-preview-size='$data_preview_size' data-preview-onload='{$this->field->arg( 'options', 'preview_onload' )}'>";

        /*
        Oembed relentiza la carga de la página. Ahora lo hacemos mediante Ajax, es mucho más rápido.
        Ver includes/class-ajax.php -> get_oembed_ajax();
        */
        /*if ( ! empty( $oembed_url ) && $this->field->arg( 'options', 'preview_onload' ) ) {
            $oembed = Functions::get_oembed( $oembed_url, $preview_size );
            if ( $oembed['success'] ) {
                $provider = strtolower( Functions::get_oembed_provider( $oembed_url ) );
                $return .= "<li class='appbear-preview-item appbear-preview-oembed'>";
                    $return .= "<div class='appbear-oembed appbear-oembed-provider-$provider $oembed_class'>";
                        $return .= $oembed['oembed'];
                    $return .= "</div>";
                    $return .= "<a class='appbear-btn appbear-btn-iconize appbear-btn-small appbear-btn-red appbear-remove-preview'><i class='appbear-icon appbear-icon-times-circle'></i></a>";
                $return .= "</li>";
            } else {
                $return .= $oembed['message'];
            }
        } else {
            $return .= '';
        }*/

        $return .= "</ul>";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: radio
    |---------------------------------------------------------------------------------------------------
    */
    public function radio( $type = '' ) {
        $items = $this->field->arg( 'items' );
        if ( Functions::is_empty( $items ) ) {
            return '';
        }
        $wrap_class = "appbear-radiochecks init-icheck";
        if ( $this->field->arg( 'options', 'in_line' ) == false ) {
            $wrap_class .= ' appbear-vertical';
        }
        if ( $this->field->arg( 'options', 'sortable' ) ) {
            $wrap_class .= ' appbear-sortable';
        }
        $return = "<div class='$wrap_class'>";
        $temp = array();

        foreach( $items as $key => $display ) {
            $key = (string) $key;//Permite 0 como clave
            $html_item = "<label>";
            $html_item .= $this->build_input( $type, $key ) . $display;
            $html_item .= "</label>";
            $temp[$key] = $html_item;
        }

        if ( $type == 'checkbox' ) {
            $value = $this->field->get_value( false );
            if ( ! Functions::is_empty( $value ) ) {
                foreach( $value as $key ) {
                    $return .= $temp[$key];
                    unset( $temp[$key] );
                }
            }
        }
        foreach( $temp as $key => $html ) {
            $return .= $html;
        }
        $return .= "</div>";
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: select
    |---------------------------------------------------------------------------------------------------
    */
    public function select( $type = '' ) {
        return $this->build_select( 'select' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: switcher
    |---------------------------------------------------------------------------------------------------
    */
    public function switcher( $type = '' ) {
        $attributes = $this->field->arg( 'attributes' );
        $attributes['data-switcher'] = json_encode( $this->field->arg( 'options' ) );
        $attributes = Functions::nice_array_merge(
            $attributes,
            array( 'class' => 'appbear-element-switcher' ),
            array(),
            array( 'class' => ' ' )
        );
        return $this->build_input( 'hidden', '', $attributes );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: text
    |---------------------------------------------------------------------------------------------------
    */
    public function text( $type = '' ) {
        $return = '';
        $return .= $this->build_input( 'text' );
        $options = $this->field->arg( 'options' );
        $value = $this->field->get_value( true );
        if ( ! empty( $options['helper'] ) ) {
            $helper = $options['helper'];
            if ( $helper == 'maxlength' && $maxlength = $this->field->arg( 'attributes', 'maxlength' ) ) {
                $helper = strlen( $value ) . '/' . $maxlength;
            }
            $return .= "<span class='appbear-field-helper'>$helper</span>";
        }
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: title
    |---------------------------------------------------------------------------------------------------
    */
    public function title() {
        $title_class = $this->field->arg( 'attributes', 'class' );
        $title = $this->field->arg( 'name' );
        if ( ! empty( $title ) ) {
            return "<h3 class='appbear-field-title $title_class'>$title</h3>";
        }
        return '';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: textarea
    |---------------------------------------------------------------------------------------------------
    */
    public function textarea( $type = '' ) {
        return $this->build_textarea( 'textarea' );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: textarea
    |---------------------------------------------------------------------------------------------------
    */
    public function build_textarea( $type = '' ) {
        $return = '';
        $attributes = $this->field->arg( 'attributes' );
        $value = $this->field->get_value( true, 'esc_textarea' );

        $element_attributes = array(
            'name' => $this->field->get_name(),
            'id' => Functions::get_id_attribute_by_name( $this->field->get_name() ),
            'class' => "appbear-element appbear-element-{$type}"
        );

        // Une todos los atributos. Evita el reemplazo de ('name', 'id', 'value', 'checked')
        // y une los valores del atributo 'class'
        $attributes = Functions::nice_array_merge(
            $element_attributes,
            $attributes,
            array( 'name', 'id' ),
            array( 'class' => ' ' )
        );

        foreach( $attributes as $attr => $val ) {
            if ( is_array( $val ) || $attr == 'value' ) {
                unset( $attributes[$attr] );
            }
        }

        return sprintf( '<textarea %s>%s</textarea>', $this->join_attributes( $attributes ), $value );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build field type: wp_editor
    |---------------------------------------------------------------------------------------------------
    */
    public function wp_editor( $type = '' ) {
        $return = '';
        $attributes = $this->field->arg( 'attributes' );
        $value = $this->field->get_value( true, 'stripslashes' );
        $id = Functions::get_id_attribute_by_name( $this->field->get_name() );
        $this->field->args['options']['textarea_name'] = $this->field->get_name();

        ob_start();
        wp_editor( $value, $id, $this->field->arg( 'options' ) );
        $return = ob_get_contents();
        ob_end_clean();

        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build input
    |---------------------------------------------------------------------------------------------------
    */
    public function build_input( $type = 'text', $value = '', $attributes = array(), $escaping_function = 'esc_attr', $exclude_attributes = array() ) {
        $attributes = wp_parse_args( $attributes, $this->field->arg( 'attributes' ) );
        $field_value = $this->field->get_value( true, $escaping_function );
        $value = $value !== '' ? esc_attr( $value ) : $field_value;

        $element_attributes = array(
            'type' => $type,
            'name' => $this->field->get_name(),
            'id' => Functions::get_id_attribute_by_name( $this->field->get_name() ),
            'value' => $value,
            'data-initial-value' => $value,//Valor inicial al cargar la página
            'data-prev-value' => $value,//Valor anterior al valor actual. Usado para (Ctrl + z)
            'data-temp-value' => $value,//Valor temporal. Usado para (Ctrl + z)
            'class' => "appbear-element appbear-element-{$type}"
        );

        if ( $type == 'radio' && $value == $field_value ) {
            $element_attributes['checked'] = 'checked';
        }
        if ( $type == 'checkbox' && is_array( $field_value ) && in_array( $value, $field_value ) ) {
            $element_attributes['checked'] = 'checked';
        }
        if ( $type == 'tagsinput' ) {
            $element_attributes['data-role'] = 'tagsinput';
        }
        if ( $type == 'radio' || $type == 'checkbox' ) {
            unset( $element_attributes['id'] );
            unset( $attributes['id'] );
            if ( isset( $attributes['disabled'] ) ) {
                if ( is_array( $attributes['disabled'] ) && ! Functions::is_empty( $attributes['disabled'] ) ) {
                    if ( in_array( $value, $attributes['disabled'] ) ) {
                        $attributes['disabled'] = 'disabled';
                    } else {
                        unset( $attributes['disabled'] );
                    }
                } else if ( $attributes['disabled'] === true || $attributes['disabled'] == $value ) {
                    $attributes['disabled'] = 'disabled';
                } else {
                    unset( $attributes['disabled'] );
                }
            }
        }

        // Une todos los atributos. Evita el reemplazo de ('name', 'id', 'value', 'checked')
        // y une los valores del atributo 'class'
        $attributes = Functions::nice_array_merge(
            $element_attributes,
            $attributes,
            array( 'name', 'id', 'value', 'checked' ),
            array( 'class' => ' ' )
        );

        //Remove invalid attributes
        foreach( $attributes as $attr => $val ) {
            if ( is_array( $val ) ) {
                unset( $attributes[$attr] );
            }
        }
        //Exclude attributes
        foreach( $attributes as $attr => $val ) {
            if ( in_array( $attr, $exclude_attributes ) ) {
                unset( $attributes[$attr] );
            }
        }

        $input = sprintf( '<input data-yes="'.$type.'" %s>', $this->join_attributes( $attributes ) );
        return $input;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Build select
    |---------------------------------------------------------------------------------------------------
    */
    public function build_select( $type = 'select', $value = '', $attributes = array(), $escaping_function = 'esc_attr' ) {
        $items = $this->field->arg( 'items' );

        $attributes = wp_parse_args( $attributes, $this->field->arg( 'attributes' ) );
        $options = $this->field->arg( 'options' );
        $items_select = "";

        //Option none
        if ( isset( $items[''] ) ) {
            $items_select .= "<div class='item' data-value=''>{$items['']}</div>";
            unset( $items[''] );
        }
        if ( $options['sort'] ) {
            $items = Functions::sort( $items, $options['sort'], $options['sort_by_values'] ? 'value' : 'key' );
        }

        foreach( $items as $key => $display ) {
            if ( is_array( $display ) ) {
                if ( ! Functions::is_empty( $display ) ) {
                    $items_select .= "<div class='divider'></div>";
                    $items_select .= "<div class='header'><i class='appbear-icon appbear-icon-tags'></i>$key</div>";
                    if ( $options['sort'] ) {
                        $display = Functions::sort( $display, $options['sort'], $options['sort_by_values'] ? 'value' : 'key' );
                    }
                    foreach( $display as $i => $d ) {
                        $i = esc_html( $i );
                        $items_select .= "<div class='item' data-value='$i'>$d</div>";
                    }
                }
            } else {
                $key = esc_html( $key );
                $items_select .= "<div class='item' data-value='$key'>$display</div>";
            }
        }

        $dropdown_class = "appbear-element appbear-element-$type ui fluid selection dropdown";

        if ( $options['search'] === true ) {
            $dropdown_class .= " search";
        }
        if ( $options['multiple'] === true ) {
            $dropdown_class .= " multiple";
        }
        if ( isset( $attributes['class'] ) ) {
            $dropdown_class .= " {$attributes['class']}";
        }

        $default_attributes = array(
            'class' => $dropdown_class,
            'data-max-selections' => $options['max_selections'],
        );
        // Une todos los atributos. Evita el reemplazo de ('name', 'id')
        // y une los valores del atributo 'class'
        $attributes = Functions::nice_array_merge(
            $default_attributes,
            $attributes,
            array( 'name', 'id' ),
            array( 'class' => ' ' )
        );

        $name = $this->field->get_name();
        $value = $this->field->get_value( true, $escaping_function );

        if ( $options['multiple'] === true ) {
            $value = implode( ',', (array) $value );
        }

        $return = sprintf( '<div %s>', $this->join_attributes( $attributes ) );
        //$return = "<div class='$dropdown_class' data-max-selections='{$options['max_selections']}'>";
        $return .= "<input type='hidden' name='{$name}' value='$value' data-initial-value='$value' data-prev-value='$value' data-temp-value='$value'>";
        $return .= "<i class='dropdown icon'></i>";
        $return .= "<div class='default text'>{$attributes['placeholder']}</div>";
        $return .= "<div class='menu'>";
        $return .= "<div class='appbear-ui-inner-menu'>";
        $return .= $items_select;
        $return .= "</div>";
        $return .= "</div>";
        $return .= "</div>";

        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la extensión de una imagen es válida
    |---------------------------------------------------------------------------------------------------
    */
    public function is_image_file( $file_path = '' ) {
        $extension = Functions::get_file_extension( $file_path );
        if ( $extension && in_array( $extension, array( 'png', 'jpg', 'jpeg', 'gif', 'ico' ) ) ) {
            return true;
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la extensión de una video válido
    |---------------------------------------------------------------------------------------------------
    */
    public function is_video_file( $file_path = '' ) {
        $extension = Functions::get_file_extension( $file_path );
        if ( $extension && in_array( $extension, array( 'mp4', 'webm', 'ogv', 'ogg', 'vp8' ) ) ) {
            return true;
        }
        return false;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Une los atributos de un campo
    |---------------------------------------------------------------------------------------------------
    */
    public function join_attributes( $attrs = array() ) {
        $attributes = '';
        foreach( $attrs as $attr => $value ) {
            $quotes = '"';
            if ( stripos( $attr, 'data-' ) !== false ) {
                $quotes = "'";
            }
            $attributes .= sprintf( ' %1$s=%3$s%2$s%3$s', $attr, $value, $quotes );
        }
        return $attributes;
    }

  /**
   * Prepare Icons Select Options Items
   *
   * @param array $items
   * @return array
   */
  private function _prepareIconsItems(array $items) {
    foreach( $items as $k => &$icon ) {
      $icon = trim(str_replace( ['<i class=', '></i>', '"', "'"], '', $icon ));
    }

    return $items;
  }
}
