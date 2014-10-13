jQuery(function($) {
    var $content_div = $('#hoo-location-detail-wrapper'),
        $location_row = $('.location-row');

    $location_row.on('click', function(event) {
        $.ajax({
            url: HOO.ajaxurl,
            type: 'GET',
            data: $.param({
                action: 'location_detail_render',
                location_id: $(this).data('location-id')
            }),

            success: function(response) {
                $content_div.html(response.location);
            }
        });
    });
});
