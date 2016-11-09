<?php

/**
 * Paco2017 Content Capability Functions
 * 
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Workshops *****************************************************************/

/**
 * Return the capability mappings for the Lector post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lector_post_type_caps'
 * @return array Lector post type caps
 */
function paco2017_get_lector_post_type_caps() {
	return apply_filters( 'paco2017_get_lector_post_type_caps', array(
		'edit_post'           => 'edit_paco2017_lector',
		'edit_posts'          => 'edit_paco2017_lectores',
		'edit_others_posts'   => 'edit_others_paco2017_lectores',
		'publish_posts'       => 'publish_paco2017_lectores',
		'read_private_posts'  => 'read_private_paco2017_lectores',
		'delete_post'         => 'delete_paco2017_lector',
		'delete_posts'        => 'delete_paco2017_lectores',
		'delete_others_posts' => 'delete_others_paco2017_lectores'
	) );
}

/**
 * Return the capability mappings for the Workshop post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_post_type_caps'
 * @return array Workshop post type caps
 */
function paco2017_get_workshop_post_type_caps() {
	return apply_filters( 'paco2017_get_workshop_post_type_caps', array(
		'edit_post'           => 'edit_paco2017_workshop',
		'edit_posts'          => 'edit_paco2017_workshops',
		'edit_others_posts'   => 'edit_others_paco2017_workshops',
		'publish_posts'       => 'publish_paco2017_workshops',
		'read_private_posts'  => 'read_private_paco2017_workshops',
		'delete_post'         => 'delete_paco2017_workshop',
		'delete_posts'        => 'delete_paco2017_workshops',
		'delete_others_posts' => 'delete_others_paco2017_workshops'
	) );
}
