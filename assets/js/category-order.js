jQuery(function($) {
    $( '.categories td.priority').css( 'cursor', 'move' );

    $( '#the-list' ).sortable({
        items: '.list-item',
        opacity: 0.4,
        cursor: 'move',
        axis: 'y',

        update: function() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: $( this ).sortable( 'serialize' ) + '&action=category_order',
                success: function( response ) {
                    
                    $( $( '.list-item' ).get().reverse() ).each( function( index, row ) {
                        // set priority text
                        $('.priority', row).text( index );
                        // set odd/even colors
                        if ( index % 2 == 0 ) {
                            $( row ).addClass( 'alternate' );
                        } else {
                            $( row ).removeClass( 'alternate' );
                        }
                    });
                }
            });
        }
    });
});
