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
 * Return the slug for the Lecture post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lecture_slug'
 * @return string Slug
 */
function paco2017_get_lecture_slug() {
	return apply_filters( 'paco2017_get_lecture_slug', get_option( '_paco2017_lecture_slug', 'lectures' ) );
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

/**
 * Return the slug for the Workshop Category taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_slug'
 * @return string Slug
 */
function paco2017_get_workshop_cat_slug() {
	return apply_filters( 'paco2017_get_workshop_cat_slug', get_option( '_paco2017_workshop_cat_slug', 'category' ) );
}

/**
 * Sanitize permalink slugs when saving the settings page.
 *
 * @since 1.0.0
 *
 * @param string $slug
 * @return string
 */
function paco2017_sanitize_slug( $slug = '' ) {

	// Don't allow multiple slashes in a row
	$value = preg_replace( '#/+#', '/', str_replace( '#', '', $slug ) );

	// Strip out unsafe or unusable chars
	$value = esc_url_raw( $value );

	// esc_url_raw() adds a scheme via esc_url(), so let's remove it
	$value = str_replace( 'http://', '', $value );

	// Trim off first and last slashes.
	//
	// We already prevent double slashing elsewhere, but let's prevent
	// accidental poisoning of options values where we can.
	$value = ltrim( $value, '/' );
	$value = rtrim( $value, '/' );

	// Filter the result and return
	return apply_filters( 'paco2017_sanitize_slug', $value, $slug );
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

	// Bail when not querying terms whole
	if ( 'all' !== $query_vars['fields'] && 'all_with_object_id' !== $query_vars['fields'] )
		return $terms;

	// Get taxonomies
	$taxes = array(
		paco2017_get_workshop_cat_tax_id(),
		paco2017_get_conf_day_tax_id(),
		paco2017_get_conf_location_tax_id(),
		paco2017_get_association_tax_id()
	);

	// Bail when not querying one of the defined taxonomies
	if ( ! array_intersect( $taxes, (array) $taxonomies ) )
		return $terms;

	// Get taxonomy data
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
 * Modify the archive title
 *
 * @since 1.0.0
 *
 * @param string $title Archive title
 * @return string Archive title
 */
function paco2017_get_the_archive_title( $title ) {

	// Reset archive title, without the 'Archives: ' prefix
	if ( is_post_type_archive( array( paco2017_get_lecture_post_type(), paco2017_get_workshop_post_type() ) ) ) {
		$title = post_type_archive_title( '', false );
	}

	return $title;
}

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
