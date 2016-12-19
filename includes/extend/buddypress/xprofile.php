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
 * Output the title of the member's association
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 */
function paco2017_bp_xprofile_association_title( $user_id = 0 ) {
	echo paco2017_bp_xprofile_get_association_title( $user_id );
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

	// Get the profile field
	$field = paco2017_bp_xprofile_get_association_field();

	// Relationship field, get term title
	if ( $field && 'relationship' === $field->type ) {
		$title = paco2017_get_association_title( $user_id );
	} else {
		$title = paco2017_bp_xprofile_get_association_value( $user_id );
	}

	return $title;
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

		// Relationship field, get unfiltered value
		if ( 'relationship' === $field->type ) {
			$association = BP_XProfile_ProfileData::get_value_byid( $field->id, $user_id );
		} else {
			$association = xprofile_get_field_data( $field->id, $user_id );
		}
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

/**
 * Sync the associaiton profile field with term assignment
 *
 * @since 1.0.0
 *
 * @param BP_XProfile_ProfileData $field_data
 */
function paco2017_bp_xprofile_sync_association_term( $field_data ) {

	// Get the association profile field
	$field = paco2017_bp_xprofile_get_association_field();

	// Bail when this is not the association profile field
	if ( ! $field || $field->id !== $field_data->field_id )
		return;

	// Get the taxonomy
	$taxonomy = paco2017_get_association_tax_id();

	// Bail when this is not an association's relationship field
	if ( 'relationship' !== $field->type || 'taxonomy-' . $taxonomy !== bp_xprofile_get_meta( $field->id, 'field', 'related_to' ) )
		return;

	// Get the term's ID from the saved value
	$term_id = $field_data->value;

	// Force integers when dealing with IDs
	if ( is_numeric( $term_id ) ) {
		$term_id = (int) $term_id;
	}

	// Data was updated, so set new term relationship
	if ( ! empty( $term_id ) ) {
		wp_set_object_terms( $field_data->user_id, $term_id, $taxonomy );

	// Data was removed, so remove all term relationships
	} elseif ( $terms = wp_get_object_terms( $field_data->user_id, $taxonomy ) ) {
		wp_remove_object_terms( $field_data->user_id, $terms, $taxonomy );
	}
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
 * Return whether this is the Enrollment XProfile field
 *
 * @since 1.0.0
 *
 * @param int|BP_XProfile_Field $field_id Field ID or object. Optional. Defaults to current field.
 * @return bool Is this the Enrollment field?
 */
function paco2017_bp_xprofile_is_enrollment_field( $field_id = 0 ) {

	// Get the field's ID
	if ( is_a( $field_id, 'BP_XProfile_Field' ) ) {
		$field_id = $field_id->id;

	// Default to the current fieid
	} elseif ( empty( $field_id ) && isset( $GLOBALS['field'] ) ) {
		$field_id = bp_get_the_profile_field_id();
	}

	$match = false;

	if ( ! empty( $field_id ) ) {
		$field = paco2017_bp_xprofile_get_enrollment_field();
		$match = ( $field->id === $field_id );
	}

	return $match;
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

/**
 * Return whether this is the Association XProfile field
 *
 * @since 1.0.0
 *
 * @param int|BP_XProfile_Field $field_id Field ID or object. Optional. Defaults to current field.
 * @return bool Is this the Association field?
 */
function paco2017_bp_xprofile_is_association_field( $field_id = 0 ) {

	// Get the field's ID
	if ( is_a( $field_id, 'BP_XProfile_Field' ) ) {
		$field_id = $field_id->id;

	// Default to the current fieid
	} elseif ( empty( $field_id ) && isset( $GLOBALS['field'] ) ) {
		$field_id = bp_get_the_profile_field_id();
	}

	$match = false;

	if ( ! empty( $field_id ) ) {
		$field = paco2017_bp_xprofile_get_association_field();
		$match = ( $field->id === $field_id );
	}

	return $match;
}
