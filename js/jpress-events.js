APPBEAR.events = (function (window, document, $) {
  'use strict';

  var jpress_events = {};
  var jpress;

  /**
   * Whether to display a field or not based on a given condition
   *
   * @param {any} field_value_
   * @param {Array} condition_
   * @returns {Boolean}
   */
  function _shouldDisplayBlock(field_value_, condition_) {
    let value_ = '';
    let operator_ = '==';
    let show_ = true;

    if (condition_.length == 2) {
      value_ = condition_[1];
    }
    else if (condition_.length == 3) {
      value_ = condition_[2];
      operator_ = !is_empty(condition_[1]) ? condition_[1] : operator_;
      operator_ = operator_ == '=' ? '==' : operator_;
    }

    if ($.inArray(operator_, ['==', '!=', '>', '>=', '<', '<=']) > -1) {
      show_ = jpress.compare_values_by_operator(field_value_, operator_, value_);
    }
    else if ($.inArray(operator_, ['in', 'not in']) > -1) {
      if (!is_empty(value_) && $.isArray(value_)) {
        show_ = operator_ == 'in' ? $.inArray(field_value_, value_) > -1 : $.inArray(field_value_, value_) == -1;
      }
    }

    // NOTE: Debug line..
    // console.info({ value_, field_value_, operator_, show_, condition_ })

    return show_;
  }

  jpress_events.init = function () {
    let $jpress = $('.jpress');

    jpress_events.on_change_colorpicker($jpress);

    jpress_events.on_change_code_editor($jpress);

    jpress_events.on_change_file($jpress);

    jpress_events.on_change_image_selector($jpress);

    jpress_events.on_change_icon_selector($jpress);

    jpress_events.on_change_number($jpress);

    jpress_events.on_change_oembed($jpress);

    jpress_events.on_change_radio($jpress);

    jpress_events.on_change_checkbox($jpress);

    jpress_events.on_change_switcher($jpress);

    jpress_events.on_change_select($jpress);

    jpress_events.on_change_text($jpress);

    jpress_events.on_change_date($jpress);

    jpress_events.on_change_time($jpress);

    jpress_events.on_change_textarea($jpress);

    jpress_events.on_change_wp_editor($jpress);

    jpress_events.init_conditional_items($jpress);

  };

  jpress_events.on_change_colorpicker = function ($jpress) {
    $jpress.on('change', '.jpress-type-colorpicker .jpress-element', function () {
      let $input = $(this);
      let value = $input.val();
      jpress.update_prev_values($(this), value);

      $(this).trigger('jpress_changed_value', value);
      jpress_events.show_hide_row($(this), value, 'colorpicker');
    });
  };

  jpress_events.on_change_code_editor = function ($jpress) {
    $jpress.find('.jpress-code-editor').each(function (index, el) {
      let editor = ace.edit($(el).attr('id'));
      editor.getSession().on('change', function (e) {
        $(el).trigger('jpress_changed_value', editor.getValue());
        jpress_events.show_hide_row($(el), editor.getValue(), 'code_editor');
      });
    });
  };

  jpress_events.on_change_file = function ($jpress) {
    $jpress.on('change', '.jpress-type-file .jpress-element', function () {
      let $field = $(this).closest('.jpress-field');
      let multiple = $field.hasClass('jpress-has-multiple');
      let value = '';
      value = $(this).val();
      if (!multiple) {
        value = $(this).val();
      } else {
        $field.find('.jpress-element').each(function (index, input) {
          value += $(input).val() + ',';
        });
        value = value.replace(/,\s*$/, "");
        $(this).trigger('jpress_changed_value', value);
      }

      $(this).trigger('jpress_changed_value', value);
      jpress_events.show_hide_row($(this), value, 'file');

      if (jpress.is_image_file(value) && !multiple) {
        let $wrap_preview = $(this).closest('.jpress-field').find('.jpress-wrap-preview').first();
        let preview_size = $wrap_preview.data('preview-size');
        let item_body;
        let obj = {
          url: value,
        };
        let $new_item = $('<li />', { 'class': 'jpress-preview-item jpress-preview-file' });
        $new_item.addClass('jpress-preview-image');
        item_body = '<img src="' + obj.url + '" style="width: ' + preview_size.width + '; height: ' + preview_size.height + '" data-full-img="' + obj.url + '" class="jpress-image jpress-preview-handler">';
        $new_item.html(item_body + '<a class="jpress-btn jpress-btn-iconize jpress-btn-small jpress-btn-red jpress-remove-preview"><i class="jpress-icon jpress-icon-times-circle"></i></a>');
        $wrap_preview.html($new_item);
      }
    });
    $jpress.on('jpress_after_add_files', '.jpress-type-file .jpress-field', function (e, selected_files, media) {
      let value;
      if (!media.multiple) {
        $(selected_files).each(function (index, obj) {
          value = obj.url;
        });
      } else {
        value = [];
        $(selected_files).each(function (index, obj) {
          value.push(obj.url);
        });
      }
      $(this).find('.jpress-element').trigger('jpress_changed_value', [value]);
      jpress_events.show_hide_row($(this), [value], 'file');
    });
  };

  jpress_events.on_change_image_selector = function ($jpress) {
    $jpress.on('imgSelectorChanged', '.jpress-type-image_selector .jpress-element', function () {

      /**
       * Spotlayer: Fix image selector change
       */
      let field_id = $(this).closest('.jpress-type-image_selector').data('field-id');
      let control_img_id = $(this).closest('.jpress-type-group').find('.jpress-group-control').data('image-field-id');
      let img_src = $(this).closest('.jpress-item-image-selector').find('img').attr('src');


      if (control_img_id == field_id) {
        jpress.synchronize_selector_preview_image('.jpress-control-image', img_src, 'add', img_src, control_img_id);
      }


      if ($(this).closest('.jpress-image-selector').data('image-selector').like_checkbox) {
        let value = [];
        $(this).closest('.jpress-radiochecks').find('input[type=checkbox]:checked').each(function (index, el) {
          value.push($(this).val());
        });
        $(this).trigger('jpress_changed_value', [value]);
        jpress_events.show_hide_row($(this), [value], 'image_selector');
      } else {
        $(this).trigger('jpress_changed_value', $(this).val());
        jpress_events.show_hide_row($(this), $(this).val(), 'image_selector');
      }
    });
  };

  jpress_events.on_change_icon_selector = function ($jpress) {
    $jpress.on('change', '.jpress-type-icon_selector .jpress-element', function () {
      $(this).trigger('jpress_changed_value', $(this).val());
      jpress_events.show_hide_row($(this), $(this).val(), 'icon_selector');
    });
  };

  jpress_events.on_change_number = function ($jpress) {
    $jpress.on('change', '.jpress-type-number .jpress-unit-number', function () {
      $(this).closest('.jpress-field').find('.jpress-element').trigger('input');
    });
    $jpress.on('input', '.jpress-type-number .jpress-element', function () {
      $(this).trigger('jpress_changed_value', $(this).val());
      jpress_events.show_hide_row($(this), $(this).val(), 'number');
    });
    $jpress.on('change', '.jpress-type-number .jpress-element', function () {
      let value = $(this).val();
      let validValue = value;
      let arr = ['auto', 'initial', 'inherit'];
      if ($.inArray(value, arr) < 0) {
        validValue = value.toString().replace(/[^0-9.\-]/g, '');
      }
      //Validate values
      if (value != validValue) {
        value = validValue;
        let $field = $(this).closest('.jpress-field');
        jpress.set_field_value($field, value, $field.find('input.jpress-unit-number').val());
      }
      $(this).trigger('jpress_changed_value', value);
      jpress_events.show_hide_row($(this), value, 'number');
    });
  };

  jpress_events.on_change_oembed = function ($jpress) {
    $jpress.on('change', '.jpress-type-oembed .jpress-element', function () {
      $(this).trigger('jpress_changed_value', $(this).val());
      jpress_events.show_hide_row($(this), $(this).val(), 'oembed');
    });
  };

  jpress_events.on_change_radio = function ($jpress) {
    $jpress.on('ifChecked', '.jpress-type-radio .jpress-element', function () {
      $(this).trigger('jpress_changed_value', $(this).val());
      jpress_events.show_hide_row($(this), $(this).val(), 'radio');
    });
  };

  jpress_events.on_change_checkbox = function ($jpress) {
    $jpress.on('ifChanged', '.jpress-type-checkbox .jpress-element', function () {
      let value = [];
      $(this).closest('.jpress-radiochecks').find('input[type=checkbox]:checked').each(function (index, el) {
        value.push($(this).val());
      });
      $(this).trigger('jpress_changed_value', [value]);
      jpress_events.show_hide_row($(this), [value], 'checkbox');
    });
  };

  jpress_events.on_change_switcher = function ($jpress) {
    $jpress.on('statusChange', '.jpress-type-switcher .jpress-element', function () {
      $(this).trigger('jpress_changed_value', $(this).val());
      jpress_events.show_hide_row($(this), $(this).val(), 'switcher');
    });
  };

  jpress_events.on_change_select = function ($jpress) {
    $jpress.on('change', '.jpress-type-select .jpress-element', function (event) {
      let $input = $(this).find('input[type="hidden"]');
      let value = $input.val();
      jpress.update_prev_values($input, value);
      $(this).trigger('jpress_changed_value', value);
      jpress_events.show_hide_row($(this), value, 'select');
    });
  };

  jpress_events.on_change_text = function ($jpress) {
    $jpress.on('input', '.jpress-type-text .jpress-element', function () {
      let $input = $(this);
      let value = $input.val();
      jpress.update_prev_values($input, value);
      $input.trigger('jpress_changed_value', value);
      jpress_events.show_hide_row($input, value, 'text');

      let $helper = $input.next('.jpress-field-helper');
      if ($helper.length && $input.closest('.jpress-helper-maxlength').length && $input.attr('maxlength')) {
        $helper.text($input.val().length + '/' + $input.attr('maxlength'));
      }
    });
  };

  jpress_events.on_change_date = function ($jpress) {
    $jpress.on('change', '.jpress-type-date .jpress-element', function () {
      let $input = $(this);
      let value = $input.val();
      jpress.update_prev_values($input, value);
      $input.trigger('jpress_changed_value', value);
      jpress_events.show_hide_row($input, value, 'date');
    });
  };

  jpress_events.on_change_time = function ($jpress) {
    $jpress.on('change', '.jpress-type-time .jpress-element', function () {
      let $input = $(this);
      let value = $input.val();
      jpress.update_prev_values($input, value);
      $input.trigger('jpress_changed_value', value);
      jpress_events.show_hide_row($input, value, 'time');
    });
  };

  jpress_events.on_change_textarea = function ($jpress) {
    $jpress.on('input', '.jpress-type-textarea .jpress-element', function () {
      $(this).text($(this).val());
      $(this).trigger('jpress_changed_value', $(this).val());
      jpress_events.show_hide_row($(this), $(this).val(), 'textarea');
    });
  };

  jpress_events.on_change_wp_editor = function ($jpress) {
    let $wp_editors = $jpress.find('.jpress-type-wp_editor textarea.wp-editor-area');
    $jpress.on('input', '.jpress-type-wp_editor textarea.wp-editor-area', function () {
      $(this).trigger('jpress_changed_value', $(this).val());
      jpress_events.show_hide_row($(this), $(this).val(), 'wp_editor');
    });
    if (typeof tinymce === 'undefined') {
      return;
    }
    setTimeout(function () {
      $wp_editors.each(function (index, el) {
        let ed_id = $(el).attr('id');
        let wp_editor = tinymce.get(ed_id);
        if (wp_editor) {
          wp_editor.on('change input', function (e) {
            let value = wp_editor.getContent();
            $(el).trigger('jpress_changed_value', wp_editor.getContent());
            jpress_events.show_hide_row($(el), wp_editor.getContent(), 'wp_editor');
          });
        }
      });
    }, 1000);
  };

  jpress_events.show_hide_row = function ($el, field_value, type) {
    let prefix = $el.closest('.jpress').data('prefix');
    let $id = $el.closest('.jpress-row').data('field-id');
    let $row_changed;

    if ($el.parents('.jpress-group-wrap').length > 0) {
      // $row_changed = $el.parents('.jpress-group-item').find(`.condition_${$id}, .condition_items_${$id}`);
      $row_changed = $el.parents('.jpress-group-item').find(`.condition_${$id}`);
    }
    else {
      // $row_changed = $(`.condition_${$id}, .condition_items_${$id}`);
      $row_changed = $(`.condition_${$id}`);
    }

    // NOTE: Debug line..
    // console.info({$row_changed});

    let value = '';
    let operator = '==';
    let $rows = $row_changed;

    // let $group_item = $row_changed.closest('.jpress-group-item');
    // if ($group_item.length) {
    //   $rows = $group_item.find('.jpress-row');
    // } else {
    //   $rows.each(function (index, el) {
    //     if ($(el).data('field-type') == 'mixed') {
    //       $(el).find('.jpress-row').each(function (i, mixed_row) {
    //         $rows.push($(mixed_row)[0]);
    //       });
    //     }
    //   });
    // }

    $rows.each(function (index, el) {
      let $row = $(el);
      let data_show_hide = $row.data('show-hide');
      let data_show_hide_items = $row.data('show-hide-items');

      if (typeof data_show_hide === 'undefined' || data_show_hide == null) {
        return;
      }

      let show = true;
      let show_if = data_show_hide.show_if;
      let hide_if = data_show_hide.hide_if;
      let show_items_if = data_show_hide_items.show_items_if;
      let hide = false;
      let check_show = true;
      let check_hide = true;

      // NOTE: DEPRECATED: Forced to be `false` at all time to prevent a relevant code block below from running, temporarily deprecated!
      let check_show_items = false;

      if (is_empty(show_if) || is_empty(show_if[0])) {
        check_show = false;
      }

      if (is_empty(hide_if) || is_empty(hide_if[0])) {
        check_hide = false;
      }

      if (is_empty(show_items_if)) {
        check_show_items = false;
      }

      //Si el campo donde se originó el cambio no afecta al campo actual, no hacer nada
      // if ($row.is($row_changed) || $row_changed.data('field-id') != prefix + show_if[0]) {
      //   return true;
      // }

      // BUG: The current way of showing/hiding rows is buggy and results in an wanted behavior while using multiple conditions.
      if (check_show) {
        if ($.isArray(show_if[0])) {
          for (const condition_ of show_if) {
            if (show === false) {
              break;
            }

            let
              targetFieldName = $(`[data-field-id="${condition_[0]}"] input`).attr('name'),
              $targetField = $(`[name="${targetFieldName}"]`),
              targetFieldValue = $targetField.val();

            // NOTE: This supports fields of type radio, other types of inputs may require special way to handle value fetching.
            switch($targetField.attr('type')) {
              case 'radio':
                targetFieldValue = $targetField.filter(':checked').val();
                break;
            }

            // NOTE: Debug Line
            // console.info({targetFieldName, targetFieldValue});

            show = _shouldDisplayBlock(targetFieldValue, condition_);
          }

          // NOTE: Debug Line
          // console.info({ n: 'multi', field_value, $row, show_if, show });
        } else {
          show = _shouldDisplayBlock(field_value, show_if);

          // NOTE: Debug Line
          // console.info({ n: 'single', field_value, $row, show_if, show });
        }
      }

      if (check_hide) {
        if ($.isArray(hide_if[0])) {

        } else {
          if (hide_if.length == 2) {
            value = hide_if[1];
          } else if (hide_if.length == 3) {
            value = hide_if[2];
            operator = !is_empty(hide_if[1]) ? hide_if[1] : operator;
            operator = operator == '=' ? '==' : operator;
          }
          if ($.inArray(operator, ['==', '!=', '>', '>=', '<', '<=']) > -1) {
            hide = jpress.compare_values_by_operator(field_value, operator, value);
          } else if ($.inArray(operator, ['in', 'not in']) > -1) {
            if (!is_empty(value) && $.isArray(value)) {
              hide = operator == 'in' ? $.inArray(field_value, value) > -1 : $.inArray(field_value, value) == -1;
            }
          }
        }
      }

      // NOTE: Debug line..
      // console.info({ check_show_items, show_items_if, });

      if (check_show_items) {
        let showItems = {};

        for (const key_ in show_items_if) {
          const conditions_ = show_items_if[key_];
          let showItem = true;

          if ( is_empty(conditions_) === false && ($.isArray(conditions_) && conditions_.length > 0) ) {
            if ($.isArray(conditions_[0])) {
              for (const condition_ of conditions_) {
                if (showItem === false) {
                  continue;
                }

                let
                  targetFieldName = $(`[data-field-id="${condition_[0]}"] input`).attr('name'),
                  $targetField = $(`[name="${targetFieldName}"]`),
                  targetFieldValue = $targetField.val();

                // NOTE: This supports fields of type radio, other types of inputs may require special way to handle value fetching.
                switch($targetField.attr('type')) {
                  case 'radio':
                    targetFieldValue = $targetField.filter(':checked').val();
                    break;
                }

                showItem = _shouldDisplayBlock(targetFieldValue, condition_);
              }
            } else {
              showItem = _shouldDisplayBlock(field_value, conditions_);
            }
          }

          showItems[key_] = showItem;

          // NOTE: Debug line..
          // console.info({ '0': 'ITEMS:', $row, key_, conditions_, showItem, showItems });
        }

        // NOTE: Supports `image_selector` fields only at the moment.
        // $row.find(`.jpress-item-image-selector`).hide();

        for (const key_ in showItems) {
          const
          item_ = showItems[key_],
          $item = $row.find(`.jpress-item-image-selector.item-key-${item_}`);

          // NOTE: Debug line..
          // console.info({$item});
          // .show();
        }
      }

      if (check_show) {
        if (check_hide) {
          if (show) {
            if (hide) {
              jpress_events.hide_row($row);
            } else {
              jpress_events.show_row($row);
            }
          } else {
            jpress_events.hide_row($row);
          }
        } else {
          if (show) {
            jpress_events.show_row($row);
          } else {
            jpress_events.hide_row($row);
          }
        }
      }

      if (check_hide) {
        if (hide) {
          jpress_events.hide_row($row);
        } else if (check_show) {
          if (show) {
            jpress_events.show_row($row);
          } else {
            jpress_events.hide_row($row);
          }
        } else {
          jpress_events.show_row($row);
        }
        // if( check_show ){
        // 	if( hide ){
        // 		jpress_events.hide_row($row);
        // 	} else {
        // 		if( show ){
        // 			jpress_events.show_row($row);
        // 		} else {
        // 			jpress_events.hide_row($row);
        // 		}
        // 	}
        // } else {
        // 	if( hide ){
        // 		jpress_events.hide_row($row);
        // 	} else {
        // 		jpress_events.show_row($row);
        // 	}
        // }
      }

      // TODO: Check show items filtering
      if (check_show_items) {
      }
    });
  };

  jpress_events.show_row = function ($row) {
    let data_show_hide = $row.data('show-hide');
    let delay = parseInt(data_show_hide.delay);

    if (data_show_hide.effect == 'slide') {
      $row.slideDown(delay, function () {
        if ($row.hasClass('jpress-row-mixed')) {
          $row.css('display', 'inline-block');
        }
      });
    }
    else if (data_show_hide.effect == 'fade') {
      $row.fadeIn(delay, function () {
        if ($row.hasClass('jpress-row-mixed')) {
          $row.css('display', 'inline-block');
        }
      });
    }
    else {
      $row.show();

      if ($row.hasClass('jpress-row-mixed')) {
        $row.css('display', 'inline-block');
      }
    }
  };
  jpress_events.hide_row = function ($row) {
    let data_show_hide = $row.data('show-hide');
    let delay = parseInt(data_show_hide.delay);
    if (data_show_hide.effect == 'slide') {
      $row.slideUp(delay, function () {
      });
    } else if (data_show_hide.effect == 'fade') {
      $row.fadeOut(delay, function () {
      });
    } else {
      $row.hide();
    }
  };

  /**
   * Init Conditional Items Extension
   *
   * @param {*} $jpress
   * @return {void}
   */
  jpress_events.init_conditional_items = ($jpress) => {
    const getRowData = ($row, $items) => {
      const data_ = $row.data('show-hide-items') || { show_items_if: {} };

      return {
        data: data_.show_items_if,
        length: Object.keys(data_.show_items_if).length,
      };
    };

    const checkShowItems = ($items, conditions, hide_) => {
      if (conditions.length === 0) {
        return;
      }

      // NOTE: Debug line..
      // console.info({ $items, conditions });

      const _getFieldValue = (fieldName) => {
        let
        targetFieldName = $(`[data-field-id="${fieldName}"] input`).attr('name'),
        $targetField = $(`[name="${targetFieldName}"]`),
        targetFieldValue = $targetField.val();

        // NOTE: This supports fields of type radio, other types of inputs may require special way to handle value fetching.
        switch($targetField.attr('type')) {
          case 'radio':
            targetFieldValue = $targetField.filter(':checked').val();
            break;
        }

        return targetFieldValue;
      };

      $items.each(function () {
        const $el = $(this);
        const key_ = $el.attr('class').replace('jpress-item-image-selector item-key-', '');
        const conditions_ = is_empty(conditions[key_]) ? [] : conditions[key_];

        if (conditions_.length > 0) {
          let showItem = true;
          let targetFieldValue = false;

          if ( is_empty(conditions_) === false && $.isArray(conditions_) && conditions_.length > 0 ) {
            if ($.isArray(conditions_[0])) {
              for (const condition_ of conditions_) {
                if (showItem === false) {
                  break;
                }

                targetFieldValue = _getFieldValue(condition_[0]);
                showItem = _shouldDisplayBlock(targetFieldValue, condition_);
              }
            } else {
              targetFieldValue = _getFieldValue(conditions_[0]);
              showItem = _shouldDisplayBlock(targetFieldValue, conditions_);
            }
          }

          // NOTE: Debug line..
          console.info({ $el, key_, conditions_, showItem, targetFieldValue, hide_ });

          if (showItem === false && typeof hide_ === 'function') {
            console.warn({ hidden: hide_(key_) });
          }
        }
      });
    };

    $jpress
    .find('.jpress-row.jpress-type-image_selector')
    .each(function () {
      const $row = $(this);
      const $items = $row.find('.jpress-item-image-selector');
      const { data: dataShowHideItems, length: dataLength } = getRowData($row, $items);
      let rowDidInit = $row.data('did-hook-show-items');

      if (rowDidInit === true) {
        return;
      } else {
        $row.data('did-hook-show-items', true);
        rowDidInit = false;
      }

      const filterItems = (keys = []) => {
        return $items.filter(function() {
          const keys_ = keys.map((value_) => String(value_).trim());
          console.info({ keys_, that: $(this) });

          for (const key_ of keys_) {
            if ($(this).attr('class').indexOf(`item-key-${key_}`) > -1) {
              return true;
            }
          }

          return false;
        });
      };

      const hide = (keys = []) => {
        keys = typeof keys === 'string' ? [ keys ] : keys;

        const $items_ = filterItems(keys);

        // NOTE: Debug line..
        console.info({ keys, $items_ });

        return $items_.hide();
      };

      const showAll = () => {
        return $items.show();
      };

      showAll();
      checkShowItems($items, dataShowHideItems, hide);

      if ( dataLength > 0 ) {
        const selectors = [];

        for (const key_ in dataShowHideItems) {
          const condition_ = dataShowHideItems[key_];
          const fieldKey = typeof condition_[0] === 'string' ? condition_[0] : false;
          const selector_ = `[data-field-id="${fieldKey}"]`;

          if (is_empty(fieldKey)) {
            continue;
          }

          selectors.push(selector_);
        }

        let selectorsParsed = '';

        selectors.map((val, idx) => {
          selectorsParsed += `${val} .jpress-element, `;
        });

        selectorsParsed = selectorsParsed.substr(0, selectorsParsed.length - 2);

        // NOTE: Debug line..
        // console.info({ $row, rowDidInit, dataShowHideItems, dataLength, selectors, selectorsParsed });

        // TODO: Hook to inputs changes here
        $jpress.on('input, statusChange', selectorsParsed, function () {
          showAll();
          checkShowItems($items, dataShowHideItems, hide);

          // const $el = $(this);
          // console.info({ $el });

          // $(this).trigger('jpress_changed_value', $(this).val());
          // jpress_events.show_hide_row($(this), $(this).val(), 'wp_editor');
        });
      }
    });
  };

  function is_empty(value) {
    return (value === undefined || value === false || $.trim(value).length === 0);
  }

  //Debug
  function c(msg) {
    console.log(msg);
  }

  function cc(msg, msg2) {
    console.log(msg, msg2);
  }

  //Document Ready
  $(function () {
    jpress = window.APPBEAR;
    jpress_events.init();
  });

  return jpress_events;

})(window, document, jQuery);


