jQuery( document ).ready( function( $ ) {
    'use strict';

    var args = {
        dateFormat: 'dd-mm-yy'
    };

    /**
     * Term datepicker
     */
    if ( typeof $.fn.datepicker === 'function' ) {
        $( '#term-date' ).datepicker( args );
    } else {
        return;
    }

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

            // Apply datepicker
            val = $( '.date span', rowData ).data( 'date' );
            $( ':input[name="term-date"]', editRow ).datepicker( args ).datepicker( 'setDate', val );
        }
    }
} );
