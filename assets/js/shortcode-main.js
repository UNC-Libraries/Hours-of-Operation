jQuery( function( $ ) {
    var $hoo_list_rows = $( '.location-row' ),
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
            $visible_panel = $('.panel:visible');

        if ( $this_panel.is( ':visible' ) ) {
            $this_panel.hide( 'slide', { direction: 'left', easing: 'easeOutExpo' }, 500 );
        }
        else if ( $visible_panel.length ) {
            $this_panel.show('fade', { 
                complete: function(){
                    $visible_panel.hide('fade');
                }
            });
        } else {
            $this_panel.show( 'slide', { direction: 'left', easing: 'easeOutBounce' }, 800 );
        }

    });

});
