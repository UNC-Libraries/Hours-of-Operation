jQuery( function( $ ) {
    $( '.category_is_visible' ).on( 'change', function( e ) {
        $.ajax( {
            url: ajaxurl,
            type: 'POST',
            data: $.param( {
                'action': 'category_is_visible',
                'category_id': this.getAttribute( 'data-category-id' ),
                'checked': this.checked
            } )
        } );
    } );
} );
