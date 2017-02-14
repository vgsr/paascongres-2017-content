/**
 * Paco2017 Content BuddyPress scripts
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */
jQuery( document ).ready( function( $ ) {
	var $filter = $( 'li.paco2017-filter' ), hideFilter;

	// Init directory filter
	if ( undefined !== jq.cookie('bp-members-extras') && jq('#members-association-select select').length ) {
		jq('#members-association-select select option[value="' + jq.cookie('bp-members-extras') + '"]').prop( 'selected', true );
	}

	// Directory filter
	$filter.on( 'change', 'select', function() {
		var el, css_id, object, extra, extras, $this = $(this);

		// Get context
		if ( $('.item-list-tabs li.selected').length ) {
			el = $('.item-list-tabs li.selected');
		} else {
			el = $(this);
		}

		css_id = el.attr('id').split('-');
		object = css_id[0];

		// $.cookie does not accept {} data. We CAN do JSON.stringify(),
		// but BP would then have to do JSON.parse() on their end when
		// reading and using cookie data.
		// extras = $.cookie( 'bp-' + object + '-extras' ) || {};
		// extras[ $this.attr('id') ] = $this.val();
		extras = $this.val();

		// Update cookie
		$.cookie( 'bp-' + object + '-extras', extras, {
			path: '/',
			secure: ( 'https:' === window.location.protocol )
		} );

		// Trigger new request
		$( 'li.filter #members-order-by' ).change();
	});

	/**
	 * Helper function to check and hide the filter element
	 */
	hideFilter = function( $div ) {
		$div = $div || $( 'div.item-list-tabs:not(#subnav) .selected' );

		if ( $div.attr('id') === 'members-paco2017_association' ) {
			$filter.addClass( 'hidden' );
		} else {
			$filter.removeClass( 'hidden' );
		}
	};

	// Toggle directory filter
	$( 'div.item-list-tabs:not(#subnav)' ).on( 'click', 'li', function() {
		hideFilter( $(this) );
	});

	// Trigger on init
	hideFilter();
});
