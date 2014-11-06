jQuery( function( $ ) {
    $( '#location_upload_image_button' ).on( 'click', function() {
        var form_field = $( '#location_image' ).attr( 'name' );

        tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
        return false;
    } );

    window.send_to_editor = function( html ) {
        var img_url = $( 'img', html ).attr( 'src' );

        $( '#location_image' ).val( img_url );
        tb_remove();

        $( '#location_image_thumb' ).attr( 'src', img_url );
    };
} );
