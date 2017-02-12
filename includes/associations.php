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
 * Act when the Association taxonomy has been registered
 *
 * @since 1.1.0
 */
function paco2017_registered_association_taxonomy() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_association_rest_fields' );
}

/**
 * Register REST fields for the Association taxonomy
 *
 * @since 1.1.0
 */
function paco2017_register_association_rest_fields() {

	// Get assets
	$association = paco2017_get_association_tax_id();

	/** Taxonomy terms ********************************************************/

	// Add color to Association
	register_rest_field(
		$association,
		'color',
		array(
			'get_callback' => 'paco2017_get_rest_term_meta'
		)
	);

	// Add photo to Association
	register_rest_field(
		$association,
		'logo',
		array(
			'get_callback' => 'paco2017_get_association_rest_logo'
		)
	);

	// Add enrolled user count
	register_rest_field(
		$association,
		'enrolled_count',
		array(
			'get_callback' => 'paco2017_get_association_rest_enrolled_count'
		)
	);
}

/**
 * Return the value for the 'logo' association REST API field
 *
 * @since 1.1.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Association logo details
 */
function paco2017_get_association_rest_logo( $object, $field_name, $request ) {
	return paco2017_get_rest_image( paco2017_get_association_logo_id( $object['id'] ), array( 150, 150 ) );
}

/**
 * Return the value for the 'enrolled_count' association REST API field
 *
 * @since 1.1.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return int Association enrolled count
 */
