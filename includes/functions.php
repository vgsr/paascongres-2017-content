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

/** Template ******************************************************************/

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
			$textcolor = ( $rgb[0] > 200 || $rgb[1] > 200 || $rgb[2] > 200 ) ? 'color: inherit;' : 'color: #fff;';

			$css[] = ".paco2017_enrollments_widget .paco2017-association-{$term->term_id}, .paco2017_enrollments_widget .paco2017-association-{$term->term_id} + dd { background: rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.6); {$textcolor} }";
		}
	}

	if ( ! empty( $css ) ) {
		wp_add_inline_style( 'paco2017-content', implode( "\n", $css ) );
	}
}
