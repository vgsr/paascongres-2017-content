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
	$sections['buddypress-members'] = array(
		'title'    => __( 'Participants', 'paco2017-content' ),
		'callback' => 'paco2017_bp_admin_setting_callback_members_section',
		'page'     => 'paco2017',
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
	$fields['buddypress-members'] = array(

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
 * BuddyPress members settings section description for the settings page
 *
 * @since 1.0.0
 */
function paco2017_bp_admin_setting_callback_members_section() { /* Nothing to display */ }

/**
 * Display a XProfile field selector settings field
 *
 * @since 1.0.0
 *
 * @param array $args Settings field arguments
 */
function paco2017_bp_admin_setting_callback_xprofile_field( $args = array() ) {

	// Bail when the setting isn't defined
	if ( ! isset( $args['setting'] ) || empty( $args['setting'] ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'This function requires the %s parameter to display the settings field input.', 'paco2017-content' ), '<code>setting</code>' ), '1.0.0' );
		return;
	}

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
	if ( isset( $args['description'] ) && ! empty( $args['description'] ) ) : ?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
	<?php endif;
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
