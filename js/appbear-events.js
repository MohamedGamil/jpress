APPBEAR.events = (function (window, document, $) {
  'use strict';

  var appbear_events = {};
  var appbear;

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
      show_ = appbear.compare_values_by_operator(field_value_, operator_, value_);
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

  appbear_events.init = function () {
    let $appbear = $('.appbear');

    appbear_events.on_change_colorpicker($appbear);

    appbear_events.on_change_code_editor($appbear);

    appbear_events.on_change_file($appbear);

    appbear_events.on_change_image_selector($appbear);

    appbear_events.on_change_icon_selector($appbear);

    appbear_events.on_change_number($appbear);

    appbear_events.on_change_oembed($appbear);

    appbear_events.on_change_radio($appbear);

    appbear_events.on_change_checkbox($appbear);

    appbear_events.on_change_switcher($appbear);

    appbear_events.on_change_select($appbear);

    appbear_events.on_change_text($appbear);

    appbear_events.on_change_date($appbear);

    appbear_events.on_change_time($appbear);

    appbear_events.on_change_textarea($appbear);

    appbear_events.on_change_wp_editor($appbear);

    appbear_events.init_conditional_items($appbear);

  };

  appbear_events.on_change_colorpicker = function ($appbear) {
    $appbear.on('change', '.appbear-type-colorpicker .appbear-element', function () {
      let $input = $(this);
      let value = $input.val();
      appbear.update_prev_values($(this), value);

      $(this).trigger('appbear_changed_value', value);
      appbear_events.show_hide_row($(this), value, 'colorpicker');
    });
  };

  appbear_events.on_change_code_editor = function ($appbear) {
    $appbear.find('.appbear-code-editor').each(function (index, el) {
      let editor = ace.edit($(el).attr('id'));
      editor.getSession().on('change', function (e) {
        $(el).trigger('appbear_changed_value', editor.getValue());
        appbear_events.show_hide_row($(el), editor.getValue(), 'code_editor');
      });
    });
  };

  appbear_events.on_change_file = function ($appbear) {
    $appbear.on('change', '.appbear-type-file .appbear-element', function () {
      let $field = $(this).closest('.appbear-field');
      let multiple = $field.hasClass('appbear-has-multiple');
      let value = '';
      value = $(this).val();
      if (!multiple) {
        value = $(this).val();
      } else {
        $field.find('.appbear-element').each(function (index, input) {
          value += $(input).val() + ',';
        });
        value = value.replace(/,\s*$/, "");
        $(this).trigger('appbear_changed_value', value);
      }

      $(this).trigger('appbear_changed_value', value);
      appbear_events.show_hide_row($(this), value, 'file');

      if (appbear.is_image_file(value) && !multiple) {
        let $wrap_preview = $(this).closest('.appbear-field').find('.appbear-wrap-preview').first();
        let preview_size = $wrap_preview.data('preview-size');
        let item_body;
        let obj = {
          url: value,
        };
        let $new_item = $('<li />', { 'class': 'appbear-preview-item appbear-preview-file' });
        $new_item.addClass('appbear-preview-image');
        item_body = '<img src="' + obj.url + '" style="width: ' + preview_size.width + '; height: ' + preview_size.height + '" data-full-img="' + obj.url + '" class="appbear-image appbear-preview-handler">';
        $new_item.html(item_body + '<a class="appbear-btn appbear-btn-iconize appbear-btn-small appbear-btn-red appbear-remove-preview"><i class="appbear-icon appbear-icon-times-circle"></i></a>');
        $wrap_preview.html($new_item);
      }
    });
    $appbear.on('appbear_after_add_files', '.appbear-type-file .appbear-field', function (e, selected_files, media) {
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
      $(this).find('.appbear-element').trigger('appbear_changed_value', [value]);
      appbear_events.show_hide_row($(this), [value], 'file');
    });
  };

  appbear_events.on_change_image_selector = function ($appbear) {
    $appbear.on('imgSelectorChanged', '.appbear-type-image_selector .appbear-element', function () {

      /**
       * Spotlayer: Fix image selector change
       */
      let field_id = $(this).closest('.appbear-type-image_selector').data('field-id');
      let control_img_id = $(this).closest('.appbear-type-group').find('.appbear-group-control').data('image-field-id');
      let img_src = $(this).closest('.appbear-item-image-selector').find('img').attr('src');


      if (control_img_id == field_id) {
        appbear.synchronize_selector_preview_image('.appbear-control-image', img_src, 'add', img_src, control_img_id);
      }


      if ($(this).closest('.appbear-image-selector').data('image-selector').like_checkbox) {
        let value = [];
        $(this).closest('.appbear-radiochecks').find('input[type=checkbox]:checked').each(function (index, el) {
          value.push($(this).val());
        });
        $(this).trigger('appbear_changed_value', [value]);
        appbear_events.show_hide_row($(this), [value], 'image_selector');
      } else {
        $(this).trigger('appbear_changed_value', $(this).val());
        appbear_events.show_hide_row($(this), $(this).val(), 'image_selector');
      }
    });
  };

  appbear_events.on_change_icon_selector = function ($appbear) {
    $appbear.on('change', '.appbear-type-icon_selector .appbear-element', function () {
      $(this).trigger('appbear_changed_value', $(this).val());
      appbear_events.show_hide_row($(this), $(this).val(), 'icon_selector');
    });
  };

  appbear_events.on_change_number = function ($appbear) {
    $appbear.on('change', '.appbear-type-number .appbear-unit-number', function () {
      $(this).closest('.appbear-field').find('.appbear-element').trigger('input');
    });
    $appbear.on('input', '.appbear-type-number .appbear-element', function () {
      $(this).trigger('appbear_changed_value', $(this).val());
      appbear_events.show_hide_row($(this), $(this).val(), 'number');
    });
    $appbear.on('change', '.appbear-type-number .appbear-element', function () {
      let value = $(this).val();
      let validValue = value;
      let arr = ['auto', 'initial', 'inherit'];
      if ($.inArray(value, arr) < 0) {
        validValue = value.toString().replace(/[^0-9.\-]/g, '');
      }
      //Validate values
      if (value != validValue) {
        value = validValue;
        let $field = $(this).closest('.appbear-field');
        appbear.set_field_value($field, value, $field.find('input.appbear-unit-number').val());
      }
      $(this).trigger('appbear_changed_value', value);
      appbear_events.show_hide_row($(this), value, 'number');
    });
  };

  appbear_events.on_change_oembed = function ($appbear) {
    $appbear.on('change', '.appbear-type-oembed .appbear-element', function () {
      $(this).trigger('appbear_changed_value', $(this).val());
      appbear_events.show_hide_row($(this), $(this).val(), 'oembed');
    });
  };

  appbear_events.on_change_radio = function ($appbear) {
    $appbear.on('ifChecked', '.appbear-type-radio .appbear-element', function () {
      $(this).trigger('appbear_changed_value', $(this).val());
      appbear_events.show_hide_row($(this), $(this).val(), 'radio');
    });
  };

  appbear_events.on_change_checkbox = function ($appbear) {
    $appbear.on('ifChanged', '.appbear-type-checkbox .appbear-element', function () {
      let value = [];
      $(this).closest('.appbear-radiochecks').find('input[type=checkbox]:checked').each(function (index, el) {
        value.push($(this).val());
      });
      $(this).trigger('appbear_changed_value', [value]);
      appbear_events.show_hide_row($(this), [value], 'checkbox');
    });
  };

  appbear_events.on_change_switcher = function ($appbear) {
    $appbear.on('statusChange', '.appbear-type-switcher .appbear-element', function () {
      $(this).trigger('appbear_changed_value', $(this).val());
      appbear_events.show_hide_row($(this), $(this).val(), 'switcher');
    });
  };

  appbear_events.on_change_select = function ($appbear) {
    $appbear.on('change', '.appbear-type-select .appbear-element', function (event) {
      let $input = $(this).find('input[type="hidden"]');
      let value = $input.val();
      appbear.update_prev_values($input, value);
      $(this).trigger('appbear_changed_value', value);
      appbear_events.show_hide_row($(this), value, 'select');
    });
  };

  appbear_events.on_change_text = function ($appbear) {
    $appbear.on('input', '.appbear-type-text .appbear-element', function () {
      let $input = $(this);
      let value = $input.val();
      appbear.update_prev_values($input, value);
      $input.trigger('appbear_changed_value', value);
      appbear_events.show_hide_row($input, value, 'text');

      let $helper = $input.next('.appbear-field-helper');
      if ($helper.length && $input.closest('.appbear-helper-maxlength').length && $input.attr('maxlength')) {
        $helper.text($input.val().length + '/' + $input.attr('maxlength'));
      }
    });
  };

  appbear_events.on_change_date = function ($appbear) {
    $appbear.on('change', '.appbear-type-date .appbear-element', function () {
      let $input = $(this);
      let value = $input.val();
      appbear.update_prev_values($input, value);
      $input.trigger('appbear_changed_value', value);
      appbear_events.show_hide_row($input, value, 'date');
    });
  };

  appbear_events.on_change_time = function ($appbear) {
    $appbear.on('change', '.appbear-type-time .appbear-element', function () {
      let $input = $(this);
      let value = $input.val();
      appbear.update_prev_values($input, value);
      $input.trigger('appbear_changed_value', value);
      appbear_events.show_hide_row($input, value, 'time');
    });
  };

  appbear_events.on_change_textarea = function ($appbear) {
    $appbear.on('input', '.appbear-type-textarea .appbear-element', function () {
      $(this).text($(this).val());
      $(this).trigger('appbear_changed_value', $(this).val());
      appbear_events.show_hide_row($(this), $(this).val(), 'textarea');
    });
  };

  appbear_events.on_change_wp_editor = function ($appbear) {
    let $wp_editors = $appbear.find('.appbear-type-wp_editor textarea.wp-editor-area');
    $appbear.on('input', '.appbear-type-wp_editor textarea.wp-editor-area', function () {
      $(this).trigger('appbear_changed_value', $(this).val());
      appbear_events.show_hide_row($(this), $(this).val(), 'wp_editor');
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
            $(el).trigger('appbear_changed_value', wp_editor.getContent());
            appbear_events.show_hide_row($(el), wp_editor.getContent(), 'wp_editor');
          });
        }
      });
    }, 1000);
  };

  appbear_events.show_hide_row = function ($el, field_value, type) {
    let prefix = $el.closest('.appbear').data('prefix');
    let $id = $el.closest('.appbear-row').data('field-id');
    let $row_changed;

    if ($el.parents('.appbear-group-wrap').length > 0) {
      // $row_changed = $el.parents('.appbear-group-item').find(`.condition_${$id}, .condition_items_${$id}`);
      $row_changed = $el.parents('.appbear-group-item').find(`.condition_${$id}`);
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

    // let $group_item = $row_changed.closest('.appbear-group-item');
    // if ($group_item.length) {
    //   $rows = $group_item.find('.appbear-row');
    // } else {
    //   $rows.each(function (index, el) {
    //     if ($(el).data('field-type') == 'mixed') {
    //       $(el).find('.appbear-row').each(function (i, mixed_row) {
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
        console.info({$row})
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
            hide = appbear.compare_values_by_operator(field_value, operator, value);
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
          console.info({ '0': 'ITEMS:', $row, key_, conditions_, showItem, showItems });
        }

        // NOTE: Supports `image_selector` fields only at the moment.
        // $row.find(`.appbear-item-image-selector`).hide();

        for (const key_ in showItems) {
          const
          item_ = showItems[key_],
          $item = $row.find(`.appbear-item-image-selector.item-key-${item_}`);

          // NOTE: Debug line..
          console.info({$item});
          // .show();
        }
      }

      if (check_show) {
        if (check_hide) {
          if (show) {
            if (hide) {
              appbear_events.hide_row($row);
            } else {
              appbear_events.show_row($row);
            }
          } else {
            appbear_events.hide_row($row);
          }
        } else {
          if (show) {
            appbear_events.show_row($row);
          } else {
            appbear_events.hide_row($row);
          }
        }
      }

      if (check_hide) {
        if (hide) {
          appbear_events.hide_row($row);
        } else if (check_show) {
          if (show) {
            appbear_events.show_row($row);
          } else {
            appbear_events.hide_row($row);
          }
        } else {
          appbear_events.show_row($row);
        }
        // if( check_show ){
        // 	if( hide ){
        // 		appbear_events.hide_row($row);
        // 	} else {
        // 		if( show ){
        // 			appbear_events.show_row($row);
        // 		} else {
        // 			appbear_events.hide_row($row);
        // 		}
        // 	}
        // } else {
        // 	if( hide ){
        // 		appbear_events.hide_row($row);
        // 	} else {
        // 		appbear_events.show_row($row);
        // 	}
        // }
      }

      // TODO: Check show items filtering
      if (check_show_items) {
      }
    });
  };

  appbear_events.show_row = function ($row) {
    let data_show_hide = $row.data('show-hide');
    let delay = parseInt(data_show_hide.delay);

    if (data_show_hide.effect == 'slide') {
      $row.slideDown(delay, function () {
        if ($row.hasClass('appbear-row-mixed')) {
          $row.css('display', 'inline-block');
        }
      });
    }
    else if (data_show_hide.effect == 'fade') {
      $row.fadeIn(delay, function () {
        if ($row.hasClass('appbear-row-mixed')) {
          $row.css('display', 'inline-block');
        }
      });
    }
    else {
      $row.show();

      if ($row.hasClass('appbear-row-mixed')) {
        $row.css('display', 'inline-block');
      }
    }
  };
  appbear_events.hide_row = function ($row) {
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
   * @param {*} $appbear
   * @return {void}
   */
  appbear_events.init_conditional_items = ($appbear) => {
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
      console.info({ $items, conditions });

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
        const key_ = $el.attr('class').replace('appbear-item-image-selector item-key-', '');
        const conditions_ = is_empty(conditions[key_]) ? [] : conditions[key_];

        if (conditions_.length > 0) {
          let showItem = true;

          if ( is_empty(conditions_) === false && $.isArray(conditions_) && conditions_.length > 0 ) {
            let targetFieldValue = false;

            if ($.isArray(conditions_[0])) {
              for (const condition_ of conditions_) {
                if (showItem === false) {
                  continue;
                }

                targetFieldValue = _getFieldValue(condition_[0]);
                showItem = _shouldDisplayBlock(targetFieldValue, condition_);
              }
            } else {
              targetFieldValue = _getFieldValue(conditions_[0]);
              showItem = _shouldDisplayBlock(targetFieldValue, conditions_);
            }

            // NOTE: Debug line..
            console.info({ $el, key_, conditions_, showItem, targetFieldValue, hide_ });
          }

          if (showItem === false && typeof hide_ === 'function') {
            hide_(key_);
          }
        }
      });
    };

    $appbear
    .find('.appbear-row.appbear-type-image_selector')
    .each(function () {
      const $row = $(this);
      const $items = $row.find('.appbear-item-image-selector');
      const { data: dataShowHideItems, length: dataLength } = getRowData($row, $items);
      let rowDidInit = $row.data('did-hook-show-items');

      if (rowDidInit === true) {
        return;
      } else {
        $row.data('did-hook-show-items', true);
        rowDidInit = false;
      }

      const filterItems = (keys = []) => {
        let selector = String(keys.join(`, .item-key-`, keys));

        selector = keys.length > 1 && $.isArray(keys) ? selector.substr(3, selector.length) : selector;

        return $items.filter(selector);
      };

      const hide = (keys = []) => {
        keys = typeof keys === 'string' ? [ keys ] : keys;

        let $items_ = filterItems(keys);

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
          selectorsParsed += `${val} .appbear-element, `;
        });

        selectorsParsed = selectorsParsed.substr(0, selectorsParsed.length - 2);

        // NOTE: Debug line..
        console.info({ $row, rowDidInit, dataShowHideItems, dataLength, selectors, selectorsParsed });

        // TODO: Hook to inputs changes here
        $appbear.on('input, statusChange', selectorsParsed, function () {
          checkShowItems($items, dataShowHideItems, hide);

          // const $el = $(this);
          // console.info({ $el });

          // $(this).trigger('appbear_changed_value', $(this).val());
          // appbear_events.show_hide_row($(this), $(this).val(), 'wp_editor');
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
    appbear = window.APPBEAR;
    appbear_events.init();
  });

  return appbear_events;

})(window, document, jQuery);


