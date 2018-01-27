<?php

/**
 * Paco2017 Content Lecture Info Template
 *
 * @package Paco2017 Content
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

?>

<div class="paco2017-info lecture-info">
	<p>

	<?php if ( $item = paco2017_get_agenda_item() ) : ?>

		<?php if ( paco2017_has_conf_day_date( $item ) ) {
			$start_text = paco2017_object_has_conf_location( $item )
				/* translators: 1. Time 2. Date 3. Location adverbial */
				? esc_html__( 'Starts on %2$s at %1$s %3$s', 'paco2017-content' )
				/* translators: 1. Time 2. Date */
				: esc_html__( 'Starts on %2$s at %1$s', 'paco2017-content' );

			printf(
				'<span class="item-detail agenda-related">' . $start_text . '</span>',
				'<span class="detail-value">' . paco2017_get_agenda_item_start_time( $item ) . '</span>',
				'<span class="detail-value">' . paco2017_get_conf_day_date( $item, 'l' ) . '</span>',
				'<span class="detail-value">' . paco2017_get_conf_location_adverbial( $item ) . '</span>'
			);

		} else {
			$start_text = paco2017_object_has_conf_location( $item )
				/* translators: 1. Time 2. Location adverbial */
				? esc_html__( 'Starts at %1$s %2$s', 'paco2017-content' )
				/* translators: 1. Time */
				: esc_html__( 'Starts at %1$s', 'paco2017-content' );

			printf(
				'<span class="item-detail agenda-related">' . $start_text . '</span>',
				'<span class="detail-value">' . paco2017_get_agenda_item_start_time( $item ) . '</span>',
				'<span class="detail-value">' . paco2017_get_conf_location_adverbial( $item ) . '</span>'
			);
		} ?>

	<?php endif; ?>

	<?php if ( paco2017_object_has_speaker() && ! paco2017_is_lecture() ) {
		printf(
			'<span class="item-detail tax-speaker">' . _x( 'Speaker: %s', 'object taxonomy detail', 'paco2017-content' ) . '</span>',
			'<span class="detail-value">' . paco2017_get_speaker_title() . '</span>'
		);
	} ?>

	</p>
</div>
