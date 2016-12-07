<?php

/**
 * Paco2017 Content BuddyPress Members Functions
 *
 * @package Pac2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Add additional members directory tabs
 *
 * @since 1.0.0
 */
function paco2017_bp_members_directory_tabs() {

	// Enrolled members
	if ( paco2017_bp_xprofile_get_enrollment_field() ) {
		printf( '<li id="members-%s"><a href="#">%s <span>%s</span></a></li>',
			'paco2017_enrollment',
			esc_html__( 'Enrolled', 'paco2017-content' ),
			paco2017_bp_get_members_count( 'enrollment' )
		);
	}

	// Associated members
	if ( paco2017_bp_xprofile_get_association_field() ) {
		printf( '<li id="members-%s"><a href="#">%s <span>%s</span></a></li>',
			'paco2017_association',
			sprintf( esc_html_x( '%s Members', 'association members', 'paco2017-content' ), paco2017_bp_xprofile_get_association_value() ),
			paco2017_bp_get_members_count( 'association' )
		);
	}
}

/**
 * Return the specified type of member count
 *
 * @since 1.0.0
 *
 * @param string $scope Optional. Count scope. Defaults to total member count
 * @return int Member count
 */
function paco2017_bp_get_members_count( $scope = '', $user_id = 0 ) {

	// Define return value
	$count = null;

	// Define profile field variable(s)
	$field = false;
	$value = null;
	$compare = '';

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	// Enrolled members
	if ( 'enrollment' === $scope ) {
		$field   = true;
		$value   = paco2017_bp_xprofile_get_enrolled_status_data();
		$compare = '<>';

	// Members by association
	} elseif ( 'association' === $scope ) {
		$field = true;
	}

	// Define profile field callback
	if ( $field && is_callable( "paco2017_bp_xprofile_get_{$scope}_field" ) ) {

		// Query members by profile field value
		$field = call_user_func( "paco2017_bp_xprofile_get_{$scope}_field" );
		$users = paco2017_bp_get_members_by_profile_field_value( $field, $user_id, $value, $compare );
		$count = count( $users );
	}

	// Default to all the site's members
	if ( null === $count ) {
		$count = bp_get_total_member_count();
	}

	return (int) $count;
}

/**
 * Return the members that have the same profile field's value
 *
 * @since 1.0.0
 *
 * @param BP_XProfile_Field|int $field Field object or ID
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @param mixed $value Optional. Value to check for. Defaults to the current user's field value.
 * @param string $compare Optional. Compare type. Defaults to '='.
 * @return array User ids
 */
function paco2017_bp_get_members_by_profile_field_value( $field, $user_id, $value = null, $compare = '' ) {

	// Define default return value
	$users = array();

	// Bail when the XProfile component is not active or field does not exist
	if ( ! bp_is_active( 'xprofile' ) || ! $field = xprofile_get_field( $field ) )
		return $users;

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	// Setup members query
	$query = new BP_User_Query( array(
		'paco2017-xprofile' => array(
			'field_id' => $field->id,
			'user_id'  => $user_id,
			'value'    => $value,
			'compare'  => $compare,
		)
	) );

	if ( $query ) {
		$users = $query->results;
	}

	return $users;
}

/**
 * Modify the BuddyPress user query
 *
 * @since 1.0.0
 *
 * @param BP_User_Query $user_query
 */
function paco2017_bp_pre_user_query( $user_query ) {

	// Get the query's vars
	$qv = $user_query->query_vars;

	// Plugin members query defined
	if ( bp_is_active( 'xprofile' ) && isset( $qv['paco2017-xprofile'] ) ) {
		$args = wp_parse_args( $qv['paco2017-xprofile'], array(
			'value'   => null,
			'compare' => null
		) );

		// Get raw db field value
		if ( null === $args['value'] ) {
			$args['value']   = BP_XProfile_ProfileData::get_value_byid( $args['field_id'], $args['user_id'] );
			$args['compare'] = '=';
		}

		// Setup XProfile query
		$xprofile_query   = $qv['xprofile_query'] ? (array) $qv['xprofile_query'] : array();
		$xprofile_query[] = array(
			'field'   => $args['field_id'],
			'value'   => $args['value'],
			'compare' => $args['compare'],
		);

		// Define XProfile query
		$user_query->query_vars['xprofile_query'] = $xprofile_query;
	}
}

/**
 * Modify the parsed members query arguments
 *
 * @since 1.0.0
 *
 * @param array $args Parsed query args.
 * @return array Parsed query args
 */
function paco2017_bp_parse_has_members_args( $args = array() ) {

	// Fetch template scope
	$scope = isset( $args['scope'] ) ? str_replace( 'paco2017_', '', $args['scope'] ) : false;

	// Bail when this is not a custom profile field scope
	if ( ! $scope || ! in_array( $scope, array( 'enrollment', 'association' ) ) )
		return $args;

	// Define profile field variable(s)
	$user_id = get_current_user_id();
	$field = false;
	$value = null;
	$compare = null;

	// Enrolled members
	if ( 'enrollment' === $scope ) {
		$value   = paco2017_bp_xprofile_get_enrolled_status_data();
		$compare = '<>';
	}

	// Define profile field callback
	if ( is_callable( "paco2017_bp_xprofile_get_{$scope}_field" ) ) {
		$field = call_user_func( "paco2017_bp_xprofile_get_{$scope}_field" );

		// Append query items
		$args['type'] = array(
			'field_id' => $field->id,
			'user_id'  => $user_id,
			'value'    => $value,
			'compare'  => $compare,

			// Take it along. Later we'll reset it
			'_type'    => $args['type'],
		);
	}

	return $args;
}

/**
 * Modify the to-parse members query arguments
 *
 * @since 1.0.0
 *
 * @param array $args Args to-parse
 * @return array Args to-parse
 */
function paco2017_bp_parse_core_get_users_args( $args = array() ) {

	// This has our modified 'type' argument 
	if ( is_array( $args['type'] ) && isset( $args['type']['_type'] ) ) {

		// Preserve 'type' arg
		$type = $args['type']['_type'];
		unset( $args['type']['_type'] );

		// Define query modifier and reset 'type' argument
		$args['paco2017-xprofile'] = $args['type'];
		$args['type'] = $type;
	}

	return $args;
}
