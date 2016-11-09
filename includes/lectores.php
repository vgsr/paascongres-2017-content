<?php

/**
 * Paco2017 Content Lector Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Post Type *****************************************************************/

/**
 * Return the Lector post type
 *
 * @since 1.0.0
 *
 * @return string Post type name
 */
function paco2017_get_lector_post_type() {
	return 'paco2017_lector';
}

/**
 * Return the labels for the Lector post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lector_post_type_labels'
 * @return array Lector post type labels
 */
function paco2017_get_lector_post_type_labels() {
	return apply_filters( 'paco2017_get_lector_post_type_labels', array(
		'name'                  => __( 'Paascongres Lectores',       'paco2017-content' ),
		'menu_name'             => __( 'Lectores',                   'paco2017-content' ),
		'singular_name'         => __( 'Paascongres Lector',        'paco2017-content' ),
		'all_items'             => __( 'All Lectores',               'paco2017-content' ),
		'add_new'               => __( 'New Lector',                'paco2017-content' ),
		'add_new_item'          => __( 'Create New Lector',         'paco2017-content' ),
		'edit'                  => __( 'Edit',                        'paco2017-content' ),
		'edit_item'             => __( 'Edit Lector',               'paco2017-content' ),
		'new_item'              => __( 'New Lector',                'paco2017-content' ),
		'view'                  => __( 'View Lector',               'paco2017-content' ),
		'view_item'             => __( 'View Lector',               'paco2017-content' ),
		'view_items'            => __( 'View Lectores',              'paco2017-content' ), // Since WP 4.7
		'search_items'          => __( 'Search Lectores',            'paco2017-content' ),
		'not_found'             => __( 'No lectors found',          'paco2017-content' ),
		'not_found_in_trash'    => __( 'No lectors found in Trash', 'paco2017-content' ),
		'insert_into_item'      => __( 'Insert into lector',        'paco2017-content' ),
		'uploaded_to_this_item' => __( 'Uploaded to this lector',   'paco2017-content' ),
		'filter_items_list'     => __( 'Filter lectors list',       'paco2017-content' ),
		'items_list_navigation' => __( 'Lectores list navigation',   'paco2017-content' ),
		'items_list'            => __( 'Lectores list',              'paco2017-content' ),
	) );
}

/**
 * Return the Lector post type rewrite settings
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lector_post_type_rewrite'
 * @return array Lector post type support
 */
function paco2017_get_lector_post_type_rewrite() {
	return apply_filters( 'paco2017_get_lector_post_type_rewrite', array(
		'slug'       => paco2017_get_lector_slug(),
		'with_front' => false
	) );
}

/**
 * Return an array of features the Lector post type supports
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lector_post_type_supports'
 * @return array Lector post type support
 */
function paco2017_get_lector_post_type_supports() {
	return apply_filters( 'paco2017_get_lector_post_type_supports', array(
		'title',
		'editor',
		'thumbnail',
	) );
}
