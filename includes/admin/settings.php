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

		// Archive settings
		'paco2017_settings_archives' => array(
			'title'    => __( 'Archive Settings', 'paco2017' ),
			'callback' => 'paco2017_admin_setting_callback_archives_section',
			'page'     => 'paco2017'
		),

		// Slug settings
		'paco2017_settings_slugs' => array(
			'title'    => __( 'Slug Settings', 'paco2017' ),
			'callback' => 'paco2017_admin_setting_callback_slugs_section',
			'page'     => 'paco2017'
		),

		// Partner settings
		'paco2017_settings_advertorials' => array(
			'title'    => __( 'Advertorials', 'paco2017' ),
			'callback' => 'paco2017_admin_setting_callback_advertorials_section',
			'page'     => 'paco2017-partners'
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

			// Enrollment deadline
			'_paco2017_enrollment_deadline' => array(
				'title'             => esc_html__( 'Enrollment Deadline', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_date',
				'sanitize_callback' => 'paco2017_admin_setting_sanitize_date',
				'args'              => array(
					'setting'     => '_paco2017_enrollment_deadline',
					'description' => esc_html__( 'Select the date after which the enrollment will be closed', 'paco2017-content' ),
				)
			),

			// Contact email
			'_paco2017_contact_email' => array(
				'title'             => esc_html__( 'Contact Email', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_contact_email',
				'sanitize_callback' => 'sanitize_email',
				'args'              => array()
			),

			// Housekeeping page
			'_paco2017_housekeeping_page' => array(
				'title'             => esc_html__( 'Housekeeping Page', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_page',
				'sanitize_callback' => 'intval',
				'args'              => array(
					'setting'     => '_paco2017_housekeeping_page',
					'description' => esc_html__( 'Select the page that contains the housekeeping information', 'paco2017-content' ),
				)
			),

			// Magazine Page
			'_paco2017_magazine_page' => array(
				'title'             => esc_html__( 'Magazine', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_magazine',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),

			// Magazine Download
			'_paco2017_magazine_download' => array(
				'sanitize_callback' => 'intval',
				'args'              => array()
			),
		),

		/** Archives Section **********************************************/

		'paco2017_settings_archives' => array(

			// Lectures description
			'_paco2017_lecture_archive_desc' => array(
				'title'             => esc_html__( 'Lectures Description', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_description',
				'sanitize_callback' => 'strip_tags',
				'args'              => array(
					'setting'     => '_paco2017_lecture_archive_desc',
					'description' => esc_html__( 'This description text appears as an introduction to the lecture archive page', 'paco2017-content' ),
				)
			),

			// Workshops description
			'_paco2017_workshop_archive_desc' => array(
				'title'             => esc_html__( 'Workshops Description', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_description',
				'sanitize_callback' => 'strip_tags',
				'args'              => array(
					'setting'     => '_paco2017_workshop_archive_desc',
					'description' => esc_html__( 'This description text appears as an introduction to the workshop archive page', 'paco2017-content' ),
				)
			),

			// Agenda description
			'_paco2017_agenda_page_desc' => array(
				'title'             => esc_html__( 'Agenda Description', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_description',
				'sanitize_callback' => 'strip_tags',
				'args'              => array(
					'setting'     => '_paco2017_agenda_page_desc',
					'description' => esc_html__( 'This description text appears as an introduction to the agenda page', 'paco2017-content' ),
				)
			),

			// Speakers description
			'_paco2017_speakers_page_desc' => array(
				'title'             => esc_html__( 'Speakers Description', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_description',
				'sanitize_callback' => 'strip_tags',
				'args'              => array(
					'setting'     => '_paco2017_speakers_page_desc',
					'description' => esc_html__( 'This description text appears as an introduction to the speakers page', 'paco2017-content' ),
				)
			),

			// Associations description
			'_paco2017_associations_page_desc' => array(
				'title'             => esc_html__( 'Associations Description', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_description',
				'sanitize_callback' => 'strip_tags',
				'args'              => array(
					'setting'     => '_paco2017_associations_page_desc',
					'description' => esc_html__( 'This description text appears as an introduction to the associations page', 'paco2017-content' ),
				)
			),
		),

		/** Slugs Section *************************************************/

		'paco2017_settings_slugs' => array(

			// Lectures
			'_paco2017_lecture_slug' => array(
				'title'             => esc_html__( 'Lecture', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_slug',
				'sanitize_callback' => 'paco2017_sanitize_slug',
				'args'              => array(
					'setting' => '_paco2017_lecture_slug',
					'default' => 'lectures'
				)
			),

			// Workshops
			'_paco2017_workshop_slug' => array(
				'title'             => esc_html__( 'Workshop', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_slug',
				'sanitize_callback' => 'paco2017_sanitize_slug',
				'args'              => array(
					'setting' => '_paco2017_workshop_slug',
					'default' => 'workshops'
				)
			),

			// Workshop Categories
			'_paco2017_workshop_cat_slug' => array(
				'title'             => esc_html__( 'Workshop Category', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_workshop_cat_slug',
				'sanitize_callback' => 'paco2017_sanitize_slug',
				'args'              => array()
			),

			// Workshop Round
			'_paco2017_workshop_round_slug' => array(
				'title'             => esc_html__( 'Workshop Round', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_workshop_round_slug',
				'sanitize_callback' => 'paco2017_sanitize_slug',
				'args'              => array()
			),

			// Speakers
			'_paco2017_speakers_slug' => array(
				'title'             => esc_html__( 'Speaker', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_slug',
				'sanitize_callback' => 'paco2017_sanitize_slug',
				'args'              => array(
					'setting' => '_paco2017_speakers_slug',
					'default' => 'speakers'
				)
			),

			// Agenda
			'_paco2017_agenda_slug' => array(
				'title'             => esc_html__( 'Agenda', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_slug',
				'sanitize_callback' => 'paco2017_sanitize_slug',
				'args'              => array(
					'setting' => '_paco2017_agenda_slug',
					'default' => 'agenda'
				)
			),

			// Associations
			'_paco2017_associations_slug' => array(
				'title'             => esc_html__( 'Association', 'paco2017-content' ),
				'callback'          => 'paco2017_admin_setting_callback_slug',
				'sanitize_callback' => 'paco2017_sanitize_slug',
				'args'              => array(
					'setting' => '_paco2017_associations_slug',
					'default' => 'associations'
				)
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
 * Date selection setting field
 *
 * @since 1.1.0
 *
 * @param array $args Setting field arguments
 */
function paco2017_admin_setting_callback_date( $args = array() ) {

	// Bail when the setting is not defined
	if ( ! isset( $args['setting'] ) || empty( $args['setting'] ) )
		return;

	// Get settings field
	$date = get_option( $args['setting'], false );

	echo '<input type="text" name="' . esc_attr( $args['setting'] ) . '" class="datepicker" value="' . esc_attr( mysql2date( 'd-m-Y', $date ) ) .'" placeholder="dd-mm-yyyy" />';

	if ( isset( $args['description'] ) ) {
		echo '<p class="description">' . $args['description'] . '</p>';
	}

	// Enqueue the date picker
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'term-date', paco2017_content()->assets_url . 'css/term-date.css', array(), paco2017_get_version() );

	// Setup date pickers
	static $date_script = null;
	if ( null === $date_script ) {
		$date_script = "jQuery( document ).ready( function( $ ) { $( '.datepicker' ).datepicker( { dateFormat: 'dd-mm-yy' } ); });";
		wp_add_inline_script( 'jquery-ui-datepicker', $date_script );
	}
}

/**
 * Sanitize the input from a Date Selection type settings field
 *
 * @since 1.1.0
 *
 * @param mixed $input Field input
 * @return string Sanitized date in mysql format
 */
function paco2017_admin_setting_sanitize_date( $input ) {
	$date = DateTime::createFromFormat( 'd-m-Y', $input );

	if ( ! $date ) {
		$date = DateTime::createFromFormat( 'Y-m-d 00:00:00', $input );
	}

	if ( $date ) {
		$input = $date->format( 'Y-m-d 00:00:00' );
	} else {
		$input = false;
	}

	return $input;
}

/**
 * Display the Contact Email settings field
 *
 * @since 1.1.0
 */
function paco2017_admin_setting_callback_contact_email() { ?>

	<input type="text" name="_paco2017_contact_email" id="_paco2017_contact_email" class="regular-text" value="<?php echo esc_attr( get_option( '_paco2017_contact_email' ) ); ?>" />
	<p class="description"><?php esc_html_e( 'Provide the main contact email address for site visitors', 'paco2017-content' ); ?></p>

	<?php
}

/**
 * Page selection setting field
 *
 * @since 1.0.0
 *
 * @param array $args Setting field arguments
 */
function paco2017_admin_setting_callback_page( $args = array() ) {

	// Bail when the setting is not defined
	if ( ! isset( $args['setting'] ) || empty( $args['setting'] ) )
		return;

	// Get settings field
	$page_id = get_option( $args['setting'], false );

	// Pages dropdown
	wp_dropdown_pages( array(
		'name'             => $args['setting'],
		'selected'         => $page_id,
		'show_option_none' => __( '&mdash; No Page &mdash;', 'paco2017-content' ),
	) );

	// Display View link
	if ( $page_id ) {
		printf( ' <a class="button button-secondary" href="%s" target="_blank">%s</a>',
			esc_url( get_permalink( $page_id ) ),
			esc_html__( 'View', 'paco2017-content' )
		);
	}

	if ( isset( $args['description'] ) ) {
		echo '<p class="description">' . $args['description'] . '</p>';
	}
}

/**
 * Display the content of the Magazine settings field
 *
 * @since 1.0.0
 *
 * @param array $args Setting field arguments
 */
function paco2017_admin_setting_callback_magazine( $args = array() ) {

	// Page field
	paco2017_admin_setting_callback_page( array(
		'setting'     => '_paco2017_magazine_page',
		'description' => esc_html__( 'Select the magazine landing page', 'paco2017-content' )
	) );

	// Download attachment field
	wp_setting_media_field( array(
		'setting'     => '_paco2017_magazine_download',
		'description' => esc_html__( 'Select the magazine file that is offered for download', 'paco2017-content' )
	) );
}

/** Archives Section ******************************************************/

/**
 * Archives settings section description for the settings page
 *
 * @since 1.0.0
 */
function paco2017_admin_setting_callback_archives_section() { /* Nothing to display */ }

/**
 * Description setting field
 *
 * @since 1.0.0
 *
 * @param array $args Setting field arguments
 */
function paco2017_admin_setting_callback_description( $args = array() ) {

	// Bail when the setting is not defined
	if ( ! isset( $args['setting'] ) || empty( $args['setting'] ) )
		return;

	$setting = esc_attr( $args['setting'] );

	?>

	<textarea name="<?php echo $setting; ?>" id="<?php echo $setting; ?>" class="large-text" cols="50" rows="4"><?php echo esc_textarea( get_option( $setting ) ); ?></textarea>

	<?php if ( isset( $args['description'] ) ) {
		echo '<p class="description">' .  $args['description'] . '</p>';
	}
}

/** Slugs Section *********************************************************/

/**
 * Slugs settings section description for the settings page
 *
 * @since 1.0.0
 */
function paco2017_admin_setting_callback_slugs_section() {

	// Flush rewrite rules when this section is saved
	if ( isset( $_GET['settings-updated'] ) && isset( $_GET['page'] ) )
		paco2017_delete_rewrite_rules(); ?>

	<p><?php esc_html_e( "Customize the structure of your conference page urls.", 'paco2017-content' ); ?></p>

	<?php
}

/**
 * Slug setting field
 *
 * @since 1.0.0
 *
 * @param array $args Setting field arguments
 */
function paco2017_admin_setting_callback_slug( $args = array() ) {

	// Bail when the setting is not defined
	if ( ! isset( $args['setting'] ) || empty( $args['setting'] ) )
		return;

	$setting = esc_attr( $args['setting'] );
	$default = isset( $args['default'] ) ? $args['default'] : '';

	?>

	<input name="<?php echo $setting; ?>" id="<?php echo $setting; ?>" type="text" class="regular-text code" value="<?php echo get_option( $args['setting'], $default ); ?>" />

	<?php if ( isset( $args['description'] ) ) {
		echo '<p class="description">' .  $args['description'] . '</p>';
	}
}

/**
 * Workshop Category slug setting field
 *
 * @since 1.0.0
 */
function paco2017_admin_setting_callback_workshop_cat_slug() {
	$slug = get_option( '_paco2017_workshop_cat_slug', 'category' ); ?>

	<input name="_paco2017_workshop_cat_slug" id="_paco2017_workshop_cat_slug" type="text" class="regular-text code" value="<?php echo $slug; ?>" />
	<p class="description"><?php printf( esc_html__( 'Will be used after the workshop slug, like: %s', 'paco2017-content' ), sprintf( '<code>%s/<strong>%s</strong></code>', get_option( '_paco2017_workshop_slug', 'workshops' ), $slug ) ); ?></p>

	<?php
}

/**
 * Workshop Round slug setting field
 *
 * @since 1.1.0
 */
function paco2017_admin_setting_callback_workshop_round_slug() {
	$slug = get_option( '_paco2017_workshop_round_slug', 'round' ); ?>

	<input name="_paco2017_workshop_round_slug" id="_paco2017_workshop_round_slug" type="text" class="regular-text code" value="<?php echo $slug; ?>" />
	<p class="description"><?php printf( esc_html__( 'Will be used after the workshop slug, like: %s', 'paco2017-content' ), sprintf( '<code>%s/<strong>%s</strong></code>', get_option( '_paco2017_workshop_slug', 'workshops' ), $slug ) ); ?></p>

	<?php
}

/** Advertorials Section **************************************************/

/**
 * Advertorials settings section description for the settings page
 *
 * @since 1.1.0
 */
function paco2017_admin_setting_callback_advertorials_section() { ?>

	<p><?php esc_html_e( "Define details of the custom partner advertorial sections on this site.", 'paco2017-content' ); ?></p>

	<?php
}

/**
 * Editor setting field
 *
 * @since 1.1.0
 *
 * @param array $args Settings field arguments
 */
function paco2017_admin_setting_callback_editor( $args = array() ) {

	// Bail when the setting is not defined
	if ( ! isset( $args['setting'] ) || empty( $args['setting'] ) )
		return;

	// Output editor
	wp_editor( get_option( $args['setting'], '' ), $args['setting'], array(
		'textarea_rows' => 7
	) );

	if ( isset( $args['description'] ) ) {
		echo '<p class="description">' .  $args['description'] . '</p>';
	}
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

		<h1><?php esc_html_e( 'Paascongres', 'paco2017-content' ); ?></h1>

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
function paco2017_admin_settings_page() {

	// Get the settings page name
	$settings_page = paco2017_admin_page_get_current_page();
	if ( 'paco2017-settings' === $settings_page ) {
		$settings_page = 'paco2017';
	}

	?>

	<form action="options.php" method="post">

		<?php settings_fields( $settings_page ); ?>

		<?php do_settings_sections( $settings_page ); ?>

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

	// Add the Partners settings page
	if ( paco2017_admin_page_has_settings( 'paco2017-partners' ) ) {
		$pages['paco2017-partners'] = esc_html_x( 'Partners', 'settings tab title', 'paco2017-content' );
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
