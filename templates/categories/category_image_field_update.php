<tr class="form-field term-group-wrap">
    <th scope="row">
      <label for="appbear-category-image-id"><?php _e( 'Image', 'textdomain' ); ?></label>
    </th>
    <td>
      <input type="hidden" id="appbear-category-image-id" name="appbear-category-image-id" value="<?php echo $image; ?>">
      <div id="appbear-category-image-wrapper">
        <?php echo ($image) ? wp_get_attachment_image( $image, 'thumbnail' ) : ''; ?>
      </div>
      <p>
        <input type="button" class="button button-secondary appbear_cat_media_button" id="appbear_cat_media_button" name="appbear_cat_media_button" value="<?php _e( 'Add Image', 'textdomain' ); ?>" />
        <input type="button" class="button button-secondary appbear_cat_media_remove" id="appbear_cat_media_remove" name="appbear_cat_media_remove" value="<?php _e( 'Remove Image', 'textdomain' ); ?>" />
      </p>
    </td>
</tr>
