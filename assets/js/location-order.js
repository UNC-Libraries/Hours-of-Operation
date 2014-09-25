jQuery(function($) {
    $('#the-list').sortable({
        items: '.list-item',
        opacity: 0.4,
        cursor: 'move',
        axis: 'y',

        update: function() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: $(this).sortable('serialize') + '&action=location_order',
                success: function() {
                    // have to redo odd/even row colors
                    $('.list-item').each(function(index, row) {
                        if ( index % 2 == 0 ) {
                            $(row).addClass('alternate');
                        } else {
                            $(row).removeClass('alternate');
                        }
                    });
                }
            });
        }
    });
});
