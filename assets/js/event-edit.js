jQuery(function($) {
    var $event_start               = $('#event_start'),
        $event_end                 = $('#event_end' ),
        $preview_calendar          = $('#preview_calendar'),
        $event_title               = $('#event_title'),
        $event_category            = $('#event_category'),

        current_event_border_color = '#ffff00',

        event_id                   = $('#event_id').val(),
        event_title                = $event_title.val(),
        event_category_color       = $event_category.find(':selected').data('color'),

        datetime_control_type      = 'select',
        date_format                = 'yy-mm-dd',
        time_format                = 'HH:mm';

    // init fullcalendar
    $preview_calendar.fullCalendar({
        eventSources: [
            {
                url: ajaxurl,
                type: 'GET',
                data: {
                    action: 'location_events',
                    location_id: $('#event_location').val()
                },
            }
        ],
        timezone: 'local',
        timeFormat: '',
        editable: false,

        loading: function(is_loading, view) {
            if ( is_loading ) {
                // add loading animation?
            } else {
                // set current_event
                if ( event_id ) {
                    // event exists. Find from our eventSources and highlight the border
                    var event_instances = $preview_calendar.fullCalendar('clientEvents', event_id);

                    // add a class to set all the current instances and highlight the border
                    $.each( event_instances, function( index, event ) {
                        $( '.fc-bg td[data-date="' + event.start.format( 'YYYY-MM-DD' ) + '"]' )
                            .addClass( 'hoo-current-event' )
                            .css( 'border-top-color', 'yellow' );
                    });
                } else {
                    // event doesn't exist. Append a new event to the eventSources
                    event_id = 'current';
                    var event_source = {
                        events: [
                            {
                                id: event_id,
                                title:  event_title,
                                start: $event_start.val(),
                                end: $event_end.val()
                            }
                        ]
                    };
                    $preview_calendar.fullCalendar('addEventSource', event_source);
                }

                /* change title event
                 TODO: title should always be the the hours the location is open?
                $event_title.on('input', function() {
                    var current_event = $preview_calendar.fullCalendar('clientEvents', event_id)[0],
                        event_title = $event_title.val();

                    current_event.title = event_title;

                    $preview_calendar.fullCalendar('updateEvent', current_event);

                });
                 */

                // change color event
                $event_category.on('change', function() {
                    var current_event = $preview_calendar.fullCalendar('clientEvents', event_id)[0],
                        event_category_color = $event_category.find(':selected').data('color'),
                        text_color = ('000000' + (('0xffffff' ^ '0x' + event_category_color.slice(1)).toString(16))).slice(-6);


                    $( '.hoo-current-event' ).css( 'background-color', event_category_color );
                    current_event.color = event_category_color;
                    $preview_calendar.fullCalendar('updateEvent', current_event);
                });

                /*
                 init datetimepicker
                 */

                $.timepicker.datetimeRange(
                    $event_start,
                    $event_end,
                    {
                        dateFormat: date_format,
                        timeFormat: time_format,

                        controlType: 'select',

                        start: {
                            onSelect: function(dt_text, dt_instance) {
                                var current_event = $preview_calendar.fullCalendar('clientEvents', event_id)[0];

                                current_event.start = $event_start.val();
                                current_event.end = $event_end.val();

                                $preview_calendar.fullCalendar('updateEvent', current_event);
                            }
                        },

                        end: {
                            onSelect: function(dt_text, dt_instance) {
                                var current_event = $preview_calendar.fullCalendar('clientEvents', event_id)[0];

                                current_event.start = $event_start.val();
                                current_event.end = $event_end.val();

                                $preview_calendar.fullCalendar('updateEvent', current_event);
                            }
                        }
                    }
                );
            } // is_loading
        }, // loading

        eventRender: function( event, element, view ) {
            $('.fc-bg td[data-date="' + event.start.format('YYYY-MM-DD') + '"]').css('background-color', event.color);
        }
    }); // fullcalendar
}); // jquery
