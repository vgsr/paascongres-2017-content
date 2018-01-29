<?php

/**
 * Paco2017 Content Workshop Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Post Type *****************************************************************/

/**
 * Return the Workshop post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_post_type'
 * @return string Post type name
 */
function paco2017_get_workshop_post_type() {
	return apply_filters( 'paco2017_get_workshop_post_type', paco2017_content()->workshop_post_type );
}

/**
 * Return the labels for the Workshop post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_post_type_labels'
 * @return array Workshop post type labels
 */
function paco2017_get_workshop_post_type_labels() {
	return apply_filters( 'paco2017_get_workshop_post_type_labels', array(
		'name'                  => esc_html__( 'Paascongres Workshops',       'paco2017-content' ),
		'menu_name'             => esc_html__( 'Workshops',                   'paco2017-content' ),
		'singular_name'         => esc_html__( 'Workshop',                    'paco2017-content' ),
		'all_items'             => esc_html__( 'All Workshops',               'paco2017-content' ),
		'add_new'               => esc_html__( 'New Workshop',                'paco2017-content' ),
		'add_new_item'          => esc_html__( 'Create New Workshop',         'paco2017-content' ),
		'edit'                  => esc_html__( 'Edit',                        'paco2017-content' ),
		'edit_item'             => esc_html__( 'Edit Workshop',               'paco2017-content' ),
		'new_item'              => esc_html__( 'New Workshop',                'paco2017-content' ),
		'view'                  => esc_html__( 'View Workshop',               'paco2017-content' ),
		'view_item'             => esc_html__( 'View Workshop',               'paco2017-content' ),
		'view_items'            => esc_html__( 'View Workshops',              'paco2017-content' ), // Since WP 4.7
		'search_items'          => esc_html__( 'Search Workshops',            'paco2017-content' ),
		'not_found'             => esc_html__( 'No workshops found',          'paco2017-content' ),
		'not_found_in_trash'    => esc_html__( 'No workshops found in Trash', 'paco2017-content' ),
		'insert_into_item'      => esc_html__( 'Insert into workshop',        'paco2017-content' ),
		'uploaded_to_this_item' => esc_html__( 'Uploaded to this workshop',   'paco2017-content' ),
		'filter_items_list'     => esc_html__( 'Filter workshops list',       'paco2017-content' ),
		'items_list_navigation' => esc_html__( 'Workshops list navigation',   'paco2017-content' ),
		'items_list'            => esc_html__( 'Workshops list',              'paco2017-content' ),
	) );
}

/**
 * Return the Workshop post type rewrite settings
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_post_type_rewrite'
 * @return array Workshop post type rewrite
 */
function paco2017_get_workshop_post_type_rewrite() {
	return apply_filters( 'paco2017_get_workshop_post_type_rewrite', array(
		'slug'       => paco2017_get_workshop_slug(),
		'with_front' => false
	) );
}

/**
 * Return an array of features the Workshop post type supports
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_post_type_supports'
 * @return array Workshop post type support
 */
function paco2017_get_workshop_post_type_supports() {
	return apply_filters( 'paco2017_get_workshop_post_type_supports', array(
		'title',
		'editor',
		'excerpt',
		'thumbnail',
		'page-attributes',
	) );
}

/**
 * Act when the Workshop post type has been registered
 *
 * @since 1.1.0
 */
function paco2017_registered_workshop_post_type() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_workshop_rest_fields' );
}

/**
 * Register REST fields for the Workshop post type
 *
 * @since 1.1.0
 */
function paco2017_register_workshop_rest_fields() {

	// Get assets
	$workshop = paco2017_get_workshop_post_type();

	// Add user count to Agenda Item
	register_rest_field(
		$workshop,
		'user_count',
		array(
			'get_callback' => 'paco2017_get_workshop_rest_user_count'
		)
	);
}

/**
 * Return the value for the 'user_count' workshop REST API field
 *
 * @since 1.0.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Location term(s)
 */
function paco2017_get_workshop_rest_user_count( $object, $field_name, $request ) {
	return paco2017_get_workshop_enrolled_user_count( $object['id'] );
}

/** Taxonomy: Workshop Category ***********************************************/

/**
 * Return the Workshop Category taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_tax_id'
 * @return string Taxonomy name
 */
function paco2017_get_workshop_cat_tax_id() {
	return apply_filters( 'paco2017_get_workshop_cat_tax_id', paco2017_content()->workshop_cat_tax_id );
}

/**
 * Return the labels for the Workshop Category taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_tax_labels'
 * @return array Workshop Category taxonomy labels
 */
