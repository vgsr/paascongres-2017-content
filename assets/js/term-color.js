jQuery( document ).ready( function( $ ) {
    'use strict';

    /**
     * Term colorpicker
     */
    if ( typeof $.wp === 'object' && typeof $.wp.wpColorPicker === 'function' ) {
        $( '#term-color' ).wpColorPicker();
    } else {
        $( '#colorpicker' ).farbtastic( '#term-color' );
    }

    $( '.editinline' ).on( 'click', function( e ) {
        var tag_id = $( this ).parents( 'tr' ).attr( 'id' ),
			color  = $( 'td.color i', '#' + tag_id ).data( 'color' );

        $( ':input[name="term-color"]', '.inline-edit-row' ).val( color );
    } );
} );
