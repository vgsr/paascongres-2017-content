<?php

/**
 * Paco2017 Content Related Agenda Item Info Template
 *
 * @package Paco2017 Content
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Bail when there's no Agenda item
if ( ! $item = paco2017_get_agenda_item() )
	return;

?>

<div class="paco2017-info agenda-info">
	<p>

	<?php if ( paco2017_has_conf_day_date( $item ) ) {
		printf(
			__( 'This item is scheduled for %2$s at %1$s.', 'paco2017-content' ),
			paco2017_get_agenda_item_start_time( $item ),
			paco2017_get_conf_day_date( $item )
		);
	} else {
		printf(
			__( 'This item is scheduled at %1$s.', 'paco2017-content' ),
			paco2017_get_agenda_item_start_time( $item )
		);
	} ?>

	<?php if ( paco2017_object_has_conf_location( $item ) ) {
		printf(
			'<span class="item-detail tax-conf-location">' . _x( 'Location: %s', 'object taxonomy detail', 'paco2017-content' ) . '</span>',
			'<span class="detail-value">' . paco2017_get_conf_location_title( $item ) . '</span>'
		);
	} ?>

	</p>
</div>
