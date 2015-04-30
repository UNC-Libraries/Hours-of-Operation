jQuery( function( $ ) {
    var $hoo_list_rows    = $( '.location-row' ),
        $hoo_list_rows_td = $( '.location-row td' ),
        $hoo_panel_close  = $( '.location-detail .close-panel' ),
        $hours_calendars  = $( '.hours-calendar' ),
        $locations_list   = $( '#locations-list'),
        $panel_container  = $( '#panel-container' ),
        $hoo_main         = $( '#hoo-main'),
        $hoo_map          = $( '#hoo-map'),

        map_options = {
            center: {
                lat: 0,
                lng: 0
            },
            zoom: 8,
            disableDefaultUI: true
        },


        locations_map = new google.maps.Map( document.getElementById( 'map-canvas' ), map_options ),
        locations_bounds = new google.maps.LatLngBounds(),
        location_markers = {},

        create_location_marker = function( location ) {
            var lat = location.dataset.lat,
                lon = location.dataset.lon,
                lat_lon = new google.maps.LatLng( lat, lon ),

                marker = new google.maps.Marker( {
                    map: locations_map,
                    draggable: false,
                    animation: google.maps.Animation.DROP,
                    position: lat_lon } ),

                slideout_panel = function() {
                    $( '#' + location.dataset.panel ).show( 'slide', {direction: 'left', easing: 'easeOutBounce' } , 800);
                    $('.hours-calendar').fullCalendar( 'render' );
                },
                highlight_row = function() { $( '.location-row[data-id="' + location.dataset.id + '"]').addClass( 'highlight' ); },
                remove_highlight = function() {$( '.location-row[data-id="' + location.dataset.id + '"]').removeClass( 'highlight' ); };

            if ( lat.length && lon.length ) {
                locations_bounds.extend( lat_lon );
            }
            google.maps.event.addListener( marker, 'mouseover', highlight_row );
            google.maps.event.addListener( marker, 'mouseout', remove_highlight );
            google.maps.event.addListener( marker, 'click', slideout_panel );

            return marker;
        };

    $hoo_main.height( $locations_list.height() );
    $hoo_map.height( $panel_container.height() );



    $hoo_list_rows.each( function( index, location ) {
        location_markers[ location.dataset.id ] = create_location_marker( location );

        $( location ).on( 'mouseover', function () { location_markers[ location.dataset.id ].setAnimation( google.maps.Animation.BOUNCE ); } );
        $( location ).on( 'mouseout', function () { location_markers[ location.dataset.id ].setAnimation( null ); } );
    } );

    locations_map.fitBounds( locations_bounds );
    google.maps.event.trigger( locations_map, 'resize' );

    $hours_calendars.each( function( index, hour_cal ) {
        $( hour_cal ).fullCalendar( {
            fixedWeekCount: false,
            aspectRatio: 1.4,
            header: {
                left: '',
                center: 'title',
                right: 'today prev,next'
            },


            events: function( cal_start, cal_end, tz, cb ) {

                $.ajax( {
                    url: HOO.ajaxurl,
                    type: 'GET',
                    data: {
                        action: 'hour_events',
                        start: cal_start.format(),
                        end: cal_end.format(),
                        location_id: $( hour_cal ).data( 'location-id' )
                    },
                    success: function( response ) {
                        cb ( response );
                    }
                });
            },
            timezone: 'local',
            allDayDefault: true,
            editable: false
        });
    } );


    $hoo_panel_close.on( 'click', function( e) {
        $( this ).closest( '.panel' ).hide( 'slide', { direction: 'left', easing: 'easeOutExpo' }, 500 );
    } );

    $hoo_list_rows.on( 'click', function( e ) {
        var location_id = $( this ).data( 'panel' ).split( '-' )[1],
            $this_panel = $( '#' + $( this ).data( 'panel' ) ),
            $visible_panel = $('.panel:visible'),

            $prev_active = $( '.location-row.active' );

        if ( $prev_active[0] === $( this )[0] ) {
            $this_panel.hide( 'slide', { direction: 'left', easing: 'easeOutExpo' }, 500 );
            $prev_active.removeClass( 'active' );
        }
        else if ( $prev_active.length ) {
            $this_panel.show('fade', {
                complete: function(){
                    $visible_panel.hide('fade');
                    $('.hours-calendar').fullCalendar( 'render' );
                }
            });
            $prev_active.removeClass( 'active' );
            $( this ).addClass( 'active' );
        } else {
            $this_panel.show( 'slide', {direction: 'left', easing: 'easeOutBounce' } , 800);
            // render as soon as possible.  hopefully this will always render and we don't have to wait for the animation to complete
            $('.hours-calendar').fullCalendar( 'render' );
            $( this ).addClass( 'active' );
        }

    });

});
