<?php
  $disabled = (isset($activated) && $activated === false) || isset($activated) === false;
  $checked = isset($checked) ? $checked : true;
  $stats = isset($stats) && is_array($stats) ? (Object) $stats : false;
?>

<div class="jpress-notification-metabox-wrapper">
    <?php if ( $disabled ) : ?>
    <div class="meta-options anm_notice">
      <a href="<?php echo admin_url('admin.php?page=jpress-activation'); ?>" target="_blank">
        <?php _e('Please connect your JPress account to enjoy this feature.', 'jpress'); ?>
      </a>
    </div>
    <hr>
    <?php endif; ?>

    <p class="meta-options anm_field anm_field_checkbox">
        <input
          <?php echo $disabled ? 'disabled="disabled"' : ''; ?>
          <?php echo $checked ? 'checked="checked"' : ''; ?>
          type="checkbox"
          name="jpress_notifications_send"
          id="jpress_notifications_send"
          class="anm_checkbox" />
        <label for="jpress_notifications_send" class="anm_checkbox_label">
          <strong><?php _e('Send notification for this post', 'jpress') ?></strong>
        </label>
    </p>
    <p class="meta-options anm_field" <?php echo $checked === false ? 'style="display: none;"' : ''; ?>>
        <label for="jpress_notifications_title"><?php _e('Notification Title', 'jpress') ?></label>
        <input
          <?php echo $disabled ? 'disabled="disabled"' : ''; ?>
          type="text"
          name="jpress_notifications_title"
          id="jpress_notifications_title"
          required />
          <!-- readonly -->
    </p>
    <p class="meta-options anm_field" <?php echo $checked === false ? 'style="display: none;"' : ''; ?>>
        <label for="jpress_notifications_message"><?php _e('Notification Message', 'jpress') ?></label>
        <textarea
          <?php echo $disabled ? 'disabled="disabled"' : ''; ?>
          type="text"
          name="jpress_notifications_message"
          id="jpress_notifications_message"
          rows="3">🔥 <?php _e('Check out this new article.', 'jpress'); ?></textarea>
    </p>

    <?php if ( is_object($stats) ) : ?>
    <hr>
    <div class="meta-options anm_notice anm_notice_normal">
      <span href="<?php echo admin_url('admin.php?page=jpress-activation'); ?>" target="_blank">
        <?php if ( $stats->sent_count > -1 || $stats->sent_count === '?' ): ?>
        <strong><?php _e('Total sent:', 'jpress'); ?></strong>
        <?php
          echo $stats->sent_count === '?'
            ? __('Unknown', 'jpress')
            : $stats->sent_count; ?>
        <br>
        <?php endif; ?>
        <strong><?php _e('Remaining Notifications:', 'jpress'); ?></strong>
        <?php
          echo $stats->remaining === '?'
            ? __('Unknown', 'jpress')
            : ( $stats->remaining === -1 ? __('Unlimited', 'jpress') : $stats->remaining ); ?>
        <!-- <br> -->
      </span>
    </div>
    <?php endif; ?>
</div>
