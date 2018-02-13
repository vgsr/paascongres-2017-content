/**
 * Paco2017 Content Administration Scripts
 *
 * @package Paco2017 Content
 * @subpackage Administration
 */

/* global ajaxurl, paco2017Admin */
(function( jQuery, window ) {

	/**
	 * Manage association users: user bulk deletion
	 *
	 * @since 1.1.0
	 */
	jQuery(document).ready( function($) {

		var l10n = paco2017Admin.l10n || {},
		    $accountsDeleteMembers = $('#delete-association-users');

		// Bail when this is not the right page
		if ( ! $accountsDeleteMembers.length ) {
			return;
		}

		// Handle button click
		$accountsDeleteMembers.on( 'click', '.delete-me:not(.is-active)', function() {
			var $this = $(this), termId = $this.data('term_id'), options = {
				$el: $this,
				termId: termId,
				nonce: $this.next('#_ajax_nonce-' + termId).val()
			};

			// Bail when not confirmed
			if ( ! termId || ! window.confirm( l10n.aysDeleteAssociationUsers ) ) {
				return;
			}

			// Mark active
			$this.add( $this.siblings('.spinner') ).addClass('is-active');

			// Disable siblings
			$accountsDeleteMembers.find('.delete-me').not( $this ).attr('disabled', true);

			// Fire AJAX call
			doDeleteAssociationUsers( options, usersDeletedCallback );
		});

		/**
		 * Fire AJAX call to delete association users
		 *
		 * @since 1.1.0
		 *
		 * @param  {Object}   options  Request parameter values
		 * @param  {Function} callback Optional. Function to run after AJAX call
		 * @return {Void}
		 */
		function doDeleteAssociationUsers( options, callback ) {
			options = options || {};

			// Create closure to pass `options` object to callback
			var cb = callback && function( resp ) {
				callback( resp, options );
			};

			$.post( ajaxurl, {
				action: 'paco2017-delete-association-users',
				term_id: options.termId || 0,
				_ajax_nonce: options.nonce || ''
			}, cb );
		}

		/**
		 * Process the response of the delete-association-users action
		 *
		 * @since 1.1.0
		 *
		 * @param  {Object} resp    AJAX response data
		 * @param  {Object} options AJAX options data
		 * @return {Void}
		 */
		function usersDeletedCallback( resp, options ) {
			if ( resp.data.left ) {

				// Update user count
				options.$el.text( resp.data.message );

				// Apply new nonce
				if ( resp.nonce ) {
					options.nonce = resp.nonce;
				}

				// Continue deleting
				doDeleteAssociationUsers( options, usersDeletedCallback );
			} else {

				// Replace content
				options.$el.parent().html( $('<p></p>').text( resp.data.message ) );

				// Enable siblings
				$accountsDeleteMembers.find('.delete-me').attr('disabled', false);
			}
		}
	});

})( jQuery, window );
