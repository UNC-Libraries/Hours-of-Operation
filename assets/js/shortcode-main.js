jQuery( function( $ ) {
    var $hoo_list_rows = $( '.location-row a' ),
        map_options = {
            center: {
                lat: -34.397,
                lng: 150.644
            },
            zoom: 8
        },
        map = new google.maps.Map( document.getElementById( 'map-canvas' ),
                                   map_options );


    $hoo_list_rows.on( 'click', function( e ) {
        var $this_panel = $( '#' + $( this ).data( 'panel' ) ),
            $panels = $( '.panel' );

        if ( $this_panel.is( ':visible' ) ) {
            $this_panel.hide( 'slide', { direction: 'left' }, 100 );
            console.log('1');
        }
        else if ( $panels.is( ':visible') ) {
            console.log('2');
            $panels.hide('fade', { easing: 'linear' }, 500, function() {
                $this_panel.show();
            });
        } else {
            console.log('3');
            $this_panel.show( 'slide', { direction: 'left', easing: 'easeOutBounce' }, 500 );
        }

        e.preventDefault();

    });

});
