<?php

/**
 * Paco2017 Content Partner Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Post Type *****************************************************************/

/**
 * Return the Partner post type
 *
 * @since 1.0.0
 *
 * @return string Post type name
 */
function paco2017_get_partner_post_type() {
	return 'paco2017_partner';
}

/**
 * Return the labels for the Partner post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_partner_post_type_labels'
 * @return array Partner post type labels
 */
function paco2017_get_partner_post_type_labels() {
	return apply_filters( 'paco2017_get_partner_post_type_labels', array(
		'name'                  => __( 'Paascongres Partners',       'paco2017-content' ),
		'menu_name'             => __( 'Partners',                   'paco2017-content' ),
		'singular_name'         => __( 'Partner',                    'paco2017-content' ),
		'all_items'             => __( 'All Partners',               'paco2017-content' ),
		'add_new'               => __( 'New Partner',                'paco2017-content' ),
		'add_new_item'          => __( 'Create New Partner',         'paco2017-content' ),
		'edit'                  => __( 'Edit',                       'paco2017-content' ),
		'edit_item'             => __( 'Edit Partner',               'paco2017-content' ),
		'new_item'              => __( 'New Partner',                'paco2017-content' ),
		'view'                  => __( 'View Partner',               'paco2017-content' ),
		'view_item'             => __( 'View Partner',               'paco2017-content' ),
		'view_items'            => __( 'View Partners',              'paco2017-content' ), // Since WP 4.7
		'search_items'          => __( 'Search Partners',            'paco2017-content' ),
		'not_found'             => __( 'No partners found',          'paco2017-content' ),
		'not_found_in_trash'    => __( 'No partners found in Trash', 'paco2017-content' ),
		'insert_into_item'      => __( 'Insert into partner',        'paco2017-content' ),
		'uploaded_to_this_item' => __( 'Uploaded to this partner',   'paco2017-content' ),
		'filter_items_list'     => __( 'Filter partners list',       'paco2017-content' ),
		'items_list_navigation' => __( 'Partners list navigation',   'paco2017-content' ),
		'items_list'            => __( 'Partners list',              'paco2017-content' ),
	) );
}

/**
 * Return an array of features the Partner post type supports
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_partner_post_type_supports'
 * @return array Partner post type support
 */
function paco2017_get_partner_post_type_supports() {
	return apply_filters( 'paco2017_get_partner_post_type_supports', array(
		'title',
	) );
}

/**
 * Act when the Partner post type has been registered
 *
 * @since 1.0.0
 */
function paco2017_registered_partner_post_type() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_partner_rest_fields' );

	// Define partner logo size
	add_image_size( 'paco2017-partner-logo', 200, 125, true );
}

/**
 * Register REST fields for the Partner post type
 *
 * @since 1.0.0
 */
function paco2017_register_partner_rest_fields() {

	// Get assets
	$partner = paco2017_get_partner_post_type();

	// Partner URL
	register_rest_field(
		$partner,
		'partner_url',
		array(
			'get_callback' => 'paco2017_get_rest_post_meta'
		)
	);

	// Logo
	register_rest_field(
		$partner,
		'logo',
		array(
			'get_callback' => 'paco2017_get_partner_rest_logo'
		)
	);
}

/**
 * Return the value for the 'logo' partner REST API field
 *
 * @since 1.1.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Day term(s)
 */
function paco2017_get_partner_rest_logo( $object, $meta, $request ) {
	return paco2017_get_rest_image( paco2017_get_partner_logo_id( $object['id'] ), 'paco2017-partner-logo' );
}

/** Taxonomy: Partner Level ***************************************************/

/**
 * Return the Partner Level taxonomy
 *
 * @since 1.0.0
 *
 * @return string Taxonomy name
 */
function paco2017_get_partner_level_tax_id() {
	return 'paco2017_partner_level';
}

/**
 * Return the labels for the Partner Level taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_partner_level_tax_labels'
 * @return array Partner Level taxonomy labels
 */
