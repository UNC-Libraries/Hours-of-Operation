jQuery( function( $ ) {
    var $color_field = $( '.category-color-field' ),
        picker_options = {
            defaultColor: false,
            palettes: true
    };

    $color_field.wpColorPicker( picker_options );
});

