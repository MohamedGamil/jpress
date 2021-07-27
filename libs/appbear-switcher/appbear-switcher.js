/*!
 * Appbear Switcher
 * Version: 1.0
 * Author: Max López
 * Copyright © 2018 Max Arthur López Pérez - All Rights Reserved.
 */

;(function ($, window, document, undefined) {

    function Plugin(element, options) {
        var _ = this;
        _.el = element;
        _.$el = $(element);
        _.defaults = {
            on_text: '',
            off_text: '',
            on_value: 'on',
            off_value: 'off',
            readonly: false,
        };
        _.metadata = _.$el.data('switcher') || {};
        _.options = $.extend({}, _.defaults, options, _.metadata);
        _.$el.data('switcher', _.options);
        _.init();
    }

    Plugin.prototype = {
        init: function () {
            var _ = this;
            if (_.$el.parent().hasClass('jpress-sw-wrap')) {
                return;
            }
            if (_.$el.attr('type') == 'hidden' || _.$el.attr('type') == 'checkbox') {
                _.build();
            }

        },

        build: function () {
            var _ = this;
            var $input = _.$el;
            var has_icons = false;
            var html_toggle_on = '<div class="jpress-sw-toggle jpress-sw-toggle-on">';
            var html_toggle_off = '<div class="jpress-sw-toggle jpress-sw-toggle-off">';
            if (_.options.on_text !== '' && _.options.off_text !== '') {
                html_toggle_on = html_toggle_on + _.options.on_text + '</div>';
                html_toggle_off = html_toggle_off + _.options.off_text + '</div>';
            } else {
                has_icons = true;
                //html_toggle_on = html_toggle_on + '<i class="icon-sw-on"></i></div>';
                //html_toggle_off = html_toggle_off + '<i class="icon-sw-off"></i></div>';

                html_toggle_on = html_toggle_on +'</div>';
                html_toggle_off = html_toggle_off +'</div>';
            }

            var is_disabled = $input.is(':disabled') ? true : false;

            var status_classes = '';
            status_classes += _.is_on() ? 'jpress-sw-on' : 'jpress-sw-off';
            status_classes += is_disabled ? ' jpress-sw-disabled' : '';
            status_classes += has_icons ? ' jpress-sw-has-icons' : '';

            var switcher_body =
                '<div class="jpress-sw-inner ' + status_classes + '">' +
                '<div class="jpress-sw-blob"></div>' +
                html_toggle_on + html_toggle_off +
                '</div>';

            $input.wrap('<div class="jpress-sw-wrap"></div>');
            $input.parent().append(switcher_body);
            $input.parent().find('.jpress-sw-inner').addClass('jpress-sw-type-' + $input.attr('type'));
        },

        is_on: function () {
            if (this.$el.next('.jpress-sw-inner').hasClass('jpress-sw-on')) {
                return true;
            }
            if (this.$el.attr('type') == 'checkbox') {
                return this.$el.is(':checked');
            } else {
                if (this.$el.val() == this.options.on_value) {
                    return true;
                }
            }
            return false;
        },

        set_on: function () {
            var $input = $(this);
            if (!$input.parent().hasClass('jpress-sw-wrap')) {
                return;
            }
            var options = $input.data('switcher');

            if ($input.attr('type') == 'checkbox') {
                $input.prop('checked', true).attr('checked', 'checked');
            } else {
                $input.val(options.on_value);
            }

            $input.parent().find('.jpress-sw-inner').removeClass('jpress-sw-off').addClass('jpress-sw-on');

            //Eventos
            $input.trigger('changeOn');
            $input.trigger('statusChange');

            return true;
        },

        set_off: function () {
            var $input = $(this);
            if (!$input.parent().hasClass('jpress-sw-wrap')) {
                return;
            }
            var options = $input.data('switcher');

            if ($input.attr('type') == 'checkbox') {
                $input.prop('checked', false).removeAttr('checked');
            } else {
                $input.val(options.off_value);
            }

            $input.parent().find('.jpress-sw-inner').removeClass('jpress-sw-on').addClass('jpress-sw-off');

            //Eventos
            $input.trigger('changeOff');
            $input.trigger('statusChange');

            return true;
        },

        destroy: function () {
            $(this).each(function () {
                $(this).parents('.jpress-sw-wrap').children().not('input').remove();
                $(this).unwrap();
            });
            return true;
        }
    };

    //Eventos
    $(document).ready(function () {
        $(document).on('click tap', '.jpress .jpress-sw-inner:not(.jpress-sw-disabled)', function (e) {
            var $input = $(this).parent().find('input');
            if ($(this).hasClass('jpress-sw-on')) {
                $input.jpressSwitcher('set_off');
            } else {
                $input.jpressSwitcher('set_on');
            }
        });

        $(document).on('change', '.jpress .jpress-sw-wrap input', function (e) {
            if ($(this).next('.jpress-sw-inner').hasClass('jpress-sw-on')) {
                $(this).jpressSwitcher('set_off');
            } else {
                $(this).jpressSwitcher('set_on');
            }
        });
    });

    $.fn.jpressSwitcher = function (options) {
        if (Plugin.prototype[options] && options != 'init' && options != 'build' && options != 'is_on') {
            return Plugin.prototype[options].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof options === 'object' || !options) {
            return this.each(function () {
                new Plugin(this, options);
            });
        } else {
            //nothing
        }
    };

    function c(msg) {
        console.log(msg);
    }

    function cc(msg, msg2) {
        console.log(msg, msg2);
    }

})(jQuery, window, document);
