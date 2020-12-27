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
   * Get post title
   *
   * @returns {string}
   */
  function _getPostTitle() {
    return String(wp.data.select( 'core/editor' ).getEditedPostAttribute( 'title' )).trim();
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

    const
      $titleInput = $inputs.filter('[name="appbear_notifications_title"]'),
      postTitle = _getPostTitle();

    $checkbox.on('change', function (event) {
      $groups.hide();

      if ( $checkbox.prop('checked') === true ) {
        $groups.show();
      }
    });

    if (String($titleInput.val()).trim().length === 0 && postTitle) {
      $titleInput.val(postTitle);
    }

    $postTitle.on('keyup', function (event) {
      $titleInput.val( _getPostTitle() );
    });
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
      $postTitle = $('#post-title-0, #post-title-1');
      $widget = $('#appbear-notifications-metabox');

      // console.info($postTitle, $widget);
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
