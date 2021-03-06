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
      if (new Date().valueOf() - now > 25) {
        // _appInstalled = true;
        _update();
        return;
      }

      window.location = _isAndroid() ? JPress_Deeplinking.android_url : JPress_Deeplinking.ios_url;
    }, 100);

    if ( !!JPress_Deeplinking.base_url_android && _isAndroid() ) {
      window.location = !!JPress_Deeplinking.deeplink_url_android ? JPress_Deeplinking.deeplink_url_android : JPress_Deeplinking.base_url_android;
    }
    else if ( !!JPress_Deeplinking.base_url_ios && _isIOS() ) {
      window.location = !!JPress_Deeplinking.deeplink_url_ios ? JPress_Deeplinking.deeplink_url_ios : JPress_Deeplinking.base_url_ios;
    }
  }

  /**
   * Update widget UI state
   *
   * @private
   * @returns {void}
   */
  function _update() {
    if (!!JPress_Deeplinking.bg_color) {
      $widget.css('background-color', JPress_Deeplinking.bg_color);
    }

    if (!!JPress_Deeplinking.fg_color) {
      // .find('.text-content').find('a, p')
      $widget.add($widget.find('.text-content a')).css('color', JPress_Deeplinking.fg_color);
    }

    $widget.find('a.jpress-appstore-link').hide();

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
    if (!window['JPress_Deeplinking']) {
      throw new Error('JPress deeplinking settings not found!');
    }

    if (_isMobile() === false) {
      return false;
    }

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
              Read this post in our mobile application
            </p>
          </div>
        </div>
      </div>
    `;
    // <div class="store-buttons tie-col-md-4">
    //   <a href="#" target="_blank" class="jpress-appstore-link google-play"></a>
    //   <a href="#" target="_blank" class="jpress-appstore-link appstore"></a>
    // </div>

    $widget.append(HTML);
    $body.append($widget);

    $widget.find('a.google-play').attr('href', JPress_Deeplinking.android_url);
    $widget.find('a.appstore').attr('href', JPress_Deeplinking.ios_url);

    _update();

    $widget.on('click', '.close-widget', function(event) {
      event.preventDefault();
      $widget.hide();
    });

    $widget.on('click', '.deeplinking-open-post', function(event) {
      event.preventDefault();
      // JPress_Deeplinking.open();
      _detectApp();
    });
  }

  /**
   * Initialize widget
   *
   * @private
   * @returns {void}
   */
  function _init() {
    if ( _checks() === true ) {
      _prepareHtml();
    }
  }

  $(document).ready(() => _init());
})(jQuery);
