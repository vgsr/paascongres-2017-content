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
