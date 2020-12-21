/**
 * Notifications Meta box
 */
(function ($) {

  let
    $postTitle = null,
    $widget = null,
    $groups = null,
    $checkbox = null,
    $inputs = null;


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

    const $titleInput = $inputs.filter('[name="appbear_notifications_title"]');

    $checkbox.on('change', function (event) {
      $groups.hide();

      if ( $checkbox.prop('checked') === true ) {
        $groups.show();
      }
    });

    if (String($titleInput.val()).trim().length === 0 && $postTitle.length > 0) {
      $titleInput.val($postTitle.val());
    }

    $postTitle.on('keyup', function (event) {
      const title = String($postTitle.val()).trim();
      $titleInput.val( title );
    });
  }

  /**
   * Hook to WordPress editor
   *
   * @private
   * @returns {void}
   */
  function _hook() {
    if (typeof _wpLoadBlockEditor === 'undefined') {
      return;
    }

    const _callback = () => {
      $postTitle = $('#post-title-0');
      $widget = $('#appbear-notifications-metabox');

      console.info($postTitle, $widget);
      _run();
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
