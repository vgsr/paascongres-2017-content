<?php

/**
 * Paco2017 Content Capability Functions
 * 
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Lectures *******************************************************************/

/**
 * Return the capability mappings for the Lecture post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lecture_post_type_caps'
 * @return array Lecture post type caps
 */
function paco2017_get_lecture_post_type_caps() {
	return apply_filters( 'paco2017_get_lecture_post_type_caps', array(
		'edit_post'           => 'edit_paco2017_lecture',
		'edit_posts'          => 'edit_paco2017_lectures',
		'edit_others_posts'   => 'edit_others_paco2017_lectures',
		'publish_posts'       => 'publish_paco2017_lectures',
		'read_private_posts'  => 'read_private_paco2017_lectures',
		'delete_post'         => 'delete_paco2017_lecture',
		'delete_posts'        => 'delete_paco2017_lectures',
		'delete_others_posts' => 'delete_others_paco2017_lectures'
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

/**
 * Return the capability mappings for the Workshop Category taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_tax_caps'
 * @return array Workshop Category taxonomy caps
 */
function paco2017_get_workshop_cat_tax_caps() {
	return apply_filters( 'paco2017_get_workshop_cat_tax_caps', array(
		'manage_terms' => 'manage_paco2017_workshop_cats',
		'edit_terms'   => 'edit_paco2017_workshop_cats',
		'delete_terms' => 'delete_paco2017_workshop_cats',
		'assign_terms' => 'assign_paco2017_workshop_cats'
	) );
}

/** Agenda ********************************************************************/

/**
 * Return the capability mappings for the Agenda post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_agenda_post_type_caps'
 * @return array Agenda post type caps
 */
function paco2017_get_agenda_post_type_caps() {
	return apply_filters( 'paco2017_get_agenda_post_type_caps', array(
		'edit_post'           => 'edit_paco2017_agenda',
		'edit_posts'          => 'edit_paco2017_agendas',
		'edit_others_posts'   => 'edit_others_paco2017_agendas',
		'publish_posts'       => 'publish_paco2017_agendas',
		'read_private_posts'  => 'read_private_paco2017_agendas',
		'delete_post'         => 'delete_paco2017_agenda',
		'delete_posts'        => 'delete_paco2017_agendas',
		'delete_others_posts' => 'delete_others_paco2017_agendas'
	) );
}

/**
 * Return the capability mappings for the Conference Day taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_tax_caps'
 * @return array Conference Day taxonomy caps
 */
function paco2017_get_conf_day_tax_caps() {
	return apply_filters( 'paco2017_get_conf_day_tax_caps', array(
		'manage_terms' => 'manage_paco2017_conf_days',
		'edit_terms'   => 'edit_paco2017_conf_days',
		'delete_terms' => 'delete_paco2017_conf_days',
		'assign_terms' => 'assign_paco2017_conf_days'
	) );
}

/**
 * Return the capability mappings for the Conference Location taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_location_tax_caps'
 * @return array Conference Location taxonomy caps
 */
function paco2017_get_conf_location_tax_caps() {
	return apply_filters( 'paco2017_get_conf_location_tax_caps', array(
		'manage_terms' => 'manage_paco2017_conf_locations',
		'edit_terms'   => 'edit_paco2017_conf_locations',
		'delete_terms' => 'delete_paco2017_conf_locations',
		'assign_terms' => 'assign_paco2017_conf_locations'
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
