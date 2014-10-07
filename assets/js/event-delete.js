jQuery(function($) {
    $('.event-delete').on('click', function(e) {
        if(confirm('Are you sure you want to delete this event?')) {
            e.preventDefault();

            var event_id = $(this).closest('tr').first().attr('id');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: $.param({
                    'action': 'location_event_delete',
                    'event_id': event_id.split('_')[1]}),

                success: function(response) {
                    $('#' + event_id).remove();

                    $('.list-item').each(function(index, row) {
                        // set odd/even colors
                        if ( index % 2 == 0 ) {
                            $(row).addClass('alternate');
                        } else {
                            $(row).removeClass('alternate');
                        }
                    });
                }
            });
        }
        return false;
    });
});
