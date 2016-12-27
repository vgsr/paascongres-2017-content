<?php

/**
 * Paco2017 Content Speaker Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Taxonomy ******************************************************************/

/**
 * Return the Speaker taxonomy
 *
 * @since 1.0.0
 *
 * @return string Taxonomy name
 */
function paco2017_get_speaker_tax_id() {
	return 'paco2017_speaker';
}

/**
 * Return the labels for the Speaker taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speaker_tax_labels'
 * @return array Speaker taxonomy labels
 */
function paco2017_get_speaker_tax_labels() {
	return apply_filters( 'paco2017_get_speaker_tax_labels', array(
		'name'          => __( 'Paascongres Speakers', 'paco2017-content' ),
		'menu_name'     => __( 'Speakers',             'paco2017-content' ),
		'singular_name' => __( 'Speaker',              'paco2017-content' ),
		'search_items'  => __( 'Search Speakers',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => __( 'All Speakers',         'paco2017-content' ),
		'no_items'      => __( 'No Speaker',           'paco2017-content' ),
		'edit_item'     => __( 'Edit Speaker',         'paco2017-content' ),
		'update_item'   => __( 'Update Speaker',       'paco2017-content' ),
		'add_new_item'  => __( 'Add New Speaker',      'paco2017-content' ),
		'new_item_name' => __( 'New Speaker Name',     'paco2017-content' ),
		'view_item'     => __( 'View Speaker',         'paco2017-content' )
	) );
}

/**
 * Return the Speaker taxonomy rewrite settings
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speaker_tax_rewrite'
 * @return array Speaker taxonomy rewrite
 */
function paco2017_get_speaker_tax_rewrite() {
	return apply_filters( 'paco2017_get_speaker_tax_rewrite', array(
		'slug'       => paco2017_get_speaker_slug(),
		'with_front' => false
	) );
}

/**
 * Act when the Speaker taxonomy has been registered
 *
 * @since 1.0.0
 */
function paco2017_registered_speaker_taxonomy() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_speaker_rest_fields' );
}

/**
 * Register REST fields for the Speaker taxonomy
 *
 * @since 1.0.0
 */
function paco2017_register_speaker_rest_fields() {

	// Get assets
	$lecture  = paco2017_get_lecture_post_type();
	$workshop = paco2017_get_workshop_post_type();

	// Add location to Lecture
	register_rest_field(
		$lecture,
		'speakers',
		array(
			'get_callback' => 'paco2017_get_workshop_rest_speakers'
		)
	);

	// Add location to Workshop
	register_rest_field(
		$workshop,
		'speakers',
		array(
			'get_callback' => 'paco2017_get_workshop_rest_speakers'
		)
	);
}

/**
 * Return the value for the 'speakers' workshop REST API field
 *
 * @since 1.0.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Location term(s)
 */
function paco2017_get_workshop_rest_speakers( $object, $field_name, $request ) {
	return wp_get_object_terms( $object['id'], paco2017_get_speaker_tax_id() );
}

/** Template ******************************************************************/

