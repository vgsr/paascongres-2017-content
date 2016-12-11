<?php

/**
 * Paco2017 Content BuddyPress Activity Functions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Modify the list of filter options in the activity directory
 *
 * @since 1.0.0
 *
 * @param array $options Options
 * @param string $context Activity context
 * @return array Options
 */
function paco2017_bp_activity_filters_options( $options, $context ) {

	// Remove unwanted filter options
	$options = array_diff_key( $options, array_flip( array(
		'new_member',     // New member registration
		'activity_update' // Custom member activity updates
	) ) );

	return $options;
}
