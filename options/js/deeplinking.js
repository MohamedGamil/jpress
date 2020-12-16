/**
 * Deeplinking Initialization
 */
(function ($) {
  let $body,
  $widget = $('<div id="deeplinking-public-widget" class="deeplinking-public-widget" />');

  function _checks() {
    if (!window['AppBear_Deeplinking']) {
      throw new Error('AppBear deeplinking settings not found!');
    }

    $body = $('body');
  }

  function _prepareHtml() {
    const HTML = `
      <div class="deeplinking-public-widget-inner container">
        <div class="tie-row">
          <div class="text-content tie-col-xs-8">
            <p>
              Read <a href="#" class="deeplinking-open-post">this post</a> in our mobile application, download
            </p>
          </div>
          <div class="store-buttons tie-col-xs-4">
            <a href="#" target="_blank" class="appbear-appstore-link google-play"></a>
            <a href="#" target="_blank" class="appbear-appstore-link appstore"></a>
          </div>
        </div>
      </div>
    `;

    if (!!AppBear_Deeplinking.bg_color) {
      $widget.css('background-color', AppBear_Deeplinking.bg_color);
    }

    $widget.append(HTML);
    $body.append($widget);

    $widget.find('a.google-play').attr('href', AppBear_Deeplinking.android_url);
    $widget.find('a.appstore').attr('href', AppBear_Deeplinking.ios_url);

    $widget.on('click', '.deeplinking-open-post', function(event) {
      event.preventDefault();
      AppBear_Deeplinking.open();
    });
  }

  function init() {
    _checks();
    _prepareHtml();
  }

  $(document).ready(() => init());
})(jQuery);