//Events when you change some value of any field.
/*jQuery(document).ready(function($) {
  $('.jpress-type-colorpicker .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'colorpicker changed:' );
    console.log( value );
  });

  $('.jpress-code-editor').on('jpress_changed_value', function( event, value ){
    console.log( 'code_editor changed:' );
    console.log( value );
  });

  $('.jpress-type-file .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'file changed:' );
    console.log( value );
  });

  $('.jpress-type-image_selector .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'image_selector changed:' );
    console.log( value );
  });

  $('.jpress-type-number .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'number changed:' );
    console.log( value );
  });

  $('.jpress-type-oembed .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'oembed changed:' );
    console.log( value );
  });

  $('.jpress-type-radio .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'radio changed:' );
    console.log( value );
  });

  $('.jpress-type-checkbox .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'checkbox changed:' );
    console.log( value );
  });

  $('.jpress-type-switcher .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'switcher:' );
    console.log( value );
  });

  $('.jpress-type-select .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'select:' );
    console.log( value );
  });

  $('.jpress-type-text .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'Texto:' );
    console.log( value );
  });

  $('.jpress-type-textarea .jpress-element').on('jpress_changed_value', function( event, value ){
    console.log( 'textarea:' );
    console.log( value );
  });

  $('.jpress-type-wp_editor .wp-editor-area').on('jpress_changed_value', function( event, value ){
    console.log( 'wp_editor:' );
    console.log( value );
  });

  $jpress.on('jpress_on_init_wp_editor', function (e, wp_editor, args) {
    //After Init
    console.log('jpress_on_init_wp_editor', wp_editor);
    wp_editor.on('click', function (e) {
      console.log('Editor was clicked');
    });
    //Enable "Right to Left" button
    if (wp_editor.controlManager.buttons.rtl) {//Check if "Right to Left" exists
      wp_editor.controlManager.buttons.rtl.$el.trigger('click');
    }
  });

  $jpress.on('jpress_on_setup_wp_editor', function (e, wp_editor) {
    //Before Init
    console.log('jpress_on_setup_wp_editor', wp_editor);

    //Add your buttons
    wp_editor.settings.toolbar3 = 'fontselect | media, image';
  });

});*/


