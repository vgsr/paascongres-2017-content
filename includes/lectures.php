<?php

/**
 * Paco2017 Content Lecture Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Post Type *****************************************************************/

/**
 * Return the Lecture post type
 *
 * @since 1.0.0
 *
 * @return string Post type name
 */
function paco2017_get_lecture_post_type() {
	return 'paco2017_lecture';
}

/**
 * Return the labels for the Lecture post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lecture_post_type_labels'
 * @return array Lecture post type labels
 */
function paco2017_get_lecture_post_type_labels() {
	return apply_filters( 'paco2017_get_lecture_post_type_labels', array(
		'name'                  => __( 'Paascongres Lectures',       'paco2017-content' ),
		'menu_name'             => __( 'Lectures',                   'paco2017-content' ),
		'singular_name'         => __( 'Lecture',                    'paco2017-content' ),
		'all_items'             => __( 'All Lectures',               'paco2017-content' ),
		'add_new'               => __( 'New Lecture',                'paco2017-content' ),
		'add_new_item'          => __( 'Create New Lecture',         'paco2017-content' ),
		'edit'                  => __( 'Edit',                       'paco2017-content' ),
		'edit_item'             => __( 'Edit Lecture',               'paco2017-content' ),
		'new_item'              => __( 'New Lecture',                'paco2017-content' ),
		'view'                  => __( 'View Lecture',               'paco2017-content' ),
		'view_item'             => __( 'View Lecture',               'paco2017-content' ),
		'view_items'            => __( 'View Lectures',              'paco2017-content' ), // Since WP 4.7
		'search_items'          => __( 'Search Lectures',            'paco2017-content' ),
		'not_found'             => __( 'No lectures found',          'paco2017-content' ),
		'not_found_in_trash'    => __( 'No lectures found in Trash', 'paco2017-content' ),
		'insert_into_item'      => __( 'Insert into lecture',        'paco2017-content' ),
		'uploaded_to_this_item' => __( 'Uploaded to this lecture',   'paco2017-content' ),
		'filter_items_list'     => __( 'Filter lectures list',       'paco2017-content' ),
		'items_list_navigation' => __( 'Lectures list navigation',   'paco2017-content' ),
		'items_list'            => __( 'Lectures list',              'paco2017-content' ),
	) );
}

/**
 * Return the Lecture post type rewrite settings
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lecture_post_type_rewrite'
 * @return array Lecture post type support
 */
function paco2017_get_lecture_post_type_rewrite() {
	return apply_filters( 'paco2017_get_lecture_post_type_rewrite', array(
		'slug'       => paco2017_get_lecture_slug(),
		'with_front' => false
	) );
}

/**
 * Return an array of features the Lecture post type supports
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lecture_post_type_supports'
 * @return array Lecture post type support
 */
function paco2017_get_lecture_post_type_supports() {
	return apply_filters( 'paco2017_get_lecture_post_type_supports', array(
		'title',
		'editor',
		'thumbnail',
	) );
}
