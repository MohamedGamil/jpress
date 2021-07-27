/**
 * JPress Options JS
 */

window.APPBEAR = (function (window, document, $) {
  'use strict';

  var jpress = {
    duplicate: false,
    media: {
      frames: {}
    },
    delays: {
      removeItem: {
        fade: 200,
        confirm: 100,
        events: 400,//tiene que ser mayor a fade
      }
    }
  };

  jpress.init = function () {
    jpress.$jpress = $('.jpress');
    var $form = jpress.$jpress.closest('.jpress-form');
    if (!$form.length) {
      $form = jpress.$jpress.closest('form#post');
    }

    //Disable form submit on enter
    $form.on('keyup keypress', 'input', function (e) {
      var keyCode = e.which;
      if (keyCode === 13) {
        e.preventDefault();
        return false;
      }
    });

    $(window).resize(function () {
      if (viewport().width <= 850) {
        $('#post-body').addClass('jpress-columns-1');
      } else {
        $('#post-body').removeClass('jpress-columns-1');
      }
    }).resize();


    jpress.init_image_selector();
    jpress.init_tab();
    jpress.init_switcher();
    jpress.init_spinner();
    jpress.init_checkbox();
    jpress.init_dropdown();
    jpress.init_colorpicker();
    jpress.init_code_editor();
    jpress.init_sortable_preview_items();
    jpress.init_sortable_checkbox();
    jpress.init_sortable_repeatable_items();
    jpress.init_sortable_group_items();
    jpress.init_tooltip();

    jpress.load_oembeds();
    setTimeout(function () {
      jpress.load_icons_for_icon_selector();
    }, 200);

    jpress.$jpress.on('click', '#jpress-reset', jpress.on_click_reset_values);
    jpress.$jpress.on('click', '#jpress-import', jpress.on_click_import_values);
    jpress.$jpress.on('ifClicked', '.jpress-type-import .jpress-radiochecks input', jpress.toggle_import);
    jpress.$jpress.on('click', '.jpress-type-import .jpress-radiochecks input, .jpress-wrap-import-inputs .item-key-from_url input, .jpress-wrap-import-inputs .item-key-from_file input', jpress.toggle_import);

    jpress.$jpress.on('click', '.jpress-add-group-item', jpress.new_group_item);
    jpress.$jpress.on('click', '.jpress-duplicate-group-item', jpress.new_group_item);
    jpress.$jpress.on('click', '.jpress-remove-group-item', jpress.remove_group_item);
    jpress.$jpress.on('click', '.jpress-group-control-item', jpress.on_click_group_control_item);
    jpress.$jpress.on('sort_group_items', '.jpress-group-wrap', jpress.sort_group_items);
    jpress.$jpress.on('sort_group_control_items', '.jpress-group-control', jpress.sort_group_control_items);

    jpress.$jpress.on('click', '.jpress-add-repeatable-item', jpress.add_repeatable_item);
    jpress.$jpress.on('click', '.jpress-remove-repeatable-item', jpress.remove_repeatable_item);
    jpress.$jpress.on('sort_repeatable_items', '.jpress-repeatable-wrap', jpress.sort_repeatable_items);

    jpress.$jpress.on('click', '.jpress-upload-file, .jpress-preview-item .jpress-preview-handler', jpress.wp_media_upload);
    jpress.$jpress.on('click', '.jpress-remove-preview', jpress.remove_preview_item);
    jpress.$jpress.on('click', '.jpress-get-oembed', jpress.get_oembed);
    jpress.$jpress.on('click', '.jpress-get-image', jpress.get_image_from_url);
    jpress.$jpress.on('focusout', '.jpress-type-colorpicker input', jpress.on_focusout_input_colorpicker);
    jpress.$jpress.on('click', '.jpress-type-colorpicker .jpress-colorpicker-default-btn', jpress.set_default_value_colorpicker);
    jpress.$jpress.on('click', '.jpress-section.jpress-toggle-1 .jpress-section-header, .jpress-section .jpress-toggle-icon', jpress.toggle_section);
    jpress.$jpress.on('click', '.jpress-type-number .jpress-unit-has-picker-1', jpress.toggle_units_dropdown);
    jpress.$jpress.on('click', '.jpress-units-dropdown .jpress-unit-item', jpress.set_unit_number);
    jpress.$jpress.on('focus', '.jpress-type-text input.jpress-element', jpress.on_focus_input_type_text);

    jpress.refresh_active_main_tab();
    jpress.$jpress.on('click', '.jpress-main-tab .jpress-item-parent a', jpress.on_cick_item_main_tab);

    $(document).on('click', jpress.hide_units_dropdown);

    jpress.$jpress.on('focus', 'input.jpress-element', function (event) {
      $(this).closest('.jpress-field').removeClass('jpress-error');
    });

    jpress.sticky_submit_buttons();
    $(window).scroll(function () {
      jpress.sticky_submit_buttons();
    });
  };

  jpress.on_cick_item_main_tab = function(e){
    var activeItem = $(this).attr('href').replace(/#/, '');
    var prefix = jpress.$jpress.data('prefix');
    localStorage.setItem('jpress-main-tab-item-active', activeItem.replace(prefix, '').replace('tab_item', 'tab-item'));
  };
  jpress.refresh_active_main_tab = function(){
    var activeItem = localStorage.getItem('jpress-main-tab-item-active');
    if( activeItem ){
      jpress.$jpress.find('.jpress-main-tab .jpress-item-parent.'+activeItem+' a').trigger('click');
    }
  };

  jpress.sticky_submit_buttons = function () {
    var $header = $('.jpress-header').first();
    var $actions = $header.find('.jpress-header-actions').first();
    var $my_account = $('#wp-admin-bar-my-account');
    if (!$actions.length || !$my_account.length || !$actions.data('sticky')) {
      return;
    }
    if ($(window).scrollTop() > $header.offset().top) {
      $my_account.css('padding-right', $actions.width() + 25);
      $actions.addClass('jpress-actions-sticky');
    } else {
      $my_account.css('padding-right', '');
      $actions.removeClass('jpress-actions-sticky');
    }
  };

  jpress.on_focus_input_type_text = function (event) {
    var $helper = $(this).next('.jpress-field-helper');
    if ($helper.length) {
      $(this).css('padding-right', ($helper.outerWidth() + 6) + 'px');
    }
  };

  jpress.hide_units_dropdown = function () {
    $('.jpress-units-dropdown').slideUp(200);
  };
  jpress.toggle_units_dropdown = function (event) {
    if ($(event.target).hasClass('jpress-spinner-handler') || $(event.target).hasClass('jpress-spinner-control')) {
      return;
    }
    event.stopPropagation();
    $(this).find('.jpress-units-dropdown').slideToggle(200);
  };
  jpress.set_unit_number = function (event) {
    var $btn = $(this);
    $btn.closest('.jpress-unit').find('input.jpress-unit-number').val($btn.data('value')).trigger('change');
    $btn.closest('.jpress-unit').find('span').text($btn.text());
  };

  jpress.load_icons_for_icon_selector = function (event) {
    var fields = [];

    $('.jpress-type-icon_selector').each(function (index, el) {
      var field_id = $(el).data('field-id');
      var options = $(el).find('.jpress-icons-wrap').data('options');
      if ($.inArray(field_id, fields) < 0 && options.load_with_ajax) {
        fields.push(field_id);
      }
    });

    $.each(fields, function (index, field_id) {
      jpress.load_icon_selector($('.jpress-field-id-' + field_id));
    });

    // $(document).on('click', function (event) {
    //   event.preventDefault();
    //   $('.jpress-icons-wrap').removeClass('d-block');
    //   $('.jpress-search-icon').removeClass('d-block');
    // });

    $(document).mouseup(function(e) {
        var container = $(".jpress-type-icon_selector .jpress-field .jpress-search-icon");

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
          container.removeClass('d-block');
        }

        var container = $(".jpress-type-icon_selector .jpress-field .jpress-icons-wrap");

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.removeClass('d-block');
        }
    });

    // NOTE: Display icons select options
    $(document).on('click', '.jpress-icon-actions', function (event) {
      event.stopPropagation();
      event.preventDefault();

      const
        $el = $(this),
        $parent = $el.parents('.jpress-type-icon_selector:first'),
        $field = $el.closest('.jpress-field'),
        $iconsWrap = $field.find('.jpress-icons-wrap'),
        $searchIcons = $field.find('.jpress-search-icon'),
        opts = $iconsWrap.data('options'),
        optSize = ~~String(opts.size || '36px').replace('px', ''),
        fieldID = $parent.data('field-id'),
        items = JPRESS_JS._field_icons[fieldID] || false,
        itemsKeys = Object.keys(items);

      // NOTE: Debug line
      // console.info({fieldID, $el, $parent, $field, $iconsWrap, $searchIcons, opts, itemsKeys, items, size: optSize - 14});

      if (!!items) {
        if ($iconsWrap.find('.jpress-item-icon-selector').length === 0) {
          $iconsWrap.empty();

          let iconsHTML = '';

          for (const iconKey of itemsKeys) {
            const
              isFontAwesome = iconKey.substr(0, 2) === 'fa',
              iconClass = isFontAwesome ? iconKey : items[iconKey],
              dataValue = isFontAwesome ? String(iconKey).trim().split(' ').pop() : iconKey,
              iconSize = optSize - 14,
              fontSize = `${iconSize}px`,
              dataKey = `font ${iconKey}`,
              dataType = 'icon font',
              $iconEl = `<i class="${iconClass}" style=""></i>`;

            // console.info({ iconKey, dataValue, iconClass, ref: items[iconKey]  });
            iconsHTML += `<div class="jpress-item-icon-selector" data-value='${dataValue}' data-key='${dataKey}' data-search='${iconClass}' data-type='${dataType}' style='width: ${opts.size}; height: ${opts.size}; font-size: ${fontSize}'>`;
            iconsHTML += $iconEl;
            iconsHTML += "</div>";
          }

          $iconsWrap.append(iconsHTML);
        }

        $iconsWrap.add($searchIcons).addClass('d-block');
      }
    });

    // NOTE: Search icons
    $(document).on('input', '.jpress-search-icon', function (event) {
      event.preventDefault();
      var value = $(this).val();
      var $container = $(this).closest('.jpress-field').find('.jpress-icons-wrap');
      jpress.filter_items(value, $container, '.jpress-item-icon-selector');
    });

    // NOTE: Icon select button action
    $(document).on('click', '.jpress-icon-actions .jpress-btn', function (event) {
      var value = $(this).data('search');
      var $container = $(this).closest('.jpress-field').find('.jpress-icons-wrap');
      jpress.filter_items(value, $container, '.jpress-item-icon-selector');
    });

    // NOTE: Select an icon
    $(document).on('click', '.jpress-icons-wrap .jpress-item-icon-selector', function (event) {
      const
        $field = $(this).closest('.jpress-field'),
        $container = $field.find('.jpress-icons-wrap'),
        options = $container.data('options'),
        $e = $(this),
        dataValue = $e.data('value'),
        elHTML = $e.html();

      $e.addClass(options.active_class).siblings().removeClass(options.active_class);
      $field.find('input.jpress-element').val(dataValue).trigger('change');
      $field.find('.jpress-icon-active').html(elHTML);
      $field.find('.jpress-icons-wrap').css('style', 'display:block  !important');

      $(".jpress-type-icon_selector .jpress-field .jpress-icons-wrap, .jpress-type-icon_selector .jpress-field .jpress-search-icon").removeClass('d-block');
    });
  };

  jpress.filter_items = function (value, $container, selector) {
    $container.find(selector).each(function (index, item) {
      var data = $(item).data('search');
      if (is_empty(data)) {
        $(item).hide();
      } else {
        if (value == 'all' || data.indexOf(value) > -1) {
          $(item).show();
        } else {
          $(item).hide();
        }
      }
    });
  };

  jpress.load_icon_selector = function ($field) {
    var options = $field.find('.jpress-icons-wrap').data('options');
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: JPRESS_JS.ajax_url,
      data: {
        action: 'jpress_get_items',
        class_name: options.ajax_data.class_name,
        function_name: options.ajax_data.function_name,
        ajax_nonce: JPRESS_JS.ajax_nonce
      },
      beforeSend: function () {
        $field.find('.jpress-icons-wrap').prepend("<i class='jpress-icon jpress-icon-spinner jpress-icon-spin jpress-loader'></i>");
      },
      success: function (response) {
        if (response) {
          if (response.success) {
            $.each(response.items, function (value, html) {
              var key = 'font ' + value;
              var type = 'icon font';
              if (key.indexOf('.svg') > -1) {
                key = key.split('/');
                key = key[key.length - 1];
                type = 'svg';
              }
              var $new_item = $('<div />', {
                'class': 'jpress-item-icon-selector',
                'data-value': value,
                'data-key': key,
                'data-type': type
              });
              $new_item.html(html);
              $field.find('.jpress-icons-wrap').append($new_item);
            });
            $field.find('.jpress-icons-wrap .jpress-item-icon-selector').css({
              'width': options.size,
              'height': options.size,
              'font-size': parseInt(options.size) - 14,
            });
            //c($field.first().find('.jpress-icons-wrap .jpress-item-icon-selector').length);//total icons
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
      },
      complete: function (jqXHR, textStatus) {
        $field.find('.jpress-icons-wrap').find('.jpress-loader').remove();
      }
    });

    return '';
  };

  jpress.toggle_section = function (event) {
    event.stopPropagation();
    var $btn = $(this);
    var $section = $btn.closest('.jpress-section.jpress-toggle-1');
    var $section_body = $section.find('.jpress-section-body');
    var data_toggle = $section.data('toggle');
    var $icon = $section.find('.jpress-toggle-icon').first();
    if ($btn.hasClass('jpress-section-header') && data_toggle.target == 'icon') {
      return;
    }
    var object_toggle = {
      duration: parseInt(data_toggle.speed),
      complete: function () {
        if ($section_body.css('display') == 'block') {
          $icon.find('i').removeClass(data_toggle.close_icon).addClass(data_toggle.open_icon);
        } else {
          $icon.find('i').removeClass(data_toggle.open_icon).addClass(data_toggle.close_icon);
        }
      }
    };
    if (data_toggle.effect == 'slide') {
      $section_body.slideToggle(object_toggle);
    } else if (data_toggle.effect == 'fade') {
      $section_body.fadeToggle(object_toggle);
    }
    return false;
  };

  jpress.toggle_import = function (event) {
    var $input = $(this);
    var $wrap_input_file = $('.jpress-wrap-input-file');
    var $wrap_input_url = $('.jpress-wrap-input-url');

    if ($input.next('img').length || ($input.val() != 'from_file' && $input.val() != 'from_url')) {
      $wrap_input_file.hide();
      $wrap_input_url.hide();
    }
    if ($input.val() == 'from_file') {
      $wrap_input_file.show();
      $wrap_input_url.hide();
    }
    if ($input.val() == 'from_url') {
      $wrap_input_url.show();
      $wrap_input_file.hide();
    }
  };

  jpress.on_click_reset_values = function (event) {
    var $btn = $(this);
    var $jpress_form = $btn.closest('.jpress-form');
    $.jpressConfirm({
      title: JPRESS_JS.text.reset_popup.title,
      content: JPRESS_JS.text.reset_popup.content,
      confirm_class: 'jpress-btn-blue',
      confirm_text: JPRESS_JS.text.popup.accept_button,
      cancel_text: JPRESS_JS.text.popup.cancel_button,
      onConfirm: function () {
        $jpress_form.prepend('<input type="hidden" name="' + $btn.attr('name') + '" value="true">');
        $jpress_form.submit();
      },
      onCancel: function () {
        return false;
      }
    });
    return false;
  };

  jpress.on_click_import_values = function (event) {
    var $btn = $(this);
    var gutenbergEditor = !!$('body.block-editor-page').length;
    if( gutenbergEditor ){
      $jpress_form = $('.block-editor__container');//Gutenberg editor
    } else {
      var $jpress_form = $btn.closest('.jpress-form');//Admin pages
      if (!$jpress_form.length) {
        $jpress_form = $btn.closest('form#post');//Default wordpress editor
      }
    }
    var importInput = '<input type="hidden" name="' + $btn.attr('name') + '" value="true">';
    $.jpressConfirm({
      title: JPRESS_JS.text.import_popup.title,
      content: JPRESS_JS.text.import_popup.content,
      confirm_class: 'jpress-btn-blue',
      confirm_text: JPRESS_JS.text.popup.accept_button,
      cancel_text: JPRESS_JS.text.popup.cancel_button,
      onConfirm: function () {
        if( gutenbergEditor ){
          $('form.metabox-location-normal').prepend(importInput);
          var $temp_button = $jpress_form.find('button.editor-post-publish-panel__toggle');
          var delay = 100;
          if( $temp_button.length ){
            $temp_button.trigger('click');
            delay = 900;
          }
          setTimeout(function(){
            var $publish_button = $jpress_form.find('button.editor-post-publish-button');
            if( $publish_button.length ){
              $publish_button.trigger('click');
              setTimeout(function(){
                location.reload();
              }, 6000);
            }
          }, delay);
        } else {
          $jpress_form.prepend(importInput);
          $jpress_form.prepend('<input type="hidden" name="jpress-import2" value="yes">');
          setTimeout(function(){
            if ($jpress_form.find('#publish').length) {
              $jpress_form.find('#publish').click();
            } else {
              $jpress_form.submit();
            }
          }, 800);
        }
      },
      onCancel: function () {
        return false;
      }
    });
    return false;
  };

  jpress.get_image_from_url = function (event) {
    var $btn = $(this);
    var $field = $btn.closest('.jpress-field');
    var $input = $field.find('.jpress-element-text');
    var $wrap_preview = $field.find('.jpress-wrap-preview');
    if (is_empty($input.val())) {
      $.jpressConfirm({
        title: JPRESS_JS.text.validation_url_popup.title,
        content: JPRESS_JS.text.validation_url_popup.content,
        confirm_text: JPRESS_JS.text.popup.accept_button,
        hide_cancel: true
      });
      return false;
    }
    var image_class = $wrap_preview.data('image-class');
    var $new_item = $('<li />', { 'class': 'jpress-preview-item jpress-preview-image' });
    $new_item.html(
      '<img src="' + $input.val() + '" class="' + image_class + '">' +
      '<a class="jpress-btn jpress-btn-iconize jpress-btn-small jpress-btn-red jpress-remove-preview"><i class="jpress-icon jpress-icon-times-circle"></i></a>'
    );
    $wrap_preview.fadeOut(400, function () {
      $(this).html('').show();
    });
    $field.find('.jpress-get-image i').addClass('jpress-icon-spin');
    setTimeout(function () {
      $wrap_preview.html($new_item);
      $field.find('.jpress-get-image i').removeClass('jpress-icon-spin');
    }, 1200);
    return false;
  };

  jpress.load_oembeds = function (event) {
    $('.jpress-type-oembed').each(function (index, el) {
      if ($(el).find('.jpress-wrap-oembed').data('preview-onload')) {
        jpress.get_oembed($(el).find('.jpress-get-oembed'));
      }
    });
  };

  jpress.get_oembed = function (event) {
    var $btn;
    if ($(event.currentTarget).length) {
      $btn = $(event.currentTarget);
    } else {
      $btn = event;
    }
    var $field = $btn.closest('.jpress-field');
    var $input = $field.find('.jpress-element-text');
    var $wrap_preview = $field.find('.jpress-wrap-preview');
    if (is_empty($input.val()) && $(event.currentTarget).length) {
      $.jpressConfirm({
        title: JPRESS_JS.text.validation_url_popup.title,
        content: JPRESS_JS.text.validation_url_popup.content,
        confirm_text: JPRESS_JS.text.popup.accept_button,
        hide_cancel: true
      });
      return false;
    }
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: JPRESS_JS.ajax_url,
      data: {
        action: 'jpress_get_oembed',
        oembed_url: $input.val(),
        preview_size: $wrap_preview.data('preview-size'),
        ajax_nonce: JPRESS_JS.ajax_nonce
      },
      beforeSend: function () {
        $wrap_preview.fadeOut(400, function () {
          $(this).html('').show();
        });
        $field.find('.jpress-get-oembed i').addClass('jpress-icon-spin');
      },
      success: function (response) {
        if (response) {
          if (response.success) {
            var $new_item = $('<li />', { 'class': 'jpress-preview-item jpress-preview-oembed' });
            $new_item.html(
              '<div class="jpress-oembed jpress-oembed-provider-' + response.provider + ' jpress-element-oembed ">' +
              response.oembed +
              '<a class="jpress-btn jpress-btn-iconize jpress-btn-small jpress-btn-red jpress-remove-preview"><i class="jpress-icon jpress-icon-times-circle"></i></a>' +
              '</div>'
            );
            $wrap_preview.html($new_item);
          } else {
            $wrap_preview.html(response.message);
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
      },
      complete: function (jqXHR, textStatus) {
        $field.find('.jpress-get-oembed i').removeClass('jpress-icon-spin');
      }
    });
    return false;
  };

  jpress.wp_media_upload = function (event) {
    if (wp === undefined) {
      return;
    }
    var $btn = $(this);
    var media = jpress.media;
    media.$field = $btn.closest('.jpress-field');
    media.field_id = media.$field.closest('.jpress-row').data('field-id');
    media.frame_id = media.$field.closest('.jpress').attr('id') + '_' + media.field_id;
    media.$upload_btn = media.$field.find('.jpress-upload-file');
    media.$wrap_preview = media.$field.find('.jpress-wrap-preview');
    media.multiple = media.$field.hasClass('jpress-has-multiple');
    media.$preview_item = undefined;
    media.attachment_id = undefined;

    if ($btn.closest('.jpress-preview-item').length) {
      media.$preview_item = $btn.closest('.jpress-preview-item');
    } else if (!media.multiple) {
      media.$preview_item = media.$field.find('.jpress-preview-item').first();
    }
    if (media.$preview_item) {
      media.attachment_id = media.$preview_item.find('.jpress-attachment-id').val();
    }

    if (media.frames[media.frame_id] !== undefined) {
      media.frames[media.frame_id].open();
      return;
    }

    media.frames[media.frame_id] = wp.media({
      title: media.$field.closest('.jpress-type-file').find('.jpress-element-label').first().text(),
      multiple: media.multiple ? 'add' : false,
    });
    media.frames[media.frame_id].on('open', jpress.on_open_wp_media).on('select', jpress.on_select_wp_media);
    media.frames[media.frame_id].open();
  };

  jpress.on_open_wp_media = function (event) {
    var media = jpress.media;
    var selected_files = jpress.media.frames[media.frame_id].state().get('selection');
    if (is_empty(media.attachment_id)) {
      return selected_files.reset();
    }
    var wp_attachment = wp.media.attachment(media.attachment_id);
    wp_attachment.fetch();
    selected_files.set(wp_attachment ? [wp_attachment] : []);
  };

  jpress.on_select_wp_media = function (event) {
    var media = jpress.media;
    var selected_files = media.frames[media.frame_id].state().get('selection').toJSON();
    var preview_size = media.$wrap_preview.data('preview-size');
    var attach_name = media.$wrap_preview.data('field-name');
    var control_img_id = media.$field.closest('.jpress-type-group').find('.jpress-group-control').data('image-field-id');

    media.$field.trigger('jpress_before_add_files', [selected_files, jpress.media]);
    $(selected_files).each(function (index, obj) {
      var image = '';
      var inputs = '';
      var item_body = '';
      var $new_item = $('<li />', { 'class': 'jpress-preview-item jpress-preview-file' });

      if (obj.type == 'image') {
        $new_item.addClass('jpress-preview-image');
        item_body = '<img src="' + obj.url + '" style="width: ' + preview_size.width + '; height: ' + preview_size.height + '" data-full-img="' + obj.url + '" class="jpress-image jpress-preview-handler">';
      } else if (obj.type == 'video') {
        $new_item.addClass('jpress-preview-video');
        item_body = '<div class="jpress-video">';
        item_body += '<video controls style="width: ' + preview_size.width + '; height: ' + preview_size.height + '"><source src="' + obj.url + '" type="' + obj.mime + '"></video>';
        item_body += '</div>';
      } else {
        item_body = '<img src="' + obj.icon + '" class="jpress-preview-icon-file jpress-preview-handler"><a href="' + obj.url + '" class="jpress-preview-download-link">' + obj.filename + '</a><span class="jpress-preview-mime jpress-preview-handler">' + obj.mime + '</span>';
      }

      if (media.multiple) {
        inputs = '<input type="hidden" name="' + media.$upload_btn.data('field-name') + '" value="' + obj.url + '" class="jpress-element jpress-element-hidden">';
      }
      inputs += '<input type="hidden" name="' + attach_name + '" value="' + obj.id + '" class="jpress-attachment-id">';

      $new_item.html(inputs + item_body + '<a class="jpress-btn jpress-btn-iconize jpress-btn-small jpress-btn-red jpress-remove-preview"><i class="jpress-icon jpress-icon-times-circle"></i></a>');

      if (media.multiple) {
        if (media.$preview_item) {
          //Sólo agregamos los nuevos
          if (media.attachment_id != obj.id) {
            media.$preview_item.after($new_item);
          }
        } else {
          media.$wrap_preview.append($new_item);
        }
      } else {
        media.$wrap_preview.html($new_item);
        media.$field.find('.jpress-element').attr('value', obj.url);
        if (obj.type == 'image') {
          //Sincronizar con la imagen de control de un grupo
          if (media.field_id == control_img_id) {
            jpress.synchronize_selector_preview_image('.jpress-control-image', media.$wrap_preview, 'add', obj.url, control_img_id);
          }
          //Sincronizar con otros elementos
          jpress.synchronize_selector_preview_image('', media.$wrap_preview, 'add', obj.url, control_img_id);
        }
      }
    });
    media.$field.trigger('jpress_after_add_files', [selected_files, media]);
  };

  jpress.remove_preview_item = function (event) {
    var $btn = $(this);
    var $field = $btn.closest('.jpress-field');
    var field_id = $field.closest('.jpress-row').data('field-id');
    var control_data_img = $field.closest('.jpress-type-group').find('.jpress-group-control').data('image-field-id');
    var $wrap_preview = $field.find('.jpress-wrap-preview');
    var multiple = $field.hasClass('jpress-has-multiple');

    $field.trigger('jpress_before_remove_preview_item', [multiple]);

    if (!multiple) {
      $field.find('.jpress-element').attr('value', '');
    }
    $btn.closest('.jpress-preview-item').remove();

    if (!multiple && $btn.closest('.jpress-preview-item').hasClass('jpress-preview-image')) {
      if (field_id == control_data_img) {
        jpress.synchronize_selector_preview_image('.jpress-control-image', $wrap_preview, 'remove', '', control_data_img);
      }else{
        jpress.synchronize_selector_preview_image('', $wrap_preview, 'remove', '', control_data_img);
      }
    }
    $field.find('.jpress-element').trigger('change');
    $field.trigger('jpress_after_remove_preview_item', [multiple]);
    return false;
  };

  jpress.synchronize_selector_preview_image = function (selectors, $wrap_preview, action, value, control_img_id = null) {
    selectors = selectors || $wrap_preview.data('synchronize-selector');
    if (!is_empty(selectors)) {
      selectors = selectors.split(',');
      $.each(selectors, function (index, selector) {
        var $element = $(this);
        if ($element.closest('.jpress-type-group').length) {
          if ($element.closest('.jpress-type-group').find('.jpress-group-control').length) {
            $element = $element.closest('.jpress-group-control-item.jpress-active').find(selector);
          } else {
            $element = $element.closest('.jpress-group-item.jpress-active').find(selector);
          }
        }
        if ($element.is('img')) {
          $element.fadeOut(300, function () {
            if ($element.closest('.jpress-group-control').length) {
              $element.attr('src', value);
            } else {
              $element.attr('src', value);
            }
          });
        } else {
          $element.closest('.jpress-type-group').find('.jpress-group-control[data-image-field-id="'+control_img_id+'"] .jpress-group-control-item.jpress-active').find(selector).fadeOut(300, function () {
            if ($element.closest('.jpress-type-group').find('.jpress-group-control').length) {
              if(control_img_id != null){
                $element.closest('.jpress-type-group').find('.jpress-group-control[data-image-field-id="'+control_img_id+'"] .jpress-group-control-item.jpress-active').find(selector).css('background-image', 'url(' + value + ')');
              }else{
                $element.css('background-image', 'url(' + value + ')');
              }
            } else {
              if(control_img_id != null){
                $element.closest('.jpress-type-group').find('.jpress-group-control[data-image-field-id="'+control_img_id+'"] .jpress-group-control-item.jpress-active').find(selector).css('background-image', 'url(' + value + ')');
              }else{
                $element.css('background-image', 'url(' + value + ')');
              }
            }
          });
        }
        if (action == 'add') {
          $element.fadeIn(300);
        }
        var $input = $element.closest('.jpress-field').find('input.jpress-element');
        if ($input.length) {
          $input.attr('value', value);
        }

        var $close_btn = $element.closest('.jpress-preview-item').find('.jpress-remove-preview');
        if ($close_btn.length) {
          if (action == 'add' && $input.is(':visible')) {
            $close_btn.show();
          }
          if (action == 'remove') {
            $close_btn.hide();
          }
        }
      });
    }
  };

  jpress.reinit_js_plugins = function ($new_element) {
    //Inicializar Tabs
    $new_element.find('.jpress-tab').each(function (iterator, item) {
      jpress.init_tab($(item));
    });

    //Inicializar Switcher
    $new_element.find('.jpress-type-switcher input.jpress-element').each(function (iterator, item) {
      $(item).jpressSwitcher('destroy');
      jpress.init_switcher($(item));
    });

    //Inicializar Spinner
    $new_element.find('.jpress-type-number .jpress-field.jpress-has-spinner').each(function (iterator, item) {
      jpress.init_spinner($(item));
    });

    //Inicializar radio buttons y checkboxes
    $new_element.find('.jpress-has-icheck .jpress-radiochecks.init-icheck').each(function (iterator, item) {
      jpress.destroy_icheck($(item));
      jpress.init_checkbox($(item));
    });

    //Inicializar Colorpicker
    $new_element.find('.jpress-colorpicker-color').each(function (iterator, item) {
      jpress.init_colorpicker($(item));
    });

    //Inicializar Dropdown
    $new_element.find('.ui.selection.dropdown').each(function (iterator, item) {
      jpress.init_dropdown($(item));
    });

    //Inicializar Sortables de grupos
    $new_element.find('.jpress-group-control.jpress-sortable').each(function (iterator, item) {
      jpress.init_sortable_group_items($(item));
    });

    //Inicializar Sortable de items repetibles
    $new_element.find('.jpress-repeatable-wrap.jpress-sortable').each(function (iterator, item) {
      jpress.init_sortable_repeatable_items($(item));
    });

    //Inicializar Sortable de preview items
    $new_element.find('.jpress-wrap-preview-multiple').each(function (iterator, item) {
      jpress.init_sortable_preview_items($(item));
    });

    //Inicializar Ace editor
    $new_element.find('.jpress-code-editor').each(function (iterator, item) {
      jpress.destroy_ace_editor($(item));
      jpress.init_code_editor($(item));
    });

    //Inicializar Tooltip
    jpress.init_tooltip($new_element.find('.jpress-tooltip-handler'));
  };


  jpress.destroy_wp_editor = function ($selector) {
    if (typeof tinyMCEPreInit === 'undefined' || typeof tinymce === 'undefined' || typeof QTags == 'undefined') {
      return;
    }

    //Destroy editor
    $selector.find('.quicktags-toolbar, .mce-tinymce.mce-container').remove();
    tinymce.execCommand('mceRemoveEditor', true, $selector.find('.wp-editor-area').attr('id'));

    //Register editor to init
    $selector.addClass('init-wp-editor');
  };

  jpress.on_init_wp_editor = function (wp_editor, args) {
    $('.jpress').trigger('jpress_on_init_wp_editor', wp_editor, args);
  };

  jpress.on_setup_wp_editor = function (wp_editor) {
    $('.jpress').trigger('jpress_on_setup_wp_editor', wp_editor);
    if (typeof tinymce === 'undefined') {
      return;
    }
    var $textarea = $(wp_editor.settings.selector);
    wp_editor.on('change mouseleave input', function (e) {
      if( wp_editor ){
        var value = wp_editor.getContent();
        $textarea.text(value).val(value);
      }
    });
  };

  jpress.init_wp_editor = function ($selector) {
    if (typeof tinyMCEPreInit === 'undefined' || typeof tinymce === 'undefined' || typeof QTags == 'undefined') {
      return;
    }
    $selector.removeClass('init-wp-editor');
    $selector.removeClass('html-active').addClass('tmce-active');
    var $textarea = $selector.find('.wp-editor-area');
    var ed_id = $textarea.attr('id');
    var old_ed_id = $selector.closest('.jpress-group-wrap').find('.jpress-group-item').eq(0).find('.wp-editor-area').first().attr('id');

    $textarea.show();

    var ed_settings = jQuery.extend(tinyMCEPreInit.mceInit[old_ed_id], {
      body_class: ed_id,
      selector: '#' + ed_id,
      skin: "lightgray",
      entities: "38,amp,60,lt,62,gt",
      entity_encoding: "raw",
      preview_styles: "font-family font-size font-weight font-style text-decoration text-transform",
      relative_urls: false,
      remove_script_host: false,
      resize: "vertical",
      plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview,directionality,image",
      tabfocus_elements: ":prev,:next",
      theme: "modern",
      fix_list_elements: true,
      mode: "tmce",//tmce,exact
      menubar : false,
      toolbar1: "formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,fullscreen,wp_adv",
      toolbar2: "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,rtl,ltr,wp_help",
      toolbar3: "",
      toolbar4: "",
      wpautop: true,
      setup: function(wp_editor) {
        jpress.on_setup_wp_editor(wp_editor);//php class-field.php set_args();
        wp_editor.on('init', function(args) {
          jpress.on_init_wp_editor(wp_editor, args);
        });
      }
    });

    tinyMCEPreInit.mceInit[ed_id] = ed_settings;

    // Initialize wp_editor tinymce instance
    tinymce.init(tinyMCEPreInit.mceInit[ed_id]);
    //tinymce.execCommand( 'mceAddEditor', true, ed_id );

    //Quick tags Settings
    var qt_settings = jQuery.extend({}, tinyMCEPreInit.qtInit[old_ed_id]);
    qt_settings.id = ed_id;
    new QTags(ed_id);
    QTags._buttonsInit();
  };

  jpress.init_switcher = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-type-switcher input.jpress-element') : $selector;
    $selector.jpressSwitcher();
  };

  jpress.init_spinner = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-type-number .jpress-field.jpress-has-spinner') : $selector;
    $selector.spinnerNum('delay', 300);
    $selector.spinnerNum('changing', function (e, newVal, oldVal) {
      $(this).trigger('jpress_changed_value', newVal);
    });
  };

  jpress.init_tab = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-tab') : $selector;
    $selector.each(function(index, el){
      var $tab = $(el);
      if( $tab.closest('.jpress-source-item').length ){
        return;//continue each
      }
      $tab.find('.jpress-tab-nav .jpress-item').removeClass('active');
      $tab.find('.jpress-accordion-title').remove();

      var type_tab = 'responsive';
      if ($tab.closest('#side-sortables').length) {
        type_tab = 'accordion';
      }
      $tab.jpressTabs({
        collapsible: true,
        type: type_tab
      });
    });
  };

  jpress.init_tooltip = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-tooltip-handler') : $selector;
    $selector.each(function (index, el) {
      var title_content = '';
      var title_tooltip = $(el).data('tipso-title');
      var position = $(el).data('tipso-position') ? $(el).data('tipso-position') : 'top';
      if (!is_empty(title_tooltip)) {
        title_content = '<h3>' + title_tooltip + '</h3>';
      }
      $(el).tipso({
        delay: 10,
        speed: 100,
        offsetY: 2,
        tooltipHover: true,
        position: position,
        titleContent: title_content,
        onBeforeShow: function ($element, element, e) {
          $(e.tipso_bubble).addClass($(el).closest('.jpress').data('skin'));
        },
        onShow: function ($element, element, e) {
          //$(e.tipso_bubble).removeClass('top').addClass(position);
        },
        //hideDelay: 1000000
      });
    });
  };

  jpress.init_checkbox = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-has-icheck .jpress-radiochecks.init-icheck') : $selector;
    $selector.find('input').iCheck({
      radioClass: 'iradio_flat-blue',
      checkboxClass: 'icheckbox_flat-blue',
    });
  };

  jpress.destroy_icheck = function ($selector) {
    $selector.find('input').each(function (index, input) {
      $(input).attr('style', '');
      $(input).next('ins').remove();
      $(input).unwrap();
    });
  };

  jpress.init_image_selector = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-type-image_selector .init-image-selector, .jpress-type-import .init-image-selector') : $selector;
    $selector.jpressImageSelector({
      active_class: 'jpress-active'
    });
  };

  jpress.init_dropdown = function ($selector) {
    $selector = is_empty($selector) ? $('.ui.selection.dropdown') : $selector;
    $selector.each(function (index, el) {
      var max_selections = parseInt($(el).data('max-selections'));
      var value = $(el).find('input[type="hidden"]').val();
      if (max_selections > 1 && $(el).hasClass('multiple')) {
        $(el).dropdownAppbear({
          maxSelections: max_selections,
        });
        $(el).dropdownAppbear('set selected', value.split(','));
      } else {
        $(el).dropdownAppbear();
      }
    });
  };

  jpress.on_focusout_input_colorpicker = function () {
    var $field = $(this).closest('.jpress-field');
    var value = $(this).val();
    $(this).attr('value', value);
    $field.find('.jpress-colorpicker-color').attr('value', value).css('background-color', value);
    return false;
  };

  jpress.set_default_value_colorpicker = function () {
    var $field = $(this).closest('.jpress-field');
    var value = $field.data('default');
    if (value) {
      $field.find('input.jpress-element').attr('value', value);
      $field.find('.jpress-colorpicker-color').attr('value', value).css('background-color', value);
    }
  };

  jpress.init_colorpicker = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-colorpicker-color') : $selector;
    $selector.colorPicker({
      cssAddon: '.cp-color-picker {margin-top:6px;}',
      buildCallback: function ($elm) {
      },
      renderCallback: function ($elm, toggled) {
        var $field = $elm.closest('.jpress-field');
        this.$UI.find('.cp-alpha').toggle($field.hasClass('jpress-has-alpha'));
        var value = this.color.toString('rgb', true);
        if (!$field.hasClass('jpress-has-alpha')) {//|| value.endsWith(', 1)')
          value = '#' + this.color.colors.HEX;
        }
        value = value.indexOf('NAN') > -1 ? '' : value;
        $field.find('input').attr('value', value);
        $field.find('.jpress-colorpicker-color').attr('value', value).css('background-color', value);

        //Para la gestión de eventos
        $field.find('input').trigger('change');
      }
    });
  };

  jpress.destroy_ace_editor = function ($selector) {
    var $textarea = $selector.closest('.jpress-field').find('textarea.jpress-element');
    $selector.text($textarea.val());
  };

  jpress.init_code_editor = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-code-editor') : $selector;
    $selector.each(function (index, el) {
      var editor = ace.edit($(el).attr('id'));
      var language = $(el).data('language');
      var theme = $(el).data('theme');
      editor.setTheme("ace/theme/" + theme);
      editor.getSession().setMode("ace/mode/" + language);
      editor.setFontSize(15);
      editor.setShowPrintMargin(false);
      editor.getSession().on('change', function (e) {
        $(el).closest('.jpress-field').find('textarea.jpress-element').text(editor.getValue());
      });

      //Include auto complete
      ace.config.loadModule('ace/ext/language_tools', function () {
        editor.setOptions({
          enableBasicAutocompletion: true,
          enableSnippets: true
        });
      });
    });
  };

  jpress.init_sortable_preview_items = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-wrap-preview-multiple') : $selector;
    $selector.sortable({
      items: '.jpress-preview-item',
      placeholder: "jpress-preview-item jpress-sortable-placeholder",
      start: function (event, ui) {
        ui.placeholder.css({
          'width': ui.item.css('width'),
          'height': ui.item.css('height'),
        });
      },
    }).disableSelection();
  };

  jpress.init_sortable_checkbox = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-has-icheck .jpress-radiochecks.init-icheck.jpress-sortable') : $selector;
    $selector.sortable({
      items: '>label',
      placeholder: "jpress-icheck-sortable-item jpress-sortable-placeholder",
      start: function (event, ui) {
        ui.placeholder.css({
          'width': ui.item.css('width'),
          'height': ui.item.css('height'),
        });
      },
    }).disableSelection();
  };

  jpress.init_sortable_repeatable_items = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-repeatable-wrap.jpress-sortable') : $selector;
    $selector.sortable({
      handle: '.jpress-sort-item',
      items: '.jpress-repeatable-item',
      placeholder: "jpress-repeatable-item jpress-sortable-placeholder",
      start: function (event, ui) {
        ui.placeholder.css({
          'width': ui.item.css('width'),
          'height': ui.item.css('height'),
        });
      },
      update: function (event, ui) {
        // No funciona bien con wp_editor, mejor usamos 'stop'
        // var $repeatable_wrap = $(event.target);
        // $repeatable_wrap.trigger('sort_repeatable_items');
      },
      stop: function (event, ui) {
        var $repeatable_wrap = $(event.target);
        $repeatable_wrap.trigger('sort_repeatable_items');
      }
    }).disableSelection();
  };

  jpress.init_sortable_group_items = function ($selector) {
    $selector = is_empty($selector) ? $('.jpress-group-control.jpress-sortable') : $selector;
    $selector.sortable({
      items: '.jpress-group-control-item',
      placeholder: "jpress-sortable-placeholder",
      start: function (event, ui) {
        ui.placeholder.css({
          'width': ui.item.css('width'),
          'height': ui.item.css('height'),
        });
      },
      update: function (event, ui) {
        var $group_control = $(event.target);
        var $group_wrap = $group_control.next('.jpress-group-wrap');

        var old_index = ui.item.attr('data-index');
        var new_index = $group_control.find('.jpress-group-control-item').index(ui.item);
        var $group_item = $group_wrap.children('.jpress-group-item[data-index=' + old_index + ']');
        var $group_item_reference = $group_wrap.children('.jpress-group-item[data-index=' + new_index + ']');
        var start_index = 0;
        var end_index;

        if (old_index < new_index) {
          $group_item.insertAfter($group_item_reference);
          start_index = old_index;
          end_index = new_index;
        } else {
          $group_item.insertBefore($group_item_reference);
          start_index = new_index;
          end_index = old_index;
        }

        $group_wrap.trigger('jpress_on_sortable_group_item', [old_index, new_index]);

        $group_control.trigger('sort_group_control_items');

        $group_wrap.trigger('sort_group_items', [start_index, end_index]);

        //Click event, to initialize some fields -> (WP Editors)
        if (ui.item.hasClass('jpress-active')) {
          ui.item.trigger('click');
        }
      }
    }).disableSelection();
  };

  jpress.add_repeatable_item = function (event) {
    var $btn = $(this);
    var $repeatable_wrap = $btn.closest('.jpress-repeatable-wrap');
    $repeatable_wrap.trigger('jpress_before_add_repeatable_item');

    var $source_item = $btn.prev('.jpress-repeatable-item');
    var index = parseInt($source_item.data('index'));
    var $cloned = $source_item.clone();
    var $new_item = $('<div />', { 'class': $cloned.attr('class'), 'data-index': index + 1, 'style': 'display: none' });

    jpress.set_changed_values($cloned, $repeatable_wrap.closest('.jpress-row').data('field-type'));

    $new_item.html($cloned.html());
    $source_item.after($new_item);
    $new_item.slideDown(150, function () {
      //Ordenar y cambiar ids y names
      $repeatable_wrap.trigger('sort_repeatable_items');
      //Actualizar eventos
      jpress.reinit_js_plugins($new_item);
    });
    $repeatable_wrap.trigger('jpress_after_add_repeatable_item');
    return false;
  };

  jpress.remove_repeatable_item = function (event) {
    var $repeatable_wrap = $(this).closest('.jpress-repeatable-wrap');
    if ($repeatable_wrap.find('.jpress-repeatable-item').length > 1) {
      $repeatable_wrap.trigger('jpress_before_remove_repeatable_item');
      var $item = $(this).closest('.jpress-repeatable-item');
      $item.slideUp(150, function () {
        $item.remove();
        $repeatable_wrap.trigger('sort_repeatable_items');
        $repeatable_wrap.trigger('jpress_after_remove_repeatable_item');
      });
    }
    return false;
  };

  jpress.sort_repeatable_items = function (event) {
    var $repeatable_wrap = $(event.target);
    var row_level = parseInt($repeatable_wrap.closest('[class*="jpress-row"]').data('row-level'));

    $repeatable_wrap.find('.jpress-repeatable-item').each(function (index, item) {
      jpress.update_attributes($(item), index, row_level);

      //Destroy WP Editors
      $(item).find('.wp-editor-wrap').each(function (index, el) {
        jpress.destroy_wp_editor($(el));
      });
      jpress.update_fields_on_item_active($(item));
    });
  };

  jpress.new_group_item = function (event) {
    if ($(event.currentTarget).hasClass('jpress-duplicate-group-item')) {
      jpress.duplicate = true;
      event.stopPropagation();
    } else {
      jpress.duplicate = false;
    }
    var $group = $(this).closest('.jpress-type-group');
    var $control_item = jpress.add_group_control_item(event, $(this));
    var $group_item = jpress.add_group_item(event, $(this));

    var args = {
      event: event,
      $btn: $(this),
      $group: $group,
      duplicate: jpress.duplicate,
      $group_item: $group_item,
      $control_item: $control_item,
      index: $group_item.data('index'),
      type: $group_item.data('type')
    };

    $group.trigger('jpress_after_add_group_item', [args]);

    //Active new item
    $control_item.trigger('click');

    return false;
  };

  jpress.add_group_control_item = function (event, $btn) {
    var item_type = $btn.data('item-type');
    var $group = $btn.closest('.jpress-type-group');
    var $group_wrap = $group.find('.jpress-group-wrap').first();
    var $group_control = $btn.closest('.jpress-type-group').find('.jpress-group-control').first();
    var $source_item = $group_control.find('.jpress-group-control-item').last();
    var index = -1;
    if ($source_item.length) {
      index = $source_item.data('index');
    }
    $source_item = $group_wrap.next('.jpress-source-item').find('.jpress-group-control-item');

    if (jpress.duplicate) {
      index = $btn.closest('.jpress-group-control-item').index();
      $source_item = $group_control.children('.jpress-group-control-item').eq(index);
      item_type = $source_item.find('.jpress-input-group-item-type').val();
    }
    index = parseInt(index);
    var args = {
      event: event,
      $btn: $btn,
      $group: $group,
      duplicate: jpress.duplicate,
      $group_item: $group_wrap.children('.jpress-group-item').eq(index),
      $control_item: $source_item,
      index: index,
      type: item_type
    };
    $group.trigger('jpress_before_add_group_item', [args]);

    var row_level = parseInt($source_item.closest('.jpress-row').data('row-level'));
    var $cloned = $source_item.clone();
    var $new_item = $('<li />', { 'class': $cloned.attr('class'), 'data-index': index + 1, 'data-type': item_type });

    $new_item.html($cloned.html());
    $source_item.after($new_item);

    //Add new item
    if (index == -1) {
      $group_control.append($new_item);
    } else {
      $group_control.children('.jpress-group-control-item').eq(index).after($new_item);
    }
    $new_item = $group_control.children('.jpress-group-control-item').eq(index + 1);

    $new_item.alterClass('control-item-type-*', 'control-item-type-' + item_type);
    $new_item.find('input.jpress-input-group-item-type').val(item_type);
    $group_control.trigger('sort_group_control_items');

    if (jpress.duplicate === false && $new_item.find('.jpress-control-image').length) {
      $new_item.find('.jpress-control-image').css('background-image', 'url()');
    }
    if (jpress.duplicate === false) {
      var $input = $new_item.find('.jpress-inner input');
      if ($input.length) {
        var value = $group_control.data('control-name').toString();
        $input.attr('value', value.replace(/(#\d?)/g, '#' + (index + 2)));
        if ($btn.hasClass('jpress-custom-add')) {
          $input.attr('value', $btn.text());
        }
      }
    }
    return $new_item;
  };

  jpress.add_group_item = function (event, $btn) {
    var item_type = $btn.data('item-type');
    var $group_wrap = $btn.closest('.jpress-type-group').find('.jpress-group-wrap').first();
    var $source_item = $group_wrap.children('.jpress-group-item').last();
    var index = -1;
    if ($source_item.length) {
      index = $source_item.data('index');
    }
    $source_item = $group_wrap.next('.jpress-source-item').find('.jpress-group-item');

    if (jpress.duplicate) {
      index = $btn.closest('.jpress-group-control-item').index();
      $source_item = $group_wrap.children('.jpress-group-item').eq(index);
      item_type = $btn.closest('.jpress-group-control-item').find('.jpress-input-group-item-type').val();
    }

    index = parseInt(index);
    var row_level = parseInt($source_item.closest('.jpress-row').data('row-level'));
    var $cloned = $source_item.clone();
    var $cooked_item = jpress.cook_group_item($cloned, row_level, index);
    var $new_item = $('<div />', { 'class': $cloned.attr('class'), 'data-index': index + 1, 'data-type': item_type });
    $new_item.html($cooked_item.html());
    //Add new item
    if (index == -1) {
      $group_wrap.append($new_item);
    } else {
      $group_wrap.children('.jpress-group-item').eq(index).after($new_item);
    }
    $new_item = $group_wrap.children('.jpress-group-item').eq(index + 1);
    $new_item.alterClass('group-item-type-*', 'group-item-type-' + item_type);
    $group_wrap.trigger('sort_group_items', [index + 1]);

    //Actualizar eventos
    jpress.reinit_js_plugins($new_item);

    if (jpress.duplicate === false) {
      //jpress.set_default_values( $new_item );//Ya no es necesario por el nuevo source item
    }
    return $new_item;
  };

  jpress.cook_group_item = function ($group_item, row_level, prev_index) {
    var index = prev_index + 1;

    if (jpress.duplicate) {
      jpress.set_changed_values($group_item);
    } else {
      //No es duplicado, restaurar todo, eliminar items de grupos internos
      $group_item.find('.jpress-group-wrap').each(function (index, wrap_group) {
        $(wrap_group).find('.jpress-group-item').first().addClass('jpress-active').siblings().remove();
        $(wrap_group).prev('.jpress-group-control').children('.jpress-group-control-item').first().addClass('jpress-active').siblings().remove();
      });
      $group_item.find('.jpress-repeatable-wrap').each(function (index, wrap_repeat) {
        $(wrap_repeat).find('.jpress-repeatable-item').not(':first').remove();
      });
    }

    jpress.update_attributes($group_item, index, row_level);

    return $group_item;
  };

  jpress.set_changed_values = function ($new_item, field_type) {
    var $textarea, $input;
    $new_item.find('.jpress-field').each(function (iterator, item) {
      var type = field_type || $(item).closest('.jpress-row').data('field-type');
      switch (type) {
        case 'text':
        case 'number':
        case 'oembed':
        case 'file':
        case 'image':
          $input = $(item).find('input.jpress-element');
          $input.attr('value', $input.val());
          break;
      }
    });
  };

  jpress.remove_group_item = function (event) {
    event.preventDefault();
    event.stopPropagation();
    var $btn = $(this);
    var $row = $btn.closest('.jpress-type-group');
    var $group_wrap = $row.find('.jpress-group-wrap').first();
    var $group_control = $btn.closest('.jpress-group-control');
    var index = $btn.closest('.jpress-group-control-item').data('index');

    $.jpressConfirm({
      title: JPRESS_JS.text.remove_item_popup.title,
      content: JPRESS_JS.text.remove_item_popup.content,
      confirm_class: 'jpress-btn-blue',
      confirm_text: JPRESS_JS.text.popup.accept_button,
      cancel_text: JPRESS_JS.text.popup.cancel_button,
      onConfirm: function () {
        setTimeout(function () {
          jpress.remove_group_control_item($btn);
          jpress._remove_group_item($btn);
        }, jpress.delays.removeItem.confirm);

        setTimeout(function () {
          $group_wrap.trigger('sort_group_items', [index]);
          $group_control.children('.jpress-group-control-item').eq(0).trigger('click');
          $group_control.trigger('sort_group_control_items');
        }, jpress.delays.removeItem.events);
      }
    });
    return false;
  };

  jpress.remove_group_items = function (items) {
    if( ! items.length ){
      return;
    }
    var $row, $group_wrap, $group_control;
    $.jpressConfirm({
      title: JPRESS_JS.text.remove_item_popup.title,
      content: JPRESS_JS.text.remove_item_popup.content,
      confirm_class: 'jpress-btn-blue',
      confirm_text: JPRESS_JS.text.popup.accept_button,
      cancel_text: JPRESS_JS.text.popup.cancel_button,
      onConfirm: function () {
        var min_index = 1000;
        var type = '';
        setTimeout(function () {
          $(items).each(function(i, $element){
            var index = $element.data('index');
            if( index < min_index ){
              min_index = index;
              type = $element.data('type');
            }
            if( i == 0){
              $row = $element.closest('.jpress-type-group');
              $group_wrap = $row.find('.jpress-group-wrap').first();
              $group_control = $element.closest('.jpress-group-control');
            }
            jpress.remove_group_control_item($element);
            jpress._remove_group_item($element);
          });
        }, jpress.delays.removeItem.confirm);

        setTimeout(function () {
          $group_wrap.trigger('sort_group_items', [min_index]);
          $group_control.children('.jpress-group-control-item').eq(0).trigger('click');
          $group_control.trigger('sort_group_control_items');
        }, jpress.delays.removeItem.events);
      }
    });
  };

  jpress.remove_group_control_item = function ($btn) {
    var $item = $btn.closest('.jpress-group-control-item');
    $item.fadeOut(jpress.delays.removeItem.fade, function () {
      $item.remove();
    });
  };

  jpress._remove_group_item = function ($btn) {
    var $row = $btn.closest('.jpress-type-group');
    var $group_wrap = $row.find('.jpress-group-wrap').first();
    var index = $btn.closest('.jpress-group-control-item').data('index');
    $row.trigger('jpress_before_remove_group_item');
    var $item = $group_wrap.children('.jpress-group-item[data-index="'+index+'"]');
    var type = $item.data('type');
    $item.fadeOut(jpress.delays.removeItem.fade, function () {
      $item.remove();
      // $group_wrap.trigger('sort_group_items', [index]);
      $row.trigger('jpress_after_remove_group_item', [index, type]);
      // $group_control.children('.jpress-group-control-item').eq(0).trigger('click');
    });
  };

  jpress.on_click_group_control_item = function (event) {
    var $control_item = $(this);
    jpress.active_control_item(event, $control_item);
    return false;
  };

  jpress.active_control_item = function (event, $control_item) {
    var $group_control = $control_item.parent();
    var index = $control_item.index();
    var $group = $group_control.closest('.jpress-type-group');
    var $group_wrap = $group.find('.jpress-group-wrap').first();
    var $group_item = $group_wrap.children('.jpress-group-item').eq(index);
    var $old_control_item = $group_control.children('.jpress-active');

    $group_control.children('.jpress-group-control-item').removeClass('jpress-active');
    $control_item.addClass('jpress-active');

    $group_wrap.children('.jpress-group-item').removeClass('jpress-active');
    $group_item.addClass('jpress-active');

    var args = {
      $group_item: $group_item,
      $control_item: $control_item,
      index: $group_item.data('index'),
      type: $group_item.data('type'),
      event: event,
      old_index: $old_control_item.data('index'),
    };

    setTimeout(function(){
      $group.trigger('jpress_on_active_group_item', [args]);
      jpress.update_fields_on_item_active($group_item);
    }, 10);//Retardar un poco para posibles eventos on click desde otras aplicaciones
    return false;
  };

  jpress.update_fields_on_item_active = function ($group_item) {
    //Init WP Editor
    $group_item.find('.wp-editor-wrap.init-wp-editor').each(function (index, el) {
      jpress.init_wp_editor($(el));
    });
  };

  jpress.sort_group_control_items = function (event) {
    var $group_control = $(event.target);
    var row_level = parseInt($group_control.closest('.jpress-row').data('row-level'));
    $group_control.children('.jpress-group-control-item').each(function (index, item) {
      jpress.update_group_control_item($(item), index, row_level);
    });
  };

  jpress.sort_group_items = function (event, start_index, end_index) {
    var $group_wrap = $(event.target);
    $group_wrap.trigger('jpress_before_sort_group');
    var row_level = parseInt($group_wrap.closest('.jpress-row').data('row-level'));
    end_index = end_index !== undefined ? parseInt(end_index) + 1 : undefined;

    var $items = $group_wrap.children('.jpress-group-item');
    var $items_to_sort = $items.slice(start_index, end_index);

    $items_to_sort.each(function (i, group_item) {
      var index = $group_wrap.find($(group_item)).index();
      jpress.update_attributes($(group_item), index, row_level);

      //Destroy WP Editors
      $(group_item).find('.wp-editor-wrap').each(function (index, el) {
        jpress.destroy_wp_editor($(el));
      });
    });
    $group_wrap.trigger('jpress_after_sort_group');
  };

  jpress.update_group_control_item = function ($item, index, row_level) {
    $item.data('index', index).attr('data-index', index);
    $item.find('.jpress-info-order-item').text('#' + (index + 1));
    var value;
    if ($item.find('.jpress-inner input').length) {
      value = $item.find('.jpress-inner input').val();
      $item.find('.jpress-inner input').val(value.replace(/(#\d+)/g, '#' + (index + 1)));
    }

    //Cambiar names
    $item.find('*[name]').each(function (i, item) {
      jpress.update_name_ttribute($(item), index, row_level);
    });
  };

  jpress.update_attributes = function ($new_item, index, row_level) {
    $new_item.data('index', index).attr('data-index', index);

    $new_item.find('*[name]').each(function (i, item) {
      jpress.update_name_ttribute($(item), index, row_level);
    });

    $new_item.find('*[id]').each(function (i, item) {
      jpress.update_id_attribute($(item), index, row_level);
    });

    $new_item.find('label[for]').each(function (i, item) {
      jpress.update_for_attribute($(item), index, row_level);
    });

    $new_item.find('*[data-field-name]').each(function (i, item) {
      jpress.update_data_name_attribute($(item), index, row_level);
    });

    $new_item.find('*[data-editor]').each(function (i, item) {
      jpress.update_data_editor_attribute($(item), index, row_level);
    });

    $new_item.find('*[data-wp-editor-id]').each(function (i, item) {
      jpress.update_data_wp_editor_id_attribute($(item), index, row_level);
    });

    jpress.set_checked_inputs($new_item, row_level);
  };

  jpress.set_checked_inputs = function ($group_item, row_level) {
    $group_item.find('.jpress-field').each(function (iterator, item) {
      if ($(item).hasClass('jpress-has-icheck') || $(item).closest('.jpress-type-image_selector').length) {
        var $input = $(item).find('input[type="radio"], input[type="checkbox"]');
        $input.each(function (i, input) {
          if ($(input).parent('div').hasClass('checked')) {
            $(input).attr('checked', 'checked').prop('checked', true);
          } else {
            $(input).removeAttr('checked').prop('checked', false);
          }
          if ($(input).next('img').hasClass('jpress-active')) {
            $(input).attr('checked', 'checked').prop('checked', true);
          }
        });
      }
    });
  };

  jpress.update_name_ttribute = function ($el, index, row_level) {
    var old_name = $el.attr('name');
    var new_name = '';
    if (typeof old_name !== 'undefined') {
      new_name = jpress.nice_replace(/(\[\d+\])/g, old_name, '[' + index + ']', row_level);
      $el.attr('name', new_name);
    }
  };

  jpress.update_id_attribute = function ($el, index, row_level) {
    var old_id = $el.attr('id');
    var new_id = '';
    if (typeof old_id !== 'undefined') {
      new_id = jpress.nice_replace(/(__\d+__)/g, old_id, '__' + index + '__', row_level);
      $el.attr('id', new_id);
    }
  };

  jpress.update_for_attribute = function ($el, index, row_level) {
    var old_for = $el.attr('for');
    var new_for = '';
    if (typeof old_for !== 'undefined') {
      new_for = jpress.nice_replace(/(__\d+__)/g, old_for, '__' + index + '__', row_level);
      $el.attr('for', new_for);
    }
  };
  jpress.update_data_name_attribute = function ($el, index, row_level) {
    var old_data = $el.attr('data-field-name');
    var new_data = '';
    if (typeof old_data !== 'undefined') {
      new_data = jpress.nice_replace(/(\[\d+\])/g, old_data, '[' + index + ']', row_level);
      $el.attr('data-field-name', new_data);
    }
  };

  jpress.update_data_editor_attribute = function ($el, index, row_level) {
    var old_data = $el.attr('data-editor');
    var new_data = '';
    if (typeof old_data !== 'undefined') {
      new_data = jpress.nice_replace(/(__\d+__)/g, old_data, '__' + index + '__', row_level);
      $el.attr('data-editor', new_data);
    }
  };
  jpress.update_data_wp_editor_id_attribute = function ($el, index, row_level) {
    var old_data = $el.attr('data-wp-editor-id');
    var new_data = '';
    if (typeof old_data !== 'undefined') {
      new_data = jpress.nice_replace(/(__\d+__)/g, old_data, '__' + index + '__', row_level);
      $el.attr('data-wp-editor-id', new_data);
    }
  };

  jpress.set_default_values = function ($group) {
    $group.find('*[data-default]').each(function (iterator, item) {
      var $field = $(item);
      var default_value = $field.data('default');
      if ($field.closest('.jpress-type-number').length) {
        jpress.set_field_value($field, default_value);
      } else {
        jpress.set_field_value($field, default_value);
      }
    });
  };

  jpress.set_field_value = function ($field, value, extra_value, update_initial_values) {
    if( !$field.length ){
      return;
    }
    var $input, array;
    var type = $field.closest('.jpress-row').data('field-type');
    value = is_empty(value) ? '' : value;

    switch (type) {
      case 'number':
        var $input = $field.find('input.jpress-element');
        //Ctrl + z functionality
        jpress.update_prev_values($input, value, update_initial_values);

        if (value == $input.val()) {
          return;
        }

        $input.attr('value', value);
        var unit = extra_value === undefined ? $input.data('default-unit') : extra_value;
        $field.find('input.jpress-unit-number').attr('value', unit).trigger('change');
        unit = unit || '#';
        $field.find('.jpress-unit span').text(unit);
        break;

      case 'text':
      case 'hidden':
      case 'colorpicker':
      case 'date':
      case 'time':
        var $input = $field.find('input.jpress-element');

        //Ctrl + z functionality
        jpress.update_prev_values($input, value, update_initial_values);

        if (value == $input.val()) {
          return;
        }
        $input.attr('value', value).trigger('change').trigger('input');
        if (type == 'colorpicker') {
          $field.find('.jpress-colorpicker-color').attr('value', value).css('background-color', value);
        }
        break;

      case 'file':
      case 'oembed':
        var $input = $field.find('input.jpress-element');

        //Ctrl + z functionality
        jpress.update_prev_values($input, value, update_initial_values);

        $input.attr('value', value).trigger('change').trigger('input');
        $field.find('.jpress-wrap-preview').html('');
        break;

      case 'image':
        $field.find('input.jpress-element').attr('value', value);
        $field.find('img.jpress-element-image').attr('src', value);
        if (is_empty(value)) {
          $field.find('img.jpress-element-image').hide().next('.jpress-remove-preview').hide();
        }
        break;

      case 'select':
        var $input = $field.find('.jpress-element input[type="hidden"]');

        //Ctrl + z functionality
        jpress.update_prev_values($input, value, update_initial_values);

        var $dropdown = $field.find('.ui.selection.dropdown');
        var max_selections = parseInt($dropdown.data('max-selections'));
        $dropdown.dropdownAppbear('clear');
        if (max_selections > 1 && $dropdown.hasClass('multiple')) {
          $dropdown.dropdownAppbear('set selected', value.split(','));
        } else {
          $dropdown.dropdownAppbear('set selected', value);
        }
        break;

      case 'switcher':
        $input = $field.find('input');

        //Ctrl + z functionality
        jpress.update_prev_values($input, value, update_initial_values);

        if ($input.val() !== value) {
          if ($input.next().hasClass('jpress-sw-on')) {
            $input.jpressSwitcher('set_off');
          } else {
            $input.jpressSwitcher('set_on');
          }
        }
        break;

      case 'wp_editor':
        var $textarea = $field.find('textarea.wp-editor-area');
        $textarea.val(value);
        var wp_editor = tinymce.get($textarea.attr('id'));
        if (wp_editor) {
          wp_editor.setContent(value);
        }
        break;

      case 'textarea':
        $field.find('textarea').val(value).trigger('input');
        break;

      case 'code_editor':
        $field.find('textarea.jpress-element').text(value);
        var editor = ace.edit($field.find('.jpress-code-editor').attr('id'));
        editor.setValue(value);
        break;

      case 'icon_selector':
        $field.find('input.jpress-element').attr('value', value).trigger('change');
        var html = '';
        if (value.indexOf('.svg') > -1) {
          html = '<img src="' + value + '">';
        } else {
          html = '<i class="' + value + '"></i>';
        }
        $field.find('.jpress-icon-active').html(html);
        break;

      case 'image_selector':
        value = value.toString().toLowerCase();
        $input = $field.find('input');

        if (!$input.closest('.jpress-image-selector').data('image-selector').like_checkbox) {
          if (is_empty($input.filter(':checked').val())) {
            return;
          }
          if ($input.filter(':checked').val().toLowerCase() != value) {
            $input.filter(function (i) {
              return $(this).val().toLowerCase() == value;
            }).trigger('click.img_selector');
          }
        } else {
          if (get_value_checkbox($input, ',').toLowerCase() != value) {
            $input.first().trigger('img_selector_disable_all');
            array = value.replace(/ /g, '').split(',');
            $.each(array, function (index) {
              $input.filter(function (i) {
                return $(this).val().toLowerCase() == array[index];
              }).trigger('click.img_selector');
            });
          }
        }
        break;

      case 'checkbox':
      case 'radio':
        value = value.toString().toLowerCase();
        if ($field.hasClass('jpress-has-icheck') && $field.find('.init-icheck').length) {
          $input = $field.find('input');
          if (type == 'radio') {
            if (is_empty($input.filter(':checked').val())) {
              return;
            }
            $input.iCheck('uncheck');
            //if( $input.filter(':checked').val().toLowerCase() != value ){
            $input.filter(function (i) {
              return $(this).val().toLowerCase() == value;
            }).iCheck('check');
            //}
          } else if (type == 'checkbox') {
            if (get_value_checkbox($input, ',').toLowerCase() != value) {
              $input.iCheck('uncheck');
              array = value.replace(/ /g, '').split(',');
              $.each(array, function (index) {
                $input.filter(function (i) {
                  return $(this).val().toLowerCase() == array[index];
                }).iCheck('check');
              });
            }
          }
        }
        break;
    }
  };

  jpress.update_prev_values = function ($input, value, update_initial_values) {
    if( update_initial_values ){
      $input.attr('data-prev-value', value).data('prev-value', value);
      $input.attr('data-initial-value', value).data('initial-value', value);
      $input.attr('data-temp-value', value).data('temp-value', value);
    } else {
      //Va un poco lento cuando hay múltiples cambios a la vez
      //Ctrl + z functionality
      // var tempValue = $input.data('temp-value');
      // if( tempValue != value ){
      //   $input.attr('data-prev-value', tempValue).data('prev-value', tempValue);
      //   $input.attr('data-temp-value', value).data('temp-value', value);
      // }
    }
  };

  jpress.nice_replace = function (regex, string, replace_with, row_level, offset) {
    offset = offset || 0;
    //http://stackoverflow.com/questions/10584748/find-and-replace-nth-occurrence-of-bracketed-expression-in-string
    var n = 0;
    string = string.replace(regex, function (match, i, original) {
      n++;
      return (n === row_level + offset) ? replace_with : match;
    });
    return string;
  };

  jpress.get_object_id = function () {
    return $('.jpress').data('object-id');
  };

  jpress.get_object_type = function () {
    return $('.jpress').data('object-type');
  };

  jpress.get_group_object_values = function ($group_item) {
    var values = $group_item.find('input[name],select[name],textarea[name]').serializeArray();
    return values;
  };

  jpress.get_group_values = function ($group_item) {
    var object_values = jpress.get_group_object_values($group_item);
    var values = {};
    $.each(object_values, function (index, field) {
      values[field.name] = field.value;
    });
    return values;
  };

  jpress.compare_values_by_operator = function (value1, operator, value2) {
    switch (operator) {
      case '<':
        return value1 < value2;
      case '<=':
        return value1 <= value2;
      case '>':
        return value1 > value2;
      case '>=':
        return value1 >= value2;
      case '==':
      case '=':
        return value1 == value2;
      case '!=':
        return value1 != value2;
      default:
        return false;
    }
    return false;
  };

  jpress.add_style_attribute = function ($element, new_style) {
    var old_style = $element.attr('style') || '';
    $element.attr('style', old_style + '; ' + new_style);
  };

  jpress.is_image_file = function (value) {
    value = $.trim(value.toString());
    return (value.match(/\.(jpeg|jpg|gif|png)$/) !== null);
  };


  //Funciones privadas
  function is_empty(value) {
    return (value === undefined || value === false || $.trim(value).length === 0);
  }

  function get_class_starts_with($elment, starts_with) {
    return $.grep($elment.attr('class').split(" "), function (v, i) {
      return v.indexOf(starts_with) === 0;
    }).join();
  }

  function get_value_checkbox($elment, separator) {
    separator = separator || ',';
    if ($elment.attr('type') != 'checkbox') {
      return '';
    }
    var value = $elment.filter(':checked').map(function () {
      return this.value;
    }).get().join(separator);
    return value;
  }

  function viewport() {
    var e = window, a = 'inner';
    if (!('innerWidth' in window)) {
      a = 'client';
      e = document.documentElement || document.body;
    }
    return { width: e[a + 'Width'], height: e[a + 'Height'] };
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
    jpress.init();
  });

  return jpress;

})(window, document, jQuery);


/**
 * jQuery alterClass plugin
 *
 * Remove element classes with wildcard matching. Optionally add classes:
 *   $( '#foo' ).alterClass( 'foo-* bar-*', 'foobar' )
 *
 * Copyright (c) 2011 Pete Boere (the-echoplex.net)
 * Free under terms of the MIT license: http://www.opensource.org/licenses/mit-license.php
 *
 */
(function ($) {
  $.fn.alterClass = function (removals, additions) {
    var self = this;
    if (removals.indexOf('*') === -1) {
      // Use native jQuery methods if there is no wildcard matching
      self.removeClass(removals);
      return !additions ? self : self.addClass(additions);
    }
    var patt = new RegExp('\\s' +
      removals.replace(/\*/g, '[A-Za-z0-9-_]+').split(' ').join('\\s|\\s') +
      '\\s', 'g');
    self.each(function (i, it) {
      var cn = ' ' + it.className + ' ';
      while (patt.test(cn)) {
        cn = cn.replace(patt, ' ');
      }
      it.className = $.trim(cn);
    });
    return !additions ? self : self.addClass(additions);
  };
})(jQuery);


// NOTE: WTF is that for?
(function ($) {
  $('.jpress .jpress-type-icon_selector .jpress-icon-active.jpress-item-icon-selector').on('click', function(){
    alert('test');
    $('.jpress .jpress-type-icon_selector .jpress-search-icon, .jpress .jpress-type-icon_selector .jpress-icons-wrap').css('style', 'display:block  !important');
  });

  //.jpress .jpress-type-icon_selector .jpress-search-icon, .jpress .jpress-type-icon_selector .jpress-icons-wrap
})(jQuery);

