/**
 * Deeplinking Support
 */
(function ($) {
  let $widget = $('<div id="deeplinking-public-widget" class="deeplinking-public-widget" />');
  let $body = null;
  let _appInstalled = false;

  /**
   * Check if the user-agent is Android
   *
   * @private
   * @returns {Boolean} true/false
   */
  function _isAndroid() {
    return !!navigator.userAgent.match('Android');
  }

  /**
   * Check if the user-agent is iPad/iPhone/iPod
   *
   * @private
   * @returns {Boolean} true/false
   */
  function _isIOS() {
    return !!navigator.userAgent.match('iPad') ||
            !!navigator.userAgent.match('iPhone') ||
            !!navigator.userAgent.match('iPod');
  }

  /**
   * Check if the user is on mobile
   *
   * @private
   * @returns {Boolean} true/false
   */
  function _isMobile() {
    return _isAndroid() || _isIOS();
  }

  /**
   * Detect mobile app installation
   *
   * @private
   * @returns {void}
   */
  function _detectApp() {
    const now = new Date().valueOf();

    setTimeout(() => {
      if (new Date().valueOf() - now > 100) {
        // _appInstalled = true;
        _update();
        return;
      }

      window.location = _isAndroid() ? AppBear_Deeplinking.android_url : AppBear_Deeplinking.ios_url;
    }, 100);

    if (!!AppBear_Deeplinking.base_url) {
      window.location = !!AppBear_Deeplinking.deeplink_url ? AppBear_Deeplinking.deeplink_url : AppBear_Deeplinking.base_url;
    }
  }

  /**
   * Update widget UI state
   *
   * @private
   * @returns {void}
   */
  function _update() {
    if (!!AppBear_Deeplinking.bg_color) {
      $widget.css('background-color', AppBear_Deeplinking.bg_color);
    }

    if (!!AppBear_Deeplinking.fg_color) {
      // .find('.text-content').find('a, p')
      $widget.add($widget.find('.text-content a')).css('color', AppBear_Deeplinking.fg_color);
    }

    $widget.find('a.appbear-appstore-link').hide();

    if (_appInstalled === true) {
      return;
    }

    if (_isAndroid()) {
      $widget.find('a.google-play').show();
    }

    if (_isIOS()) {
      $widget.find('a.appstore').show();
    }
  }

  /**
   * Run few checks before initialization
   *
   * @private
   * @returns {Boolean} true/false
   */
  function _checks() {
    if (!window['AppBear_Deeplinking']) {
      throw new Error('AppBear deeplinking settings not found!');
    }

    if (_isMobile() === false) {
      return false;
    }

    // _detectApp();

    $body = $('body');

    return true;
  }

  /**
   * Prepare widget HTML code
   *
   * @private
   * @returns {void}
   */
  function _prepareHtml() {
    const HTML = `
      <a href="#" class="close-widget"><i class="fa fa-times"></i></a>
      <div class="deeplinking-public-widget-inner container deeplinking-open-post">
        <div class="tie-row">
          <div class="text-content tie-col-md-12">
            <p>
              Read <a href="#" class="_deeplinking-open-post">this post</a> in our mobile application
            </p>
          </div>
        </div>
      </div>
    `;
    // <div class="store-buttons tie-col-md-4">
    //   <a href="#" target="_blank" class="appbear-appstore-link google-play"></a>
    //   <a href="#" target="_blank" class="appbear-appstore-link appstore"></a>
    // </div>

    $widget.append(HTML);
    $body.append($widget);

    $widget.find('a.google-play').attr('href', AppBear_Deeplinking.android_url);
    $widget.find('a.appstore').attr('href', AppBear_Deeplinking.ios_url);

    _update();

    $widget.on('click', '.close-widget', function(event) {
      event.preventDefault();
      $widget.hide();
    });

    $widget.on('click', '.deeplinking-open-post', function(event) {
      event.preventDefault();
      // AppBear_Deeplinking.open();
      _detectApp();
    });
  }

  /**
   * Initialize widget
   *
   * @private
   * @returns {Boolean} true/false
   */
  function _init() {
    if ( _checks() === true ) {
      _prepareHtml();
    }
  }

  $(document).ready(() => _init());
})(jQuery);
