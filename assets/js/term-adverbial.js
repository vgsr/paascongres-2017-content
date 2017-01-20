jQuery( document ).ready( function( $ ) {
    'use strict';

    /**
     * Extend `edit` method on `inlineEditTax` for adding input values.
     */
    if ( typeof inlineEditTax !== 'undefined' ) {
        var wp_inline_edit = inlineEditTax.edit;

        // Overwrite method to extend it
        inlineEditTax.edit = function( id ) {
            wp_inline_edit.apply( this, arguments );

            /**
             * From here our own extending logic
             */
            var editRow, rowData, val, t = this;
            if ( typeof(id) === 'object' ) {
                id = t.getId(id);
            }

            editRow = $( '.inline-editor' ), rowData = $( '#tag-' + id );

            // Set quick edit value
            val = $( '.adverbial span', rowData ).data( 'adverbial' );
            $( ':input[name="term-adverbial"]', editRow ).val( val );
        }
    }
} );