function paco2017_get_workshop_cat_tax_labels() {
	return apply_filters( 'paco2017_get_workshop_cat_tax_labels', array(
		'name'          => esc_html__( 'Paascongres Workshop Categories', 'paco2017-content' ),
		'menu_name'     => esc_html__( 'Categories',                      'paco2017-content' ),
		'singular_name' => esc_html__( 'Workshop Category',               'paco2017-content' ),
		'search_items'  => esc_html__( 'Search Workshop Categories',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => esc_html__( 'All Workshop Categories',         'paco2017-content' ),
		'no_items'      => esc_html__( 'No Workshop Category',            'paco2017-content' ),
		'edit_item'     => esc_html__( 'Edit Workshop Category',          'paco2017-content' ),
		'update_item'   => esc_html__( 'Update Workshop Category',        'paco2017-content' ),
		'add_new_item'  => esc_html__( 'Add New Workshop Category',       'paco2017-content' ),
		'new_item_name' => esc_html__( 'New Workshop Category Name',      'paco2017-content' ),
		'view_item'     => esc_html__( 'View Workshop Category',          'paco2017-content' )
	) );
}

/**
 * Return the Workshop Category taxonomy rewrite settings
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_tax_rewrite'
 * @return array Workshop Category taxonomy rewrite
 */
function paco2017_get_workshop_cat_tax_rewrite() {
	return apply_filters( 'paco2017_get_workshop_cat_tax_rewrite', array(
		'slug'       => paco2017_get_workshop_slug() . '/' . paco2017_get_workshop_cat_slug(),
		'with_front' => false
	) );
}

/**
 * Act when the Workshop Category taxonomy has been registered
 *
 * @since 1.0.0
 */
function paco2017_registered_workshop_cat_taxonomy() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_workshop_cat_rest_fields' );

	/**
	 * Prioritize Workshop Category requests over Workshop requests.
	 *
	 * This is done because a request for '/workshops/category/cat-name/'
	 * is catched as a query for a non-existent workshop entry. Instead we'd
	 * like it to identify a workshop category before attempting to match a
	 * workshop entry.
	 *
	 * This is caused by the prioritization of workshop object requests
	 * over workshop category requests, based on the order of rewrite rules
	 * registration. To solve this, we move the workshop object rules
	 * (permalink structure or 'permastruct') down in the list of registered
	 * rules to match.
	 */
	paco2017_reduce_rewrite_rules_priority( paco2017_get_workshop_post_type() );
}

/**
 * Register REST fields for the Workshop Category taxonomy
 *
 * @since 1.0.0
 */
function paco2017_register_workshop_cat_rest_fields() {

	// Get assets
	$workshop = paco2017_get_workshop_post_type();

	// Add location to Agenda Item
	register_rest_field(
		$workshop,
		'workshop_categories',
		array(
			'get_callback' => 'paco2017_get_workshop_rest_workshop_categories'
		)
	);
}

/**
 * Return the value for the 'workshop_categories' workshop REST API field
 *
 * @since 1.0.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Location term(s)
 */
function paco2017_get_workshop_rest_workshop_categories( $object, $field_name, $request ) {
	return wp_get_object_terms( $object['id'], paco2017_get_workshop_cat_tax_id() );
}

/**
 * Return whether the given post has any or the given Workshop Category
 *
 * @since 1.1.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to any term.
 * @return bool Object has a/the Workshop Category
 */
function paco2017_object_has_workshop_cat( $post = 0, $term = 0 ) {
	return has_term( $term, paco2017_get_workshop_cat_tax_id(), $post );
}

/** Taxonomy: Workshop Round **************************************************/

/**
 * Return the Workshop Round taxonomy
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_round_tax_id'
 * @return string Taxonomy name
 */
function paco2017_get_workshop_round_tax_id() {
	return apply_filters( 'paco2017_get_workshop_round_tax_id', paco2017_content()->workshop_round_tax_id );
}

/**
 * Return the labels for the Workshop Round taxonomy
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_round_tax_labels'
 * @return array Workshop Round taxonomy labels
 */
