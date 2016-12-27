<?php

/**
 * Paco2017 Content Association Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Taxonomy ******************************************************************/

/**
 * Return the Association taxonomy
 *
 * @since 1.0.0
 *
 * @return string Taxonomy name
 */
function paco2017_get_association_tax_id() {
	return 'paco2017_association';
}

/**
 * Return the labels for the Association taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_association_tax_labels'
 * @return array Association taxonomy labels
 */
function paco2017_get_association_tax_labels() {
	return apply_filters( 'paco2017_get_association_tax_labels', array(
		'name'          => __( 'Paascongres Associations', 'paco2017-content' ),
		'menu_name'     => __( 'Associations',             'paco2017-content' ),
		'singular_name' => __( 'Association',              'paco2017-content' ),
		'search_items'  => __( 'Search Associations',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => __( 'All Associations',         'paco2017-content' ),
		'no_items'      => __( 'No Association',           'paco2017-content' ),
		'edit_item'     => __( 'Edit Association',         'paco2017-content' ),
		'update_item'   => __( 'Update Association',       'paco2017-content' ),
		'add_new_item'  => __( 'Add New Association',      'paco2017-content' ),
		'new_item_name' => __( 'New Association Name',     'paco2017-content' ),
		'view_item'     => __( 'View Association',         'paco2017-content' )
	) );
}

/**
 * Modify the link returned for the given association term
 *
 * @since 1.0.0
 *
 * @param string $link Term link
 * @param WP_Term $term Term object
 * @param string $taxonomy Taxonomy name
 * @return string Term link
 */
function paco2017_get_association_term_link( $link, $term, $taxonomy ) {
	
	// When this is an association term
	if ( paco2017_get_association_tax_id() === $taxonomy ) {

		// For admins, link to the association-filtered admin user list
		if ( current_user_can( 'edit_users' ) ) {
			$link = add_query_arg( array( 'paco2017-association' => $term->term_id ), admin_url( 'users.php' ) );
		}
	}

	return $link;
}

/** Template ******************************************************************/

/**
 * Return the user's association term
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_user_association'
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return WP_Term|bool User association term or False when not found.
 */
function paco2017_get_user_association( $user_id = 0 ) {

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	// Define return value
	$association = false;

	// Get the user's terms
	$terms = wp_get_object_terms( $user_id, paco2017_get_association_tax_id() );

	if ( $terms && ! is_wp_error( $terms ) ) {
		$association = $terms[0];
	}

	return apply_filters( 'paco2017_get_user_association', $association, $user_id );
}

/**
 * Return the user's association term
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_association_users'
 *
 * @param WP_Term|int|string $term Term object or id or name or slug.
 * @param string $by Value type to get the term with in {@see get_term_by()}. Defaults to 'term_id'.
 * @return array Association users.
 */
function paco2017_get_association_users( $association, $by = 'term_id' ) {

	// Default to the current user
	if ( empty( $association ) )
		return array();

	$taxonomy = paco2017_get_association_tax_id();

	// Get the term
	if ( ! is_a( $association, 'WP_Term' ) ) {
		$association = get_term_by( $by, $association, $taxonomy );
		if ( ! $association ) {
			return array();
		}
	}

	// Get the term's users
	$users = get_objects_in_term( $association->term_id, $taxonomy );

	// Default to empty error
	if ( ! $users || is_wp_error( $users ) ) {
		$users = array();
	}

	return (array) apply_filters( 'paco2017_get_association_users', $users, $term_id );
}

/**
 * Return the user's association title
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return string Association title
 */
function paco2017_get_association_title( $user_id = 0 ) {

	// Get the user's association
	$association = paco2017_get_user_association( $user_id );
	$title = '';

	if ( is_a( $association, 'WP_Term' ) ) {
		$title = $association->name;
	} elseif ( is_string( $association ) ) {
		$title = $association;
	}

	return apply_filters( 'paco2017_get_association_title', $title, $user_id, $association );
}

/**
 * Return the user count of the enrolled users for the given association
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|string $association Term object or id or name or slug
 * @return int Enrolled association user count
 */
function paco2017_get_enrolled_users_for_association_count( $association ) {

	// Count the queried users
	$users = paco2017_get_enrolled_users_for_association( $association );
	$count = count( $users );

	return $count;
}

/**
 * Return the enrolled users for the given association
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|string $association Term object or id or name or slug
 * @return array Enrolled association users
 */
function paco2017_get_enrolled_users_for_association( $association ) {
	return (array) apply_filters( 'paco2017_get_enrolled_users_for_association', array(), $association );
}