function paco2017_get_association_rest_enrolled_count( $object, $field_name, $request ) {
	return paco2017_get_enrolled_users_for_association_count( $object['id'] );
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

/** Query *********************************************************************/

/**
 * Setup and run the Associations query
 *
 * @since 1.0.0
 *
 * @param array $args Query arguments.
 * @return bool Has the query returned any results?
 */
function paco2017_query_associations( $args = array() ) {

	// Get query object
	$query = paco2017_content()->association_query;

	// Reset query defaults
	$query->in_the_loop  = false;
	$query->current_term = -1;
	$query->term_count   = 0;
	$query->term         = null;
	$query->terms        = array();

	// Define query args
	$r = wp_parse_args( $args, array(
		'taxonomy'        => paco2017_get_association_tax_id(),
		'number'          => 0,
		'paged'           => 0,
		'fields'          => 'all',
		'hide_empty'      => true
	) );

	// Pagination
	if ( (int) $r['number'] > 0 ) {
		$r['paged'] = absint( $r['paged'] );
		if ( $r['paged'] == 0 ) {
			$r['paged'] = 1;
		}
		$r['offset'] = absint( ( $r['paged'] - 1 ) * (int) $r['number'] );
	} else {
		$r['number'] = 0;
	}

	// Run query to get the taxonomy terms
	$query->query( $r );

	// Set query results
	$query->term_count = count( $query->terms );
	if ( $query->term_count > 0 ) {
		$query->term = $query->terms[0];
	}

	// Determine the total term count
	if ( isset( $r['offset'] ) && ! $query->term_count < $r['number'] ) {
		$query->found_terms = paco2017_query_terms_found_rows( $r );
	} else {
		$query->found_terms = $query->term_count;
	}
	if ( $query->found_terms > $query->term_count ) {
		$query->max_num_pages = (int) ceil( $query->found_terms / $r['number'] );
	} else {
		$query->max_num_pages = 1;
	}

	// Return whether the query has returned results
	return paco2017_have_associations();
}

/**
 * Return whether the query has Associations to loop over
 *
 * @since 1.0.0
 *
 * @return bool Query has Associations
 */
function paco2017_have_associations() {

	// Get query object
	$query = paco2017_content()->association_query;

	// Get array keys
	$term_keys = array_keys( $query->terms );

	// Current element is not the last
	$has_next = $query->term_count && $query->current_term < end( $term_keys );

	// We're in the loop when there are still elements
	if ( ! $has_next ) {
		$query->in_the_loop = false;

		// Clean up after the loop
		paco2017_rewind_associations();
	}

	return $has_next;
}

/**
 * Setup next Association in the current loop
 *
 * @since 1.0.0
 *
 * @return bool Are we still in the loop?
 */
function paco2017_the_association() {

	// Get query object
	$query = paco2017_content()->association_query;

	// We're looping
	$query->in_the_loop = true;

	// Increase current term index
	$query->current_term++;

	// Get next term in list
	$query->term = $query->terms[ $query->current_term ];

	return $query->term;
}

/**
 * Rewind the associations and reset term index
 *
 * @since 1.0.0
 */
function paco2017_rewind_associations() {

	// Get query object
	$query = paco2017_content()->association_query;

	// Reset current term index
	$query->current_term = -1;

	if ( $query->term_count > 0 ) {
		$query->term = $query->terms[0];
	}
}

/**
 * Return whether we're in the Association loop
 *
 * @since 1.0.0
 *
 * @return bool Are we in the Association loop?
 */
function paco2017_in_the_association_loop() {
	return isset( paco2017_content()->association_query->in_the_loop ) ? paco2017_content()->association_query->in_the_loop : false;
}

/** Template ******************************************************************/

/**
 * Return the Association item term
 *
 * @since 1.0.0
 * @since 1.1.0 Added option to provide a WP_User object for th `$term` parameter.
 *
 * @param WP_Term|int|WP_User $term Optional. Term object or ID or User object. Defaults to the current term.
 * @param string $by Optional. Method to fetch term through `get_term_by()`. Defaults to 'id'.
 * @return WP_Term|false Associations term object or False when not found.
 */
function paco2017_get_association( $term = 0, $by = 'id' ) {

	// Default empty parameter to the term in the loop
	if ( empty( $term ) && paco2017_in_the_association_loop() ) {
		$term = paco2017_content()->association_query->term;

	// Default to or get the term by user
	} elseif ( empty( $term ) || $term instanceof WP_User ) {
		$user_id = empty( $term ) ? 0 : $term->ID;
		$term    = paco2017_get_user_association( $user_id );

	// Get the term by id or slug
	} elseif ( ! $term instanceof WP_Term ) {
		$term = get_term_by( $by, $term, paco2017_get_association_tax_id() );
	}

	// Reduce error to false
	if ( ! $term || is_wp_error( $term ) ) {
		$term = false;
	}

	return $term;
}

/**
 * Output the Association title
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 */
function paco2017_the_association_title( $term = 0 ) {
	echo paco2017_get_association_title( $term );
}

/**
 * Return the Association title
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_association_title'
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @return string Term title
 */
function paco2017_get_association_title( $term = 0 ) {
	$term  = paco2017_get_association( $term );
	$title = '';

	if ( $term ) {
		$title = get_term_field( 'name', $term );
	}

	return apply_filters( 'paco2017_get_association_title', $title, $term );
}

/**
 * Output the Association content
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 */
function paco2017_the_association_content( $term = 0 ) {
	echo paco2017_get_association_content( $term );
}

/**
 * Return the Association content
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_association_content'
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @return string Term content
 */
function paco2017_get_association_content( $term = 0 ) {
	$term    = paco2017_get_association( $term );
	$content = '';

	if ( $term ) {
		$content = get_term_field( 'description', $term );
	}

	return apply_filters( 'paco2017_get_association_content', $content, $term );
}

/**
 * Output the Association logo attachment ID
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 */
function paco2017_the_association_logo_id( $term = 0 ) {
	echo paco2017_get_association_logo_id( $term );
}

/**
 * Return the Association logo attachment ID
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_association_logo_id'
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @return int Term logo attachment ID
 */
function paco2017_get_association_logo_id( $term = 0 ) {
	$term     = paco2017_get_association( $term );
	$logo_id = 0;

	if ( $term ) {
		$logo_id = (int) get_term_meta( $term->term_id, 'logo', true );
	}

	return (int) apply_filters( 'paco2017_get_association_logo_id', $logo_id, $term );
}

/**
 * Return whether the Association has a logo
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @return bool Has the association a term logo?
 */
function paco2017_has_association_logo( $term = 0 ) {
	return (bool) paco2017_get_association_logo_id( $term );
}

/**
 * Output the Association logo
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @param string|array $size Optional. Attachment image size. Defaults to 'thumbnail'.
 * @param array $args Optional. Attachment image arguments for {@see wp_get_attachment_image()}.
 */
function paco2017_the_association_logo( $term = 0, $size = 'thumbnail', $args = array() ) {
	echo paco2017_get_association_logo( $term, $size );
}

/**
 * Return the Association logo
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_association_logo'
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @param string|array $size Optional. Attachment image size. Defaults to 'thumbnail'.
 * @param array $args Optional. Attachment image arguments for {@see wp_get_attachment_image()}.
 * @return string Term logo
 */
function paco2017_get_association_logo( $term = 0, $size = 'thumbnail', $args = array() ) {
	$term     = paco2017_get_association( $term );
	$image    = '';

	if ( $term ) {
		$logo_id = paco2017_get_association_logo_id( $term );
		$logo    = wp_get_attachment_image( $logo_id, $size, false, $args );
	}

	return apply_filters( 'paco2017_get_association_logo', $logo, $term );
}

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
	$terms = wp_get_object_terms( $user_id, paco2017_get_association_tax_id() );
	$term  = false;

	if ( $terms && ! is_wp_error( $terms ) ) {
		$term = $terms[0];
	}

	return apply_filters( 'paco2017_get_user_association', $term, $user_id );
}

