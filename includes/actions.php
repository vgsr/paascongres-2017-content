<?php

/**
 * Paco2017 Content Actions
 *
 * @package Paco2017 Content
 * @subpackage Hooks
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Sub-actions ***************************************************************/

add_action( 'init',              'paco2017_init'                     );
add_action( 'widgets_init',      'paco2017_widgets_init'             );
add_action( 'after_setup_theme', 'paco2017_after_setup_theme'        );
add_filter( 'map_meta_cap',      'paco2017_map_meta_caps',     10, 4 );

/** Admin *********************************************************************/

if ( is_admin() ) {
	add_action( 'init', 'paco2017_admin' );
}

/** Extend ********************************************************************/

add_action( 'bp_core_loaded', 'paco2017_buddypress' );
