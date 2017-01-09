<?php

/**
 * Paco2017 Content Sub-actions
 *
 * @package Paco2017 Content
 * @subpackage Hooks
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Run dedicated activation hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'paco2017_activation'
 */
function paco2017_activation() {
	do_action( 'paco2017_activation' );
}

/**
 * Run dedicated deactivation hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'paco2017_deactivation'
 */
function paco2017_deactivation() {
	do_action( 'paco2017_deactivation' );
}

/**
 * Run dedicated init hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'paco2017_init'
 */
function paco2017_init() {
	do_action( 'paco2017_init' );
}

/**
 * Run dedicated REST API init hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'paco2017_rest_api_init'
 */
function paco2017_rest_api_init() {
	do_action( 'paco2017_rest_api_init' );
}

/**
 * Run dedicated after post type registration for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'paco2017_registered_{$type}_post_type'
 * @param string $post_type
 */
function paco2017_registered_post_type( $post_type ) {

	// Define plugin post types
	$types = array( 'lecture', 'workshop', 'agenda', 'partner' );

	foreach ( $types as $type ) {

		// Skip when this is not the current post type
		if ( call_user_func( "paco2017_get_{$type}_post_type" ) !== $post_type )
			continue;

		$hook = "paco2017_registered_{$type}_post_type";

		if ( is_callable( $hook ) ) {
			call_user_func( $hook );
		}

		do_action( $hook );

		break;
	}
}

/**
 * Run dedicated after taxonomy registration for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'paco2017_registered_{$tax}_taxonomy'
 * @param string $taxonomy
 */
function paco2017_registered_taxonomy( $taxonomy ) {

	// Define plugin taxonomies
	$taxes = array( 'association', 'speaker', 'conf_day', 'conf_location', 'workshop_cat' );

	foreach ( $taxes as $tax ) {

		// Skip when this is not the current taxonomy
		if ( call_user_func( "paco2017_get_{$tax}_tax_id" ) !== $taxonomy )
			continue;

		$hook = "paco2017_registered_{$tax}_taxonomy";

		if ( is_callable( $hook ) ) {
			call_user_func( $hook );
		}

		do_action( $hook );

		break;
	}
}

/**
 * Run dedicated widgets hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'paco2017_widgets_init'
 */
function paco2017_widgets_init() {
	do_action( 'paco2017_widgets_init' );
}

/**
 * Run dedicated hook after theme setup for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'paco2017_after_setup_theme'
 */
function paco2017_after_setup_theme() {
	do_action( 'paco2017_after_setup_theme' );
}

/**
 * Run dedicated map meta caps filter for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'paco2017_map_meta_caps'
 *
 * @param array $caps Mapped caps
 * @param string $cap Required capability name
 * @param int $user_id User ID
 * @param array $args Additional arguments
 * @return array Mapped caps
 */
function paco2017_map_meta_caps( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {
	return apply_filters( 'paco2017_map_meta_caps', $caps, $cap, $user_id, $args );
}
