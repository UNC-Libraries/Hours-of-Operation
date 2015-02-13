jQuery(function($) {
    $('.category-delete').on('click', function(e) {
        if(confirm('Are you sure you want to delete this category?')) {
            e.preventDefault();

            var category_id = $(this).closest('tr').first().attr('id');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: $.param({
                    'action': 'category_delete',
                    'category_id': category_id.split('_')[1]}),

                success: function(response) {
                    $('#' + category_id).remove();

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
