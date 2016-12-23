<?php

/**
 * Paco2017 Content Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Rewrite *******************************************************************/

/**
 * Return the slug for the Lector post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lector_slug'
 * @return string Slug
 */
function paco2017_get_lector_slug() {
	return apply_filters( 'paco2017_get_lector_slug', get_option( '_paco2017_lector_slug', 'lectors' ) );
}

/**
 * Return the slug for the Workshop post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_slug'
 * @return string Slug
 */
function paco2017_get_workshop_slug() {
	return apply_filters( 'paco2017_get_workshop_slug', get_option( '_paco2017_workshop_slug', 'workshops' ) );
}

/** Options *******************************************************************/

/**
 * Output the enrolled users count
 *
 * @since 1.0.0
 */
function paco2017_enrolled_users_count() {
	echo paco2017_get_enrolled_users_count();
}

/**
 * Return the enrolled users count
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_enrolled_users_count'
 * @return int Enrolled user count
 */
function paco2017_get_enrolled_users_count() {
	return apply_filters( 'paco2017_get_enrolled_users_count', 0 );
}

/**
 * Return the page ID of the Agenda page setting
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_agenda_page_id'
 * @return int Page ID
 */
function paco2017_get_agenda_page_id() {
	return (int) apply_filters( 'paco2017_get_agenda_page_id', get_option( '_paco2017_agenda_page', 0 ) );
}

/**
 * Return the page ID of the Housekeeping page setting
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_housekeeping_page_id'
 * @return int Page ID
 */
function paco2017_get_housekeeping_page_id() {
	return (int) apply_filters( 'paco2017_get_housekeeping_page_id', get_option( '_paco2017_housekeeping_page', 0 ) );
}

/** Taxonomy ******************************************************************/

/**
 * Modify the list of queried Conference Day terms
 *
 * @since 1.0.0
 *
 * @param array $terms Queried terms
 * @param array $taxonomies Taxonomy names
 * @param array $query_vars Query variables
 * @param WP_Term_Query $term_query Term query object
 * @return array Terms
 */
function paco2017_get_terms( $terms, $taxonomies, $query_vars, $term_query ) {

	// Get taxonomies
	$taxes = array(
		paco2017_get_conf_day_tax_id(),
		paco2017_get_conf_location_tax_id(),
		paco2017_get_association_tax_id()
	);
	$taxes = array_filter( array_map( 'get_taxonomy', $taxes ) );
	$taxes = array_combine( wp_list_pluck( $taxes, 'name' ), $taxes );
	$metas = array( 'color', 'date' );

	// Queried conference days
	foreach ( $terms as $k => $term ) {

		// Skip when this is not a day
		if ( ! in_array( $term->taxonomy, array_keys( $taxes ) ) )
			continue;

		// Add term meta when supported
		foreach ( $metas as $meta ) {
			$meta_key = "term_meta_{$meta}";

			if ( isset( $taxes[ $term->taxonomy ]->{$meta_key} ) && true === $taxes[ $term->taxonomy ]->{$meta_key} ) {
				$term->{$meta} = get_term_meta( $term->term_id, $meta, true );
			}
		}

		// Store modified term
		$terms[ $k ] = $term;
	}

	return $terms;
}

/** Template ******************************************************************/

/**
 * Return whether the given background color requires a light text color
 *
 * @link Calculation of perceptive luminance. http://stackoverflow.com/a/1855903/3601434
 *
 * @since 1.0.0
 *
 * @param array $rgb Array of RGB color values
 * @return bool Whether a lighter textcolor is required
 */
function paco2017_light_textcolor_for_background( $rgb ) {
	$luminance = 1 - ( 0.299 * $rgb[0] + 0.587 * $rgb[1] + 0.114 * $rgb[2] ) / 255;

	return $luminance > .5;
}

/**
 * Enqueue plugin styles
 *
 * @since 1.0.0
 */
function paco2017_enqueue_styles() {

	wp_enqueue_style( 'paco2017-content', paco2017_content()->assets_url . 'css/paco2017-content.css' );

	// Define additional custom styles
	$css = array();

	/** Associations **********************************************************/

	foreach ( get_terms( array(
		'taxonomy'   => paco2017_get_association_tax_id(),
		'hide_empty' => false
	) ) as $term ) {

		// Colors
		if ( $color = get_term_meta( $term->term_id, 'color', true ) ) {

			// Turn hex to rgb
			$_color    = sanitize_hex_color_no_hash( $color );
			$rgb       = array( hexdec( substr( $_color, 0, 2 ) ), hexdec( substr( $_color, 2, 2 ) ), hexdec( substr( $_color, 4, 2 ) ) );

			// Dark text on light backgrounds
			$textcolor = paco2017_light_textcolor_for_background( $rgb ) ? 'color: #fff;' : 'color: inherit;';

			$css[] = ".paco2017_enrollments_widget .paco2017-association-{$term->term_id}, .paco2017_enrollments_widget .paco2017-association-{$term->term_id} + dd { background: rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.6); {$textcolor} }";
		}
	}

	if ( ! empty( $css ) ) {
		wp_add_inline_style( 'paco2017-content', implode( "\n", $css ) );
	}
}
