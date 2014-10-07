jQuery(function($) {
    var $event_start = $('#event_start'),
        $event_end = $('#event_end' ),
        $preview_calendar = $('#preview_calendar'),

        current_event_border_color = '#ffff00',

        event_id = $('#event_id').val(),
        event_title = $('#event_title').val(),
        event_category_color = $('#event_category').find(':selected').data('color');

    // init fullcalendar
    $preview_calendar.fullCalendar({
        eventSources: [
            {
                url: ajaxurl,
                type: 'GET',
                data: {
                    action: 'location_events',
                    location_id: $('#event_location').val()
                }
            }
        ],
        editable: false,

        loading: function(is_loading, view) {

            if ( is_loading ) {
                // add loading animation?
            } else {
                // set current_event
                if ( event_id ) {
                    // event exists. Find from our eventSources and highlight the border
                    var current_event = $preview_calendar.fullCalendar('clientEvents', event_id)[0];

                    console.log(current_event);
                    current_event.borderColor = current_event_border_color;
                    $preview_calendar.fullCalendar('updateEvent', current_event);
                } else {
                    console.log('doesnt exists');
                    // event doesn't exist. Append a new event to the eventSources
                    event_id = 'current';
                    var event_source = [
                        {
                            events: [
                                {
                                    id: event_id,
                                    title:  event_title,
                                    start: $event_start.val(),
                                    end: $event_end.val(),
                                    backgroundColor: event_category_color,
                                    border_color: current_event_border_color
                                }
                            ]
                        }
                    ];


                    $preview_calendar.fullCalendar('addEventSource', event_source);
                }

                // init timepicker
                $.timepicker.datetimeRange(
                    $event_start,
                    $event_end,
                    {
                        dateFormat: 'yy/mm/dd',
                        timeFormat: 'HH:mm',

                        controlType: 'select',
                        stepMinute: 15,


                        start: {
                            onSelect: function(dt_text, dt_instance) {
                                var current_event = $preview_calendar.fullCalendar('clientEvents', event_id)[0];

                                current_event.title = event_title,
                                current_event.start = $event_start.val();
                                current_event.end = $event_end.val();
                                current_event.backgroundColor = event_category_color;

                                $preview_calendar.fullCalendar('updateEvent', current_event);
                            }
                        },
                        end: {
                            onSelect: function(dt_text, dt_instance) {
                                console.log('onselect');
                                console.log(event_id);
                                var current_event = $preview_calendar.fullCalendar('clientEvents', event_id)[0];

                                current_event.title = event_title;
                                current_event.start = $event_start.val();
                                current_event.end = $event_end.val();
                                current_event.backgroundColor = event_category_color;

                                $preview_calendar.fullCalendar('updateEvent', current_event);
                            }
                        }
                    }
                ); // timepicker
            } // is_loading
        } // loading
    }); // fullcalendar
}); // jquery
