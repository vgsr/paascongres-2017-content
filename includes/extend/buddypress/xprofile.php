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
 * Return the title of the member's association
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return string The member's association title
 */
function paco2017_bp_xprofile_get_association_title( $user_id = 0 ) {
	return paco2017_bp_xprofile_get_association_value( $user_id );
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

/**
 * Return whether two members' have a matching associations
 *
 * @since 1.0.0
 *
 * @param int $user1_id Optional. User ID. Defaults to the displayed user.
 * @param int $user2_id Optional. User ID. Defaults to the current user.
 * @return bool Do associations match?
 */
function paco2017_bp_xprofile_association_matches( $user1_id = 0, $user2_id = 0 ) {

	// Default to the displayed user
	if ( empty( $user1_id ) ) {
		$user1_id = bp_displayed_user_id();
	}

	// Default to the current user
	if ( empty( $user2_id ) ) {
		$user2_id = get_current_user_id();
	}

	$assoc1 = paco2017_bp_xprofile_get_association_value( $user1_id );
	$assoc2 = paco2017_bp_xprofile_get_association_value( $user2_id );
	$match  = $assoc1 === $assoc2;

	return (bool) $match;
}

/**
 * Modify the queried profile groups' fields
 *
 * @since 1.0.0
 *
 * @param array $groups Profile groups
 * @param array $args Query arguments
 * @return array Profile groups
 */
function paco2017_bp_xprofile_no_edit_association_field( $groups, $args ) {

	// Bail when no fields were fetched
	if ( ! isset( $args['fetch_fields'] ) || ! $args['fetch_fields'] )
		return $groups;

	// Get association field
	$association_field = paco2017_bp_xprofile_get_association_field();

	// Are we editing fields? Front or in admin
	$editing = bp_is_user_profile_edit() || ( is_admin() && isset( $_GET['page'] ) && 'bp-profile-edit' === $_GET['page'] );
	$no_edit = ! current_user_can( 'bp_moderate' );

	// Walk profile groups
	foreach ( $groups as $gk => $group ) {

		// No fields were queried
		if ( ! isset( $group->fields ) )
			continue;

		// Walk group fields
		foreach ( $group->fields as $fk => $field ) {

			// Remove association field
			if ( $editing && $no_edit && $field->id === $association_field->id ) {
				unset( $groups[ $gk ]->fields[ $fk ] );

				// Reset numeric keys
				$groups[ $gk ]->fields = array_values( $groups[ $gk ]->fields );

				break 2;
			}
		}
	}

	return $groups;
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
