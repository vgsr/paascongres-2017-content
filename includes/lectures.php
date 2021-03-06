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
 * @uses apply_filters() Calls 'paco2017_get_lecture_post_type'
 * @return string Post type name
 */
function paco2017_get_lecture_post_type() {
	return apply_filters( 'paco2017_get_lecture_post_type', paco2017_content()->lecture_post_type );
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
		'name'                  => esc_html__( 'Paascongres Lectures',       'paco2017-content' ),
		'menu_name'             => esc_html__( 'Lectures',                   'paco2017-content' ),
		'singular_name'         => esc_html__( 'Lecture',                    'paco2017-content' ),
		'all_items'             => esc_html__( 'All Lectures',               'paco2017-content' ),
		'add_new'               => esc_html__( 'New Lecture',                'paco2017-content' ),
		'add_new_item'          => esc_html__( 'Create New Lecture',         'paco2017-content' ),
		'edit'                  => esc_html__( 'Edit',                       'paco2017-content' ),
		'edit_item'             => esc_html__( 'Edit Lecture',               'paco2017-content' ),
		'new_item'              => esc_html__( 'New Lecture',                'paco2017-content' ),
		'view'                  => esc_html__( 'View Lecture',               'paco2017-content' ),
		'view_item'             => esc_html__( 'View Lecture',               'paco2017-content' ),
		'view_items'            => esc_html__( 'View Lectures',              'paco2017-content' ), // Since WP 4.7
		'search_items'          => esc_html__( 'Search Lectures',            'paco2017-content' ),
		'not_found'             => esc_html__( 'No lectures found',          'paco2017-content' ),
		'not_found_in_trash'    => esc_html__( 'No lectures found in Trash', 'paco2017-content' ),
		'insert_into_item'      => esc_html__( 'Insert into lecture',        'paco2017-content' ),
		'uploaded_to_this_item' => esc_html__( 'Uploaded to this lecture',   'paco2017-content' ),
		'filter_items_list'     => esc_html__( 'Filter lectures list',       'paco2017-content' ),
		'items_list_navigation' => esc_html__( 'Lectures list navigation',   'paco2017-content' ),
		'items_list'            => esc_html__( 'Lectures list',              'paco2017-content' ),
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
		'page-attributes',
	) );
}

/** Template ******************************************************************/

/**
 * Return the Lecture
 *
 * @since 1.1.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current post.
 * @return WP_Post|bool Lecture post object or False when not found.
 */
function paco2017_get_lecture( $post = 0 ) {

	// Get the post
	$post = get_post( $post );

	// Return false when this is not a Lecture
	if ( ! $post || paco2017_get_lecture_post_type() !== $post->post_type ) {
		$post = false;
	}

	return $post;
}

/**
 * Modify the content of a Lecture post
 *
 * @since 1.1.0
 *
 * @param string $content Post content
 * @return string Post content
 */
function paco2017_lecture_post_content( $content ) {

	// This is a Lecture
	if ( paco2017_get_lecture() && ( is_single() || is_archive() ) && ! is_admin() ) {
		$content = paco2017_buffer_template_part( 'info', 'lecture' ) . $content;
	}

	return $content;
}
