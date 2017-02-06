<?php

/**
 * Paco2017 Content Users Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Return whether the user is enrolled
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_is_user_enrolled'
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return bool Is the user enrolled?
 */
function paco2017_is_user_enrolled( $user_id = 0 ) {

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	return (bool) apply_filters( 'paco2017_is_user_enrolled', false, $user_id );
}

/**
 * Output the enrolled users count
 *
 * @since 1.0.0
 */
function paco2017_enrolled_users_count() {
	echo paco2017_get_enrolled_users_count();
}

/**
 * Return the enrolled users count
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_enrolled_users_count'
 * @return int Enrolled user count
 */
function paco2017_get_enrolled_users_count() {
	$users = paco2017_get_enrolled_users();

	// Grouped list
	if ( count( $users ) !== count( $users, COUNT_RECURSIVE ) ) {
		$count = array_sum( array_map( 'count', $users ) );

	// Plain list
	} else {
		$count = count( $users );
	}

	return (int) apply_filters( 'paco2017_get_enrolled_users_count', $count );
}

/**
 * Return the enrolled users
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_enrolled_users'
 * @return array Enrolled users
 */
function paco2017_get_enrolled_users() {
	return (array) apply_filters( 'paco2017_get_enrolled_users', array() );
}

/**
 * Return the enrolled users by association
 *
 * @since 1.1.0
 *
 * @param bool $object Optional. Whether to return user objects or ids. Defaults to false.
 * @param int|array $term_id Optional. Association term to query the users for. Defaults to all associations.
 * @return array Enrolled users by association
 */
function paco2017_get_enrolled_users_by_association( $object = false, $term_id = 0 ) {
	$users = paco2017_get_enrolled_users();
	$users_by_association = array();

	// Walk associations
	foreach ( get_terms( array(
		'taxonomy' => paco2017_get_association_tax_id(),
		'include'  => $term_id ? (array) $term_id : array()
	) ) as $term ) {

		// Query enrolled users per association
		$uquery = new WP_User_Query( array(
			'fields'    => $object ? 'all' : 'ID',
			'include'   => $users,
			'tax_query' => array(
				array(
					'taxonomy' => paco2017_get_association_tax_id(),
					'terms'    => array( $term->term_id )
				)
			)
		) );
		$users_by_association[ $term->term_id ] = $uquery->results;
	}

	return $users_by_association;
}

/**
 * Return the enrolled users from cache
 *
 * @since 1.1.0
 *
 * @return array Enrolled users IDs
 */
function paco2017_get_enrolled_users_cache() {
	return get_option( '_paco2017_enrolled_users', array() );
}

/**
 * Update the enrolled users cache
 *
 * @since 1.1.0
 *
 * @param array $users Current list of enrolled users IDs
 */
function paco2017_update_enrolled_users_cache( $users = array() ) {
	update_option( '_paco2017_enrolled_users', array_map( 'absint', (array) $users ) );
}

/**
 * Modify the user query before it is executed
 *
 * @since 1.1.0
 *
 * @global WPDB $wpdb
 * @param WP_User_Query $user_query
 */
function paco2017_pre_user_query( $user_query ) {
	global $wpdb;

	/**
	 * Taxonomy queries are not supported by default in WP_User_Query.
	 * Untill {@link https://core.trac.wordpress.org/ticket/31383} is put
	 * into place, run our own tax query parser.
	 */
	$tax_query = $user_query->get( 'tax_query' );
	if ( $tax_query ) {
		$tax_query   = new WP_Tax_Query( $tax_query );
		$tax_clauses = $tax_query->get_sql( $wpdb->users, 'ID' );

		if ( ! empty( $tax_clauses['join'] ) ) {
			$user_query->query_from  .= $tax_clauses['join'];
			$user_query->query_where .= $tax_clauses['where'];
		}

		// Remove the tax query vars to prevent duplicate parsing
		$user_query->set( 'tax_query', null );
	}
}
