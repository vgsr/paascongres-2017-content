<?php

/**
 * Paco2017 Content BuddyPress Template functions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Query *********************************************************************/

/**
 * Add checks for plugin BuddyPress conditions to posts_clauses filter
 *
 * @since 1.1.0
 *
 * @global WPDB $wpdb
 *
 * @param array $clauses SQL clauses
 * @param WP_Query $query Post query object
 * @return array SQL clauses
 */
function paco2017_bp_posts_clauses( $clauses, $query ) {
	global $wpdb;

	// Bail when filters are suppressed on this query
	if ( true === $query->get( 'suppress_filters' ) )
		return $clauses;

	// Get BuddyPress
	$bp = buddypress();

	// Filter by workshop limit
	if ( $query->get( 'workshop_limit', false ) ) {

		// Limit by field's attendee count
		if ( $field = $query->get( 'workshop_field', false ) ) {
			$field_ids = $field->id;
		} else {
			$field_ids = paco2017_bp_xprofile_get_workshop_fields( true );
			$field_ids = implode( ',', $field_ids );
		}

		// Include user's selections
		$selected    = paco2017_get_user_workshops();
		$or_selected = ! empty( $selected ) ? " OR {$wpdb->posts}.ID IN ( " . implode( ',', $selected ) . ' )' : '';

		/**
		 * Query posts that haven't reached their attendee limit
		 *
		 * Fetches workshop limit from post meta and current workshop attendee count
		 * from BP profile data as the count of registered posts for workshop fields.
		 * Then filter out those that reached their limit unless they're in the user's
		 * selection.
		 */
		$clauses['join']  .= $wpdb->prepare( " LEFT JOIN {$wpdb->postmeta} workshop_limit ON {$wpdb->posts}.ID = workshop_limit.post_id AND workshop_limit.meta_key = %s LEFT JOIN ( SELECT value, COUNT( * ) AS count FROM {$bp->profile->table_name_data} WHERE field_id IN ({$field_ids}) GROUP BY value ) workshop_data ON {$wpdb->posts}.ID = workshop_data.value", 'limit' );
		$clauses['where'] .= " AND ( workshop_limit.meta_value > workshop_data.count $or_selected )";
	}

	return $clauses;
}
