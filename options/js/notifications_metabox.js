/**
 * Notifications Meta box
 */
(function ($) {
  "use strict";

  let
    $widget = null,
    $groups = null,
    $checkbox = null,
    $inputs = null,
    $titleInput = null,
    $msgInput = null,
    locks = {};


  /**
   * Lock / Unlock post editor save action
   *
   * @param {*} lockIt
   * @param {*} handle
   * @param {*} message
   */
  function _lock( lockIt, handle, message ) {
    console.info( {handle, lockIt} );

    if ( !!lockIt ) {
      if ( ! locks[ handle ] ) {

        locks[ handle ] = true;
        wp.data.dispatch( 'core/editor' ).lockPostSaving( handle );

        _addAlert( message, 'error', handle, false );
      }
    } else if ( locks[ handle ] ) {
      locks[ handle ] = false;
      wp.data.dispatch( 'core/editor' ).unlockPostSaving( handle );
      wp.data.dispatch( 'core/notices' ).removeNotice( handle );
    }
  }

  /**
   * Get post title
   *
   * @returns {string}
   */
  function _getPostTitle() {
    return String(wp.data.select( 'core/editor' ).getEditedPostAttribute( 'title' )).trim();
  }

  /**
   * Display an alert or a snackbar
   *
   * @param {*} handle
   * @param {*} message
   * @param {*} type
   * @param {*} isDismissible
   */
  function _addAlert( message, type = 'error', handle = 'appbear-notifications', isDismissible = true, isSnackbar = false ) {
    const opts = {
      id: handle,
      isDismissible: isDismissible === true,
    };

    if (isSnackbar === true) {
      opts.type = 'snackbar';
    }

    message = String(message).trim();

    wp.data.dispatch( 'core/notices' ).createNotice( type, message, opts );
  }

  /**
   * Run
   *
   * @private
   * @returns {void}
   */
  function _run() {
    $groups = $widget.find('.anm_field').filter(idx => idx > 0);
    $inputs = $groups.find('input, textarea');
    $checkbox = $widget.find('.anm_checkbox:first');
    $titleInput = $inputs.filter('[name="appbear_notifications_title"]');
    $msgInput = $inputs.filter('[name="appbear_notifications_message"]');

    const postTitle = _getPostTitle();

    $checkbox.on('change', function (event) {
      $groups.hide();

      if ( $checkbox.prop('checked') === true ) {
        $groups.show();
      }

      _inputChecks();
    });

    if (String($titleInput.val()).trim().length === 0 && postTitle) {
      $titleInput.val(postTitle);
    }

    wp.data.subscribe((_e) => {
      if ( _getPostTitle().length > 0 ) {
        $titleInput.val( _getPostTitle() );
      }

      _inputChecks();
    });

    $titleInput.add($msgInput).on('input', () => {
      _inputChecks();
    });
  }

  /**
   * Run inputs checks
   */
  function _inputChecks() {
    const
      isChecked = $checkbox.prop('checked') === true,
      titleLength = $titleInput.val().length,
      msgLength = $msgInput.val().length;

    console.info({ isChecked, titleLength, msgLength });

    _lock(
      isChecked && ( titleLength === 0 || msgLength === 0 ),
      'appbear-notifications',
      'You must enter a push notification title and message!'
    );
  }

  /**
   * Register plugin checks
   */
  function _registerPlugin() {
    wp.plugins.registerPlugin(
      'appbear-notifications-metabox-checks',
      {
        render: () => {
          _inputChecks();

          return React.createElement('div', null, '');
        },
      }
    );
  }

  /**
   * Hook to WordPress editor
   *
   * @private
   * @returns {void}
   */
  function _hook() {
    if (typeof _wpLoadBlockEditor === 'undefined' || typeof wp === 'undefined') {
      console.warn('Notifications metabox is not properly loaded!');
      return;
    }

    const _callback = () => {
      $widget = $('#appbear-notifications-metabox');

      _run();
      _registerPlugin();
    };

    window._wpLoadBlockEditor.then(() => setTimeout( _callback, 1000 ));
  }

  /**
   * Initialize widget
   *
   * @private
   * @returns {void}
   */
  function _init() {
    _hook()
  }

  $(document).ready(() => _init());
})(jQuery);
