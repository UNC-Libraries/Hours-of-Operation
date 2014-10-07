jQuery(function($) {
    $('.location-delete').on('click', function(e) {
        if(confirm('Are you sure you want to delete this location?')) {
            e.preventDefault();

            var location_id = $(this).closest('tr').first().attr('id');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: $.param({
                    'action': 'location_delete',
                    'location_id': location_id.split('_')[1]}),

                success: function(response) {
                    $('#' + location_id).remove();

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
