/**
 * AppBear Options JS
 */

window.APPBEAR = (function (window, document, $) {
  'use strict';

  var appbear = {
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

  appbear.init = function () {
    appbear.$appbear = $('.appbear');
    var $form = appbear.$appbear.closest('.appbear-form');
    if (!$form.length) {
      $form = appbear.$appbear.closest('form#post');
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
        $('#post-body').addClass('appbear-columns-1');
      } else {
        $('#post-body').removeClass('appbear-columns-1');
      }
    }).resize();


    appbear.init_image_selector();
    appbear.init_tab();
    appbear.init_switcher();
    appbear.init_spinner();
    appbear.init_checkbox();
    appbear.init_dropdown();
    appbear.init_colorpicker();
    appbear.init_code_editor();
    appbear.init_sortable_preview_items();
    appbear.init_sortable_checkbox();
    appbear.init_sortable_repeatable_items();
    appbear.init_sortable_group_items();
    appbear.init_tooltip();

    appbear.load_oembeds();
    setTimeout(function () {
      appbear.load_icons_for_icon_selector();
    }, 200);

    appbear.$appbear.on('click', '#appbear-reset', appbear.on_click_reset_values);
    appbear.$appbear.on('click', '#appbear-import', appbear.on_click_import_values);
    appbear.$appbear.on('ifClicked', '.appbear-type-import .appbear-radiochecks input', appbear.toggle_import);
    appbear.$appbear.on('click', '.appbear-type-import .appbear-radiochecks input, .appbear-wrap-import-inputs .item-key-from_url input, .appbear-wrap-import-inputs .item-key-from_file input', appbear.toggle_import);

    appbear.$appbear.on('click', '.appbear-add-group-item', appbear.new_group_item);
    appbear.$appbear.on('click', '.appbear-duplicate-group-item', appbear.new_group_item);
    appbear.$appbear.on('click', '.appbear-remove-group-item', appbear.remove_group_item);
    appbear.$appbear.on('click', '.appbear-group-control-item', appbear.on_click_group_control_item);
    appbear.$appbear.on('sort_group_items', '.appbear-group-wrap', appbear.sort_group_items);
    appbear.$appbear.on('sort_group_control_items', '.appbear-group-control', appbear.sort_group_control_items);

    appbear.$appbear.on('click', '.appbear-add-repeatable-item', appbear.add_repeatable_item);
    appbear.$appbear.on('click', '.appbear-remove-repeatable-item', appbear.remove_repeatable_item);
    appbear.$appbear.on('sort_repeatable_items', '.appbear-repeatable-wrap', appbear.sort_repeatable_items);

    appbear.$appbear.on('click', '.appbear-upload-file, .appbear-preview-item .appbear-preview-handler', appbear.wp_media_upload);
    appbear.$appbear.on('click', '.appbear-remove-preview', appbear.remove_preview_item);
    appbear.$appbear.on('click', '.appbear-get-oembed', appbear.get_oembed);
    appbear.$appbear.on('click', '.appbear-get-image', appbear.get_image_from_url);
    appbear.$appbear.on('focusout', '.appbear-type-colorpicker input', appbear.on_focusout_input_colorpicker);
    appbear.$appbear.on('click', '.appbear-type-colorpicker .appbear-colorpicker-default-btn', appbear.set_default_value_colorpicker);
    appbear.$appbear.on('click', '.appbear-section.appbear-toggle-1 .appbear-section-header, .appbear-section .appbear-toggle-icon', appbear.toggle_section);
    appbear.$appbear.on('click', '.appbear-type-number .appbear-unit-has-picker-1', appbear.toggle_units_dropdown);
    appbear.$appbear.on('click', '.appbear-units-dropdown .appbear-unit-item', appbear.set_unit_number);
    appbear.$appbear.on('focus', '.appbear-type-text input.appbear-element', appbear.on_focus_input_type_text);

    appbear.refresh_active_main_tab();
    appbear.$appbear.on('click', '.appbear-main-tab .appbear-item-parent a', appbear.on_cick_item_main_tab);

    $(document).on('click', appbear.hide_units_dropdown);

    appbear.$appbear.on('focus', 'input.appbear-element', function (event) {
      $(this).closest('.appbear-field').removeClass('appbear-error');
    });

    appbear.sticky_submit_buttons();
    $(window).scroll(function () {
      appbear.sticky_submit_buttons();
    });
  };

  appbear.on_cick_item_main_tab = function(e){
    var activeItem = $(this).attr('href').replace(/#/, '');
    var prefix = appbear.$appbear.data('prefix');
    localStorage.setItem('appbear-main-tab-item-active', activeItem.replace(prefix, '').replace('tab_item', 'tab-item'));
  };
  appbear.refresh_active_main_tab = function(){
    var activeItem = localStorage.getItem('appbear-main-tab-item-active');
    if( activeItem ){
      appbear.$appbear.find('.appbear-main-tab .appbear-item-parent.'+activeItem+' a').trigger('click');
    }
  };

  appbear.sticky_submit_buttons = function () {
    var $header = $('.appbear-header').first();
    var $actions = $header.find('.appbear-header-actions').first();
    var $my_account = $('#wp-admin-bar-my-account');
    if (!$actions.length || !$my_account.length || !$actions.data('sticky')) {
      return;
    }
    if ($(window).scrollTop() > $header.offset().top) {
      $my_account.css('padding-right', $actions.width() + 25);
      $actions.addClass('appbear-actions-sticky');
    } else {
      $my_account.css('padding-right', '');
      $actions.removeClass('appbear-actions-sticky');
    }
  };

  appbear.on_focus_input_type_text = function (event) {
    var $helper = $(this).next('.appbear-field-helper');
    if ($helper.length) {
      $(this).css('padding-right', ($helper.outerWidth() + 6) + 'px');
    }
  };

  appbear.hide_units_dropdown = function () {
    $('.appbear-units-dropdown').slideUp(200);
  };
  appbear.toggle_units_dropdown = function (event) {
    if ($(event.target).hasClass('appbear-spinner-handler') || $(event.target).hasClass('appbear-spinner-control')) {
      return;
    }
    event.stopPropagation();
    $(this).find('.appbear-units-dropdown').slideToggle(200);
  };
  appbear.set_unit_number = function (event) {
    var $btn = $(this);
    $btn.closest('.appbear-unit').find('input.appbear-unit-number').val($btn.data('value')).trigger('change');
    $btn.closest('.appbear-unit').find('span').text($btn.text());
  };

  appbear.load_icons_for_icon_selector = function (event) {
    var fields = [];

    $('.appbear-type-icon_selector').each(function (index, el) {
      var field_id = $(el).data('field-id');
      var options = $(el).find('.appbear-icons-wrap').data('options');
      if ($.inArray(field_id, fields) < 0 && options.load_with_ajax) {
        fields.push(field_id);
      }
    });

    $.each(fields, function (index, field_id) {
      appbear.load_icon_selector($('.appbear-field-id-' + field_id));
    });

    // $(document).on('click', function (event) {
    //   event.preventDefault();
    //   $('.appbear-icons-wrap').removeClass('d-block');
    //   $('.appbear-search-icon').removeClass('d-block');
    // });

    $(document).mouseup(function(e) {
        var container = $(".appbear-type-icon_selector .appbear-field .appbear-search-icon");

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
          container.removeClass('d-block');
        }

        var container = $(".appbear-type-icon_selector .appbear-field .appbear-icons-wrap");

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.removeClass('d-block');
        }
    });

    // NOTE: Display icons select options
    $(document).on('click', '.appbear-icon-actions', function (event) {
      event.stopPropagation();
      event.preventDefault();

      const
        $el = $(this),
        $parent = $el.parents('.appbear-type-icon_selector:first'),
        $field = $el.closest('.appbear-field'),
        $iconsWrap = $field.find('.appbear-icons-wrap'),
        $searchIcons = $field.find('.appbear-search-icon'),
        opts = $iconsWrap.data('options'),
        optSize = ~~String(opts.size || '36px').replace('px', ''),
        fieldID = $parent.data('field-id'),
        items = APPBEAR_JS._field_icons[fieldID] || false,
        itemsKeys = Object.keys(items);

      // NOTE: Debug line
      // console.info({fieldID, $el, $parent, $field, $iconsWrap, $searchIcons, opts, itemsKeys, items, size: optSize - 14});

      if (!!items) {
        if ($iconsWrap.find('.appbear-item-icon-selector').length === 0) {
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
            iconsHTML += `<div class="appbear-item-icon-selector" data-value='${dataValue}' data-key='${dataKey}' data-search='${iconClass}' data-type='${dataType}' style='width: ${opts.size}; height: ${opts.size}; font-size: ${fontSize}'>`;
            iconsHTML += $iconEl;
            iconsHTML += "</div>";
          }

          $iconsWrap.append(iconsHTML);
        }

        $iconsWrap.add($searchIcons).addClass('d-block');
      }
    });

    // NOTE: Search icons
    $(document).on('input', '.appbear-search-icon', function (event) {
      event.preventDefault();
      var value = $(this).val();
      var $container = $(this).closest('.appbear-field').find('.appbear-icons-wrap');
      appbear.filter_items(value, $container, '.appbear-item-icon-selector');
    });

    // NOTE: Icon select button action
    $(document).on('click', '.appbear-icon-actions .appbear-btn', function (event) {
      var value = $(this).data('search');
      var $container = $(this).closest('.appbear-field').find('.appbear-icons-wrap');
      appbear.filter_items(value, $container, '.appbear-item-icon-selector');
    });

    // NOTE: Select an icon
    $(document).on('click', '.appbear-icons-wrap .appbear-item-icon-selector', function (event) {
      const
        $field = $(this).closest('.appbear-field'),
        $container = $field.find('.appbear-icons-wrap'),
        options = $container.data('options'),
        $e = $(this),
        dataValue = $e.data('value'),
        elHTML = $e.html();

      $e.addClass(options.active_class).siblings().removeClass(options.active_class);
      $field.find('input.appbear-element').val(dataValue).trigger('change');
      $field.find('.appbear-icon-active').html(elHTML);
      $field.find('.appbear-icons-wrap').css('style', 'display:block  !important');

      $(".appbear-type-icon_selector .appbear-field .appbear-icons-wrap, .appbear-type-icon_selector .appbear-field .appbear-search-icon").removeClass('d-block');
    });
  };

  appbear.filter_items = function (value, $container, selector) {
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

  appbear.load_icon_selector = function ($field) {
    var options = $field.find('.appbear-icons-wrap').data('options');
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: APPBEAR_JS.ajax_url,
      data: {
        action: 'appbear_get_items',
        class_name: options.ajax_data.class_name,
        function_name: options.ajax_data.function_name,
        ajax_nonce: APPBEAR_JS.ajax_nonce
      },
      beforeSend: function () {
        $field.find('.appbear-icons-wrap').prepend("<i class='appbear-icon appbear-icon-spinner appbear-icon-spin appbear-loader'></i>");
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
                'class': 'appbear-item-icon-selector',
                'data-value': value,
                'data-key': key,
                'data-type': type
              });
              $new_item.html(html);
              $field.find('.appbear-icons-wrap').append($new_item);
            });
            $field.find('.appbear-icons-wrap .appbear-item-icon-selector').css({
              'width': options.size,
              'height': options.size,
              'font-size': parseInt(options.size) - 14,
            });
            //c($field.first().find('.appbear-icons-wrap .appbear-item-icon-selector').length);//total icons
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
      },
      complete: function (jqXHR, textStatus) {
        $field.find('.appbear-icons-wrap').find('.appbear-loader').remove();
      }
    });

    return '';
  };

  appbear.toggle_section = function (event) {
    event.stopPropagation();
    var $btn = $(this);
    var $section = $btn.closest('.appbear-section.appbear-toggle-1');
    var $section_body = $section.find('.appbear-section-body');
    var data_toggle = $section.data('toggle');
    var $icon = $section.find('.appbear-toggle-icon').first();
    if ($btn.hasClass('appbear-section-header') && data_toggle.target == 'icon') {
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

  appbear.toggle_import = function (event) {
    var $input = $(this);
    var $wrap_input_file = $('.appbear-wrap-input-file');
    var $wrap_input_url = $('.appbear-wrap-input-url');

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

  appbear.on_click_reset_values = function (event) {
    var $btn = $(this);
    var $appbear_form = $btn.closest('.appbear-form');
    $.appbearConfirm({
      title: APPBEAR_JS.text.reset_popup.title,
      content: APPBEAR_JS.text.reset_popup.content,
      confirm_class: 'appbear-btn-blue',
      confirm_text: APPBEAR_JS.text.popup.accept_button,
      cancel_text: APPBEAR_JS.text.popup.cancel_button,
      onConfirm: function () {
        $appbear_form.prepend('<input type="hidden" name="' + $btn.attr('name') + '" value="true">');
        $appbear_form.submit();
      },
      onCancel: function () {
        return false;
      }
    });
    return false;
  };

  appbear.on_click_import_values = function (event) {
    var $btn = $(this);
    var gutenbergEditor = !!$('body.block-editor-page').length;
    if( gutenbergEditor ){
      $appbear_form = $('.block-editor__container');//Gutenberg editor
    } else {
      var $appbear_form = $btn.closest('.appbear-form');//Admin pages
      if (!$appbear_form.length) {
        $appbear_form = $btn.closest('form#post');//Default wordpress editor
      }
    }
    var importInput = '<input type="hidden" name="' + $btn.attr('name') + '" value="true">';
    $.appbearConfirm({
      title: APPBEAR_JS.text.import_popup.title,
      content: APPBEAR_JS.text.import_popup.content,
      confirm_class: 'appbear-btn-blue',
      confirm_text: APPBEAR_JS.text.popup.accept_button,
      cancel_text: APPBEAR_JS.text.popup.cancel_button,
      onConfirm: function () {
        if( gutenbergEditor ){
          $('form.metabox-location-normal').prepend(importInput);
          var $temp_button = $appbear_form.find('button.editor-post-publish-panel__toggle');
          var delay = 100;
          if( $temp_button.length ){
            $temp_button.trigger('click');
            delay = 900;
          }
          setTimeout(function(){
            var $publish_button = $appbear_form.find('button.editor-post-publish-button');
            if( $publish_button.length ){
              $publish_button.trigger('click');
              setTimeout(function(){
                location.reload();
              }, 6000);
            }
          }, delay);
        } else {
          $appbear_form.prepend(importInput);
          $appbear_form.prepend('<input type="hidden" name="appbear-import2" value="yes">');
          setTimeout(function(){
            if ($appbear_form.find('#publish').length) {
              $appbear_form.find('#publish').click();
            } else {
              $appbear_form.submit();
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

  appbear.get_image_from_url = function (event) {
    var $btn = $(this);
    var $field = $btn.closest('.appbear-field');
    var $input = $field.find('.appbear-element-text');
    var $wrap_preview = $field.find('.appbear-wrap-preview');
    if (is_empty($input.val())) {
      $.appbearConfirm({
        title: APPBEAR_JS.text.validation_url_popup.title,
        content: APPBEAR_JS.text.validation_url_popup.content,
        confirm_text: APPBEAR_JS.text.popup.accept_button,
        hide_cancel: true
      });
      return false;
    }
    var image_class = $wrap_preview.data('image-class');
    var $new_item = $('<li />', { 'class': 'appbear-preview-item appbear-preview-image' });
    $new_item.html(
      '<img src="' + $input.val() + '" class="' + image_class + '">' +
      '<a class="appbear-btn appbear-btn-iconize appbear-btn-small appbear-btn-red appbear-remove-preview"><i class="appbear-icon appbear-icon-times-circle"></i></a>'
    );
    $wrap_preview.fadeOut(400, function () {
      $(this).html('').show();
    });
    $field.find('.appbear-get-image i').addClass('appbear-icon-spin');
    setTimeout(function () {
      $wrap_preview.html($new_item);
      $field.find('.appbear-get-image i').removeClass('appbear-icon-spin');
    }, 1200);
    return false;
  };

  appbear.load_oembeds = function (event) {
    $('.appbear-type-oembed').each(function (index, el) {
      if ($(el).find('.appbear-wrap-oembed').data('preview-onload')) {
        appbear.get_oembed($(el).find('.appbear-get-oembed'));
      }
    });
  };

  appbear.get_oembed = function (event) {
    var $btn;
    if ($(event.currentTarget).length) {
      $btn = $(event.currentTarget);
    } else {
      $btn = event;
    }
    var $field = $btn.closest('.appbear-field');
    var $input = $field.find('.appbear-element-text');
    var $wrap_preview = $field.find('.appbear-wrap-preview');
    if (is_empty($input.val()) && $(event.currentTarget).length) {
      $.appbearConfirm({
        title: APPBEAR_JS.text.validation_url_popup.title,
        content: APPBEAR_JS.text.validation_url_popup.content,
        confirm_text: APPBEAR_JS.text.popup.accept_button,
        hide_cancel: true
      });
      return false;
    }
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: APPBEAR_JS.ajax_url,
      data: {
        action: 'appbear_get_oembed',
        oembed_url: $input.val(),
        preview_size: $wrap_preview.data('preview-size'),
        ajax_nonce: APPBEAR_JS.ajax_nonce
      },
      beforeSend: function () {
        $wrap_preview.fadeOut(400, function () {
          $(this).html('').show();
        });
        $field.find('.appbear-get-oembed i').addClass('appbear-icon-spin');
      },
      success: function (response) {
        if (response) {
          if (response.success) {
            var $new_item = $('<li />', { 'class': 'appbear-preview-item appbear-preview-oembed' });
            $new_item.html(
              '<div class="appbear-oembed appbear-oembed-provider-' + response.provider + ' appbear-element-oembed ">' +
              response.oembed +
              '<a class="appbear-btn appbear-btn-iconize appbear-btn-small appbear-btn-red appbear-remove-preview"><i class="appbear-icon appbear-icon-times-circle"></i></a>' +
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
        $field.find('.appbear-get-oembed i').removeClass('appbear-icon-spin');
      }
    });
    return false;
  };

  appbear.wp_media_upload = function (event) {
    if (wp === undefined) {
      return;
    }
    var $btn = $(this);
    var media = appbear.media;
    media.$field = $btn.closest('.appbear-field');
    media.field_id = media.$field.closest('.appbear-row').data('field-id');
    media.frame_id = media.$field.closest('.appbear').attr('id') + '_' + media.field_id;
    media.$upload_btn = media.$field.find('.appbear-upload-file');
    media.$wrap_preview = media.$field.find('.appbear-wrap-preview');
    media.multiple = media.$field.hasClass('appbear-has-multiple');
    media.$preview_item = undefined;
    media.attachment_id = undefined;

    if ($btn.closest('.appbear-preview-item').length) {
      media.$preview_item = $btn.closest('.appbear-preview-item');
    } else if (!media.multiple) {
      media.$preview_item = media.$field.find('.appbear-preview-item').first();
    }
    if (media.$preview_item) {
      media.attachment_id = media.$preview_item.find('.appbear-attachment-id').val();
    }

    if (media.frames[media.frame_id] !== undefined) {
      media.frames[media.frame_id].open();
      return;
    }

    media.frames[media.frame_id] = wp.media({
      title: media.$field.closest('.appbear-type-file').find('.appbear-element-label').first().text(),
      multiple: media.multiple ? 'add' : false,
    });
    media.frames[media.frame_id].on('open', appbear.on_open_wp_media).on('select', appbear.on_select_wp_media);
    media.frames[media.frame_id].open();
  };

  appbear.on_open_wp_media = function (event) {
    var media = appbear.media;
    var selected_files = appbear.media.frames[media.frame_id].state().get('selection');
    if (is_empty(media.attachment_id)) {
      return selected_files.reset();
    }
    var wp_attachment = wp.media.attachment(media.attachment_id);
    wp_attachment.fetch();
    selected_files.set(wp_attachment ? [wp_attachment] : []);
  };

  appbear.on_select_wp_media = function (event) {
    var media = appbear.media;
    var selected_files = media.frames[media.frame_id].state().get('selection').toJSON();
    var preview_size = media.$wrap_preview.data('preview-size');
    var attach_name = media.$wrap_preview.data('field-name');
    var control_img_id = media.$field.closest('.appbear-type-group').find('.appbear-group-control').data('image-field-id');

    media.$field.trigger('appbear_before_add_files', [selected_files, appbear.media]);
    $(selected_files).each(function (index, obj) {
      var image = '';
      var inputs = '';
      var item_body = '';
      var $new_item = $('<li />', { 'class': 'appbear-preview-item appbear-preview-file' });

      if (obj.type == 'image') {
        $new_item.addClass('appbear-preview-image');
        item_body = '<img src="' + obj.url + '" style="width: ' + preview_size.width + '; height: ' + preview_size.height + '" data-full-img="' + obj.url + '" class="appbear-image appbear-preview-handler">';
      } else if (obj.type == 'video') {
        $new_item.addClass('appbear-preview-video');
        item_body = '<div class="appbear-video">';
        item_body += '<video controls style="width: ' + preview_size.width + '; height: ' + preview_size.height + '"><source src="' + obj.url + '" type="' + obj.mime + '"></video>';
        item_body += '</div>';
      } else {
        item_body = '<img src="' + obj.icon + '" class="appbear-preview-icon-file appbear-preview-handler"><a href="' + obj.url + '" class="appbear-preview-download-link">' + obj.filename + '</a><span class="appbear-preview-mime appbear-preview-handler">' + obj.mime + '</span>';
      }

      if (media.multiple) {
        inputs = '<input type="hidden" name="' + media.$upload_btn.data('field-name') + '" value="' + obj.url + '" class="appbear-element appbear-element-hidden">';
      }
      inputs += '<input type="hidden" name="' + attach_name + '" value="' + obj.id + '" class="appbear-attachment-id">';

      $new_item.html(inputs + item_body + '<a class="appbear-btn appbear-btn-iconize appbear-btn-small appbear-btn-red appbear-remove-preview"><i class="appbear-icon appbear-icon-times-circle"></i></a>');

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
        media.$field.find('.appbear-element').attr('value', obj.url);
        if (obj.type == 'image') {
          //Sincronizar con la imagen de control de un grupo
          if (media.field_id == control_img_id) {
            appbear.synchronize_selector_preview_image('.appbear-control-image', media.$wrap_preview, 'add', obj.url, control_img_id);
          }
          //Sincronizar con otros elementos
          appbear.synchronize_selector_preview_image('', media.$wrap_preview, 'add', obj.url, control_img_id);
        }
      }
    });
    media.$field.trigger('appbear_after_add_files', [selected_files, media]);
  };

  appbear.remove_preview_item = function (event) {
    var $btn = $(this);
    var $field = $btn.closest('.appbear-field');
    var field_id = $field.closest('.appbear-row').data('field-id');
    var control_data_img = $field.closest('.appbear-type-group').find('.appbear-group-control').data('image-field-id');
    var $wrap_preview = $field.find('.appbear-wrap-preview');
    var multiple = $field.hasClass('appbear-has-multiple');

    $field.trigger('appbear_before_remove_preview_item', [multiple]);

    if (!multiple) {
      $field.find('.appbear-element').attr('value', '');
    }
    $btn.closest('.appbear-preview-item').remove();

    if (!multiple && $btn.closest('.appbear-preview-item').hasClass('appbear-preview-image')) {
      if (field_id == control_data_img) {
        appbear.synchronize_selector_preview_image('.appbear-control-image', $wrap_preview, 'remove', '', control_data_img);
      }else{
        appbear.synchronize_selector_preview_image('', $wrap_preview, 'remove', '', control_data_img);
      }
    }
    $field.find('.appbear-element').trigger('change');
    $field.trigger('appbear_after_remove_preview_item', [multiple]);
    return false;
  };

  appbear.synchronize_selector_preview_image = function (selectors, $wrap_preview, action, value, control_img_id = null) {
    selectors = selectors || $wrap_preview.data('synchronize-selector');
    if (!is_empty(selectors)) {
      selectors = selectors.split(',');
      $.each(selectors, function (index, selector) {
        var $element = $(this);
        if ($element.closest('.appbear-type-group').length) {
          if ($element.closest('.appbear-type-group').find('.appbear-group-control').length) {
            $element = $element.closest('.appbear-group-control-item.appbear-active').find(selector);
          } else {
            $element = $element.closest('.appbear-group-item.appbear-active').find(selector);
          }
        }
        if ($element.is('img')) {
          $element.fadeOut(300, function () {
            if ($element.closest('.appbear-group-control').length) {
              $element.attr('src', value);
            } else {
              $element.attr('src', value);
            }
          });
        } else {
          $element.closest('.appbear-type-group').find('.appbear-group-control[data-image-field-id="'+control_img_id+'"] .appbear-group-control-item.appbear-active').find(selector).fadeOut(300, function () {
            if ($element.closest('.appbear-type-group').find('.appbear-group-control').length) {
              if(control_img_id != null){
                $element.closest('.appbear-type-group').find('.appbear-group-control[data-image-field-id="'+control_img_id+'"] .appbear-group-control-item.appbear-active').find(selector).css('background-image', 'url(' + value + ')');
              }else{
                $element.css('background-image', 'url(' + value + ')');
              }
            } else {
              if(control_img_id != null){
                $element.closest('.appbear-type-group').find('.appbear-group-control[data-image-field-id="'+control_img_id+'"] .appbear-group-control-item.appbear-active').find(selector).css('background-image', 'url(' + value + ')');
              }else{
                $element.css('background-image', 'url(' + value + ')');
              }
            }
          });
        }
        if (action == 'add') {
          $element.fadeIn(300);
        }
        var $input = $element.closest('.appbear-field').find('input.appbear-element');
        if ($input.length) {
          $input.attr('value', value);
        }

        var $close_btn = $element.closest('.appbear-preview-item').find('.appbear-remove-preview');
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

  appbear.reinit_js_plugins = function ($new_element) {
    //Inicializar Tabs
    $new_element.find('.appbear-tab').each(function (iterator, item) {
      appbear.init_tab($(item));
    });

    //Inicializar Switcher
    $new_element.find('.appbear-type-switcher input.appbear-element').each(function (iterator, item) {
      $(item).appbearSwitcher('destroy');
      appbear.init_switcher($(item));
    });

    //Inicializar Spinner
    $new_element.find('.appbear-type-number .appbear-field.appbear-has-spinner').each(function (iterator, item) {
      appbear.init_spinner($(item));
    });

    //Inicializar radio buttons y checkboxes
    $new_element.find('.appbear-has-icheck .appbear-radiochecks.init-icheck').each(function (iterator, item) {
      appbear.destroy_icheck($(item));
      appbear.init_checkbox($(item));
    });

    //Inicializar Colorpicker
    $new_element.find('.appbear-colorpicker-color').each(function (iterator, item) {
      appbear.init_colorpicker($(item));
    });

    //Inicializar Dropdown
    $new_element.find('.ui.selection.dropdown').each(function (iterator, item) {
      appbear.init_dropdown($(item));
    });

    //Inicializar Sortables de grupos
    $new_element.find('.appbear-group-control.appbear-sortable').each(function (iterator, item) {
      appbear.init_sortable_group_items($(item));
    });

    //Inicializar Sortable de items repetibles
    $new_element.find('.appbear-repeatable-wrap.appbear-sortable').each(function (iterator, item) {
      appbear.init_sortable_repeatable_items($(item));
    });

    //Inicializar Sortable de preview items
    $new_element.find('.appbear-wrap-preview-multiple').each(function (iterator, item) {
      appbear.init_sortable_preview_items($(item));
    });

    //Inicializar Ace editor
    $new_element.find('.appbear-code-editor').each(function (iterator, item) {
      appbear.destroy_ace_editor($(item));
      appbear.init_code_editor($(item));
    });

    //Inicializar Tooltip
    appbear.init_tooltip($new_element.find('.appbear-tooltip-handler'));
  };


  appbear.destroy_wp_editor = function ($selector) {
    if (typeof tinyMCEPreInit === 'undefined' || typeof tinymce === 'undefined' || typeof QTags == 'undefined') {
      return;
    }

    //Destroy editor
    $selector.find('.quicktags-toolbar, .mce-tinymce.mce-container').remove();
    tinymce.execCommand('mceRemoveEditor', true, $selector.find('.wp-editor-area').attr('id'));

    //Register editor to init
    $selector.addClass('init-wp-editor');
  };

  appbear.on_init_wp_editor = function (wp_editor, args) {
    $('.appbear').trigger('appbear_on_init_wp_editor', wp_editor, args);
  };

  appbear.on_setup_wp_editor = function (wp_editor) {
    $('.appbear').trigger('appbear_on_setup_wp_editor', wp_editor);
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

  appbear.init_wp_editor = function ($selector) {
    if (typeof tinyMCEPreInit === 'undefined' || typeof tinymce === 'undefined' || typeof QTags == 'undefined') {
      return;
    }
    $selector.removeClass('init-wp-editor');
    $selector.removeClass('html-active').addClass('tmce-active');
    var $textarea = $selector.find('.wp-editor-area');
    var ed_id = $textarea.attr('id');
    var old_ed_id = $selector.closest('.appbear-group-wrap').find('.appbear-group-item').eq(0).find('.wp-editor-area').first().attr('id');

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
        appbear.on_setup_wp_editor(wp_editor);//php class-field.php set_args();
        wp_editor.on('init', function(args) {
          appbear.on_init_wp_editor(wp_editor, args);
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

  appbear.init_switcher = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-type-switcher input.appbear-element') : $selector;
    $selector.appbearSwitcher();
  };

  appbear.init_spinner = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-type-number .appbear-field.appbear-has-spinner') : $selector;
    $selector.spinnerNum('delay', 300);
    $selector.spinnerNum('changing', function (e, newVal, oldVal) {
      $(this).trigger('appbear_changed_value', newVal);
    });
  };

  appbear.init_tab = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-tab') : $selector;
    $selector.each(function(index, el){
      var $tab = $(el);
      if( $tab.closest('.appbear-source-item').length ){
        return;//continue each
      }
      $tab.find('.appbear-tab-nav .appbear-item').removeClass('active');
      $tab.find('.appbear-accordion-title').remove();

      var type_tab = 'responsive';
      if ($tab.closest('#side-sortables').length) {
        type_tab = 'accordion';
      }
      $tab.appbearTabs({
        collapsible: true,
        type: type_tab
      });
    });
  };

  appbear.init_tooltip = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-tooltip-handler') : $selector;
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
          $(e.tipso_bubble).addClass($(el).closest('.appbear').data('skin'));
        },
        onShow: function ($element, element, e) {
          //$(e.tipso_bubble).removeClass('top').addClass(position);
        },
        //hideDelay: 1000000
      });
    });
  };

  appbear.init_checkbox = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-has-icheck .appbear-radiochecks.init-icheck') : $selector;
    $selector.find('input').iCheck({
      radioClass: 'iradio_flat-blue',
      checkboxClass: 'icheckbox_flat-blue',
    });
  };

  appbear.destroy_icheck = function ($selector) {
    $selector.find('input').each(function (index, input) {
      $(input).attr('style', '');
      $(input).next('ins').remove();
      $(input).unwrap();
    });
  };

  appbear.init_image_selector = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-type-image_selector .init-image-selector, .appbear-type-import .init-image-selector') : $selector;
    $selector.appbearImageSelector({
      active_class: 'appbear-active'
    });
  };

  appbear.init_dropdown = function ($selector) {
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

  appbear.on_focusout_input_colorpicker = function () {
    var $field = $(this).closest('.appbear-field');
    var value = $(this).val();
    $(this).attr('value', value);
    $field.find('.appbear-colorpicker-color').attr('value', value).css('background-color', value);
    return false;
  };

  appbear.set_default_value_colorpicker = function () {
    var $field = $(this).closest('.appbear-field');
    var value = $field.data('default');
    if (value) {
      $field.find('input.appbear-element').attr('value', value);
      $field.find('.appbear-colorpicker-color').attr('value', value).css('background-color', value);
    }
  };

  appbear.init_colorpicker = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-colorpicker-color') : $selector;
    $selector.colorPicker({
      cssAddon: '.cp-color-picker {margin-top:6px;}',
      buildCallback: function ($elm) {
      },
      renderCallback: function ($elm, toggled) {
        var $field = $elm.closest('.appbear-field');
        this.$UI.find('.cp-alpha').toggle($field.hasClass('appbear-has-alpha'));
        var value = this.color.toString('rgb', true);
        if (!$field.hasClass('appbear-has-alpha')) {//|| value.endsWith(', 1)')
          value = '#' + this.color.colors.HEX;
        }
        value = value.indexOf('NAN') > -1 ? '' : value;
        $field.find('input').attr('value', value);
        $field.find('.appbear-colorpicker-color').attr('value', value).css('background-color', value);

        //Para la gestión de eventos
        $field.find('input').trigger('change');
      }
    });
  };

  appbear.destroy_ace_editor = function ($selector) {
    var $textarea = $selector.closest('.appbear-field').find('textarea.appbear-element');
    $selector.text($textarea.val());
  };

  appbear.init_code_editor = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-code-editor') : $selector;
    $selector.each(function (index, el) {
      var editor = ace.edit($(el).attr('id'));
      var language = $(el).data('language');
      var theme = $(el).data('theme');
      editor.setTheme("ace/theme/" + theme);
      editor.getSession().setMode("ace/mode/" + language);
      editor.setFontSize(15);
      editor.setShowPrintMargin(false);
      editor.getSession().on('change', function (e) {
        $(el).closest('.appbear-field').find('textarea.appbear-element').text(editor.getValue());
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

  appbear.init_sortable_preview_items = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-wrap-preview-multiple') : $selector;
    $selector.sortable({
      items: '.appbear-preview-item',
      placeholder: "appbear-preview-item appbear-sortable-placeholder",
      start: function (event, ui) {
        ui.placeholder.css({
          'width': ui.item.css('width'),
          'height': ui.item.css('height'),
        });
      },
    }).disableSelection();
  };

  appbear.init_sortable_checkbox = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-has-icheck .appbear-radiochecks.init-icheck.appbear-sortable') : $selector;
    $selector.sortable({
      items: '>label',
      placeholder: "appbear-icheck-sortable-item appbear-sortable-placeholder",
      start: function (event, ui) {
        ui.placeholder.css({
          'width': ui.item.css('width'),
          'height': ui.item.css('height'),
        });
      },
    }).disableSelection();
  };

  appbear.init_sortable_repeatable_items = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-repeatable-wrap.appbear-sortable') : $selector;
    $selector.sortable({
      handle: '.appbear-sort-item',
      items: '.appbear-repeatable-item',
      placeholder: "appbear-repeatable-item appbear-sortable-placeholder",
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

  appbear.init_sortable_group_items = function ($selector) {
    $selector = is_empty($selector) ? $('.appbear-group-control.appbear-sortable') : $selector;
    $selector.sortable({
      items: '.appbear-group-control-item',
      placeholder: "appbear-sortable-placeholder",
      start: function (event, ui) {
        ui.placeholder.css({
          'width': ui.item.css('width'),
          'height': ui.item.css('height'),
        });
      },
      update: function (event, ui) {
        var $group_control = $(event.target);
        var $group_wrap = $group_control.next('.appbear-group-wrap');

        var old_index = ui.item.attr('data-index');
        var new_index = $group_control.find('.appbear-group-control-item').index(ui.item);
        var $group_item = $group_wrap.children('.appbear-group-item[data-index=' + old_index + ']');
        var $group_item_reference = $group_wrap.children('.appbear-group-item[data-index=' + new_index + ']');
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

        $group_wrap.trigger('appbear_on_sortable_group_item', [old_index, new_index]);

        $group_control.trigger('sort_group_control_items');

        $group_wrap.trigger('sort_group_items', [start_index, end_index]);

        //Click event, to initialize some fields -> (WP Editors)
        if (ui.item.hasClass('appbear-active')) {
          ui.item.trigger('click');
        }
      }
    }).disableSelection();
  };

  appbear.add_repeatable_item = function (event) {
    var $btn = $(this);
    var $repeatable_wrap = $btn.closest('.appbear-repeatable-wrap');
    $repeatable_wrap.trigger('appbear_before_add_repeatable_item');

    var $source_item = $btn.prev('.appbear-repeatable-item');
    var index = parseInt($source_item.data('index'));
    var $cloned = $source_item.clone();
    var $new_item = $('<div />', { 'class': $cloned.attr('class'), 'data-index': index + 1, 'style': 'display: none' });

    appbear.set_changed_values($cloned, $repeatable_wrap.closest('.appbear-row').data('field-type'));

    $new_item.html($cloned.html());
    $source_item.after($new_item);
    $new_item.slideDown(150, function () {
      //Ordenar y cambiar ids y names
      $repeatable_wrap.trigger('sort_repeatable_items');
      //Actualizar eventos
      appbear.reinit_js_plugins($new_item);
    });
    $repeatable_wrap.trigger('appbear_after_add_repeatable_item');
    return false;
  };

  appbear.remove_repeatable_item = function (event) {
    var $repeatable_wrap = $(this).closest('.appbear-repeatable-wrap');
    if ($repeatable_wrap.find('.appbear-repeatable-item').length > 1) {
      $repeatable_wrap.trigger('appbear_before_remove_repeatable_item');
      var $item = $(this).closest('.appbear-repeatable-item');
      $item.slideUp(150, function () {
        $item.remove();
        $repeatable_wrap.trigger('sort_repeatable_items');
        $repeatable_wrap.trigger('appbear_after_remove_repeatable_item');
      });
    }
    return false;
  };

  appbear.sort_repeatable_items = function (event) {
    var $repeatable_wrap = $(event.target);
    var row_level = parseInt($repeatable_wrap.closest('[class*="appbear-row"]').data('row-level'));

    $repeatable_wrap.find('.appbear-repeatable-item').each(function (index, item) {
      appbear.update_attributes($(item), index, row_level);

      //Destroy WP Editors
      $(item).find('.wp-editor-wrap').each(function (index, el) {
        appbear.destroy_wp_editor($(el));
      });
      appbear.update_fields_on_item_active($(item));
    });
  };

  appbear.new_group_item = function (event) {
    if ($(event.currentTarget).hasClass('appbear-duplicate-group-item')) {
      appbear.duplicate = true;
      event.stopPropagation();
    } else {
      appbear.duplicate = false;
    }
    var $group = $(this).closest('.appbear-type-group');
    var $control_item = appbear.add_group_control_item(event, $(this));
    var $group_item = appbear.add_group_item(event, $(this));

    var args = {
      event: event,
      $btn: $(this),
      $group: $group,
      duplicate: appbear.duplicate,
      $group_item: $group_item,
      $control_item: $control_item,
      index: $group_item.data('index'),
      type: $group_item.data('type')
    };

    $group.trigger('appbear_after_add_group_item', [args]);

    //Active new item
    $control_item.trigger('click');

    return false;
  };

  appbear.add_group_control_item = function (event, $btn) {
    var item_type = $btn.data('item-type');
    var $group = $btn.closest('.appbear-type-group');
    var $group_wrap = $group.find('.appbear-group-wrap').first();
    var $group_control = $btn.closest('.appbear-type-group').find('.appbear-group-control').first();
    var $source_item = $group_control.find('.appbear-group-control-item').last();
    var index = -1;
    if ($source_item.length) {
      index = $source_item.data('index');
    }
    $source_item = $group_wrap.next('.appbear-source-item').find('.appbear-group-control-item');

    if (appbear.duplicate) {
      index = $btn.closest('.appbear-group-control-item').index();
      $source_item = $group_control.children('.appbear-group-control-item').eq(index);
      item_type = $source_item.find('.appbear-input-group-item-type').val();
    }
    index = parseInt(index);
    var args = {
      event: event,
      $btn: $btn,
      $group: $group,
      duplicate: appbear.duplicate,
      $group_item: $group_wrap.children('.appbear-group-item').eq(index),
      $control_item: $source_item,
      index: index,
      type: item_type
    };
    $group.trigger('appbear_before_add_group_item', [args]);

    var row_level = parseInt($source_item.closest('.appbear-row').data('row-level'));
    var $cloned = $source_item.clone();
    var $new_item = $('<li />', { 'class': $cloned.attr('class'), 'data-index': index + 1, 'data-type': item_type });

    $new_item.html($cloned.html());
    $source_item.after($new_item);

    //Add new item
    if (index == -1) {
      $group_control.append($new_item);
    } else {
      $group_control.children('.appbear-group-control-item').eq(index).after($new_item);
    }
    $new_item = $group_control.children('.appbear-group-control-item').eq(index + 1);

    $new_item.alterClass('control-item-type-*', 'control-item-type-' + item_type);
    $new_item.find('input.appbear-input-group-item-type').val(item_type);
    $group_control.trigger('sort_group_control_items');

    if (appbear.duplicate === false && $new_item.find('.appbear-control-image').length) {
      $new_item.find('.appbear-control-image').css('background-image', 'url()');
    }
    if (appbear.duplicate === false) {
      var $input = $new_item.find('.appbear-inner input');
      if ($input.length) {
        var value = $group_control.data('control-name').toString();
        $input.attr('value', value.replace(/(#\d?)/g, '#' + (index + 2)));
        if ($btn.hasClass('appbear-custom-add')) {
          $input.attr('value', $btn.text());
        }
      }
    }
    return $new_item;
  };

  appbear.add_group_item = function (event, $btn) {
    var item_type = $btn.data('item-type');
    var $group_wrap = $btn.closest('.appbear-type-group').find('.appbear-group-wrap').first();
    var $source_item = $group_wrap.children('.appbear-group-item').last();
    var index = -1;
    if ($source_item.length) {
      index = $source_item.data('index');
    }
    $source_item = $group_wrap.next('.appbear-source-item').find('.appbear-group-item');

    if (appbear.duplicate) {
      index = $btn.closest('.appbear-group-control-item').index();
      $source_item = $group_wrap.children('.appbear-group-item').eq(index);
      item_type = $btn.closest('.appbear-group-control-item').find('.appbear-input-group-item-type').val();
    }

    index = parseInt(index);
    var row_level = parseInt($source_item.closest('.appbear-row').data('row-level'));
    var $cloned = $source_item.clone();
    var $cooked_item = appbear.cook_group_item($cloned, row_level, index);
    var $new_item = $('<div />', { 'class': $cloned.attr('class'), 'data-index': index + 1, 'data-type': item_type });
    $new_item.html($cooked_item.html());
    //Add new item
    if (index == -1) {
      $group_wrap.append($new_item);
    } else {
      $group_wrap.children('.appbear-group-item').eq(index).after($new_item);
    }
    $new_item = $group_wrap.children('.appbear-group-item').eq(index + 1);
    $new_item.alterClass('group-item-type-*', 'group-item-type-' + item_type);
    $group_wrap.trigger('sort_group_items', [index + 1]);

    //Actualizar eventos
    appbear.reinit_js_plugins($new_item);

    if (appbear.duplicate === false) {
      //appbear.set_default_values( $new_item );//Ya no es necesario por el nuevo source item
    }
    return $new_item;
  };

  appbear.cook_group_item = function ($group_item, row_level, prev_index) {
    var index = prev_index + 1;

    if (appbear.duplicate) {
      appbear.set_changed_values($group_item);
    } else {
      //No es duplicado, restaurar todo, eliminar items de grupos internos
      $group_item.find('.appbear-group-wrap').each(function (index, wrap_group) {
        $(wrap_group).find('.appbear-group-item').first().addClass('appbear-active').siblings().remove();
        $(wrap_group).prev('.appbear-group-control').children('.appbear-group-control-item').first().addClass('appbear-active').siblings().remove();
      });
      $group_item.find('.appbear-repeatable-wrap').each(function (index, wrap_repeat) {
        $(wrap_repeat).find('.appbear-repeatable-item').not(':first').remove();
      });
    }

    appbear.update_attributes($group_item, index, row_level);

    return $group_item;
  };

  appbear.set_changed_values = function ($new_item, field_type) {
    var $textarea, $input;
    $new_item.find('.appbear-field').each(function (iterator, item) {
      var type = field_type || $(item).closest('.appbear-row').data('field-type');
      switch (type) {
        case 'text':
        case 'number':
        case 'oembed':
        case 'file':
        case 'image':
          $input = $(item).find('input.appbear-element');
          $input.attr('value', $input.val());
          break;
      }
    });
  };

  appbear.remove_group_item = function (event) {
    event.preventDefault();
    event.stopPropagation();
    var $btn = $(this);
    var $row = $btn.closest('.appbear-type-group');
    var $group_wrap = $row.find('.appbear-group-wrap').first();
    var $group_control = $btn.closest('.appbear-group-control');
    var index = $btn.closest('.appbear-group-control-item').data('index');

    $.appbearConfirm({
      title: APPBEAR_JS.text.remove_item_popup.title,
      content: APPBEAR_JS.text.remove_item_popup.content,
      confirm_class: 'appbear-btn-blue',
      confirm_text: APPBEAR_JS.text.popup.accept_button,
      cancel_text: APPBEAR_JS.text.popup.cancel_button,
      onConfirm: function () {
        setTimeout(function () {
          appbear.remove_group_control_item($btn);
          appbear._remove_group_item($btn);
        }, appbear.delays.removeItem.confirm);

        setTimeout(function () {
          $group_wrap.trigger('sort_group_items', [index]);
          $group_control.children('.appbear-group-control-item').eq(0).trigger('click');
          $group_control.trigger('sort_group_control_items');
        }, appbear.delays.removeItem.events);
      }
    });
    return false;
  };

  appbear.remove_group_items = function (items) {
    if( ! items.length ){
      return;
    }
    var $row, $group_wrap, $group_control;
    $.appbearConfirm({
      title: APPBEAR_JS.text.remove_item_popup.title,
      content: APPBEAR_JS.text.remove_item_popup.content,
      confirm_class: 'appbear-btn-blue',
      confirm_text: APPBEAR_JS.text.popup.accept_button,
      cancel_text: APPBEAR_JS.text.popup.cancel_button,
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
              $row = $element.closest('.appbear-type-group');
              $group_wrap = $row.find('.appbear-group-wrap').first();
              $group_control = $element.closest('.appbear-group-control');
            }
            appbear.remove_group_control_item($element);
            appbear._remove_group_item($element);
          });
        }, appbear.delays.removeItem.confirm);

        setTimeout(function () {
          $group_wrap.trigger('sort_group_items', [min_index]);
          $group_control.children('.appbear-group-control-item').eq(0).trigger('click');
          $group_control.trigger('sort_group_control_items');
        }, appbear.delays.removeItem.events);
      }
    });
  };

  appbear.remove_group_control_item = function ($btn) {
    var $item = $btn.closest('.appbear-group-control-item');
    $item.fadeOut(appbear.delays.removeItem.fade, function () {
      $item.remove();
    });
  };

  appbear._remove_group_item = function ($btn) {
    var $row = $btn.closest('.appbear-type-group');
    var $group_wrap = $row.find('.appbear-group-wrap').first();
    var index = $btn.closest('.appbear-group-control-item').data('index');
    $row.trigger('appbear_before_remove_group_item');
    var $item = $group_wrap.children('.appbear-group-item[data-index="'+index+'"]');
    var type = $item.data('type');
    $item.fadeOut(appbear.delays.removeItem.fade, function () {
      $item.remove();
      // $group_wrap.trigger('sort_group_items', [index]);
      $row.trigger('appbear_after_remove_group_item', [index, type]);
      // $group_control.children('.appbear-group-control-item').eq(0).trigger('click');
    });
  };

  appbear.on_click_group_control_item = function (event) {
    var $control_item = $(this);
    appbear.active_control_item(event, $control_item);
    return false;
  };

  appbear.active_control_item = function (event, $control_item) {
    var $group_control = $control_item.parent();
    var index = $control_item.index();
    var $group = $group_control.closest('.appbear-type-group');
    var $group_wrap = $group.find('.appbear-group-wrap').first();
    var $group_item = $group_wrap.children('.appbear-group-item').eq(index);
    var $old_control_item = $group_control.children('.appbear-active');

    $group_control.children('.appbear-group-control-item').removeClass('appbear-active');
    $control_item.addClass('appbear-active');

    $group_wrap.children('.appbear-group-item').removeClass('appbear-active');
    $group_item.addClass('appbear-active');

    var args = {
      $group_item: $group_item,
      $control_item: $control_item,
      index: $group_item.data('index'),
      type: $group_item.data('type'),
      event: event,
      old_index: $old_control_item.data('index'),
    };

    setTimeout(function(){
      $group.trigger('appbear_on_active_group_item', [args]);
      appbear.update_fields_on_item_active($group_item);
    }, 10);//Retardar un poco para posibles eventos on click desde otras aplicaciones
    return false;
  };

  appbear.update_fields_on_item_active = function ($group_item) {
    //Init WP Editor
    $group_item.find('.wp-editor-wrap.init-wp-editor').each(function (index, el) {
      appbear.init_wp_editor($(el));
    });
  };

  appbear.sort_group_control_items = function (event) {
    var $group_control = $(event.target);
    var row_level = parseInt($group_control.closest('.appbear-row').data('row-level'));
    $group_control.children('.appbear-group-control-item').each(function (index, item) {
      appbear.update_group_control_item($(item), index, row_level);
    });
  };

  appbear.sort_group_items = function (event, start_index, end_index) {
    var $group_wrap = $(event.target);
    $group_wrap.trigger('appbear_before_sort_group');
    var row_level = parseInt($group_wrap.closest('.appbear-row').data('row-level'));
    end_index = end_index !== undefined ? parseInt(end_index) + 1 : undefined;

    var $items = $group_wrap.children('.appbear-group-item');
    var $items_to_sort = $items.slice(start_index, end_index);

    $items_to_sort.each(function (i, group_item) {
      var index = $group_wrap.find($(group_item)).index();
      appbear.update_attributes($(group_item), index, row_level);

      //Destroy WP Editors
      $(group_item).find('.wp-editor-wrap').each(function (index, el) {
        appbear.destroy_wp_editor($(el));
      });
    });
    $group_wrap.trigger('appbear_after_sort_group');
  };

  appbear.update_group_control_item = function ($item, index, row_level) {
    $item.data('index', index).attr('data-index', index);
    $item.find('.appbear-info-order-item').text('#' + (index + 1));
    var value;
    if ($item.find('.appbear-inner input').length) {
      value = $item.find('.appbear-inner input').val();
      $item.find('.appbear-inner input').val(value.replace(/(#\d+)/g, '#' + (index + 1)));
    }

    //Cambiar names
    $item.find('*[name]').each(function (i, item) {
      appbear.update_name_ttribute($(item), index, row_level);
    });
  };

  appbear.update_attributes = function ($new_item, index, row_level) {
    $new_item.data('index', index).attr('data-index', index);

    $new_item.find('*[name]').each(function (i, item) {
      appbear.update_name_ttribute($(item), index, row_level);
    });

    $new_item.find('*[id]').each(function (i, item) {
      appbear.update_id_attribute($(item), index, row_level);
    });

    $new_item.find('label[for]').each(function (i, item) {
      appbear.update_for_attribute($(item), index, row_level);
    });

    $new_item.find('*[data-field-name]').each(function (i, item) {
      appbear.update_data_name_attribute($(item), index, row_level);
    });

    $new_item.find('*[data-editor]').each(function (i, item) {
      appbear.update_data_editor_attribute($(item), index, row_level);
    });

    $new_item.find('*[data-wp-editor-id]').each(function (i, item) {
      appbear.update_data_wp_editor_id_attribute($(item), index, row_level);
    });

    appbear.set_checked_inputs($new_item, row_level);
  };

  appbear.set_checked_inputs = function ($group_item, row_level) {
    $group_item.find('.appbear-field').each(function (iterator, item) {
      if ($(item).hasClass('appbear-has-icheck') || $(item).closest('.appbear-type-image_selector').length) {
        var $input = $(item).find('input[type="radio"], input[type="checkbox"]');
        $input.each(function (i, input) {
          if ($(input).parent('div').hasClass('checked')) {
            $(input).attr('checked', 'checked').prop('checked', true);
          } else {
            $(input).removeAttr('checked').prop('checked', false);
          }
          if ($(input).next('img').hasClass('appbear-active')) {
            $(input).attr('checked', 'checked').prop('checked', true);
          }
        });
      }
    });
  };

  appbear.update_name_ttribute = function ($el, index, row_level) {
    var old_name = $el.attr('name');
    var new_name = '';
    if (typeof old_name !== 'undefined') {
      new_name = appbear.nice_replace(/(\[\d+\])/g, old_name, '[' + index + ']', row_level);
      $el.attr('name', new_name);
    }
  };

  appbear.update_id_attribute = function ($el, index, row_level) {
    var old_id = $el.attr('id');
    var new_id = '';
    if (typeof old_id !== 'undefined') {
      new_id = appbear.nice_replace(/(__\d+__)/g, old_id, '__' + index + '__', row_level);
      $el.attr('id', new_id);
    }
  };

  appbear.update_for_attribute = function ($el, index, row_level) {
    var old_for = $el.attr('for');
    var new_for = '';
    if (typeof old_for !== 'undefined') {
      new_for = appbear.nice_replace(/(__\d+__)/g, old_for, '__' + index + '__', row_level);
      $el.attr('for', new_for);
    }
  };
  appbear.update_data_name_attribute = function ($el, index, row_level) {
    var old_data = $el.attr('data-field-name');
    var new_data = '';
    if (typeof old_data !== 'undefined') {
      new_data = appbear.nice_replace(/(\[\d+\])/g, old_data, '[' + index + ']', row_level);
      $el.attr('data-field-name', new_data);
    }
  };

  appbear.update_data_editor_attribute = function ($el, index, row_level) {
    var old_data = $el.attr('data-editor');
    var new_data = '';
    if (typeof old_data !== 'undefined') {
      new_data = appbear.nice_replace(/(__\d+__)/g, old_data, '__' + index + '__', row_level);
      $el.attr('data-editor', new_data);
    }
  };
  appbear.update_data_wp_editor_id_attribute = function ($el, index, row_level) {
    var old_data = $el.attr('data-wp-editor-id');
    var new_data = '';
    if (typeof old_data !== 'undefined') {
      new_data = appbear.nice_replace(/(__\d+__)/g, old_data, '__' + index + '__', row_level);
      $el.attr('data-wp-editor-id', new_data);
    }
  };

  appbear.set_default_values = function ($group) {
    $group.find('*[data-default]').each(function (iterator, item) {
      var $field = $(item);
      var default_value = $field.data('default');
      if ($field.closest('.appbear-type-number').length) {
        appbear.set_field_value($field, default_value);
      } else {
        appbear.set_field_value($field, default_value);
      }
    });
  };

  appbear.set_field_value = function ($field, value, extra_value, update_initial_values) {
    if( !$field.length ){
      return;
    }
    var $input, array;
    var type = $field.closest('.appbear-row').data('field-type');
    value = is_empty(value) ? '' : value;

    switch (type) {
      case 'number':
        var $input = $field.find('input.appbear-element');
        //Ctrl + z functionality
        appbear.update_prev_values($input, value, update_initial_values);

        if (value == $input.val()) {
          return;
        }

        $input.attr('value', value);
        var unit = extra_value === undefined ? $input.data('default-unit') : extra_value;
        $field.find('input.appbear-unit-number').attr('value', unit).trigger('change');
        unit = unit || '#';
        $field.find('.appbear-unit span').text(unit);
        break;

      case 'text':
      case 'hidden':
      case 'colorpicker':
      case 'date':
      case 'time':
        var $input = $field.find('input.appbear-element');

        //Ctrl + z functionality
        appbear.update_prev_values($input, value, update_initial_values);

        if (value == $input.val()) {
          return;
        }
        $input.attr('value', value).trigger('change').trigger('input');
        if (type == 'colorpicker') {
          $field.find('.appbear-colorpicker-color').attr('value', value).css('background-color', value);
        }
        break;

      case 'file':
      case 'oembed':
        var $input = $field.find('input.appbear-element');

        //Ctrl + z functionality
        appbear.update_prev_values($input, value, update_initial_values);

        $input.attr('value', value).trigger('change').trigger('input');
        $field.find('.appbear-wrap-preview').html('');
        break;

      case 'image':
        $field.find('input.appbear-element').attr('value', value);
        $field.find('img.appbear-element-image').attr('src', value);
        if (is_empty(value)) {
          $field.find('img.appbear-element-image').hide().next('.appbear-remove-preview').hide();
        }
        break;

      case 'select':
        var $input = $field.find('.appbear-element input[type="hidden"]');

        //Ctrl + z functionality
        appbear.update_prev_values($input, value, update_initial_values);

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
        appbear.update_prev_values($input, value, update_initial_values);

        if ($input.val() !== value) {
          if ($input.next().hasClass('appbear-sw-on')) {
            $input.appbearSwitcher('set_off');
          } else {
            $input.appbearSwitcher('set_on');
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
        $field.find('textarea.appbear-element').text(value);
        var editor = ace.edit($field.find('.appbear-code-editor').attr('id'));
        editor.setValue(value);
        break;

      case 'icon_selector':
        $field.find('input.appbear-element').attr('value', value).trigger('change');
        var html = '';
        if (value.indexOf('.svg') > -1) {
          html = '<img src="' + value + '">';
        } else {
          html = '<i class="' + value + '"></i>';
        }
        $field.find('.appbear-icon-active').html(html);
        break;

      case 'image_selector':
        value = value.toString().toLowerCase();
        $input = $field.find('input');

        if (!$input.closest('.appbear-image-selector').data('image-selector').like_checkbox) {
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
        if ($field.hasClass('appbear-has-icheck') && $field.find('.init-icheck').length) {
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

  appbear.update_prev_values = function ($input, value, update_initial_values) {
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

  appbear.nice_replace = function (regex, string, replace_with, row_level, offset) {
    offset = offset || 0;
    //http://stackoverflow.com/questions/10584748/find-and-replace-nth-occurrence-of-bracketed-expression-in-string
    var n = 0;
    string = string.replace(regex, function (match, i, original) {
      n++;
      return (n === row_level + offset) ? replace_with : match;
    });
    return string;
  };

  appbear.get_object_id = function () {
    return $('.appbear').data('object-id');
  };

  appbear.get_object_type = function () {
    return $('.appbear').data('object-type');
  };

  appbear.get_group_object_values = function ($group_item) {
    var values = $group_item.find('input[name],select[name],textarea[name]').serializeArray();
    return values;
  };

  appbear.get_group_values = function ($group_item) {
    var object_values = appbear.get_group_object_values($group_item);
    var values = {};
    $.each(object_values, function (index, field) {
      values[field.name] = field.value;
    });
    return values;
  };

  appbear.compare_values_by_operator = function (value1, operator, value2) {
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

  appbear.add_style_attribute = function ($element, new_style) {
    var old_style = $element.attr('style') || '';
    $element.attr('style', old_style + '; ' + new_style);
  };

  appbear.is_image_file = function (value) {
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
    appbear.init();
  });

  return appbear;

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
  $('.appbear .appbear-type-icon_selector .appbear-icon-active.appbear-item-icon-selector').on('click', function(){
    alert('test');
    $('.appbear .appbear-type-icon_selector .appbear-search-icon, .appbear .appbear-type-icon_selector .appbear-icons-wrap').css('style', 'display:block  !important');
  });

  //.appbear .appbear-type-icon_selector .appbear-search-icon, .appbear .appbear-type-icon_selector .appbear-icons-wrap
})(jQuery);

