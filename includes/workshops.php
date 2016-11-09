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
 * @return array Workshop post type support
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
