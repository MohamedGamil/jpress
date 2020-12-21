<?php $checked = isset($checked) ? $checked : true; ?>
<div class="appbear-notification-metabox-wrapper">
    <p class="meta-options anm_field anm_field_checkbox">
        <input
          type="checkbox"
          name="appbear_notifications_send"
          id="appbear_notifications_send"
          class="anm_checkbox"
          <?php echo $checked ? 'checked="checked"' : ''; ?> />
        <label for="appbear_notifications_send" class="anm_checkbox_label">
          <strong><?php _e('Send notification for this post', 'textdomain') ?></strong>
        </label>
    </p>
    <p class="meta-options anm_field">
        <label for="appbear_notifications_title"><?php _e('Notification Title', 'textdomain') ?></label>
        <input type="text" name="appbear_notifications_title" id="appbear_notifications_title" required />
    </p>
    <p class="meta-options anm_field">
        <label for="appbear_notifications_message"><?php _e('Notification Message', 'textdomain') ?></label>
        <textarea type="text" name="appbear_notifications_message" id="appbear_notifications_message" rows="3"></textarea>
    </p>
</div>
