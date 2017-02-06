<?php

/**
 * Paco2017 Content BuddyPress XProfile Functions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Enrollment ************************************************************/

/**
 * Modify whether the member is enrolled
 *
 * This filters `paco2017_is_user_enrolled()`, use that function when
 * checking whether a user is enrolled.
 *
 * @since 1.0.0
 *
 * @param bool $enrolled Whether the user is enrolled
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return bool Is the member enrolled?
 */
function paco2017_bp_xprofile_is_user_enrolled( $is = false, $user_id = 0 ) {

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	// Read the user's profile field value
	if ( $field = paco2017_bp_xprofile_get_enrollment_field() ) {
		$_enrolled = (array) paco2017_bp_xprofile_get_enrollment_success_data();
		$enrolled  = (array) xprofile_get_field_data( $field->id, $user_id );
		$is        = $_enrolled === $enrolled;
	}

	return $is;
}

/**
 * Return the query ready format of the xprofile field data which compares to an enrolled status
 *
 * @since 1.1.0
 *
 * @param bool $string_format Optional. Whether to return in string format. Defaults to False.
 * @return array|string Query ready format of the field data for the enrolled status
 */
function paco2017_bp_xprofile_get_enrollment_success_data_for_query( $string_format = false ) {

	// Define the data
	$data   = paco2017_bp_xprofile_get_enrollment_success_data();
	$retval = '';

	if ( $data ) {
		if ( $string_format ) {
			$retval = "'$data', '" . serialize( (array) $data ) . "'";
		} else {
			$retval = array( $data, serialize( (array) $data ) );
		}
	}

	return $retval;
}

/**
 * Return the xprofile field data which compares to an enrolled status
 *
 * @since 1.0.0
 *
 * @return mixed Profile field data for the enrolled status
 */
function paco2017_bp_xprofile_get_enrollment_success_data() {

	// Define return value
	$enrolled = '';

	// Read the user's profile field value
	if ( $field = paco2017_bp_xprofile_get_enrollment_field() ) {
		$enrolled = get_option( '_paco2017_bp_xprofile_enrollment_field_success_data', null );
	}

	return $enrolled;
}

/**
 * Update the enrolled users cache on profile update
 *
 * @since 1.1.0
 */
function paco2017_bp_xprofile_update_enrolled_users_cache() {
	paco2017_update_enrolled_users_cache( array_keys( paco2017_bp_get_enrolled_members() ) );
}

