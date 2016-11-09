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
	return apply_filters( 'paco2017_get_lector_slug', get_option( '_paco2017_lector_slug', 'lectores' ) );
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
