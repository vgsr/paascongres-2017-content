<?php

/**
 * Paco2017 Content Settings Functions
 *
 * @package Paco2017 Content
 * @subpackage Administration
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Settings **************************************************************/

/**
 * Return admin settings sections
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_admin_get_settings_sections' with the sections
 * @return array Settings sections
 */
function paco2017_admin_get_settings_sections() {
	return (array) apply_filters( 'paco2017_admin_get_settings_sections', array(

		// Main settings
		'paco2017_settings_main' => array(
			'title'    => __( 'Main Settings', 'paco2017' ),
			'callback' => 'paco2017_admin_setting_callback_main_section',
			'page'     => 'paco2017'
		),
	) );
}

/**
 * Return admin settings fields
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_admin_get_settings_fields' with the fields
 * @return array Settings fields
 */
function paco2017_admin_get_settings_fields() {
	return (array) apply_filters( 'paco2017_admin_get_settings_fields', array(

		/** Main Section **************************************************/

		'paco2017_settings_main' => array(

			// Agenda page
			'_paco2017_agenda_page' => array(
				'title'             => esc_html__( 'Agenda Page', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_agenda_page',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),

			// Housekeeping page
			'_paco2017_housekeeping_page' => array(
				'title'             => esc_html__( 'Housekeeping Page', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_housekeeping_page',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),
		),
	) );
}

/**
 * Get settings fields by section
 *
 * @since 1.0.0
 *
 * @param string $section_id Section id
 * @return mixed False if section is invalid, array of fields otherwise
 */
function paco2017_admin_get_settings_fields_for_section( $section_id = '' ) {

	// Bail if section is empty
	if ( empty( $section_id ) )
		return false;

	$fields = paco2017_admin_get_settings_fields();
	$retval = isset( $fields[ $section_id ] ) ? $fields[ $section_id ] : false;

	return (array) apply_filters( 'paco2017_admin_get_settings_fields_for_section', $retval, $section_id );
}

/**
 * Return whether the admin page has registered settings
 *
 * @since 1.0.0
 *
 * @param string $page
 * @return bool Does the admin page have settings?
 */
function paco2017_admin_page_has_settings( $page = '' ) {

	// Bail when page is empty
	if ( empty( $page ) )
		return false;

	// Loop through the available sections
	$sections = wp_list_filter( paco2017_admin_get_settings_sections(), array( 'page' => $page ) );
	foreach ( (array) $sections as $section_id => $section ) {

		// Find out whether the section has fields
		$fields = paco2017_admin_get_settings_fields_for_section( $section_id );
		if ( ! empty( $fields ) ) {
			return true;
		}
	}

	return false;
}

/** Main Section **********************************************************/

/**
 * Main settings section description for the settings page
 *
 * @since 1.0.0
 */
function paco2017_admin_setting_callback_main_section() { /* Nothing to display */ }

/**
 * Display the content of the Agenda Page settings field
 *
 * @since 1.0.0
 */
function paco2017_admin_setting_callback_agenda_page() {

	// Get settings field
	$page_id = get_option( '_paco2017_agenda_page', false );

	// Pages dropdown
	wp_dropdown_pages( array(
		'name'             => '_paco2017_agenda_page',
		'selected'         => $page_id,
		'show_option_none' => __( '&mdash; No Page &mdash;', 'paco2017-content' ),
	) );

	// Display View link
	if ( $page_id ) {
		printf( ' <a class="button button-secondary" href="%s" target="_blank">%s</a>',
			esc_url( get_permalink( $page_id ) ),
			esc_html__( 'View', 'paco2017-content' )
		);
	} ?>

	<p class="description"><?php esc_html_e( 'Select the page that should contain the agenda information', 'paco2017-content' ); ?></p>

	<?php
}

/**
 * Display the content of the Housekeeping Page settings field
 *
 * @since 1.0.0
 */
function paco2017_admin_setting_callback_housekeeping_page() {

	// Get settings field
	$page_id = get_option( '_paco2017_housekeeping_page', false );

	// Pages dropdown
	wp_dropdown_pages( array(
		'name'             => '_paco2017_housekeeping_page',
		'selected'         => $page_id,
		'show_option_none' => __( '&mdash; No Page &mdash;', 'paco2017-content' ),
	) );

	// Display View link
	if ( $page_id ) {
		printf( ' <a class="button button-secondary" href="%s" target="_blank">%s</a>',
			esc_url( get_permalink( $page_id ) ),
			esc_html__( 'View', 'paco2017-content' )
		);
	} ?>

	<p class="description"><?php esc_html_e( 'Select the page that contains the housekeeping information', 'paco2017-content' ); ?></p>

	<?php
}

/** Pages ***************************************************************/

/**
 * Output the contents of the main admin page
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'paco2017_admin_page-{$page}'
 */
function paco2017_admin_page() { ?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Paascongres 2017', 'paco2017-content' ); ?></h1>

		<h2 class="nav-tab-wrapper"><?php paco2017_admin_page_tabs(); ?></h2>

		<?php do_action( 'paco2017_admin_page-' . paco2017_admin_page_get_current_page() ); ?>

	</div>

	<?php

}

/**
 * Output the contents of the Settings admin page
 *
 * @since 1.0.0
 */
function paco2017_admin_settings_page() { ?>

	<form action="options.php" method="post">

		<?php settings_fields( 'paco2017' ); ?>

		<?php do_settings_sections( 'paco2017' ); ?>

		<?php submit_button(); ?>

	</form>

	<?php
}

/**
 * Display the admin settings page tabs items
 *
 * @since 1.0.0
 */
function paco2017_admin_page_tabs() {

	// Get the admin pages
	$pages = paco2017_admin_page_get_pages();
	$page  = paco2017_admin_page_get_current_page();

	// Walk registered pages
	foreach ( $pages as $slug => $label ) {

		// Skip empty pages
		if ( empty( $label ) )
			continue;

		// Print the tab item
		printf( '<a class="nav-tab%s" href="%s">%s</a>',
			( $page === $slug ) ? ' nav-tab-active' : '',
			esc_url( add_query_arg( array( 'page' => $slug ), admin_url( 'admin.php' ) ) ),
			$label
		);
	}
}

/**
 * Return the admin page pages
 *
 * @since 0.0.7
 *
 * @uses apply_filters() Calls 'paco2017_admin_page_get_pages'
 * @return array Tabs as $page-slug => $label
 */
function paco2017_admin_page_get_pages() {

	// Setup return value
	$pages = array(
		'paco2017' => esc_html__( 'Dashboard', 'paco2017-content' )
	);

	// Add the settings page
	if ( paco2017_admin_page_has_settings( 'paco2017' ) ) {
		$pages['paco2017-settings'] = esc_html__( 'Settings', 'paco2017-content' );
	}

	return (array) apply_filters( 'paco2017_admin_page_get_pages', $pages );
}

/**
 * Return whether any admin page pages are registered
 *
 * @since 1.0.0
 *
 * @return bool Haz admin page pages?
 */
function paco2017_admin_page_has_pages() {
	return (bool) paco2017_admin_page_get_pages();
}

/**
 * Return the current admin page
 *
 * @since 1.0.0
 *
 * @return string The current admin page. Defaults to the first page.
 */
function paco2017_admin_page_get_current_page() {

	$pages = array_keys( paco2017_admin_page_get_pages() );
	$page  = ( isset( $_GET['page'] ) && in_array( $_GET['page'], $pages ) ) ? $_GET['page'] : false;

	// Default to the first page
	if ( ! $page && $pages ) {
		$page = reset( $pages );
	}

	return $page;
}
