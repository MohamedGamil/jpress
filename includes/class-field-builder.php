<?php

namespace JPress\Includes;


class FieldBuilder {
	private $field = null;

	/*
	|---------------------------------------------------------------------------------------------------
	| Constructor de la clase
	|---------------------------------------------------------------------------------------------------
	*/
	public function __construct( $field = null ){
		$this->field = $field;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build(){
		$return = '';

		switch( $this->field->arg( 'type' ) ){
			case 'private':
				break;

			case 'mixed':
				$return .= $this->build_mixed();
				break;

			case 'tab':
				if( $this->field->arg( 'action' ) == 'open' ){
					$return .= $this->build_tab_menu();
				} else {
					$return .= "</div></div><div class='jpress-separator jpress-separator-tab'></div>";//.jpress-tab-body .jpress-tab
				}
				break;

			case 'tab_item':
				$return .= $this->build_tab_item();
				break;

			case 'group':
				$return .= $this->build_group();
				break;

			case 'section':
				$return .= $this->build_section();
				break;

            case 'html_content':
                $return .= $this->field->arg( 'content' );
                break;

			default:
				$return .= $this->build_field();
				break;
		}

		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el contendor de campos mixtos
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_mixed(){
		$return = "";
		if( $this->field->arg( 'action' ) == 'open' ){
			$return .= $this->build_open_row();
		} else if( $this->field->arg( 'action' ) == 'close' ){
			$return .= $this->build_close_row();
		}
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Crea el inicio de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_open_row(){
		$return = "";
		$condition = "";
		$type = $this->field->arg( 'type' );
		$grid = $this->field->arg( 'grid' );
		$options = $this->field->arg( 'options' );
		$show_if = json_encode( (array) $options['show_if'] );
		$hide_if = json_encode( (array) $options['hide_if'] );
		$row_class = $this->get_row_class();
		$row_id = Functions::get_id_attribute_by_name( $this->field->get_name() );
		$content_class = "jpress-content";

		if ( $this->field->in_mixed ){
			$content_class .= "-mixed";
    }

		$data_show_hide = json_encode(array(
			'show_if' => (array) $options['show_if'],
			'hide_if' => (array) $options['hide_if'],
			'effect' => $options['show_hide_effect'],
			'delay' => $options['show_hide_delay'],
    ));

    // NOTE: Passes conditions to support conditional show/hide of items in a multi-choice input
		$data_show_hide_items = json_encode(array(
			'show_items_if' => isset($options['show_items_if']) && empty($options['show_items_if']) === false ? (array) $options['show_items_if'] : array(),
    ));

		if (isset($options['show_if'][0])) {
      $isMulti = is_array($options['show_if'][0]);

      if ($isMulti) {
        foreach ($options['show_if'] as $showIfCondition) {
          $row_class .= " condition_" . $showIfCondition[0];
        }
      } else {
        $row_class .= " condition_" . $options['show_if'][0];
      }
		}

    // NOTE: BUG: Results in an-unwanted behavior!
		// if (isset($options['show_items_if']) && empty($options['show_items_if']) === false) {
    //   foreach ($options['show_items_if'] as $key => $conditions) {
    //     $isMulti = is_array($conditions[0]);

    //     if ($isMulti) {
    //       foreach ($conditions as $showIfCondition) {
    //         $row_class .= " condition_items_" . $showIfCondition[0];
    //       }
    //     } else {
    //       $row_class .= " condition_items_" . $conditions[0];
    //     }
    //   }
		// }

		$return .= "<div id='{$this->field->arg( 'row_id' )}' class='$row_class' data-row-level='{$this->field->get_row_level()}' data-field-id='{$this->field->id}' data-field-type='$type'  data-show-hide='$data_show_hide' data-show-hide-items='$data_show_hide_items'>";
			$return .= $this->build_label();
			$return .= "<div class='$content_class jpress-clearfix'>";

		if( $type == 'mixed' ){
			$return .= "<div class='jpress-wrap-mixed jpress-clearfix'>";
		}

		return $this->field->arg( 'insert_before_row' ) . $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Crea el final de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_close_row(){
		$return = "";
		$type = $this->field->arg( 'type' );
		$options = $this->field->arg( 'options' );
		$description = $this->field->arg( 'desc' );
		$description_title = $this->field->arg( 'desc_title' );

		if( $type == 'mixed' ){
			$return .= "</div>"; //.jpress-wrap-mixed
		}

		//Field description
		if( ! Functions::is_empty( $description )  ){
			if( ! $options['desc_tooltip'] ){
				$return .= $this->build_field_description( 'desc' );
			} else if( ! $this->field->in_mixed ){
				$return .= "<div class='jpress-tooltip-handler jpress-icon jpress-icon-question' data-tipso='$description' data-tipso-title='$description_title' data-tipso-position='left'></div>";
			}
		}

		$return .= "</div>";//.jpress-content
		$return .= "</div>";//.jpress-row
		return $return . $this->field->arg( 'insert_after_row' );
	}

    /*
    |---------------------------------------------------------------------------------------------------
    | Descripción del campo
    |---------------------------------------------------------------------------------------------------
    */
    public function build_field_description( $arg_name ){
        $description = $this->field->arg( $arg_name );
        $description_title = $this->field->arg( 'desc_title' );
        if( Functions::is_empty( $description )  ){
            return '';
        }
        return "<div class='jpress-field-description'><strong class='jpress-field-description-title'>$description_title</strong>$description</div>";
    }


	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el menú de los tabs
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_tab_menu(){
		$return = "";
		$items = $this->field->arg( 'items' );
		$name = $this->field->arg( 'name' );
		$options = $this->field->arg( 'options' );
		$fields_prefix = $this->field->get_jpress()->arg('fields_prefix');
		// if( ! is_array( $items ) || Functions::is_empty( $items ) ){
		// 	return '';
		// }
		$item_tab = '';
		$i = 0;


    $conditions	=	(isset($options['conditions']) ?$options['conditions'] : false);

		foreach ( $items as $key => $display ){
			$active = $i == 0 ? ' active' : ''; $i++;
			$sub_items = '';
			$item_class = 'jpress-item jpress-item-parent';
			if( ! is_array( $display ) ){
				$text = $display;
			} else {
				$text = isset( $display['text'] ) ? $display['text'] : 'Tab item';
				if( ! empty( $display['items'] ) && is_array( $display['items'] ) ){
					foreach( $display['items'] as $item => $show ){
						$sub_items .= "<li class='jpress-item tab-item-{$item} jpress-item-child' data-parent='$key' data-tab='#{$fields_prefix}tab_item-{$item}'>";
							$sub_items .= "<a href='#{$fields_prefix}tab_item-{$item}'>$show</a>";
						$sub_items .= "</li>";
					}
				}
			}

			if( $sub_items != '' ){
				$item_class .= ' jpress-item-has-childs';
			}
			$show_hide = $hide_if = $show_if = '';
			if($options['conditions'] && isset($options['conditions'][$key])){

				if(isset($options['conditions'][$key]['show_if'][0])){
					$show_if = $options['conditions'][$key]['show_if'];
					$item_class .= ' condition_'.$show_if[0];
				}
				if(isset($options['conditions'][$key]['hide_if'][0])){
					$hide_if = $options['conditions'][$key]['hide_if'];
					$item_class .= ' condition_'.$hide_if[0];
				}
				$data_show_hide = json_encode(array(
					'show_if' => (array) $show_if,
					'hide_if' => (array) $hide_if,
				));
				$show_hide = "data-show-hide='$data_show_hide'";
			}

			//Show
			$show = true;
			if( empty( $show_if ) || empty( $show_if[0] ) ){
				$show = true;
			} else if( is_array( $show_if[0] ) ){

			} else {
				$get_jpress = \JPress::get($this->field->get_jpress()->id);
				if($field_value = $get_jpress->get_field_value($show_if[0])){
					$value = '';
					$operator = '==';
					if( count( $show_if ) == 2 ){
						$value = isset( $show_if[1] ) ? $show_if[1] : '';
					} else if( count( $show_if ) == 3 ){
						$value = isset( $show_if[2] ) ? $show_if[2] : '';
						$operator = ! empty( $show_if[1] ) ? $show_if[1] : $operator;
						$operator = $operator == '=' ? '==' : $operator;
					}
					if( in_array( $operator,  array('==', '!=', '>', '>=', '<', '<=') ) ){
						$show = Functions::compare_values_by_operator( $field_value, $operator, $value );
					} else if( in_array( $operator,  array('in', 'not in' ) ) ){
						if( ! empty( $value ) && is_array( $value ) ){
							$show = $operator == 'in' ? in_array( $field_value, $value ) : ! in_array( $field_value, $value );
						}
					}
				}
			}

			//Hide
			$hide = false;
			if( empty( $hide_if ) || empty( $hide_if[0] ) ){
				$hide = false;
			} else if( is_array( $hide_if[0] ) ) {

			} else {
				$get_jpress = \JPress::get($this->field->get_jpress()->id);
				if($field_value = $get_jpress->get_field_value($hide_if[0])){
					$value = '';
					$operator = '==';
					if( count( $hide_if ) == 2 ){
						$value = isset( $hide_if[1] ) ? $hide_if[1] : '';
					} else if( count( $hide_if ) == 3 ){
						$value = isset( $hide_if[2] ) ? $hide_if[2] : '';
						$operator = ! empty( $hide_if[1] ) ? $hide_if[1] : $operator;
						$operator = $operator == '=' ? '==' : $operator;
					}
					if( in_array( $operator,  array('==', '!=', '>', '>=', '<', '<=') ) ){
						$hide = Functions::compare_values_by_operator( $field_value, $operator, $value );
					} else if( in_array( $operator,  array('in', 'not in' ) ) ){
						if( ! empty( $value ) && is_array( $value ) ){
							$hide = $operator == 'in' ? in_array( $field_value, $value ) : ! in_array( $field_value, $value );
						}
					}
        }
			}

			$style = '';
			if( $show ){
				if( $hide == true ){
					$style = 'style="display:none"';
				} else {
					$style = 'style="display:block"';
				}
			} else {
				$style = 'style="display:none"';
			}

			$item_tab .= "<li class='$item_class tab-item-{$key} $active' $show_hide data-item='$key' data-tab='#{$fields_prefix}tab_item-{$key}' $style >";
				$item_tab .= $sub_items == '' ? '': "<span class='jpress-toggle-icon'><i class='jpress-icon jpress-icon-chevron-down'></i></span>";
				$item_tab .= "<a href='#{$fields_prefix}tab_item-{$key}'>$text</a>";
			$item_tab .= "</li>";
			$item_tab .= $sub_items;
		}


		$tab_class = 'jpress-tab';

		if( $options['main_tab'] ){
			$tab_class .= ' jpress-main-tab jpress-tab-left';
		}

		if( $this->field->get_jpress()->arg( 'context' ) == 'side' ){
			$tab_class .= ' accordion';
		}
		$tab_class .= ' ' . str_replace( $fields_prefix.'open-', '', $this->field->id );
		$tab_class .= " jpress-tab-{$options['skin']}";
		$tab_class .= " {$this->field->arg( 'attributes', 'class' )}";

		$return .= "<div class='$tab_class' data-tab-id='{$this->field->id}'>";
			$return .= "<div class='jpress-tab-header'>";
				$return .= "<nav class='jpress-tab-nav'><ul class='jpress-tab-menu jpress-clearfix'>";
					$return .= $item_tab;
				$return .= "</ul></nav>";
			$return .= "</div>";
			$return .= "<div class='jpress-tab-body'>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el contenido de los tabs
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_tab_item(){
		$return = "";
		$options = $this->field->arg('options');
		$data_tab = str_replace( 'open-', '', $this->field->id );
		$class = str_replace( $this->field->get_jpress()->arg('fields_prefix').'tab_item-', '', $data_tab );
		if( $this->field->arg( 'action' ) == 'open' ){
			$return .= "<div class='jpress-tab-content tab-content-{$class}' data-tab='#{$data_tab}'>";
		} else if( $this->field->arg( 'action' ) == 'close' ){
			$return .= "</div>";
		}

		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Crea el label
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_label(){
		$options = $this->field->arg( 'options' );
		if( ! $options['show_name'] ){
			return '';
		}

		$return = "";
		$label = $this->field->arg( 'name' );
		if( $this->field->arg( 'attributes', 'required' ) ){
			$label .= '<span class="jpress-required-field">*</span>';
		}
		$for = Functions::get_id_attribute_by_name( $this->field->get_name() );
		$mixed = $this->field->in_mixed ? '-mixed' : '';
		$description = $this->field->arg( 'desc' );
		$description_title = $this->field->arg( 'desc_title' );

		$return .= "<div class='jpress-label$mixed'>";
			$return .= $this->field->arg( 'insert_before_name' );

			//Field description
			if( ! $this->field->in_mixed || Functions::is_empty( $description ) || ! $options['desc_tooltip'] ){
				$return .= "<label for='$for' class='jpress-element-label'>$label</label>";
			} else {
				$return .= "<label for='$for' class='jpress-element-label'>$label <i class='jpress-tooltip-handler jpress-icon jpress-icon-question-circle' data-tipso='$description' data-tipso-title='$description_title'></i></label>";
			}

			if( $this->field->arg( 'type' ) == 'group' ){
				$controls = $this->field->arg( 'controls' );
				$return .= "<a class='jpress-btn jpress-btn-small jpress-btn-teal jpress-add-group-item {$options['add_item_class']}' title='{$options['add_item_text']}' data-item-type='{$controls['default_type']}'><i class='jpress-icon jpress-icon-plus'></i>{$options['add_item_text']}</a>";
			}
			$return .= $this->build_field_description( 'desc_name' );
			$return .= $this->field->arg( 'insert_after_name' );
		$return .= "</div>";

		return $this->check_data() ? $return : '';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un section
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_section(){
		$return = "";
		$description = $this->field->arg( 'desc' );
		$options = $this->field->arg( 'options' );
		$data_toggle = json_encode( array(
			'effect' => $options['toggle_effect'],
			'target' => $options['toggle_target'],
			'speed' => $options['toggle_speed'],
			'open_icon' => $options['toggle_open_icon'],
			'close_icon' => $options['toggle_close_icon'],
		));

		$return .= "<div class='jpress-section jpress-clearfix jpress-toggle-{$options['toggle']} jpress-toggle-{$options['toggle_default']} jpress-toggle-{$options['toggle_target']}' data-toggle='$data_toggle' >";
			$return .= "<div class='jpress-section-header'>";
				$return .= "<h3 class='jpress-section-title'>{$this->field->arg( 'name' )}</h3>";
				$return .= $this->build_field_description( 'desc' );//sin strong title
				if( $options['toggle'] ){
					$icon = $options['toggle_default'] == 'open' ? $options['toggle_open_icon'] : $options['toggle_close_icon'];
					$return .= "<span class='jpress-toggle-icon'><i class='jpress-icon $icon'></i></span>";
				}
			$return .= "</div>";//.jpress-section-header
			$return .= "<div class='jpress-section-body'>";
				foreach ( $this->field->fields_objects as $field ){
					$field_builder = new FieldBuilder( $field );
					$return .= $field_builder->build();
				}
			$return .= "</div>";//.jpress-section-body
		$return .= "</div>";//.jpress-section
		$return .= "<div class='jpress-separator jpress-separator-section'></div>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un grupo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group(){
		$return = "";
		$value = $this->field->get_group_value();

		$return .= $this->build_open_row();
			$return .= $this->build_group_control();
			$return .= "<div class='jpress-group-wrap jpress-clearfix'>";//No estaba clearfix
				if( empty( $value ) ){
					//$return .= $this->build_group_item();
				}
				else {
					foreach ( $value as $key => $field_id ){
						$return .= $this->build_group_item();
						$this->field->index++;
					}
					$this->field->index = 0;
				}
			$return .= "</div>";//.jpress-group-wrap

			//Source item
			$return .= "<div class='jpress-source-item'>";
			$this->field->index = 1000;
				$return .= $this->build_group_control_item(1000);
				$return .= $this->build_group_item();
			$return .= "</div>";
			$this->field->index = 0;

		$return .= $this->build_close_row();

		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el control de un grupo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group_control(){
		$return = "";
		$value = $this->field->get_group_value();
		$options = $this->field->arg( 'options' );
		$controls = $this->field->arg( 'controls' );

		$control_class = "jpress-group-control";

		if( $options['sortable'] ) {
			$control_class .= " jpress-sortable";
		}
		if( $controls['images'] == true ) {
			$control_class .= " jpress-has-images";
		}
		$control_class .= " jpress-position-{$controls['position']}";

		$return .= "<ul class='$control_class' data-control-name='{$controls['name']}' data-image-field-id='{$controls['image_field_id']}'>";
			if( empty( $value ) ){
				//$return .= $this->build_group_control_item( 0 );
			}
			else {
				foreach ( $value as $i => $field_item ){
					$return .= $this->build_group_control_item( $this->field->index );
					$this->field->index++;
				}
				$this->field->index = 0;
			}
		$return .= "</ul>";

		$css = '';
		$css .= "<style>";
		if( $controls['position'] === 'left' && ! empty( $controls['width'] ) ) {
			$css .= "
			.jpress-row-id-{$this->field->id} > .jpress-content > .jpress-group-control {
				width: {$controls['width']};
			}
			";
		}
		if( ! empty( $controls['width'] ) ){
			$css .= "
			.jpress-row-id-{$this->field->id} > .jpress-content > .jpress-group-control > li,
			.jpress-row-id-{$this->field->id} > .jpress-content > .jpress-group-control.jpress-has-images > li {
				width: {$controls['width']};
				max-width: 100%;
			}
			";
		}
		if( ! empty( $controls['height'] ) ){
			$css .= "
			.jpress-row-id-{$this->field->id} > .jpress-content > .jpress-group-control > li,
			.jpress-row-id-{$this->field->id} > .jpress-content > .jpress-group-control.jpress-has-images > li {
				height: {$controls['height']};
			}
			";
		}
		$css .= "</style>";
		return $css.$return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye cada item del control de un grupo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group_control_item( $index = 0 ){
		$return = "";
		//$value = $this->field->get_group_value();
		$controls = $this->field->arg( 'controls' );
		$options = $this->field->arg( 'options' );

		//Private fields
		$private_field_name = $this->field->get_field( $this->field->id.'_name' );
		$private_field_type = $this->field->get_field( $this->field->id.'_type' );
		$private_field_visibility = $this->field->get_field( $this->field->id.'_visibility' );
		$item_type = $private_field_type->get_value();

		$item_class = "jpress-group-control-item control-item-type-".$item_type;

		if( $index === 0 ){
			$item_class .= " jpress-active";
		}

		$return .= "<li class='$item_class' data-index='$index' data-type='{$item_type}'>";
			//Private fields
			$return .= "<input type='hidden' class='jpress-input-group-item-type' name='{$private_field_type->get_name()}' value='{$private_field_type->get_value()}'>";
			$return .= "<input type='hidden' class='jpress-input-group-item-visibility' name='{$private_field_visibility->get_name()}' value='{$private_field_visibility->get_value()}'>";

			$return .= "<div class='jpress-inner'>";
				if( $controls['images'] == false ){
					$value = $private_field_name->get_value();
					$value = $value ? $value : str_replace('#', '#'.($index + 1) , $controls['name'] );
					$return .= "<input type='text' name='{$private_field_name->get_name()}' value='{$value}'";
					if( $controls['readonly_name'] ){
						$return .= " readonly>";
						$return .= "<div class='jpress-readonly-name'></div>";
					} else {
						$return .= ">";
					}
				} else {
					$image = '';
					$field_id = $controls['image_field_id'];

					if( $this->field->get_jpress()->exists_field( $field_id.'_id', $this->field->fields ) ){
						$field = $this->field->get_field( $field_id.'_id' );
						$attachment_id = $field->get_value();
						if( $attachment_id && ! is_array( $attachment_id ) ){
							$image = wp_get_attachment_image_src( $attachment_id, array( 300,300 ), false );
							$image = isset( $image[0] ) ? $image[0] : '';
						}
					}
					if( ! $image && $this->field->get_jpress()->exists_field( $field_id, $this->field->fields ) ){
						$field = $this->field->get_field( $field_id );
						$value = $field->get_value();
						/**
						 * Spotlayer: Fix image selector change
						 */
						if($field->arg( 'type' ) == 'image_selector'){
							$image = $field->arg( 'items' )[$value];
						}else{
							if( $value && ! is_array( $value ) && $attachment_id = Functions::get_attachment_id_by_url( $value ) ){
								$image = wp_get_attachment_image_src( $attachment_id, array( 300,300 ), false );
								$image = isset( $image[0] ) ? $image[0] : '';
							}
							if( ! $image && ! is_array( $value ) ){
								$image = $value;
							}
						}
					}
					$return .= "<div class='jpress-wrap-image' style='background-image: url({$controls['default_image']})'>";
						$return .= "<div class='jpress-control-image' style='background-image: url($image)'>";
						$return .= "</div>";
					$return .= "</div>";
				}
			$return .= "</div>";
			$return .= "<div class='jpress-actions jpress-clearfix'>";
				$return .= "<div class='jpress-actions-left'>";
				$title = "";
				$custom_class = "";
				foreach( (array) $controls['left_actions'] as $btn_class => $icon ){
					if( ! $icon ) continue;
					$icon = $icon == '#' ? "#".( $index + 1 ) : $icon;
					if( stripos( $btn_class, 'sort') !== false ){
						$title = $options['sort_item_text'];
						$custom_class = $options['sort_item_class'];
					} else if( stripos( $btn_class, 'eye') !== false || stripos( $btn_class, 'visibility') !== false ){
						$title = $options['visibility_item_text'];
						$custom_class = $options['visibility_item_class'];
					}
					$return .= "<a class='jpress-btn jpress-btn-tiny jpress-btn-iconize $btn_class $custom_class' title='$title'>$icon</a>";
				}
				$return .= "</div>";
				$return .= "<div class='jpress-actions-right'>";
				foreach( (array) $controls['right_actions'] as $btn_class => $icon ){
					if( ! $icon ) continue;
					$icon = $icon == '#' ? "#".( $index + 1 ) : $icon;
					if( stripos( $btn_class, 'duplicate') !== false ){
						$title = $options['duplicate_item_text'];
						$custom_class = $options['duplicate_item_class'];
					} else if( stripos( $btn_class, 'remove') !== false ){
						$title = $options['remove_item_text'];
						$custom_class = $options['remove_item_class'];
					} else if( stripos( $btn_class, 'eye') !== false || stripos( $btn_class, 'visibility') !== false ){
						$title = $options['visibility_item_text'];
						$custom_class = $options['visibility_item_class'];
					}
					$return .= "<a class='jpress-btn jpress-btn-tiny jpress-btn-iconize $btn_class $custom_class' title='$title'>$icon</a>";
				}
				$return .= "</div>";//.jpress-actions-right
			$return .= "</div>";//.jpress-actions
		$return .= "</li>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el campo de un grupo, si este campo es un grupo, entonces se vuelve a llamar a build_group()
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group_item(){
		$return = "";

		//Private fields
		$private_field_type = $this->field->get_field( $this->field->id.'_type' );
		$item_type = $private_field_type->get_value();

		$item_class = "jpress-group-item jpress-clearfix group-item-type-".$item_type;
		if( $this->field->index === 0 ){
			$item_class .= " jpress-active";
		}
		$return .= "<div class='$item_class' data-index='{$this->field->index}' data-type='{$item_type}'>";
		foreach ( $this->field->fields_objects as $field ){
			$field_builder = new FieldBuilder( $field );
			$return .= $field_builder->build();
		}
		$return .= "</div>";//.jpress-group-item

		return $return;
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un campo, si es un campo repetible se llama a build_repeatable_items()
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_field(){
		$return = "";
		$return .= $this->build_open_row();
			if( $this->field->arg( 'repeatable' ) ){
				$return .= $this->build_repeatable_items();
			} else {
				$return .= $this->build_field_type();
			}
		$return .= $this->build_close_row();
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un los campos repetibles
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_repeatable_items(){
		$return = "";
		$value = $this->field->get_value( true, 'esc_html', null, true );//default, escape, index, all
		$options = $this->field->arg( 'options' );
		$grid = $this->field->arg( 'grid' );

		$wrap_class = "jpress-repeatable-wrap";
		if( $options['sortable'] ){
			$wrap_class .= " jpress-sortable";
		}

		if( ! $this->field->in_mixed && $this->field->is_valid_grid_value( $grid ) ){
			$wrap_class .= " jpress-grid jpress-col-$grid";
		}

		$return .= "<div class='$wrap_class'>";
			if( empty( $value ) ){
				$return .= $this->build_repeatable_item();
			} else {
				foreach ( $value as $key => $field_id ){
					$return .= $this->build_repeatable_item();
					$this->field->index++;
				}
				$this->field->index = 0;
			}
			$return .= "<a class='jpress-btn jpress-btn-small jpress-btn-teal jpress-add-repeatable-item {$options['add_item_class']}' title='{$options['add_item_text']}'><i class='jpress-icon jpress-icon-plus'></i>{$options['add_item_text']}</a>";
		$return .= "</div>";//.jpress-repeatable-wrap

		return $this->field->arg( 'insert_before_repeatable' ) . $return . $this->field->arg( 'insert_after_repeatable' );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un item de campos repetibles
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_repeatable_item(){
		$return = "";
		$options = $this->field->arg( 'options' );
		$return .= "<div class='jpress-repeatable-item' data-index='{$this->field->index}'>";
			$return .= $this->build_field_type();
			$return .= "<a class='jpress-btn jpress-btn-small jpress-btn-iconize jpress-sort-item {$options['sort_item_class']}' title='{$options['sort_item_text']}'><i class='jpress-icon jpress-icon-sort'></i></a>";
			$return .= "<a class='jpress-btn jpress-btn-small jpress-btn-red jpress-btn-iconize jpress-opacity-80 jpress-remove-repeatable-item {$options['remove_item_class']}' title='{$options['remove_item_text']}'><i class='jpress-icon jpress-icon-times-circle'></i></a>";
		$return .= "</div>";//.jpress-repeatable-item

		return $return;
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el campo en sí
	|---------------------------------------------------------------------------------------------------
	*/
	private function build_field_type(){
		$return = "";
		$type = $this->field->arg( 'type' );
		$field_type = new FieldTypes( $this->field );

		$field_class = $this->get_field_class();
		$default = $this->field->arg( 'default' );
		if( is_array( $default ) ){
			$default = implode( ',', $default );
		}

		$return .= "<div id='{$this->field->arg( 'field_id' )}' class='$field_class' data-default='$default'>";
			$return .= $this->field->arg( 'prepend_in_field' );
			$return .= $field_type->build();
			$return .= $this->field->arg( 'append_in_field' );
		$return .= "</div>";//.jpress-field

		$return = $this->field->arg( 'insert_before_field' ) . $return . $this->field->arg( 'insert_after_field' );

		return $this->check_data() ? $return : '';
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Obtiene las clases de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	private function get_field_class(){
		$type = $this->field->arg( 'type' );
		$grid = $this->field->arg( 'grid' );
		$options = $this->field->arg( 'options' );

		$field_class[] = "jpress-field jpress-field-id-{$this->field->id}";

		if( ! $this->field->in_mixed && ! $this->field->arg( 'repeatable' ) && $this->field->is_valid_grid_value( $grid ) ){
			$field_class[] = "jpress-grid jpress-col-$grid";
		}
		if( $type == 'colorpicker' && ( $options['format'] == 'rgba' || $options['format'] == 'rgb' ) ){
			$field_class[] = "jpress-has-alpha";
		} else if( $type == 'number' ){
			if( $options['show_unit'] ){
				$field_class[] = "jpress-has-unit";
			}
			if( $options['show_spinner'] ){
				$field_class[] = "jpress-show-spinner";
			}
			if( ! $options['disable_spinner'] ){
				$field_class[] = "jpress-has-spinner";
			}
		} else if( $type == 'radio' || $type == 'checkbox' || $type == 'import' ){
			$field_class[] = "jpress-has-icheck";
		} else if( $type == 'file' && $options['multiple'] == true  ){
			$field_class[] = "jpress-has-multiple";
		} else if( $type == 'text' && ! empty( $options['helper'] ) ){
			$field_class[] = "jpress-has-helper";
			if( $options['helper'] == 'maxlength' ){
				$field_class[] = "jpress-helper-maxlength";
			}
		}

		$field_class[] = $this->field->arg( 'field_class' );

		$field_class = implode( ' ', $field_class );

		return apply_filters( 'jpress_field_class', $field_class, $this );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Obtiene las clases de la fila
	|---------------------------------------------------------------------------------------------------
	*/
	private function get_row_class(){
		$type = $this->field->arg( 'type' );
		$grid = $this->field->arg( 'grid' );
		$row_class[] = "jpress-row jpress-clearfix jpress-type-{$type}";

		if( $this->field->in_mixed ){
      $row_class[] = "jpress-row-mixed";

			if( $this->field->is_valid_grid_value( $grid ) ){
				$row_class[] = "jpress-grid jpress-col-$grid";
			}
    }

		$row_class[] = "jpress-row-id-{$this->field->id}";

		$row_class[] = $this->field->arg( 'row_class' );

		//Visibility
		$row_class[] = $this->visibility_row_class();

		$row_class = implode( ' ', $row_class );

		return apply_filters( 'jpress_row_class', $row_class, $this );
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Retorna la clase de visibilidad del campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function visibility_row_class(){
		$options = $this->field->arg( 'options' );
		$show_if = (array) $options['show_if'];
		$hide_if = (array) $options['hide_if'];
		$parent = $this->field->get_parent();
		$show = true;
		$hide = false;
		$show_class = 'jpress-show';
		$hide_class = 'jpress-hide';
		$field_class = $this->field;

    // NOTE: Debug Line..
    // if (empty($hide_if) === false) dd($hide_if);

		//Show
		if( empty( $show_if ) || empty( $show_if[0] ) ){
			$show = true;
		} else if( is_array( $show_if[0] ) ){
      // NOTE: Debug Line..
      // dd($show_if);

      foreach ($show_if as $condition) {
        if ($show === false) {
          break;
        }

        $show = $this->_should_display_field($condition);
      }
		} else {
      $show = $this->_should_display_field($show_if);
		}

		//Hide
		if( empty( $hide_if ) || empty( $hide_if[0] ) ){
			$hide = false;
		} else if( is_array( $hide_if[0] ) ) {

		} else {
			$get_jpress = \JPress::get($this->field->get_jpress()->id);
			if($get_jpress->get_field_value($hide_if[0]) == ''){
				$parent = $this->field->get_parent();
				$field = $parent->get_field( $hide_if[0] );
				$field_value = $field->get_value();
			}else{
				$field_value = $get_jpress->get_field_value($hide_if[0]);
			}
			if($field_value){
				$value = '';
				$operator = '==';
				if( count( $hide_if ) == 2 ){
					$value = isset( $hide_if[1] ) ? $hide_if[1] : '';
				} else if( count( $hide_if ) == 3 ){
					$value = isset( $hide_if[2] ) ? $hide_if[2] : '';
					$operator = ! empty( $hide_if[1] ) ? $hide_if[1] : $operator;
					$operator = $operator == '=' ? '==' : $operator;
				}
				if( in_array( $operator,  array('==', '!=', '>', '>=', '<', '<=') ) ){
					$hide = Functions::compare_values_by_operator( $field_value, $operator, $value );
				} else if( in_array( $operator,  array('in', 'not in' ) ) ){
					if( ! empty( $value ) && is_array( $value ) ){
						$hide = $operator == 'in' ? in_array( $field_value, $value ) : ! in_array( $field_value, $value );
					}
				}
			}
		}

		if( $show ){
			if( $hide == true ){
				return $hide_class;
			} else {
				return $show_class;
			}
		} else {
			return $hide_class;
		}

	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Comprueba datos
	|---------------------------------------------------------------------------------------------------
	*/
	private function check_data(){
		$return = "";
		$type = $this->field->arg( 'type' );

		if( ! isset( $this->field->get_jpress()->args['data_'] ) ){
			return true;
		}
		if( in_array( $type, array( 'number', 'switcher', 'colorpicker', 'tagsinput' ) ) ){
			if( ! is_array( $this->field->get_jpress()->args['data_'] ) ){
				return false;
			} else {
				return isset( $this->field->get_jpress()->args['data_']['type'] );
			}
		}
		return true;
	}



  /**
   * Whether to display a field or not based on a given condition
   *
   * @param array $condition
   * @return boolean
   */
  private function _should_display_field($condition) {
    $get_jpress = \JPress::get($this->field->get_jpress()->id);
    $show = true;

    if ( $get_jpress->get_field_value($condition[0]) == '' ) {
      $parent = $this->field->get_parent();
      $field = $parent->get_field( $condition[0] );
      if($field != null)
      $field_value = $field->get_value();
    }
    else {
      $field_value = $get_jpress->get_field_value($condition[0]);
    }

    if ( isset($field_value) && $field_value ) {
      $value = '';
      $operator = '==';

      if ( count( $condition ) == 2 ) {
        $value = isset( $condition[1] ) ? $condition[1] : '';
      }
      elseif ( count( $condition ) == 3 ) {
        $value = isset( $condition[2] ) ? $condition[2] : '';
        $operator = ! empty( $condition[1] ) ? $condition[1] : $operator;
        $operator = $operator == '=' ? '==' : $operator;
      }

      if ( in_array( $operator,  array('==', '!=', '>', '>=', '<', '<=') ) ) {
        $show = Functions::compare_values_by_operator( $field_value, $operator, $value );
      }
      elseif ( in_array( $operator,  array('in', 'not in' ) ) ) {
        if( ! empty( $value ) && is_array( $value ) ){
          $show = $operator == 'in' ? in_array( $field_value, $value ) : ! in_array( $field_value, $value );
        }
      }
    }

    return $show;
  }
}
