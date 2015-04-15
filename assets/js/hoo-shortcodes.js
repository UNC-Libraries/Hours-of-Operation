jQuery( function( $ ) {
    var $shortcode_attributes = $( '.shortcode_attribute' ),
        $shortcode_output = $( 'pre.shortcode code' ),
        $hoo_widget   = $( '#hoo_widget' ),

        generate_shortcode = function() {
            var attributes = $shortcode_attributes.filter( function() {
                return this.value && 0 !== this.value.length && ! $( this ).prop( 'disabled' ); } ).map( function() {
                    return this.name + '="' + this.value + '"';
                } ).get();

            return '[hoo' + ( 0 < attributes.length ? ' ': '' ) + attributes.join( ' ' ) + ']';
        };

    $hoo_widget.on( 'change', function() {
        var selected_widget = this.value;

        $shortcode_attributes.each( function() {
            if ( 'widget' === this.name ) return;

            var valid_widgets = $( this ).data( 'validWidgets' ).split( ' ' ),
                is_valid = ! ( -1 < $.inArray( selected_widget, valid_widgets ) );

                $( this ).prop( 'disabled', is_valid );
        } );
    } );

    $shortcode_attributes.on( 'change', function() {
        $shortcode_output.text( generate_shortcode() );
    } );
} );
