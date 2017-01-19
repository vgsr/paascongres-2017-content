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
 * @return string Post type name
 */
function paco2017_get_workshop_post_type() {
	return 'paco2017_workshop';
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
		'name'                  => __( 'Paascongres Workshops',       'paco2017-content' ),
		'menu_name'             => __( 'Workshops',                   'paco2017-content' ),
		'singular_name'         => __( 'Workshop',                    'paco2017-content' ),
		'all_items'             => __( 'All Workshops',               'paco2017-content' ),
		'add_new'               => __( 'New Workshop',                'paco2017-content' ),
		'add_new_item'          => __( 'Create New Workshop',         'paco2017-content' ),
		'edit'                  => __( 'Edit',                        'paco2017-content' ),
		'edit_item'             => __( 'Edit Workshop',               'paco2017-content' ),
		'new_item'              => __( 'New Workshop',                'paco2017-content' ),
		'view'                  => __( 'View Workshop',               'paco2017-content' ),
		'view_item'             => __( 'View Workshop',               'paco2017-content' ),
		'view_items'            => __( 'View Workshops',              'paco2017-content' ), // Since WP 4.7
		'search_items'          => __( 'Search Workshops',            'paco2017-content' ),
		'not_found'             => __( 'No workshops found',          'paco2017-content' ),
		'not_found_in_trash'    => __( 'No workshops found in Trash', 'paco2017-content' ),
		'insert_into_item'      => __( 'Insert into workshop',        'paco2017-content' ),
		'uploaded_to_this_item' => __( 'Uploaded to this workshop',   'paco2017-content' ),
		'filter_items_list'     => __( 'Filter workshops list',       'paco2017-content' ),
		'items_list_navigation' => __( 'Workshops list navigation',   'paco2017-content' ),
		'items_list'            => __( 'Workshops list',              'paco2017-content' ),
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

/** Taxonomy: Workshop Category ***********************************************/

/**
 * Return the Workshop Category taxonomy
 *
 * @since 1.0.0
 *
 * @return string Taxonomy name
 */
function paco2017_get_workshop_cat_tax_id() {
	return 'paco2017_workshop_category';
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
		'name'          => __( 'Paascongres Workshop Categories', 'paco2017-content' ),
		'menu_name'     => __( 'Categories',                      'paco2017-content' ),
		'singular_name' => __( 'Workshop Category',               'paco2017-content' ),
		'search_items'  => __( 'Search Workshop Categories',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => __( 'All Workshop Categories',         'paco2017-content' ),
		'no_items'      => __( 'No Workshop Category',            'paco2017-content' ),
		'edit_item'     => __( 'Edit Workshop Category',          'paco2017-content' ),
		'update_item'   => __( 'Update Workshop Category',        'paco2017-content' ),
		'add_new_item'  => __( 'Add New Workshop Category',       'paco2017-content' ),
		'new_item_name' => __( 'New Workshop Category Name',      'paco2017-content' ),
		'view_item'     => __( 'View Workshop Category',          'paco2017-content' )
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

	// Make category rules work
	paco2017_prioritize_workshop_cat_rewrite_rules();
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
 *
 * @since 1.0.0
 *
 * @global WP_Rewrite $wp_rewrite
 */
function paco2017_prioritize_workshop_cat_rewrite_rules() {
	global $wp_rewrite;

	// Get the current permastruct
	$name = paco2017_get_workshop_post_type();
	$args = $wp_rewrite->extra_permastructs[ $name ];

	// Remove and append again at the bottom of the list
	remove_permastruct( $name );
	$wp_rewrite->extra_permastructs[ $name ] = $args;
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
 * @return string Taxonomy name
 */
function paco2017_get_workshop_round_tax_id() {
	return 'paco2017_workshop_round';
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
		'name'          => __( 'Paascongres Workshop Rounds', 'paco2017-content' ),
		'menu_name'     => __( 'Rounds',                      'paco2017-content' ),
		'singular_name' => __( 'Workshop Round',              'paco2017-content' ),
		'search_items'  => __( 'Search Workshop Rounds',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => __( 'All Workshop Rounds',         'paco2017-content' ),
		'no_items'      => __( 'No Workshop Round',           'paco2017-content' ),
		'edit_item'     => __( 'Edit Workshop Round',         'paco2017-content' ),
		'update_item'   => __( 'Update Workshop Round',       'paco2017-content' ),
		'add_new_item'  => __( 'Add New Workshop Round',      'paco2017-content' ),
		'new_item_name' => __( 'New Workshop Round Name',     'paco2017-content' ),
		'view_item'     => __( 'View Workshop Round',         'paco2017-content' )
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

	// Make category rules work
	paco2017_prioritize_workshop_cat_rewrite_rules();
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
 *
 * @since 1.1.0
 *
 * @global WP_Rewrite $wp_rewrite
 */
function paco2017_prioritize_workshop_round_rewrite_rules() {
	global $wp_rewrite;

	// Get the current permastruct
	$name = paco2017_get_workshop_post_type();
	$args = $wp_rewrite->extra_permastructs[ $name ];

	// Remove and append again at the bottom of the list
	remove_permastruct( $name );
	$wp_rewrite->extra_permastructs[ $name ] = $args;
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
