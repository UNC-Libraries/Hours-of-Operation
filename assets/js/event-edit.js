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

        $rrule_container           = $('#rrule-custom-container'),
        $rrule_frequency           = $( '#event_recurrence_rule' ),
        $rrule_custom_frequency    = $( '#event_recurrence_rule_custom' ),
        $rrule                     = $( '.hoo-rrule' ),

        freq_units                 = { 'WEEKLY': 'week',
                                       'HOURLY': 'hour',
                                       'DAILY' : 'day',
                                       'MONTHLY': 'month'
                                     },

        datetime_control_type      = 'select',
        date_format                = 'yy-mm-dd',
        time_format                = 'HH:mm';

    // init fullcalendar
    $preview_calendar.fullCalendar({
        events: function( cal_start, cal_end, tz, cb ) {
            var ajax_action = 'action=location_events',
                cal_start = 'start=' + cal_start.format(),
                cal_end = 'end=' + cal_end.format(),
                event_inputs = $(':input', 'form').not("input[name='action']").serialize();

            console.log('fetch');
            // reset calendar day
            $('.fc-bg td').css('background-color', 'transparent');
            $.ajax(
                {
                    url: ajaxurl,
                    type: 'GET',
                    // TODO: filter out more uneeded inputs
                    data: [ajax_action, cal_start, cal_end, event_inputs].join( '&' ),
                    success: function( response) {
                        console.log('blah');
                        cb( response );
                    }
                }
            );
        },
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

                $event_category.on('change', function() {
                    $preview_calendar.fullCalendar( 'refetchEvents' );
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
                                $preview_calendar.fullCalendar( 'refetchEvents' );
                            }
                        },

                        end: {
                            onSelect: function(dt_text, dt_instance) {
                                $preview_calendar.fullCalendar( 'refetchEvents' );
                            }
                        }
                    }
                );

                // recurrence rules
                $rrule_frequency.on('change', function() {
                    if ( this.value == 'CUSTOM' ) {
                        $rrule_container.removeClass( 'is-hidden' );
                    } else {
                        $rrule_container.addClass( 'is-hidden' );
                    }

                });

                $rrule_custom_frequency.on( 'change', function() {
                    var $option = $(this);

                    // hide all custom rule options
                    $( '#rrule-custom-container .rrule-custom').not('.interval').addClass( 'is-hidden' );

                    // set unit text
                    $( '#interval-unit' ).text( freq_units[ $option.find(':selected').val() ] );

                    // show options for current frequency
                    $( '#rrule-custom-container .' + $option.val().toLowerCase() ).removeClass( 'is-hidden' );
                });

                $(".hoo-rrule").on('change', function(event) {
                    event.stopImmediatePropagation();
                    $preview_calendar.fullCalendar( 'refetchEvents' );
                });
            } // is_loading
        }, // loading

        eventRender: function( event, element, view ) {
            // render the whole events calendar square with the events category color
            $('.fc-bg td[data-date="' + event.start.format('YYYY-MM-DD') + '"]').css('background-color', event.color);
        },
        eventAfterAllRender: function( event, element, view ) {
        }
    }); // fullcalendar
}); // jquery
