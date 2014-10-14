jQuery(function($) {
    var $hoo_main = $('.hoo-main');

    $hoo_main.tabs({
        active: false,
        collapsible: true,

        show: {
            effect: 'slide',
            duration: 500
        },
        hide: {
            effect: 'slide',
            duration: 500
        }
    });
});
