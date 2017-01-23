<?php

/**
 * Paco2017 Content BuddyPress Settings Functions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Settings **************************************************************/

/**
 * Modify the list of settings sections
 *
 * @since 1.0.0
 *
 * @param array $sections Settings sections
 * @return array Settings sections
 */
function paco2017_bp_add_settings_sections( $sections ) {

	// Members settings
	$sections['buddypress-fields'] = array(
		'title'    => __( 'Fields', 'paco2017-content' ),
		'callback' => 'paco2017_bp_admin_setting_callback_fields_section',
		'page'     => 'paco2017-buddypress',
	);

	return $sections;
}

/**
 * Modify the list of settings fields
 *
 * @since 1.0.0
 *
 * @param array $fields Settings fields
 * @return array Settings fields
 */
function paco2017_bp_add_settings_fields( $fields ) {

	// Members settings
	$fields['buddypress-fields'] = array(

		// The Enrollment field
		'_paco2017_bp_xprofile_enrollment_field' => array(
			'title'             => __( 'Enrollment Field', 'paco2017-content' ),
			'callback'          => 'paco2017_bp_admin_setting_callback_xprofile_field',
			'sanitize_callback' => 'intval',
			'args'              => array(
				'setting'     => '_paco2017_bp_xprofile_enrollment_field',
				'description' => esc_html__( "Select the field that holds the member's enrollment status.", 'paco2017-content' ),
			)
		),

		// Required fields
		'_paco2017_bp_xprofile_required_fields' => array(
			'title'             => __( 'Required Fields', 'paco2017-content' ),
			'callback'          => 'paco2017_bp_admin_setting_callback_required_fields',
			'sanitize_callback' => null, // This is not an actual setting
			'args'              => array()
		),

		// Association field
		'_paco2017_bp_xprofile_association_field' => array(
			'title'             => __( 'Association Field', 'paco2017-content' ),
			'callback'          => 'paco2017_bp_admin_setting_callback_xprofile_field',
			'sanitize_callback' => 'intval',
			'args'              => array(
				'setting'     => '_paco2017_bp_xprofile_association_field',
				'description' => esc_html__( "Select the field that holds the member's association value.", 'paco2017-content' ),
			)
		),

		// Workshop 1 field
		'_paco2017_bp_xprofile_workshop1_field' => array(
			'title'             => __( 'Workshop 1 Field', 'paco2017-content' ),
			'callback'          => 'paco2017_bp_admin_setting_callback_workshop_field',
			'sanitize_callback' => 'intval',
			'args'              => array(
				'setting'     => '_paco2017_bp_xprofile_workshop1_field',
				'description' => esc_html__( "Select the field that holds the member's first workshop selection.", 'paco2017-content' ),
			)
		),

		// Workshop 2 field
		'_paco2017_bp_xprofile_workshop2_field' => array(
			'title'             => __( 'Workshop 2 Field', 'paco2017-content' ),
			'callback'          => 'paco2017_bp_admin_setting_callback_workshop_field',
			'sanitize_callback' => 'intval',
			'args'              => array(
				'setting'     => '_paco2017_bp_xprofile_workshop2_field',
				'description' => esc_html__( "Select the field that holds the member's second workshop selection.", 'paco2017-content' ),
			)
		),

		// Workshop 1 field round
		'_paco2017_bp_xprofile_workshop1_field_round' => array(
			'sanitize_callback' => 'intval',
			'args'              => array()
		),

		// Workshop 2 field round
		'_paco2017_bp_xprofile_workshop2_field_round' => array(
			'sanitize_callback' => 'intval',
			'args'              => array()
		),
	);

	return $fields;
}

/** General Section *******************************************************/

/**
 * BuddyPress general settings section description for the settings page
 *
 * @since 1.0.0
 */
function paco2017_bp_admin_setting_callback_general_section() { /* Nothing to display */ }

/** Members Section *******************************************************/

/**
 * BuddyPress fields settings section description for the settings page
 *
 * @since 1.0.0
 */
function paco2017_bp_admin_setting_callback_fields_section() { /* Nothing to display */ }

/**
 * Display a XProfile field selector settings field
 *
 * @since 1.0.0
 *
 * @param array $args Settings field arguments
 */
function paco2017_bp_admin_setting_callback_xprofile_field( $args = array() ) {

	// Bail when the setting isn't defined
	if ( ! isset( $args['setting'] ) || empty( $args['setting'] ) )
		return;

	// Bail when the XProfile component is not active
	if ( ! bp_is_active( 'xprofile' ) ) {
		echo '<p>'. __( 'Activate the Extended Profiles component to use this setting.', 'paco2017-content' ) . '</p>';
		return;
	}

	// Get the settings field
	$field = paco2017_bp_xprofile_get_setting_field( $args['setting'] );

	// Fields dropdown
	paco2017_bp_admin_xprofile_fields_dropdown( array(
		'name'     => $args['setting'],
		'selected' => $field ? $field->id : false,
		'echo'     => true,
	) );

	// Display View link
	if ( current_user_can( 'bp_moderate' ) && $field ) {
		printf( ' <a class="button button-secondary" href="%s" target="_blank">%s</a>', 
			esc_url( add_query_arg(
				array(
					'page'     => 'bp-profile-setup',
					'group_id' => $field->group_id,
					'field_id' => $field->id,
					'mode'     => 'edit_field'
				),
				bp_get_admin_url( 'users.php' )
			) ),
			esc_html__( 'View', 'paco2017-content' )
		);
	}

	// Output description
	if ( isset( $args['description'] ) && ! empty( $args['description'] ) ) {
		echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
	}
}