function paco2017_get_partner_level_tax_labels() {
	return apply_filters( 'paco2017_get_partner_level_tax_labels', array(
		'name'          => __( 'Paascongres Partner Levels', 'paco2017-content' ),
		'menu_name'     => __( 'Partner Levels',             'paco2017-content' ),
		'singular_name' => __( 'Partner Level',              'paco2017-content' ),
		'search_items'  => __( 'Search Partner Levels',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => __( 'All Partner Levels',         'paco2017-content' ),
		'no_items'      => __( 'No Partner Level',           'paco2017-content' ),
		'edit_item'     => __( 'Edit Partner Level',         'paco2017-content' ),
		'update_item'   => __( 'Update Partner Level',       'paco2017-content' ),
		'add_new_item'  => __( 'Add New Partner Level',      'paco2017-content' ),
		'new_item_name' => __( 'New Partner Level Name',     'paco2017-content' ),
		'view_item'     => __( 'View Partner Level',         'paco2017-content' )
	) );
}

/**
 * Act when the Partner Level taxonomy has been registered
 *
 * @since 1.0.0
 */
function paco2017_registered_partner_level_taxonomy() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_partner_level_rest_fields' );
}

/**
 * Register REST fields for the Partner Level taxonomy
 *
 * @since 1.0.0
 */
function paco2017_register_partner_level_rest_fields() {

	// Get assets
	$partner = paco2017_get_partner_post_type();

	// Add level to Partner
	register_rest_field(
		$partner,
		'partner_levels',
		array(
			'get_callback' => 'paco2017_get_partner_rest_partner_levels'
		)
	);
}

/**
 * Return the value for the 'partner_levels' partner REST API field
 *
 * @since 1.0.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Level term(s)
 */
function paco2017_get_partner_rest_partner_levels( $object, $field_name, $request ) {
	return wp_get_object_terms( $object['id'], paco2017_get_partner_level_tax_id() );
}

/**
 * Return whether the given post has any or the given Partner Level
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to any term.
 * @return bool Object has a/the Partner Level
 */
function paco2017_object_has_partner_level( $post = 0, $term = 0 ) {
	return has_term( $term, paco2017_get_partner_level_tax_id(), $post );
}

/** Template ******************************************************************/

/**
 * Return the Partner
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @return WP_Post|false Partner post object or False when not found.
 */
function paco2017_get_partner( $post = 0 ) {

	// Get the post
	$post = get_post( $post );

	// Return false when this is not an Partner
	if ( ! $post || paco2017_get_partner_post_type() !== $post->post_type ) {
		$post = false;
	}

	return $post;
}

/**
 * Return the Partner logo attachment ID
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @return int Partner logo attachment ID
 */
function paco2017_get_partner_logo_id( $post = 0 ) {
	$post    = paco2017_get_partner( $post );
	$logo_id = 0;

	if ( $post ) {
		$logo_id = get_post_meta( $post->ID, 'logo', true );
	}

	return apply_filters( 'paco2017_get_partner_logo_id', $logo_id, $post );
}

/**
 * Return the Partner logo image element
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @return string Partner logo image element
 */
function paco2017_get_partner_logo( $post = 0 ) {
	$post    = paco2017_get_partner( $post );
	$logo_id = paco2017_get_partner_logo_id( $post );
	$image   = '';

	if ( $logo_id ) {
		$image = wp_get_attachment_image( $logo_id, 'paco2017-partner-logo' );
	}

	return apply_filters( 'paco2017_get_partner_logo', $image, $post );
}

/**
 * Return the Partner url
 *
 * @since 1.1.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @return string Partner url
 */
function paco2017_get_partner_url( $post = 0 ) {
	$post = paco2017_get_partner( $post );
	$url  = '';

	if ( $post ) {
		$url = get_post_meta( $post->ID, 'partner_url', true );
	}

	return apply_filters( 'paco2017_get_partner_url', $url, $post );
}