function paco2017_get_workshop_round_tax_labels() {
	return apply_filters( 'paco2017_get_workshop_round_tax_labels', array(
		'name'          => esc_html__( 'Paascongres Workshop Rounds', 'paco2017-content' ),
		'menu_name'     => esc_html__( 'Rounds',                      'paco2017-content' ),
		'singular_name' => esc_html__( 'Workshop Round',              'paco2017-content' ),
		'search_items'  => esc_html__( 'Search Workshop Rounds',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => esc_html__( 'All Workshop Rounds',         'paco2017-content' ),
		'no_items'      => esc_html__( 'No Workshop Round',           'paco2017-content' ),
		'edit_item'     => esc_html__( 'Edit Workshop Round',         'paco2017-content' ),
		'update_item'   => esc_html__( 'Update Workshop Round',       'paco2017-content' ),
		'add_new_item'  => esc_html__( 'Add New Workshop Round',      'paco2017-content' ),
		'new_item_name' => esc_html__( 'New Workshop Round Name',     'paco2017-content' ),
		'view_item'     => esc_html__( 'View Workshop Round',         'paco2017-content' )
	) );
}

/**
 * Return the Workshop Round taxonomy rewrite settings
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_round_tax_rewrite'
 * @return array Workshop Round taxonomy rewrite
 */
function paco2017_get_workshop_round_tax_rewrite() {
	return apply_filters( 'paco2017_get_workshop_round_tax_rewrite', array(
		'slug'       => paco2017_get_workshop_slug() . '/' . paco2017_get_workshop_round_slug(),
		'with_front' => false
	) );
}

/**
 * Act when the Workshop Round taxonomy has been registered
 *
 * @since 1.1.0
 */
function paco2017_registered_workshop_round_taxonomy() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_workshop_round_rest_fields' );

	/**
	 * Prioritize Workshop Round requests over Workshop requests.
	 *
	 * This is done because a request for '/workshops/round/round-name/'
	 * is catched as a query for a non-existent workshop entry. Instead we'd
	 * like it to identify a workshop round before attempting to match a
	 * workshop entry.
	 *
	 * This is caused by the prioritization of workshop object requests
	 * over workshop round requests, based on the order of rewrite rules
	 * registration. To solve this, we move the workshop object rules
	 * (permalink structure or 'permastruct') down in the list of registered
	 * rules to match.
	 */
	paco2017_reduce_rewrite_rules_priority( paco2017_get_workshop_post_type() );
}

/**
 * Register REST fields for the Workshop Round taxonomy
 *
 * @since 1.1.0
 */
function paco2017_register_workshop_round_rest_fields() {

	// Get assets
	$workshop = paco2017_get_workshop_post_type();

	// Add location to Agenda Item
	register_rest_field(
		$workshop,
		'workshop_rounds',
		array(
			'get_callback' => 'paco2017_get_workshop_rest_workshop_rounds'
		)
	);
}

/**
 * Return the value for the 'workshop_rounds' workshop REST API field
 *
 * @since 1.1.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Location term(s)
 */
function paco2017_get_workshop_rest_workshop_rounds( $object, $field_name, $request ) {
	return wp_get_object_terms( $object['id'], paco2017_get_workshop_round_tax_id() );
}

/**
 * Return whether the given post has any or the given Workshop Round
 *
 * @since 1.1.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to any term.
 * @return bool Object has a/the Workshop Round
 */
function paco2017_object_has_workshop_round( $post = 0, $term = 0 ) {
	return has_term( $term, paco2017_get_workshop_round_tax_id(), $post );
}

/** Template ******************************************************************/

/**
 * Return the Workshop
 *
 * @since 1.1.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current post.
 * @return WP_Post|bool Workshop post object or False when not found.
 */
function paco2017_get_workshop( $post = 0 ) {

	// Get the post
	$post = get_post( $post );

	// Return false when this is not a Workshop
	if ( ! $post || paco2017_get_workshop_post_type() !== $post->post_type ) {
		$post = false;
	}

	return $post;
}

/**
 * Return the attendee limit for a workshop
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_limit'
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @return int Workshop limit. 0 means no limit.
 */
function paco2017_get_workshop_limit( $post = 0 ) {
	$post  = paco2017_get_workshop( $post );
	$limit = 0;

	if ( $post ) {
		$limit = (int) get_post_meta( $post->ID, 'limit', true );
	}

	return (int) apply_filters( 'paco2017_get_workshop_limit', $limit, $post );
}

/**
 * Modify the content of a Workshop post before content filters apply
 *
 * @since 1.1.0
 *
 * @param string $content Post content
 * @return string Post content
 */
function paco2017_workshop_pre_post_content( $content ) {

	// This is a Workshop in an archive
	if ( paco2017_get_workshop() && is_archive() && ! is_admin() ) {
		$content = wp_trim_words( $content, 35, '&hellip; <a href="' . esc_url( get_permalink() ) . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'paco2017-content' ) . '</a>' );
	}

	return $content;
}

/**
 * Modify the content of a Workshop post
 *
 * @since 1.1.0
 *
 * @param string $content Post content
 * @return string Post content
 */
