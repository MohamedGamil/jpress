<?php
  $disabled = (isset($activated) && $activated === false) || isset($activated) === false;
  $checked = isset($checked) ? $checked : true;
  $stats = isset($stats) && is_array($stats) ? (Object) $stats : false;
?>

<div class="appbear-notification-metabox-wrapper">
    <?php if ( $disabled ) : ?>
    <div class="meta-options anm_notice">
      <a href="<?php echo admin_url('admin.php?page=appbear-activation'); ?>" target="_blank">
        <?php _e('Please connect your AppBear account to enjoy this feature.', 'textdomain'); ?>
      </a>
    </div>
    <hr>
    <?php endif; ?>

    <p class="meta-options anm_field anm_field_checkbox">
        <input
          <?php echo $disabled ? 'disabled="disabled"' : ''; ?>
          <?php echo $checked ? 'checked="checked"' : ''; ?>
          type="checkbox"
          name="appbear_notifications_send"
          id="appbear_notifications_send"
          class="anm_checkbox" />
        <label for="appbear_notifications_send" class="anm_checkbox_label">
          <strong><?php _e('Send notification for this post', 'textdomain') ?></strong>
        </label>
    </p>
    <p class="meta-options anm_field" <?php echo $checked === false ? 'style="display: none;"' : ''; ?>>
        <label for="appbear_notifications_title"><?php _e('Notification Title', 'textdomain') ?></label>
        <input
          <?php echo $disabled ? 'disabled="disabled"' : ''; ?>
          type="text"
          name="appbear_notifications_title"
          id="appbear_notifications_title"
          required />
    </p>
    <p class="meta-options anm_field" <?php echo $checked === false ? 'style="display: none;"' : ''; ?>>
        <label for="appbear_notifications_message"><?php _e('Notification Message', 'textdomain') ?></label>
        <textarea
          <?php echo $disabled ? 'disabled="disabled"' : ''; ?>
          type="text"
          name="appbear_notifications_message"
          id="appbear_notifications_message"
          rows="3">🔥 <?php _e('Check out this new article.', 'textdomain'); ?></textarea>
    </p>

    <?php if ( is_object($stats) ) : ?>
    <hr>
    <div class="meta-options anm_notice anm_notice_normal">
      <span href="<?php echo admin_url('admin.php?page=appbear-activation'); ?>" target="_blank">
        <strong><?php _e('Total sent:', 'textdomain'); ?></strong>
        <?php
          echo $stats->sent_count === '?'
            ? __('Unknown', 'textdomain')
            : ( $stats->sent_count === -1 ? __('UNLIMITED', 'textdomain') : $stats->sent_count ); ?>
        <br>
        <strong><?php _e('Remaining Notifications:', 'textdomain'); ?></strong>
        <?php
          echo $stats->remaining === '?'
            ? __('Unknown', 'textdomain')
            : ( $stats->remaining === -1 ? __('UNLIMITED', 'textdomain') : $stats->remaining ); ?>
        <!-- <br> -->
      </span>
    </div>
    <?php endif; ?>
</div>
