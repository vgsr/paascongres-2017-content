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

	<?php

	// Scheduled by day
	if ( paco2017_has_conf_day_date( $item ) ) {
		$info_text = paco2017_object_has_conf_location( $item )
			/* translators: 1. Time 2. Date 3. Location adverbial */
			? __( 'This item is scheduled for %2$s %3$s at %1$s.', 'paco2017-content' )
			/* translators: 1. Time 2. Date */
			: __( 'This item is scheduled for %2$s at %1$s.', 'paco2017-content' );

		printf(
			$info_text,
			paco2017_get_agenda_item_start_time( $item ),
			paco2017_get_conf_day_date( $item ),
			paco2017_get_conf_location_adverbial( $item )
		);

	// Scheduled without day
	} else {
		$info_text = paco2017_object_has_conf_location( $item )
			/* translators: 1. Time 2. Location adverbial */
			? __( 'This item is scheduled at %1$s %2$s.', 'paco2017-content' )
			/* translators: 1. Time */
			: __( 'This item is scheduled at %1$s.', 'paco2017-content' );

		printf(
			$info_text,
			paco2017_get_agenda_item_start_time( $item ),
			paco2017_get_conf_location_adverbial( $item )
		);
	} ?>

	</p>
</div>