function paco2017_workshop_post_content( $content ) {

	// This is a Workshop
	if ( paco2017_get_workshop() && ( is_single() || is_archive() ) && ! is_admin() ) {
		$content = paco2017_buffer_template_part( 'info', 'workshop' ) . $content;
	}

	return $content;
}

/**
 * Return the workshop's enrolled user count
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_enrolled_user_count'
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @return int Enrolled user count.
 */
function paco2017_get_workshop_enrolled_user_count( $post = 0 ) {
	$post  = paco2017_get_workshop( $post );
	$count = 0;

	if ( $post ) {
		$users = paco2017_get_workshop_enrolled_users( $post );
		$count = count( $users );
	}

	return (int) apply_filters( 'paco2017_get_workshop_enrolled_user_count', $count, $post );
}

/**
 * Return the workshop's enrolled users
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_enrolled_users'
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @param bool $object Optional. Whether to return user objects or ids.
 * @return array Enrolled users objects or ids
 */
function paco2017_get_workshop_enrolled_users( $post = 0, $object = false ) {
	$post  = paco2017_get_workshop( $post );
	$users = array();

	return (array) apply_filters( 'paco2017_get_workshop_enrolled_users', $users, $post, $object );
}

/**
 * Return the user's registered workshops
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_user_workshops'
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return array Workshop post ids
 */
function paco2017_get_user_workshops( $user_id = 0 ) {
	$workshops = array();

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	return (array) apply_filters( 'paco2017_get_user_workshops', $workshops, $user_id );
}

/**
 * Output the list of the given user's workshops
 *
 * @since 1.1.0
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 */
function paco2017_the_user_workshops_list( $user_id = 0 ) {
	echo paco2017_get_user_workshops_list( $user_id );
}

/**
 * Return the list of the given user's workshops
 *
 * @since 1.1.0
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return string User workshops list
 */
function paco2017_get_user_workshops_list( $user_id = 0 ) {
	$workshops = paco2017_get_user_workshops( $user_id );
	$list      = '';

	if ( $workshops ) {

		// Define list
		$list = '<ul class="user-workshops">';
		foreach ( $workshops as $workshop_id ) {
			$list .= sprintf( '<li><a href="%s">%s</a></li>', get_permalink( $workshop_id ), get_the_title( $workshop_id ) );
		}
		$list .= '</ul>';
	}

	return $list;
}

/** Template: Workshop Category **************************************************/

/**
 * Return the Workshop Category term
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or post object. Defaults to the current term or post.
 * @param string $by Optional. Method to fetch term through `get_term_by()`. Defaults to 'id'.
 * @return WP_Term|false Workshop Category term object or False when not found.
 */
function paco2017_get_workshop_cat( $term = 0, $by = 'id' ) {

	// Default to the current post's term
	if ( empty( $term ) && paco2017_object_has_workshop_cat() ) {
		$terms = wp_get_object_terms( get_the_ID(), paco2017_get_workshop_cat_tax_id() );
		$term  = $terms[0];

	// Default to the provided post's term
	} elseif ( is_a( $term, 'WP_Post' ) && paco2017_object_has_workshop_cat( $term ) ) {
		$terms = wp_get_object_terms( $term->ID, paco2017_get_workshop_cat_tax_id() );
		$term  = $terms[0];

	// Get the term by id or slug
	} elseif ( ! $term instanceof WP_Term ) {
		$term = get_term_by( $by, $term, paco2017_get_workshop_cat_tax_id() );
	}

	// Reduce error to false
	if ( ! $term || is_wp_error( $term ) ) {
		$term = false;
	}

	return $term;
}

/**
 * Output the Workshop Category title
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_workshop_cat_title( $term = 0 ) {
	echo paco2017_get_workshop_cat_title( $term );
}

/**
 * Return the Workshop Category title
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_title'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return string Term title
 */
function paco2017_get_workshop_cat_title( $term = 0 ) {
	$term  = paco2017_get_workshop_cat( $term );
	$title = '';

	if ( $term ) {
		$title = get_term_field( 'name', $term );
	}

	return apply_filters( 'paco2017_get_workshop_cat_title', $title, $term );
}

/**
 * Output the Workshop Category content
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_workshop_cat_content( $term = 0 ) {
	echo paco2017_get_workshop_cat_content( $term );
}

/**
 * Return the Workshop Category content
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_content'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return string Term content
 */
function paco2017_get_workshop_cat_content( $term = 0 ) {
	$term    = paco2017_get_workshop_cat( $term );
	$content = '';

	if ( $term ) {
		$content = get_term_field( 'description', $term );
	}

	return apply_filters( 'paco2017_get_workshop_cat_content', $content, $term );
}

