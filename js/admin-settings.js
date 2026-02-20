/**
 * Abstract Box — Admin Settings Script
 *
 * Initialises WordPress colour pickers on the settings page.
 *
 * @package AbstractBox
 * @since   2.0.0
 */

( function ( $ ) {
    'use strict';

    $( document ).ready( function () {
        // Initialise all colour picker inputs.
        $( '.abstract-box-color-picker' ).wpColorPicker();
    } );
} )( jQuery );
