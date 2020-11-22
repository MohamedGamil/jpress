<?php
$prefix_taxonomy = 'category';

add_action('wp_enqueue_scripts', 'add_category_image');
add_action('wp_enqueue_styles', 'add_category_image');
function add_category_image($category) {

	wp_register_script('category', APPBEAR_URL . 'options/js/category.js', array());
	wp_enqueue_script('category');
	wp_register_style('category', APPBEAR_URL . 'options/css/category.css', array());
	wp_enqueue_style('category');
	if (current_filter() == 'category_add_form_fields') {
	?>
	<!-- <tr class="form-field">
    <th scope="row" valign="top"><label for="category_theme"><?php //_e('Category Theme') ?></label></th>
    <td> -->
		<div style="padding-top: 10px;">
			<div class="appbear_postbox ">
				<h2 style="padding:0 20px"><span><?php echo __( 'AppBear - Category Options', 'textdomain' );?></span></h2>
				<div class="inside">
					<div class="appbear-category-panel">
						<div id="category-item" class="option-item ">
							<span class="appbear-label"><?php echo __( 'Category Image', 'textdomain' );?></span>
							<input id="category" name="term_meta[custom_term_meta]" class="appbear-img-path" type="text" value="">
							<input id="upload_category_button" type="button" class="appbear-upload-img button" value="Upload">
							<div id="category-preview" class="img-preview" style="display:none">
								<img loading="lazy" src="<?php echo APPBEAR_URL;?>options/img/demos/empty.png" alt="">
								<a class="delete-category-img"></a>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
    <!-- </td>
	</tr> -->
	<?php
	}
	}
add_action( sprintf( '%s_add_form_fields', $prefix_taxonomy ), 'add_category_image' );

	
add_action( 'saved_term', 'add_term_meta_data', 10, 2 ); 
function add_term_meta_data( $term_id, $taxonomy ){
	$cat_image = $_POST['term_meta']['custom_term_meta'];
	add_term_meta($term_id, "cat_image",$cat_image);
}

add_action('wp_enqueue_scripts', 'edit_category_image');
add_action('wp_enqueue_styles', 'edit_category_image');
function edit_category_image($category) {
	wp_register_script('category', APPBEAR_URL . 'options/js/category.js', array());
	wp_enqueue_script('category');
	wp_register_style('category', APPBEAR_URL . 'options/css/category.css', array());
	wp_enqueue_style('category');
	$options = array(
		'id' => 'category-options',
		'title' => __( 'AppBear - Category Options', 'textdomain' ),
	);
	if (current_filter() == 'category_edit_form_fields') {

?>
	<tr class="form-field">
		<td colspan="2">
			<div style="padding-top: 10px;">
				<div class="appbear_postbox ">
					<h2 style="padding:0 20px"><span><?php echo __( 'AppBear - Category Options', 'textdomain' );?></span></h2>
					<div class="inside">
						<div class="appbear-category-panel">
							<div id="category-item" class="option-item ">
								<span class="appbear-label"><?php echo __( 'Category Image', 'textdomain' );?></span>	
								<input id="category" name="term_meta[custom_term_meta]" class="appbear-img-path" type="text" value="">
								<input id="upload_category_button" type="button" class="appbear-upload-img button" value="Upload">
								<input type="hidden" value="<?php echo $category->term_id; ?>" id="ajaxtestdel_postid">
									<?php
										$image = get_term_meta($category->term_id, "cat_image", true);
									?>
								<div id="category-preview" class="img-preview" <?php if(!$image) {?>style="display:none"<?php } ?>>
									<img loading="lazy" src="<?php if(!$image) { echo APPBEAR_URL;?>options/img/demos/empty.png<?php }else{ ?><?php  echo $image;?><? } ?>" alt="">
									<a class="delete-category-img delete-img"></a>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>
<?php
}
}

function delete_category_image() {
	$postid = isset($_POST['id']) ? $_POST['id'] : '';
	if ( $postid ) {
	//   $status = delete_metadata('term' , $_POST['id'] , 'cat_image', '', false );
		$status = true;
	} else {
	   $status = 'Error';
	}
	die($status);
  }  
add_action('wp_ajax_delete_category_image', 'delete_category_image');

add_action( sprintf( '%s_edit_form_fields', $prefix_taxonomy ), 'edit_category_image', 100, 20);

add_action( 'edit_terms', 'update_term_meta_data', 10, 2 ); 
function update_term_meta_data( $term_id, $taxonomy ){
	$cat_image = $_POST['term_meta']['custom_term_meta'];
	if($cat_image==''||$cat_image==null||empty($cat_image)){
		delete_metadata('term' , $term_id , 'cat_image', '', false );
	}
	else{
		update_term_meta($term_id, "cat_image",$cat_image);
	}
}
?>