/** Association ***********************************************************/

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

	// Bail when we're not editing fields or the user can moderate
	if ( ! $editing || current_user_can( 'bp_moderate' ) )
		return $groups;

	// Walk profile groups
	foreach ( $groups as $gk => $group ) {

		// No fields were queried
		if ( ! isset( $group->fields ) )
			continue;

		// Walk group fields
		foreach ( $group->fields as $fk => $field ) {

			// Remove association field
			if ( $field->id === $association_field->id ) {
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
	if ( ! $field || $field->id !== (int) $field_data->field_id )
		return;

	// Get the taxonomy
	$taxonomy = paco2017_get_association_tax_id();

	// Get the term's ID or name from the saved value
	$term_id_or_name = $field_data->value;

	// Force integers when dealing with IDs
	if ( is_numeric( $term_id_or_name ) ) {
		$term_id = (int) $term_id_or_name;

	// Try to find the term by name
	} else {
		$term = get_term_by( 'name', $term_id_or_name, $taxonomy );

		// Bail when the term wasn't found
		if ( $term && ! is_wp_error( $term ) ) {
			$term_id = $term->term_id;
		} else {
			$term_id = false;
		}
	}

	// Data was updated, so set new term relationship
	if ( ! empty( $term_id ) ) {
		wp_set_object_terms( $field_data->user_id, $term_id, $taxonomy );

	// Data was removed, so remove all term relationships
	} elseif ( $terms = wp_get_object_terms( $field_data->user_id, $taxonomy ) ) {
		wp_remove_object_terms( $field_data->user_id, $terms, $taxonomy );
	}
}

/** Workshops *************************************************************/

/**
 * Modify the query arguments for the Relationship field's options
 *
 * @since 1.1.0
 *
 * @param array $args Field options query arguments
 * @param string $object Field relationship object
 * @param BP_XProfile_Field $field The current field
 * @return array Query arguments
 */
function paco2017_bp_xprofile_workshop_options_args( $args, $object, $field ) {

	// Workshop field
	if ( paco2017_bp_xprofile_is_a_workshop_field( $field ) ) {

		// Get field's workshop round
		$term = paco2017_bp_xprofile_get_workshop_field_round( $field->id );

		// Term was found, query by workshop round
		if ( ! empty( $term ) ) {

			// Add workshop round taxonomy query
			$tax_query = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
			$tax_query[] = array(
				'taxonomy' => paco2017_get_workshop_round_tax_id(),
				'terms'    => array( $term->term_id ),
			);
			$args['tax_query'] = $tax_query;
		}

		// Set additional query variable(s)
		$args['workshop_limit'] = true;
		$args['workshop_field'] = $field;
	}

	return $args;
}

/**
 * Modify the details of the queried Relationship field's options
 *
 * @since 1.1.0
 *
 * @param array $options Relationship options
 * @param string $object Relationship object
 * @param BP_XProfile_Field $field Field object
 * @param array $query_args Query arguments
 * @return array Relationship options
 */
function paco2017_bp_xprofile_workshop_options( $options, $object, $field, $query_args ) {

	// Workshop field
	if ( paco2017_bp_xprofile_is_a_workshop_field( $field ) ) {

		// Walk queried posts
		foreach ( $options as $k => $option ) {

			// Append available limit count
			if ( $option->id && $limit = paco2017_get_workshop_limit( $option->id ) ) {
				$available = $limit - paco2017_get_workshop_enrolled_user_count( $option->id );
				$options[ $k ]->name .= ' (' . $available . ')';
			}
		}
	}

	return $options;
}

/**
 * Modify the XProfile field description
 *
 * @since 1.1.0
 *
 * @param string $description Profile field description
 * @return string Profile field description
 */
function paco2017_bp_xprofile_workshop_field_description( $description ) {

	// When this is a workshop field, append description of limit count
	if ( paco2017_bp_xprofile_is_a_workshop_field() ) {
		$description .= ' ' . __( 'Between parentheses is the remaining amount of seats available at the workshop.', 'paco2017-content' );
	}

	return $description;
}

/**
 * Return the Workshop Round of the given Sorkshop XProfile field
 *
 * @since 1.1.0
 *
 * @param int $field_id Field id
 * @return WP_Term|bool Workshop round object or False when not found
 */
function paco2017_bp_xprofile_get_workshop_field_round( $field_id = 0 ) {
	$fields = paco2017_bp_xprofile_get_workshop_fields( true );
	$round  = false;

	if ( $num = array_search( $field_id, $fields ) ) {
		$round = get_option( "_paco2017_bp_xprofile_workshop{$num}_field_round", false );
		$round = paco2017_get_workshop_round( $round );
	}

	return $round;
}

/** Advertorials **********************************************************/

/**
 * Modify the advertorial for the current BP page
 *
 * @since 1.1.0
 *
 * @param string $advertorial Advertorial content
 * @param string $location Location name
 * @return string Advertorial content
 */
function paco2017_bp_xprofile_filter_advertorial( $advertorial, $location ) {

	if ( 'bp_before_profile_content' === $location ) {
		$args = paco2017_get_advertorial_args( $location );

		// Remove advertorial for non-assigned pages
		if ( ! empty( $args['page'] ) && bp_current_action() !== $args['page'] ) {
			$advertorial = '';
		}
	}

	return $advertorial;
}

/** Field Options *********************************************************/

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
 * Return whether this is a certain setting's profile field
 *
 * @since 1.1.0
 *
 * @param string $setting_name The field setting's name
 * @param int|BP_XProfile_Field $field_id Field ID or object. Optional. Defaults to current field.
 * @return bool Is this the Enrollment field?
 */
function paco2017_bp_xprofile_is_the_field( $setting_name, $field_id = 0 ) {

	// Bail when the setting name was invalid
	if ( empty( $setting_name ) )
		return false;

	// Get the field's ID
	if ( is_a( $field_id, 'BP_XProfile_Field' ) ) {
		$field_id = $field_id->id;

	// Default to the current fieid
	} elseif ( empty( $field_id ) && isset( $GLOBALS['field'] ) ) {
		$field_id = bp_get_the_profile_field_id();
	}

	$match = false;

	if ( ! empty( $field_id ) ) {
		$field = paco2017_bp_xprofile_get_setting_field( $setting_name );
		$match = is_a( $field, 'BP_XProfile_Field' ) && ( $field->id === $field_id );
	}

	return $match;
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
	return paco2017_bp_xprofile_is_the_field( '_paco2017_bp_xprofile_enrollment_field', $field_id );
}

/**
 * Return the required XProfile fields
 *
 * @since 1.0.0
 *
 * @return array Profile groups with the required fields
 */
function paco2017_bp_xprofile_get_required_fields() {

	// Get the required field ids
	$field_ids = paco2017_bp_xprofile_get_required_field_ids();
	$groups = array();

	if ( ! empty( $field_ids ) ) {
		/**
		 * Because BP has no equivalent for `include_fields` when querying
		 * profile groups, we query all groups by default, and then only
		 * return the matching fields within.
		 */
		$groups = bp_xprofile_get_groups( array( 'hide_emtpy_groups' => true, 'fetch_fields' => true ) );
		foreach ( $groups as $gk => $group ) {
			if ( ! empty( $group->fields ) ) {
				foreach ( $group->fields as $fk => $field ) {
					if ( ! $field->is_required ) {
						unset( $groups[ $gk ]->fields[ $fk ] );
					}
				}
			}

			// Remove empty groups
			if ( empty( $group->fields ) ) {
				unset( $groups[ $gk ] );
			}
		}
	}

	return $groups;
}

/**
 * Return the required XProfile field ids
 *
 * @since 1.0.0
 *
 * @return array required profile field ids
 */
function paco2017_bp_xprofile_get_required_field_ids() {
	global $wpdb;

	// Get BuddyPress
	$bp = buddypress();

	/**
	 * Run a custom query to get fields by detail, since the profile
	 * query logic in BP_XProfile_Group::get() isn't that advanced.
	 */
	$field_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$bp->profile->table_name_fields} WHERE is_required = %d", 1 ) );
	$field_ids = array_map( 'intval', $field_ids );

	return $field_ids;
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
 * Return the selected Presence XProfile field
 *
 * @since 1.1.0
 *
 * @return BP_XProfile_Field|null Profile field when found, else Null.
 */
function paco2017_bp_xprofile_get_presence_field() {
	return paco2017_bp_xprofile_get_setting_field( '_paco2017_bp_xprofile_presence_field' );
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
	return paco2017_bp_xprofile_is_the_field( '_paco2017_bp_xprofile_association_field', $field_id );
}

/**
 * Return the selected Workshop 1 XProfile field
 *
 * @since 1.1.0
 *
 * @return BP_XProfile_Field|null Profile field when found, else Null.
 */
function paco2017_bp_xprofile_get_workshop1_field() {
	return paco2017_bp_xprofile_get_setting_field( '_paco2017_bp_xprofile_workshop1_field' );
}

/**
 * Return the selected Workshop 2 XProfile field
 *
 * @since 1.1.0
 *
 * @return BP_XProfile_Field|null Profile field when found, else Null.
 */
function paco2017_bp_xprofile_get_workshop2_field() {
	return paco2017_bp_xprofile_get_setting_field( '_paco2017_bp_xprofile_workshop2_field' );
}

/**
 * Return whether this is any Workshop XProfile field
 *
 * @since 1.1.0
 *
 * @param int|BP_XProfile_Field $field_id Field ID or object. Optional. Defaults to current field.
 * @return bool Is this any Workshop field?
 */
function paco2017_bp_xprofile_is_a_workshop_field( $field_id = 0 ) {

	// Get the field's ID
	if ( is_a( $field_id, 'BP_XProfile_Field' ) ) {
		$field_id = $field_id->id;

	// Default to the current fieid
	} elseif ( empty( $field_id ) && isset( $GLOBALS['field'] ) ) {
		$field_id = bp_get_the_profile_field_id();
	}

	$match = false;

	if ( $field_id ) {
		$field_ids = paco2017_bp_xprofile_get_workshop_fields( true );
		$match     = in_array( $field_id, $field_ids );
	}

	return $match;
}

/**
 * Return the registered Workshop XProfile fields
 *
 * @since 1.1.0
 *
 * @param bool $ids Optional. Whether to return field ids. Defaults to False.
 * @return array Field objects or ids
 */
function paco2017_bp_xprofile_get_workshop_fields( $ids = false ) {

	// Collect fields
	$fields = array(
		1 => paco2017_bp_xprofile_get_workshop1_field(),
		2 => paco2017_bp_xprofile_get_workshop2_field(),
	);

	// Remove fields that do not exist
	$fields = array_filter( $fields );

	// Pluck ids
	if ( $ids ) {
		$fields = wp_list_pluck( $fields, 'id' );
	}

	return $fields;
}