/**
 * Output or return a dropdown with XProfile fields
 *
 * @since 1.0.0
 *
 * @param array $args Dropdown arguments
 * @return string Dropdown HTML markup
 */
function paco2017_bp_admin_xprofile_fields_dropdown( $args = array() ) {

	// Parse default args
	$args = wp_parse_args( $args, array(
		'id' => '', 'name' => '', 'multiselect' => false, 'selected' => 0, 'echo' => false,
	) );

	// Bail when missing attributes
	if ( empty( $args['name'] ) )
		return '';

	// Default id attribute to name
	if ( empty( $args['id'] ) ) {
		$args['id'] = $args['name'];
	}

	// Get all field groups with their fields
	$xprofile = bp_xprofile_get_groups( array( 'fetch_fields' => true, 'hide_empty_groups' => true ) );

	// Start dropdown markup
	$dd  = sprintf( '<select id="%s" name="%s" %s>', esc_attr( $args['id'] ), esc_attr( $args['name'] ), $args['multiselect'] ? 'multiple="multiple"' : '' );
	$dd .= '<option value="">' . __( '&mdash; No Field &mdash;', 'vgsr-entity' )  . '</option>';

	// Walk profile groups
	foreach ( $xprofile as $field_group ) {

		// Start optgroup
		$dd .= sprintf( '<optgroup label="%s">', esc_attr( $field_group->name ) );

		// Walk profile group fields
		foreach ( $field_group->fields as $field ) {
			$dd .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $field->id ), selected( $args['selected'], $field->id, false ), esc_html( $field->name ) );
		}

		// Close optgroup
		$dd .= '</optgroup>';
	}

	// Close dropdown
	$dd .= '</select>';

	if ( $args['echo'] ) {
		echo $dd;
	} else {
		return $dd;
	}
}

/**
 * Output the Required Fields settings field
 *
 * @since 1.0.0
 */
function paco2017_bp_admin_setting_callback_required_fields() {
	$groups    = paco2017_bp_xprofile_get_required_fields();
	$can_edit  = bp_current_user_can( 'bp_moderate' );
	$name_wrap = $can_edit ? '<a href="%2$s">%1$s</a>' : '%1%s';
	if ( $can_edit ) {
		$admin_url = add_query_arg( array( 'page' => 'bp-profile-setup', 'mode' => 'edit_field' ), bp_get_admin_url( 'users.php' ) );
	}

	// Display description
	echo '<p>' . esc_html__( 'These are the required profile fields for this site. You can mark fields as required when editing the field.', 'paco2017-content' ) . '</p>';

	// List required fields
	if ( ! empty( $groups ) ) {
		foreach ( $groups as $group ) {
			echo '<h4>' . $group->name . '</h4>';
			echo '<ul>';
			foreach ( $group->fields as $field ) {
				echo '<li>' . sprintf( $name_wrap, $field->name, esc_url( add_query_arg( array( 'field_id' => $field->id, 'group_id' => $field->group_id ), $admin_url ) ) ) . '</li>';
			}
			echo '</ul>';
		}
	} else {
		echo '<p class="description">' . esc_html__( 'There are no fields selected yet.', 'paco2017-content' ) . '</p>';
	}
}

/**
 * Display a Workshop field selector settings field
 *
 * @since 1.1.0
 *
 * @param array $args Settings field arguments
 */
function paco2017_bp_admin_setting_callback_workshop_field( $args = array() ) {

	// Bail when the setting isn't defined
	if ( ! isset( $args['setting'] ) || empty( $args['setting'] ) )
		return;

	// Profile field selection
	paco2017_bp_admin_setting_callback_xprofile_field( $args );

	// Workshop round selection
	$round_setting = "{$args['setting']}_round";
	wp_dropdown_categories( array(
		'name'              => $round_setting,
		'taxonomy'          => paco2017_get_workshop_round_tax_id(),
		'selected'          => get_option( $round_setting ),
		'option_none_value' => 0,
		'show_option_none'  => __( '&mdash; No Round &mdash;', 'paco2017-content' )
	) );

	echo '<p class="description">' . esc_html__( "Optionally select the workshop round to filter the field's options by.", 'paco2017-content' ) . '</p>';
}

/** Pages ***************************************************************/

/**
 * Modify the plugin's admin pages
 *
 * @since 1.1.0
 *
 * @param array $pages Admin pages
 * @return array Admin pages
 */
function paco2017_bp_add_settings_pages( $pages ) {

	// Add BP settings page
	$pages['paco2017-buddypress'] = esc_html__( 'Profiles', 'paco2017-content' );

	return $pages;
}

/**
 * Output the contents of the BuddyPress Settings admin page
 *
 * @since 1.0.0
 */
function paco2017_bp_admin_settings_page() { ?>

	<form action="options.php" method="post">

		<?php settings_fields( 'paco2017-buddypress' ); ?>

		<?php do_settings_sections( 'paco2017-buddypress' ); ?>

		<?php submit_button(); ?>

	</form>

	<?php
}
