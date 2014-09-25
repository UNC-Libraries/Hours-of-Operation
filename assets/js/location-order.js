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
                success: function(response) {
                    
                    $('.list-item').each(function(index, row) {
                        // set position text
                        $('.position', row).text( index );
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
    });
});
