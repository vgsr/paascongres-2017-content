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
		'singular_name'         => __( 'Paascongres Workshop',        'paco2017-content' ),
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
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_rewrite'
 * @return array Workshop Category taxonomy rewrite
 */
function paco2017_get_workshop_cat_tax_rewrite() {
	return apply_filters( 'paco2017_get_workshop_cat_rewrite', array(
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
