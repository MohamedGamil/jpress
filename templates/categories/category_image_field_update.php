<tr class="form-field term-group-wrap">
    <th scope="row">
      <label for="jpress-category-image-id"><?php _e( 'Image', 'jpress' ); ?></label>
    </th>
    <td>
      <input type="hidden" id="jpress-category-image-id" name="jpress-category-image-id" value="<?php echo $image; ?>">
      <div id="jpress-category-image-wrapper">
        <?php echo ($image) ? wp_get_attachment_image( $image, 'thumbnail' ) : ''; ?>
      </div>
      <p>
        <input type="button" class="button button-secondary jpress_cat_media_button" id="jpress_cat_media_button" name="jpress_cat_media_button" value="<?php _e( 'Add Image', 'jpress' ); ?>" />
        <input type="button" class="button button-secondary jpress_cat_media_remove" id="jpress_cat_media_remove" name="jpress_cat_media_remove" value="<?php _e( 'Remove Image', 'jpress' ); ?>" />
      </p>
    </td>
</tr>
