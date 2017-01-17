<?php

/**
 * Paco2017 Content Speaker Info Page Template
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

<div class="agenda-info">
	<p><?php if ( paco2017_has_conf_day_date( $item ) ) {
		printf(
			__( 'This item is scheduled for %1$s at %2$s.', 'paco2017-content' ),
			paco2017_get_conf_day_date( $item ),
			paco2017_get_agenda_item_start_time( $item )
		);
	} else {
		printf(
			__( 'This item is scheduled at %2$s.', 'paco2017-content' ),
			paco2017_get_agenda_item_start_time( $item )
		);
	} ?></p>
</div>
