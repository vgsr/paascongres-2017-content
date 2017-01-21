<?php

/**
 * Paco2017 Content Workshop Info Template
 *
 * @package Paco2017 Content
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

?>

<div class="paco2017-info workshop-info">
	<p>

	<?php if ( paco2017_object_has_workshop_round() && ! paco2017_is_workshop_round() ) {
		printf(
			'<span class="item-detail tax-workshop-round">' . _x( 'Workshop round: %s', 'object taxonomy detail', 'paco2017-content' ) . '</span>',
			'<span class="detail-value">' . paco2017_get_workshop_round_link() . '</span>'
		);
	} ?>

	<?php if ( paco2017_object_has_speaker() && ! paco2017_is_workshop() ) {
		printf(
			'<span class="item-detail tax-speaker">' . _x( 'Speaker: %s', 'object taxonomy detail', 'paco2017-content' ) . '</span>',
			'<span class="detail-value">' . paco2017_get_speaker_title() . '</span>'
		);
	} ?>

	<?php if ( paco2017_object_has_conf_location() ) {
		printf(
			'<span class="item-detail tax-conf-location">' . _x( 'Location: %s', 'object taxonomy detail', 'paco2017-content' ) . '</span>',
			'<span class="detail-value">' . paco2017_get_conf_location_title() . '</span>'
		);
	} ?>

	<?php if ( paco2017_object_has_workshop_cat() && ! paco2017_is_workshop_category() ) {
		printf(
			'<span class="item-detail tax-workshop-cat">' . _x( 'Category: %s', 'object taxonomy detail', 'paco2017-content' ) . '</span>',
			'<span class="detail-value">' . paco2017_get_workshop_cat_link() . '</span>'
		);
	} ?>

	</p>
</div>
