<?php

/**
 * Paco2017 Content Capability Functions
 * 
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Lectors *******************************************************************/

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
		'edit_posts'          => 'edit_paco2017_lectors',
		'edit_others_posts'   => 'edit_others_paco2017_lectors',
		'publish_posts'       => 'publish_paco2017_lectors',
		'read_private_posts'  => 'read_private_paco2017_lectors',
		'delete_post'         => 'delete_paco2017_lector',
		'delete_posts'        => 'delete_paco2017_lectors',
		'delete_others_posts' => 'delete_others_paco2017_lectors'
	) );
}

/** Workshops *****************************************************************/

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

/** Association ***************************************************************/

/**
 * Return the capability mappings for the Association taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_association_tax_caps'
 * @return array Association taxonomy caps
 */
function paco2017_get_association_tax_caps() {
	return apply_filters( 'paco2017_get_association_tax_caps', array(
		'manage_terms' => 'manage_paco2017_associations',
		'edit_terms'   => 'edit_paco2017_associations',
		'delete_terms' => 'delete_paco2017_associations',
		'assign_terms' => 'assign_paco2017_associations'
	) );
}
