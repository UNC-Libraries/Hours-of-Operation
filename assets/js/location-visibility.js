jQuery( function( $ ) {
    $( '.location_is_visible' ).on( 'change', function( e ) {
        $.ajax( {
            url: ajaxurl,
            type: 'POST',
            data: $.param( {
                'action': 'location_is_visible',
                'location_id': this.getAttribute( 'data-location-id' ),
                'checked': this.checked
            } )
        } );
    } );
} );
