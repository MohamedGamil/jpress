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
    locks = {},
    didSave = false,
    didSuccess = false;


  /**
   * Lock / Unlock post editor save action
   *
   * @param {*} lockIt
   * @param {*} handle
   * @param {*} message
   */
  function _lock( lockIt, handle, message ) {
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
  function _addAlert( message, type = 'success', handle = 'appbear-notifications', isDismissible = true, isSnackbar = false ) {
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

    if (String($titleInput.val()).trim().length === 0 && postTitle) {
      $titleInput.val(postTitle);
    }

    $checkbox.on('change', function (event) {
      $groups.hide();

      if ( $checkbox.prop('checked') === true ) {
        _inputChecks();
        $groups.show();
      }
    });

    $titleInput.add($msgInput).on('input', () => {
      _inputChecks();
    });

    wp.data.subscribe((_e) => {
      if ( _getPostTitle().length > 0 ) {
        $titleInput.val( _getPostTitle() );
      }

      _inputChecks();

      // FIXME: These call invokations result in callback hell and infinite regression!

      // console.info({ didSave, didSuccess });

      // if (didSave === false) {
      //   return;
      // }

      // if (didSuccess) {
      //   _addAlert('Push notification sent successfully.');
      //   $checkbox.prop('checked', false);
      // } else {
      //   _addAlert('Unable to send push notification, please check your inputs and plan limits!', 'error');
      // }
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

    // console.info({ isChecked, titleLength, msgLength });

    _lock(
      isChecked && ( titleLength === 0 || msgLength === 0 ),
      'appbear-notifications-checks',
      'You must fill the title and notification message before saving!'
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
          const
          $el = React.createElement('div', null, ''),
          $editor = wp.data.select( 'core/editor' );

          // console.info({ $editor });

          _inputChecks();

          if ($editor.isSavingPost()) {
            didSave = true;
            return $el;
          }

          didSuccess = $editor.didPostSaveRequestSucceed();

          return $el;
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