/** Advertorials **************************************************************/

/**
 * Register embed filters for the advertorial item
 *
 * @since 1.1.0
 */
function paco2017_advertorial_embed( $advertorial ) {
	add_filter( 'paco2017_embed_context_id',   'paco2017_get_advertorial_id'                   );
	add_filter( 'paco2017_embed_get_cache',    'paco2017_get_option_autoembed_cache',    10, 3 );
	add_filter( 'paco2017_embed_update_cache', 'paco2017_update_option_autoembed_cache', 10, 3 );

	// Unhook the above
	add_filter( 'paco2017_get_advertorial', function( $advertorial ) {
		remove_filter( 'paco2017_embed_context_id',   'paco2017_get_advertorial_id'                   );
		remove_filter( 'paco2017_embed_get_cache',    'paco2017_get_option_autoembed_cache',    10, 3 );
		remove_filter( 'paco2017_embed_update_cache', 'paco2017_update_option_autoembed_cache', 10, 3 );

		return $advertorial;
	}, 99 );

	return $advertorial;
}
add_filter( 'paco2017_get_advertorial', 'paco2017_advertorial_embed', 1 );

/**
 * Return the location's advertorial setting name
 *
 * @since 1.1.0
 *
 * @param string $location Optional. Defaults to the current action.
 * @return string Setting name
 */
function paco2017_get_advertorial_id( $location = '' ) {
	$location = paco2017_get_advertorial_location( $location );
	$name     = "_paco2017_advertorial_{$location}";

	return $name;
}

/**
 * Return the location for the advertorial
 *
 * @since 1.1.0
 *
 * @param string $location Optional. Defaults to the current action
 * @return string Advertorial location name
 */
function paco2017_get_advertorial_location( $location = '' ) {

	// Default to current action name
	if ( empty( $location ) ) {
		$plugin = paco2017_content();

		if ( empty( $plugin->advertorial_location ) ) {
			$plugin->advertorial_location = current_action();
		}

		$location = $plugin->advertorial_location;
	}

	return apply_filters( 'paco2017_get_advertorial_location', $location );
}

/**
 * Reset the plugin's global advertorial setting name
 *
 * @since 1.1.0
 *
 * @param string $advertorial Advertorial content
 * @return string Advertorial content
 */
function paco2017_reset_advertorial_location( $advertorial ) {
	paco2017_content()->advertorial_location = false;
	return $advertorial;
}

/**
 * Output the advertorial content of the given location
 *
 * @since 1.1.0
 *
 * @param string $location Optional. Defaults to the current action.
 */
function paco2017_the_advertorial( $location = '' ) {
	$location    = paco2017_get_advertorial_location( $location );
	$advertorial = paco2017_get_advertorial( $location );

	if ( ! empty( $advertorial ) ) {
		echo '<div class="paco2017-advertorial advertorial-' . esc_attr( $location ) . '">' . $advertorial . '</div>';
	}
}

/**
 * Return the advertorial content of the given location
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_advertorial'
 *
 * @param string $location Optional. Defaults to the current action.
 * @return string Advertorial content
 */
function paco2017_get_advertorial( $location = '' ) {
	$location    = paco2017_get_advertorial_location( $location );
	$advertorial = get_option( paco2017_get_advertorial_id( $location ), '' );

	return apply_filters( 'paco2017_get_advertorial', $advertorial, $location );
}

/**
 * Return the additional advertorial arguments of the given location
 *
 * @since 1.1.0
 *
 * @param string $location Optional. Defaults to the current action.
 * @return array Advertorial arguments
 */
function paco2017_get_advertorial_args( $location = '' ) {
	$location = paco2017_get_advertorial_location( $location );
	$args     = get_option( paco2017_get_advertorial_id( $location ) . '_args', array() );

	// Define args defaults
	$args = wp_parse_args( $args, array(
		'page' => ''
	) );

	return apply_filters( 'paco2017_get_advertorial_args', $args, $location );
}
