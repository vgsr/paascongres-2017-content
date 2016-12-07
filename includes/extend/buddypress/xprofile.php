<?php

/**
 * Paco2017 Content BuddyPress XProfile Functions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Return the member's enrollment status
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return bool Is the member enrolled?
 */
function paco2017_bp_xprofile_get_enrollment_status( $user_id = 0 ) {

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	// Define return value
	$enrollment = false;

	// Read the user's profile field value
	if ( $field = paco2017_bp_xprofile_get_enrollment_field() ) {
		$enrollment = (bool) xprofile_get_field_data( $field->id, $user_id );
	}

	return (bool) $enrollment;
}

/**
 * Return the xprofile field data which compares to an enrolled status
 *
 * @since 1.0.0
 *
 * @return mixed Profile field data for the enrolled status
 */
function paco2017_bp_xprofile_get_enrolled_status_data() {

	// Define return value
	$enrolled = '';

	// Read the user's profile field value
	if ( $field = paco2017_bp_xprofile_get_enrollment_field() ) {

		/**
		 * This is a *very* naive way to determine the 'true'-ish field value
		 * @todo This could cause future trouble!
		 */
		$options  = $field->get_children();
		$enrolled = maybe_serialize( array( $options[0]->name ) );
	}

	return $enrolled;
}

/**
 * Return the member's association
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return string The member's association
 */
function paco2017_bp_xprofile_get_association_value( $user_id = 0 ) {

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	// Define return value
	$association = false;

	// Read the user's profile field value
	if ( $field = paco2017_bp_xprofile_get_association_field() ) {
		$association = xprofile_get_field_data( $field->id, $user_id );
	}

	return $association;
}

/** Options ***************************************************************/

/**
 * Return the requested setting's XProfile field
 *
 * @since 1.0.0
 *
 * @param string $setting Setting name
 * @return BP_XProfile_Field|null Profile field when found, else Null.
 */
function paco2017_bp_xprofile_get_setting_field( $setting = '' ) {

	// Bail when the XProfile component is not active
	if ( ! bp_is_active( 'xprofile' ) )
		return null;

	return xprofile_get_field( get_option( $setting, 0 ) );
}

/**
 * Return the selected Enrollment XProfile field
 *
 * @since 1.0.0
 * 
 * @return BP_XProfile_Field|null Profile field when found, else Null.
 */
function paco2017_bp_xprofile_get_enrollment_field() {
	return paco2017_bp_xprofile_get_setting_field( '_paco2017_bp_xprofile_enrollment_field' );
}

/**
 * Return the selected Association XProfile field
 *
 * @since 1.0.0
 * 
 * @return BP_XProfile_Field|null Profile field when found, else Null.
 */
function paco2017_bp_xprofile_get_association_field() {
	return paco2017_bp_xprofile_get_setting_field( '_paco2017_bp_xprofile_association_field' );
}