//Events when you change some value of any field.
/*jQuery(document).ready(function($) {
  $('.appbear-type-colorpicker .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'colorpicker changed:' );
    console.log( value );
  });

  $('.appbear-code-editor').on('appbear_changed_value', function( event, value ){
    console.log( 'code_editor changed:' );
    console.log( value );
  });

  $('.appbear-type-file .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'file changed:' );
    console.log( value );
  });

  $('.appbear-type-image_selector .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'image_selector changed:' );
    console.log( value );
  });

  $('.appbear-type-number .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'number changed:' );
    console.log( value );
  });

  $('.appbear-type-oembed .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'oembed changed:' );
    console.log( value );
  });

  $('.appbear-type-radio .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'radio changed:' );
    console.log( value );
  });

  $('.appbear-type-checkbox .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'checkbox changed:' );
    console.log( value );
  });

  $('.appbear-type-switcher .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'switcher:' );
    console.log( value );
  });

  $('.appbear-type-select .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'select:' );
    console.log( value );
  });

  $('.appbear-type-text .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'Texto:' );
    console.log( value );
  });

  $('.appbear-type-textarea .appbear-element').on('appbear_changed_value', function( event, value ){
    console.log( 'textarea:' );
    console.log( value );
  });

  $('.appbear-type-wp_editor .wp-editor-area').on('appbear_changed_value', function( event, value ){
    console.log( 'wp_editor:' );
    console.log( value );
  });

  $appbear.on('appbear_on_init_wp_editor', function (e, wp_editor, args) {
    //After Init
    console.log('appbear_on_init_wp_editor', wp_editor);
    wp_editor.on('click', function (e) {
      console.log('Editor was clicked');
    });
    //Enable "Right to Left" button
    if (wp_editor.controlManager.buttons.rtl) {//Check if "Right to Left" exists
      wp_editor.controlManager.buttons.rtl.$el.trigger('click');
    }
  });

  $appbear.on('appbear_on_setup_wp_editor', function (e, wp_editor) {
    //Before Init
    console.log('appbear_on_setup_wp_editor', wp_editor);

    //Add your buttons
    wp_editor.settings.toolbar3 = 'fontselect | media, image';
  });

});*/


