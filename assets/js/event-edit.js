jQuery(function($) {
    var $event_start_date          = $( '#event_start_date' ),
        $event_start_datetime      = $( '#event_start' ),
        $event_end_datetime        = $( '#event_end' ),
        $preview_calendar          = $( '#preview_calendar' ),
        $event_title               = $( '#event_title' ),
        $event_category            = $( '#event_category' ),
        $event_is_all_day          = $( '#event_is_all_day' ),
        $event_is_visible          = $( '#event_is_visible' ),
        $event_is_closed           = $( '#event_is_closed' ),
        $event_form                = $( '#event_form' ),

        current_event_border_color = '#ffff00',

        event_id                   = $( '#event_id' ).val(),
        event_title                = $event_title.val(),
        event_category_color       = $event_category.find( ':selected' ).data( 'color' ),

        $rrule_container           = $( '#rrule-custom-container' ),
        $rrule_frequency           = $( '#event_recurrence_rule' ),
        $rrule_custom_frequency    = $( '#event_recurrence_rule_custom' ),
        $rrule_until               = $( '#event_recurrence_rule_custom_until' ),
        $rrule                     = $( '.hoo-rrule' ),

        datetime_control_type      = 'select';


    $event_form.validate();

    // init fullcalendar
    $preview_calendar.fullCalendar(
        {
            events: function( cal_start, cal_end, tz, cb ) {
                var ajax_action  = 'action=location_events',
                    cal_start    = 'start=' + cal_start.format(),
                    cal_end      = 'end=' + cal_end.format(),
                    event_inputs = $( ':input:visible,#event_id,#event_category,#event_location,#event_is_visible' ).serialize();

                $.ajax(
                    {
                        url: ajaxurl,
                        type: 'GET',
                        // TODO: filter out more uneeded inputs
                        data: [ajax_action, cal_start, cal_end, event_id, event_inputs].join( '&' ),
                        success: function( response) {
                            cb( response );
                        }
                    }
                );
            },

            defaultDate: $event_start_date.val(), 
            fixedWeekCount: false,
            timeFormat: '',
            editable: false,

            loading: function( is_loading, view ) {
                if ( is_loading ) {
                    // add loading animation?
                } else {
                    $event_category.on( 'change', function() {
                        $preview_calendar.fullCalendar( 'refetchEvents' );
                    } );

                    /*
                     init date and time pickers
                     */

                    $event_start_date.datepicker(
                        {
                            dateFormat: 'yy-mm-dd',
                            onSelect: function( select_date ) {
                                $preview_calendar.fullCalendar('gotoDate', $event_start_date.datetimepicker( 'getDate' ));
                                $preview_calendar.fullCalendar( 'refetchEvents' );
                            }
                        } );

                    $rrule_until.datepicker( {
                        dateFormat: 'yy-mm-dd',
                        onSelect: function( select_date ) {
                            $preview_calendar.fullCalendar('gotoDate', $rrule_until.datetimepicker( 'getDate' ));
                            $preview_calendar.fullCalendar( 'refetchEvents' );
                        }
                    } );


                    $event_start_datetime.datetimepicker(
                        {
                            dateFormat: 'yy-mm-dd',
                            timeFormat: 'hh:mm tt',
                            stepMinute: 15,

                            onSelect: function ( selected ) {
                                if ( $event_end_datetime.val() != '' ) {
                                    var test_start = $event_start_datetime.datetimepicker( 'getDate' ),
                                        test_end   = $event_end_datetime.datetimepicker( 'getDate' );

                                    if ( test_start > test_end ) {
                                        test_start.setHours( test_end.getHours(), test_end.getMinutes() );
                                        $event_end_datetime.datetimepicker( 'setDate', test_start );
                                    }
                                }
                                $preview_calendar.fullCalendar('gotoDate', $event_start_datetime.datetimepicker( 'getDate' ));
                                $preview_calendar.fullCalendar( 'refetchEvents' );
                            },
                            onClose: function( selected ) {
                                var test_start = $event_start_datetime.datetimepicker( 'getDate' ),
                                    test_end   = $event_end_datetime.datetimepicker( 'getDate' );

                                    if ( test_start > test_end ) {
                                        $event_end_datetime.datetimepicker( 'setDate', test_start );
                                }
                                $preview_calendar.fullCalendar( 'refetchEvents' );
                            }
                        } );

                    $event_end_datetime.datetimepicker(
                        {
                            dateFormat: 'yy-mm-dd',
                            timeFormat: 'hh:mm tt',
                            stepMinute: 15,
                            showButtonPanel: true,

                            onSelect: function ( selected ) {
                                if ( $event_start_datetime.val() != '' ) {
                                    var test_start = $event_start_datetime.datetimepicker( 'getDate' ),
                                        test_end   = $event_end_datetime.datetimepicker( 'getDate' );

                                    if ( test_start > test_end ) {
                                        test_start.setHours( test_end.getHours(), test_end.getMinutes() );
                                        $event_end_datetime.datetimepicker( 'setDate', test_start );
                                    }

                                    $preview_calendar.fullCalendar('gotoDate', $event_end_datetime.datetimepicker( 'getDate' ));
                                    $preview_calendar.fullCalendar( 'refetchEvents' );
                                }
                            },
                            onClose: function( selected ) {
                                var test_start = $event_start_datetime.datetimepicker( 'getDate' ),
                                    test_end   = $event_end_datetime.datetimepicker( 'getDate' );

                                if ( test_start > test_end ) {
                                    test_start.setHours( test_end.getHours(), test_end.getMinutes() );
                                        $event_end_datetime.datetimepicker( 'setDate', test_start );
                                }
                                $preview_calendar.fullCalendar( 'refetchEvents' );
                            }
                        } );

                    // title
                    $event_title.on( 'change', function() {
                        $preview_calendar.fullCalendar( 'refetchEvents' );
                    } );

                    // visibility
                    $event_is_visible.on( 'change', function() {
                        $preview_calendar.fullCalendar( 'refetchEvents' );
                    } );

                    // all day
                    $event_is_all_day.on( 'change', function() {
                        if ( this.checked ) {
                            $( '.date-field' ).removeClass( 'is-hidden' );
                            $( '.datetime-field' ).addClass( 'is-hidden' );
                            $event_is_closed.prop( 'checked', false );
                        } else {
                            $( '.date-field' ).addClass( 'is-hidden' );
                            $( '.datetime-field' ).removeClass( 'is-hidden' );
                        }
                        $preview_calendar.fullCalendar( 'refetchEvents' );
                    } );

                    // is closed
                    $event_is_closed.on( 'change', function() {
                        if ( this.checked ) {
                            $( '.date-field' ).removeClass( 'is-hidden' );
                            $( '.datetime-field' ).addClass( 'is-hidden' );
                            $event_is_all_day.prop( 'checked', false );
                        } else {
                            $( '.date-field' ).addClass( 'is-hidden' );
                            $( '.datetime-field' ).removeClass( 'is-hidden' );
                        }
                        $preview_calendar.fullCalendar( 'refetchEvents' );

                    } );


                    // recurrence rules
                    $rrule_frequency.on('change', function() {
                        if ( this.value == 'CUSTOM' ) {
                            $rrule_container.removeClass( 'is-hidden' );
                        } else {
                            $rrule_container.addClass( 'is-hidden' );
                        }

                        if ( this.value == 'NONE' ) {
                            $( '#rrule-until' ).addClass( 'is-hidden' );
                        } else {
                            $( '#rrule-until' ).removeClass( 'is-hidden' );
                        }

                    });

                    $rrule_custom_frequency.on( 'change', function() {
                        var $option = $(this);

                        // hide all cust    m rule options
                        $( '#rrule-custom-container .rrule-custom').not('.interval').addClass( 'is-hidden' );

                        // set unit text
                        $( '#interval-unit' ).text( $option.find(':selected').data( 'freq-unit' ) );

                        // show options for current frequency
                        $( '#rrule-custom-container .' + $option.val().toLowerCase() ).removeClass( 'is-hidden' );
                    });

                    $(".hoo-rrule").on('change', function(event) {
                        event.stopImmediatePropagation();
                        $preview_calendar.fullCalendar( 'refetchEvents' );
                    });
                } // is_loading
            } // loading
        }); // fullcalendar
}); // jquery