/**
 * Output the Workshop Category url
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_workshop_cat_url( $term = 0 ) {
	echo paco2017_get_workshop_cat_url( $term );
}

/**
 * Return the Workshop Category url
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_url'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return string Term url
 */
function paco2017_get_workshop_cat_url( $term = 0 ) {
	$term = paco2017_get_workshop_cat( $term );
	$url  = '';

	if ( $term ) {
		$url = get_term_link( $term );
	}

	return apply_filters( 'paco2017_get_workshop_cat_url', $url, $term );
}

/**
 * Output the Workshop Category link
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_workshop_cat_link( $term = 0 ) {
	echo paco2017_get_workshop_cat_link( $term );
}

/**
 * Return the Workshop Category link
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_link'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return string Term link
 */
function paco2017_get_workshop_cat_link( $term = 0 ) {
	$term = paco2017_get_workshop_cat( $term );
	$url  = paco2017_get_workshop_cat_url( $term );
	$link = '';

	if ( $term && $url ) {
		$link = '<a href="' . esc_url( $url ) . '">'. paco2017_get_workshop_cat_title( $term ) . '</a>';
	}

	return apply_filters( 'paco2017_get_workshop_cat_link', $link, $term );
}

/** Template: Workshop Round **************************************************/

/**
 * Return the Workshop Round term
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or post object. Defaults to the current term or post.
 * @param string $by Optional. Method to fetch term through `get_term_by()`. Defaults to 'id'.
 * @return WP_Term|false Workshop Round term object or False when not found.
 */
function paco2017_get_workshop_round( $term = 0, $by = 'id' ) {

	// Default to the current post's term
	if ( empty( $term ) && paco2017_object_has_workshop_round() ) {
		$terms = wp_get_object_terms( get_the_ID(), paco2017_get_workshop_round_tax_id() );
		$term  = $terms[0];

	// Default to the provided post's term
	} elseif ( is_a( $term, 'WP_Post' ) && paco2017_object_has_workshop_round( $term ) ) {
		$terms = wp_get_object_terms( $term->ID, paco2017_get_workshop_round_tax_id() );
		$term  = $terms[0];

	// Get the term by id or slug
	} elseif ( ! $term instanceof WP_Term ) {
		$term = get_term_by( $by, $term, paco2017_get_workshop_round_tax_id() );
	}

	// Reduce error to false
	if ( ! $term || is_wp_error( $term ) ) {
		$term = false;
	}

	return $term;
}

/**
 * Output the Workshop Round title
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_workshop_round_title( $term = 0 ) {
	echo paco2017_get_workshop_round_title( $term );
}

/**
 * Return the Workshop Round title
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_round_title'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return string Term title
 */
function paco2017_get_workshop_round_title( $term = 0 ) {
	$term  = paco2017_get_workshop_round( $term );
	$title = '';

	if ( $term ) {
		$title = get_term_field( 'name', $term );
	}

	return apply_filters( 'paco2017_get_workshop_round_title', $title, $term );
}

/**
 * Output the Workshop Round content
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_workshop_round_content( $term = 0 ) {
	echo paco2017_get_workshop_round_content( $term );
}

/**
 * Return the Workshop Round content
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_round_content'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return string Term content
 */
function paco2017_get_workshop_round_content( $term = 0 ) {
	$term    = paco2017_get_workshop_round( $term );
	$content = '';

	if ( $term ) {
		$content = get_term_field( 'description', $term );
	}

	return apply_filters( 'paco2017_get_workshop_round_content', $content, $term );
}

/**
 * Output the Workshop Round url
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_workshop_round_url( $term = 0 ) {
	echo paco2017_get_workshop_round_url( $term );
}

/**
 * Return the Workshop Round url
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_round_url'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return string Term url
 */
function paco2017_get_workshop_round_url( $term = 0 ) {
	$term = paco2017_get_workshop_round( $term );
	$url  = '';

	if ( $term ) {
		$url = get_term_link( $term );
	}

	return apply_filters( 'paco2017_get_workshop_round_url', $url, $term );
}

/**
 * Output the Workshop Round link
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_workshop_round_link( $term = 0 ) {
	echo paco2017_get_workshop_round_link( $term );
}

/**
 * Return the Workshop Round link
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_round_link'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return string Term link
 */
function paco2017_get_workshop_round_link( $term = 0 ) {
	$term = paco2017_get_workshop_round( $term );
	$url  = paco2017_get_workshop_round_url( $term );
	$link = '';

	if ( $term && $url ) {
		$link = '<a href="' . esc_url( $url ) . '">'. paco2017_get_workshop_round_title( $term ) . '</a>';
	}

	return apply_filters( 'paco2017_get_workshop_round_link', $link, $term );
}