/**
 * Return the user's association title
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return string Association title
 */
function paco2017_get_association_title_for_user( $user_id = 0 ) {

	// Get the user's association
	$term  = paco2017_get_user_association( $user_id );
	$title = '';

	if ( is_a( $term, 'WP_Term' ) ) {
		$title = get_term_field( 'name', $term );
	} elseif ( is_string( $term ) ) {
		$title = $term;
	}

	return apply_filters( 'paco2017_get_association_title_for_user', $title, $user_id, $term );
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
 * @return array Association user ids
 */
function paco2017_get_association_users( $term, $by = 'term_id' ) {

	// Default to the current user
	if ( empty( $term ) )
		return array();

	$taxonomy = paco2017_get_association_tax_id();

	// Get the term
	if ( ! is_a( $term, 'WP_Term' ) ) {
		$term = get_term_by( $by, $term, $taxonomy );
		if ( ! $term ) {
			return array();
		}
	}

	// Get the term's users
	$users = get_objects_in_term( $term->term_id, $taxonomy );

	// Default to empty error
	if ( ! $users || is_wp_error( $users ) ) {
		$users = array();
	}

	return (array) apply_filters( 'paco2017_get_association_users', $users, $term );
}

/**
 * Return the user count of the enrolled users for the given association
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|string $term Term object or id or name or slug
 * @return int Enrolled association user count
 */
function paco2017_get_enrolled_users_for_association_count( $term ) {

	// Count the queried users
	$users = paco2017_get_enrolled_users_for_association( $term );
	$count = count( $users );

	return $count;
}

/**
 * Return the enrolled users for the given association
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|string $term Term object or id or name or slug
 * @return array Enrolled association users
 */
function paco2017_get_enrolled_users_for_association( $term ) {
	return (array) apply_filters( 'paco2017_get_enrolled_users_for_association', array(), $term );
}

/**
 * Return whether two users are in the same association
 *
 * @since 1.1.0
 *
 * @param int $user1_id User ID. Initial user to check for.
 * @param int $user2_id Optional. User ID. Defaults to the current user.
 * @return bool Are users in the same association?
 */
function paco2017_users_in_same_association( $user1_id, $user2_id = 0 ) {

	// Require an initial user to check for
	if ( empty( $user1_id ) ) {
		return false;
	}

	// Default to the current user
	if ( empty( $user2_id ) ) {
		$user2_id = get_current_user_id();
	}

	$assoc1 = paco2017_get_user_association( $user1_id );
	$assoc2 = paco2017_get_user_association( $user2_id );

	// Are associations found?
	if ( $assoc1 && $assoc2 ) {
		$same = $assoc1->term_id === $assoc2->term_id;
	} else {
		$same = false;
	}

	return (bool) $same;
}
