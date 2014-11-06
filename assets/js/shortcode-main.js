jQuery( function( $ ) {
    var $hoo_list_rows = $( '.location-row' ),
        $hours_calendars = $( '.hours-calendar' ),
        map_options = {
            center: {
                lat: -34.397,
                lng: 150.644
            },
            zoom: 8
        },
        map = new google.maps.Map( document.getElementById( 'map-canvas' ),
                                   map_options );

    $hours_calendars.each( function( index, hour_cal ) {
        $( hour_cal ).fullCalendar( {
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
            eventRender: function( event, element, view ) {
                // render the whole events calendar square with the events category color
                $('.fc-bg td[data-date="' + event.start.format('YYYY-MM-DD') + '"]', hour_cal).css('background-color', event.color);
            },
            height: 325,
            timezone: 'local',
            timeFormat: '',
            editable: false
        });
    } );

    $hoo_list_rows.on( 'click', function( e ) {
        var location_id = $( this ).data( 'panel' ).split( '-' )[1],
            $this_panel = $( '#' + $( this ).data( 'panel' ) ),
            $visible_panel = $('.panel:visible');

        if ( $this_panel.is( ':visible' ) ) {
            $this_panel.hide( 'slide', { direction: 'left', easing: 'easeOutExpo' }, 500 );
        }
        else if ( $visible_panel.length ) {
            $this_panel.show('fade', {
                complete: function(){
                    $visible_panel.hide('fade');
                    $('.hours-calendar').fullCalendar( 'render' );
                }
            });
        } else {
            $this_panel.show( 'slide', {direction: 'left', easing: 'easeOutBounce' } , 800);
            // render as soon as possible.  hopefully this will always render and we don't have to wait for the animation to complete
            $('.hours-calendar').fullCalendar( 'render' );
        }

    });

});
