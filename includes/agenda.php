<?php

/**
 * Paco2017 Content Agenda Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Post Type *****************************************************************/

/**
 * Return the Agenda post type
 *
 * @since 1.0.0
 *
 * @return string Post type name
 */
function paco2017_get_agenda_post_type() {
	return 'paco2017_agenda';
}

/**
 * Return the labels for the Agenda post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_agenda_post_type_labels'
 * @return array Agenda post type labels
 */
function paco2017_get_agenda_post_type_labels() {
	return apply_filters( 'paco2017_get_agenda_post_type_labels', array(
		'name'                  => __( 'Paascongres Agenda Items',       'paco2017-content' ),
		'menu_name'             => __( 'Agenda',                         'paco2017-content' ),
		'singular_name'         => __( 'Agenda Item',                    'paco2017-content' ),
		'all_items'             => __( 'All Agenda Items',               'paco2017-content' ),
		'add_new'               => __( 'New Agenda Item',                'paco2017-content' ),
		'add_new_item'          => __( 'Create New Agenda Item',         'paco2017-content' ),
		'edit'                  => __( 'Edit',                           'paco2017-content' ),
		'edit_item'             => __( 'Edit Agenda Item',               'paco2017-content' ),
		'new_item'              => __( 'New Agenda Item',                'paco2017-content' ),
		'view'                  => __( 'View Agenda Item',               'paco2017-content' ),
		'view_item'             => __( 'View Agenda Item',               'paco2017-content' ),
		'view_items'            => __( 'View Agenda Items',              'paco2017-content' ), // Since WP 4.7
		'search_items'          => __( 'Search Agenda Items',            'paco2017-content' ),
		'not_found'             => __( 'No agenda items found',          'paco2017-content' ),
		'not_found_in_trash'    => __( 'No agenda items found in Trash', 'paco2017-content' ),
		'insert_into_item'      => __( 'Insert into agenda item',        'paco2017-content' ),
		'uploaded_to_this_item' => __( 'Uploaded to this agenda item',   'paco2017-content' ),
		'filter_items_list'     => __( 'Filter agenda items list',       'paco2017-content' ),
		'items_list_navigation' => __( 'Agenda Items list navigation',   'paco2017-content' ),
		'items_list'            => __( 'Agenda Items list',              'paco2017-content' ),
	) );
}

/**
 * Return an array of features the Agenda post type supports
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_agenda_post_type_supports'
 * @return array Agenda post type support
 */
function paco2017_get_agenda_post_type_supports() {
	return apply_filters( 'paco2017_get_agenda_post_type_supports', array(
		'title',
		'editor',
		'thumbnail',
	) );
}

/** Taxonomy: Conference Day **************************************************/

/**
 * Return the Conference Day taxonomy
 *
 * @since 1.0.0
 *
 * @return string Taxonomy name
 */
function paco2017_get_conf_day_tax_id() {
	return 'paco2017_conf_day';
}

/**
 * Return the labels for the Conference Day taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_tax_labels'
 * @return array Conference Day taxonomy labels
 */
function paco2017_get_conf_day_tax_labels() {
	return apply_filters( 'paco2017_get_conf_day_tax_labels', array(
		'name'          => __( 'Paascongres Conference Days', 'paco2017-content' ),
		'menu_name'     => __( 'Conference Days',             'paco2017-content' ),
		'singular_name' => __( 'Conference Day',              'paco2017-content' ),
		'search_items'  => __( 'Search Conference Days',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => __( 'All Conference Days',         'paco2017-content' ),
		'no_items'      => __( 'No Conference Day',           'paco2017-content' ),
		'edit_item'     => __( 'Edit Conference Day',         'paco2017-content' ),
		'update_item'   => __( 'Update Conference Day',       'paco2017-content' ),
		'add_new_item'  => __( 'Add New Conference Day',      'paco2017-content' ),
		'new_item_name' => __( 'New Conference Day Name',     'paco2017-content' ),
		'view_item'     => __( 'View Conference Day',         'paco2017-content' )
	) );
}

/** Taxonomy: Conference Location *********************************************/

/**
 * Return the Conference Location taxonomy
 *
 * @since 1.0.0
 *
 * @return string Taxonomy name
 */
function paco2017_get_conf_location_tax_id() {
	return 'paco2017_conf_location';
}

/**
 * Return the labels for the Conference Location taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_location_tax_labels'
 * @return array Conference Location taxonomy labels
 */
function paco2017_get_conf_location_tax_labels() {
	return apply_filters( 'paco2017_get_conf_location_tax_labels', array(
		'name'          => __( 'Paascongres Conference Locations', 'paco2017-content' ),
		'menu_name'     => __( 'Locations',                        'paco2017-content' ),
		'singular_name' => __( 'Conference Location',              'paco2017-content' ),
		'search_items'  => __( 'Search Conference Locations',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => __( 'All Conference Locations',         'paco2017-content' ),
		'no_items'      => __( 'No Conference Location',           'paco2017-content' ),
		'edit_item'     => __( 'Edit Conference Location',         'paco2017-content' ),
		'update_item'   => __( 'Update Conference Location',       'paco2017-content' ),
		'add_new_item'  => __( 'Add New Conference Location',      'paco2017-content' ),
		'new_item_name' => __( 'New Conference Location Name',     'paco2017-content' ),
		'view_item'     => __( 'View Conference Location',         'paco2017-content' )
	) );
}
