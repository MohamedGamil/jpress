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
    shouldStopCopyingTitle = false,
    shouldDisplayNotice = false;


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
   * Destroy notification widget and run unhooks routine
   */
  function _demolish() {
    // FIXME: This causes issues with submission
    // $checkbox.prop('checked', false);
    // $widget.remove();
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

      _inputChecks();

      if ( $checkbox.prop('checked') === true ) {
        $groups.show();
      }
    });

    $titleInput.add($msgInput).on('input', (event) => {
      if ($(event.target).is($titleInput)) {
        shouldStopCopyingTitle = true;
      }

      _inputChecks();
    });

    const unsubscribe = wp.data.subscribe((_e) => {
      if ( _getPostTitle().length > 0 && shouldStopCopyingTitle === false ) {
        $titleInput.val( _getPostTitle() );
      }

      _inputChecks();

      const
        $editor = wp.data.select( 'core/editor' ),
        isSaving = $editor.isSavingPost(),
        isAutoSaving = $editor.isAutosavingPost(),
        didSuccess = $editor.didPostSaveRequestSucceed();

      if (isSaving && isAutoSaving === false) {
        shouldDisplayNotice = true;
        return;
      }

      if (shouldDisplayNotice === true) {
        unsubscribe();
        _demolish();

        if (didSuccess) {
          _addAlert('Push notification sent successfully.');
        } else {
          _addAlert('Unable to send push notification, please check your inputs and plan limits!', 'error');
        }
      }
    });
  }

  /**
   * Run inputs checks
   */
  function _inputChecks() {
    if ($($checkbox).length === 0 || $($titleInput).length === 0 || $($msgInput).length === 0) {
      return;
    }

    const
      isChecked = $checkbox.prop('checked') === true,
      titleLength = $titleInput.val().length,
      msgLength = $msgInput.val().length;

    // console.info({ isChecked, titleLength, msgLength });

    _lock(
      isChecked && ( titleLength === 0 || msgLength === 0 ),
      'appbear-notifications-checks',
      'Please fill the post title and notification message before saving.'
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

      if ($widget.length === 0) {
        return;
      }

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
