jQuery(document).ready(function($){
    var image_path = $(".jpress-img-path");
    image_path.each(function(){
        jpress_image_uploader_trigger( jQuery(this) );
    });
});

jQuery(document).ready(function($){
    jQuery(document).on('click', '.delete-category-img', function(){
        var $img = jQuery(this).parent();
        wp.ajax.post( "delete_category_image", {id : jQuery('#ajaxtestdel_postid').val()} ).done(function(response) {
            $img.fadeOut( 'fast',function() {
                $img.hide();
                $img.closest('.option-item').find('.jpress-img-path').attr( 'value', '' );
            });
        });
    });
});

function jpress_image_uploader_trigger( $thisElement ){

var thisElementID      = $thisElement.attr('id').replace('#',''),
        $thisElementParent = $thisElement.closest('.option-item'),
        $thisElementImage  = $thisElementParent.find('.img-preview'),
        uploaderTypeStyles = false;

$thisElement.change(function(){
    $thisElementImage.show();
    $thisElementImage.find('img').attr('src', $thisElement.val());
});

if( $thisElement.hasClass('tie-background-path') ){
    thisElementID = thisElementID.replace('-img','');
    uploaderTypeStyles = true;
}

jpress_set_uploader( thisElementID, uploaderTypeStyles );
}



function jpress_set_uploader( field, styling ) {
    var jpress_bg_uploader;

     jQuery(document).on('click', '#upload_'+field+'_button', function( event ){

    event.preventDefault();
    jpress_bg_uploader = wp.media.frames.jpress_bg_uploader = wp.media({
        title: 'Choose Image',
        library: {type: 'image' },
        button: {text: 'Select'},
        multiple: false
    });

    jpress_bg_uploader.on( 'select', function() {
        var selection = jpress_bg_uploader.state().get('selection');
        selection.map( function( attachment ) {

            attachment = attachment.toJSON();

            if( styling ){
                jQuery('#'+field+'-img').val(attachment.url);
            }

            else{
                jQuery('#'+field).val(attachment.url);
            }

            jQuery('#'+field+'-preview').show();
            jQuery('#'+field+'-preview img').attr('src', attachment.url );

            //Set the Retina Logo Width and Height
            if( field == 'logo' ){
                jQuery('#logo_retina_height').val(attachment.height);
                jQuery('#logo_retina_width').val(attachment.width);
            }
        });
    });

    jpress_bg_uploader.open();
    });
}
