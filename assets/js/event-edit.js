jQuery(function($) {
    var $event_start = $('#event_start'),
        $event_end = $('#event_end' );

    $.timepicker.datetimeRange(
        $event_start,
        $event_end,
        { dateFormat: 'yy-mm-dd',
          timeFormat: 'HH:mm',

          controlType: 'select',

          stepMinute: 15,

          start: { 
          },
          end: {}
        }
    );
});
