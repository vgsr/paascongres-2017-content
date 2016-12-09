<?php

/**
 * Paco2017 Content BuddyPress Members Functions
 *
 * @package Pac2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Template ************************************************************/

/**
 * Add additional members directory tabs
 *
 * @since 1.0.0
 */
function paco2017_bp_members_directory_tabs() {

	// Enrolled members
	if ( paco2017_bp_xprofile_get_enrollment_field() ) {
		printf( '<li class="selected" id="members-all"><a href="#">%s <span>%s</span></a></li>',
			esc_html__( 'Enrolled', 'paco2017-content' ),
			paco2017_bp_get_members_count( 'enrollment' )
		);
	}

	// Associated members
	if ( paco2017_bp_xprofile_get_association_field() ) {
		printf( '<li id="members-%s"><a href="#">%s <span>%s</span></a></li>',
			'paco2017_association',
			paco2017_bp_xprofile_get_association_value(),
			// __( 'My Association', 'paco2017-content' ),
			sprintf( '%s/%s', paco2017_bp_get_enrolled_members_count( 'association' ), paco2017_bp_get_members_count( 'association' ) )
		);
	}
}

/**
 * 404 and bail template loading for restricted profile views
 *
 * @since 1.0.0
 */
function paco2017_bp_members_block_member() {

	// Bail when the user can moderate
	if ( current_user_can( 'bp_moderate' ) )
		return;

	// Bail when this is not a profile view
	if ( ! bp_is_user() )
		return;

	/**
	 * Bail when the user can view this profile:
	 *
	 * 1. When the displayed user is enrolled
	 * 2. When the displayed user is in the same association
	 */
	if ( paco2017_bp_xprofile_get_enrollment_status( bp_displayed_user_id() ) || paco2017_bp_xprofile_association_matches() )
		return;

	// 404 and prevent components from loading their templates
	remove_all_actions( 'bp_template_redirect' );
	bp_do_404();
}

/** Query ***************************************************************/

/**
 * Return the member count of the specified subset of the registered members
 *
 * @since 1.0.0
 *
 * @param string $scope Optional. Count scope. Defaults to total member count
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return int Member count within the scope
 */
function paco2017_bp_get_members_count( $scope = '', $user_id = 0 ) {

	// Count the queried members
	$members = paco2017_bp_get_members( $scope, $user_id );
	$count   = count( $members );

	return $count;
}

/**
 * Return the members in the specified subset of the registered members
 *
 * @since 1.0.0
 *
 * @param string $scope Optional. Count scope. Defaults to total member count
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return array Members within the scope
 */
function paco2017_bp_get_members( $scope = '', $user_id = 0 ) {

	// Define local variable(s)
	$users = array();
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
	}

	return (array) $users;
}

/**
 * Return the member count in a specified subset of the enrolled members
 *
 * @since 1.0.0
 *
 * @param string $scope Optional. Count scope. Defaults to total member count
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return int Member count of enrolled members within the scope
 */
function paco2017_bp_get_enrolled_members_count( $scope, $user_id = 0 ) {

	// Get enrolled members within the scope
	$members = paco2017_bp_get_enrolled_members( $scope, $user_id );
	$count   = count( $members );

	return (int) $count;
}

/**
 * Return the member count in a specified subset of the enrolled members
 *
 * @todo This could someday be integrated in a single query, but that's too much
 *       trouble for now.
 *
 * @since 1.0.0
 *
 * @param string $scope Optional. Count scope. Defaults to total member count
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return array Enrolled members within the scope
 */
function paco2017_bp_get_enrolled_members( $scope, $user_id = 0 ) {

	// Get the enrolled and scoped members
	$enrolled = paco2017_bp_get_members( 'enrollment', $user_id );
	$scoped   = paco2017_bp_get_members( $scope, $user_id );

	// Find intersection of enrolled members within the scope
	$users = array_intersect_key( $scoped, $enrolled );

	return (array) $users;
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
		'type'              => false, // Consider all registered users
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
 * Modify the AJAX query string
 *
 * @since 1.0.0
 *
 * @param string $query_string Query string
 * @param string $context Query context
 * @return string Query string
 */
function paco2017_bp_ajax_query_string( $query_string, $context = '' ) {

	// Default the primary member query to list Enrolled members only
	if ( 'members' === $context && ! in_array( 'scope', array_keys( wp_parse_args( $query_string ) ) ) ) {
		$query_string .= '&scope=paco2017_enrollment';
	}

	return $query_string;
}

/**
 * Modify the parsed members query arguments
 *
 * This filter is paired with {@see paco2017_bp_parse_core_get_users_args()}.
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

			// Take the real type along. Later we'll reset it
			'_type'    => $args['type'],
		);
	}

	return $args;
}

/**
 * Modify the to-parse members query arguments
 *
 * This filter is paired with {@see paco2017_bp_parse_has_members_args()}.
 *
 * @since 1.0.0
 *
 * @param array $args Args to-parse
 * @return array Args to-parse
 */
function paco2017_bp_parse_core_get_users_args( $args = array() ) {

	// This has our modified 'type' argument 
	if ( is_array( $args['type'] ) && isset( $args['type']['_type'] ) ) {

		// Preserve `type` argument
		$type = $args['type']['_type'];
		unset( $args['type']['_type'] );

		// Define query modifier
		$args['paco2017-xprofile'] = $args['type'];

		// Reset `type` argument
		$args['type'] = $type;
	}

	return $args;
}
