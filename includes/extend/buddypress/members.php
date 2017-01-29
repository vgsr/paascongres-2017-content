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

	// Enrolled
	if ( paco2017_bp_xprofile_get_enrollment_field() ) {
		printf( '<li class="selected" id="members-all"><a href="#">%s <span>%s</span></a></li>',
			esc_html__( 'All Enrolled', 'paco2017-content' ),
			paco2017_bp_get_enrolled_members_count()
		);
	}

	// My Association
	if ( paco2017_bp_xprofile_get_association_field() && paco2017_get_user_association() ) {
		printf( '<li id="members-%s"><a href="#">%s <span>%s</span></a></li>',
			paco2017_bp_members_get_association_scope(),
			paco2017_bp_get_association_title(),
			// __( 'My Association', 'paco2017-content' ),
			sprintf( '%s/%s', paco2017_bp_get_enrolled_members_count_by_scope( 'association' ), paco2017_bp_get_members_count( 'association' ) )
		);
	}
}

/**
 * Add order options for the Members directory
 *
 * @since 1.0.0
 */
function paco2017_bp_members_directory_order_options() {

	// Newest Enrolled option
	if ( paco2017_bp_xprofile_get_enrollment_field() ) {
		echo '<option value="' . paco2017_bp_members_get_enrolled_scope() . '">' . esc_html__( 'Newest Enrolled', 'paco2017-content' ) . '</option>';
	}
}

/**
 * Return the current members query scope
 *
 * @since 1.0.0
 *
 * @return string The current members query scope
 */
function paco2017_bp_members_get_query_scope() {

	// Get the current member query's args
	$query_vars = wp_parse_args( bp_ajax_querystring( 'members' ) );

	// Bail when not viewing with a scope
	if ( isset( $query_vars['scope'] ) ) {
		$scope = $query_vars['scope'];
	} else {
		$scope = '';
	}

	return $scope;
}

/**
 * Return the Enrolled scope key
 *
 * @since 1.0.0
 *
 * @return string Enrolled scope key
 */
function paco2017_bp_members_get_enrolled_scope() {
	return 'paco2017_enrollment';
}

/**
 * Return the Association scope key
 *
 * @since 1.0.0
 *
 * @return string Association scope key
 */
function paco2017_bp_members_get_association_scope() {
	return 'paco2017_association';
}

/**
 * Return whether we're viewing the Enrolled members scope
 *
 * @since 1.0.0
 *
 * @return bool Is this the Enrolled members scope?
 */
function paco2017_bp_members_is_enrolled_scope() {
	return ( paco2017_bp_members_get_enrolled_scope() === paco2017_bp_members_get_query_scope() );
}

/**
 * Return whether we're viewing the Association members scope
 *
 * @since 1.0.0
 *
 * @return bool Is this the Association members scope?
 */
function paco2017_bp_members_is_association_scope() {
	return ( paco2017_bp_members_get_association_scope() === paco2017_bp_members_get_query_scope() );
}

/**
 * Display directory details before the start of the members list
 *
 * @since 1.0.0
 */
function paco2017_bp_members_directory_details() {

	// Enrolled
	if ( paco2017_bp_members_is_enrolled_scope() ) {
		esc_html_e( 'The following people have enrolled for the event.', 'paco2017-content' );
	}

	// My Association
	if ( paco2017_bp_members_is_association_scope() ) {
		esc_html_e( 'The following people from your own association have received an invitation.', 'paco2017-content' );
	}
}

/**
 * Modify the member's directory name
 *
 * @since 1.0.0
 *
 * @param string $name Member name
 * @return string Member name
 */
function paco2017_bp_member_name( $name ) {

	// Append enrollment mark
	if ( paco2017_bp_is_user_enrolled( bp_get_member_user_id() ) ) {
		$name .= paco2017_bp_get_enrollment_mark();
	}

	return $name;
}

/**
 * Return the enrollment mark HTML markup
 *
 * @since 1.1.0
 *
 * @return string Enrollmet mark markup
 */
function paco2017_bp_get_enrollment_mark() {
	return '<i class="paco2017-enrolled" title="' . __( 'This member is enrolled for the event.', 'paco2017-content' ) . '"></i>';
}

/**
 * Output the member's item association badge
 *
 * @since 1.0.0
 */
function paco2017_bp_members_item_association_badge() {

	// Display association badge
	if ( paco2017_get_user_association( bp_get_member_user_id() ) ) {
		echo '<i class="paco2017-association-badge"></i>';
	}
}

/**
 * Modify the list of member classes
 *
 * @since 1.0.0
 *
 * @param array $classes Member classes
 * @return array Member classes
 */
function paco2017_bp_get_member_class( $classes ) {

	// Member enrollment status
	if ( paco2017_bp_members_is_enrolled_scope() || paco2017_bp_is_user_enrolled( bp_get_member_user_id() ) ) {
		$classes[] = 'paco2017-is-enrolled';
	} else {
		$classes[] = 'paco2017-not-enrolled';
	}

	// Member association
	if ( $term = paco2017_get_user_association( bp_get_member_user_id() ) ) {
		$classes[] = 'paco2017-association-' . $term->term_id;
	}

	return $classes;
}

/**
 * Modify the member's front page template
 *
 * @since 1.0.0
 *
 * @param array $templates Front page templates
 * @return array Front page templates
 */
function paco2017_bp_members_front_page_template( $templates ) {

	// Do not display the front page for the self
	if ( bp_is_my_profile() ) {
		$templates = array();
	}

	return $templates;
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
	 * 1. When this is the user's own profile
	 * 2. When the displayed user is enrolled
	 * 3. When the displayed user is in the same association
	 */
	if ( bp_is_my_profile() || paco2017_bp_is_user_enrolled() || paco2017_bp_users_in_same_association() )
		return;

	// 404 and prevent components from loading their templates
	remove_all_actions( 'bp_template_redirect' );
	bp_do_404();
}

/**
 * Act when theme compat is defined for a single member's page
 *
 * @since 1.1.0
 */
function paco2017_bp_members_screen_display_profile() {
	add_action( 'bp_template_include_reset_dummy_post_data', 'paco2017_bp_members_reset_dummy_post', 20 );
}

/**
 * Act when theme compat is defined for the members component
 *
 * @see BP_Members_Theme_Compat::single_dummy_post()
 *
 * @since 1.1.0
 */
function paco2017_bp_members_reset_dummy_post() {
	global $post;

	// Append enrollment mark
	if ( paco2017_bp_is_user_enrolled() ) {
		$post->post_title .= paco2017_bp_get_enrollment_mark();
	}
}

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
		$field         = true;
		$value         = paco2017_bp_xprofile_get_enrollment_success_data_for_query();
		$compare       = 'IN';

	// Members by association
	} elseif ( 'association' === $scope ) {
		$field = true;
	}

	// Define profile field callback
	if ( $field && is_callable( "paco2017_bp_xprofile_get_{$scope}_field" ) ) {

		// Query members by profile field value
		$field = call_user_func( "paco2017_bp_xprofile_get_{$scope}_field" );
		$users = paco2017_bp_get_members_by_profile_field_value( $field, $user_id, $value, $compare );

	// Default to all site members
	} elseif ( empty( $scope ) ) {
		$users = get_users();
		$users = array_combine( wp_list_pluck( $users, 'ID' ), $users );
	}

	return (array) $users;
}

/**
 * Return the count of the enrolled members
 *
 * @since 1.0.0
 *
 * @return int Member count of enrolled members
 */
function paco2017_bp_get_enrolled_members_count() {

	// Get members within the enrollment scope
	return (int) paco2017_bp_get_members_count( 'enrollment' );
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
function paco2017_bp_get_enrolled_members_count_by_scope( $scope = '', $user_id = 0 ) {

	// Get enrolled members within the scope
	$members = paco2017_bp_get_enrolled_members_by_scope( $scope, $user_id );
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
function paco2017_bp_get_enrolled_members_by_scope( $scope = '', $user_id = 0 ) {

	// Get the enrolled and scoped members
	$enrolled = paco2017_bp_get_members( 'enrollment', $user_id );
	$scoped   = paco2017_bp_get_members( $scope, $user_id );

	// Find intersection of enrolled members within the scope
	$users = array_intersect_key( $scoped, $enrolled );

	return (array) $users;
}

/**
 * Return or modify the enrolled members for the given association
 *
 * @since 1.0.0
 *
 * @param array $members Enrolled association members
 * @param WP_Term|int|string $association Term object or id or name or slug
 * @return array Enrolled association members
 */
function paco2017_bp_get_enrolled_members_for_association( $members = array(), $association ) {

	// Get profile fields
	$enrollment_field  = paco2017_bp_xprofile_get_enrollment_field();
	$association_field = paco2017_bp_xprofile_get_association_field();

	// Bail when the fields do not exist
	if ( ! $association_field || ! $enrollment_field )
		return $members;

	// Setup members query
	$query = new BP_User_Query( array(
		'type'           => false, // Consider all registered users
		'xprofile_query' => array(
			array(
				'field_id' => $enrollment_field->id,
				'value'    => paco2017_bp_xprofile_get_enrollment_success_data_for_query(),
				'compare'  => 'IN',
			),

			// Query based on profile field association (so no user tax_query)
			array(
				'field_id' => $association_field->id,
				'value'    => is_a( $association, 'WP_Term' ) ? $association->term_id : $association,
				'compare'  => '=',
			)
		)
	) );

	if ( $query ) {
		$members = $query->results;
	}

	return $members;
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
 * Return or modify the enrolled members for the given workshop
 *
 * @since 1.0.0
 *
 * @param array $members Enrolled workshop members
 * @param WP_Term|int|string $workshop Post object or ID
 * @return array Enrolled workshop members
 */
function paco2017_bp_get_enrolled_members_for_workshop( $members = array(), $workshop ) {

	// Get profile fields
	$enrollment_field  = paco2017_bp_xprofile_get_enrollment_field();
	$workshop1_field   = paco2017_bp_xprofile_get_workshop1_field();
	$workshop2_field   = paco2017_bp_xprofile_get_workshop2_field();

	// Bail when the fields do not exist
	if ( ( ! $workshop1_field && ! $workshop2_field ) || ! $enrollment_field )
		return $members;

	// Define profile query workshop part
	$workshop_profile_query = array(
		'relation' => 'OR'
	);

	if ( $workshop1_field ) {
		$workshop_profile_query[] = array(
			'field_id' => $workshop1_field->id,
			'value'    => is_a( $workshop, 'WP_Post' ) ? $workshop->ID : $workshop,
			'compare'  => '=',
		);
	}

	if ( $workshop2_field ) {
		$workshop_profile_query[] = array(
			'field_id' => $workshop2_field->id,
			'value'    => is_a( $workshop, 'WP_Post' ) ? $workshop->ID : $workshop,
			'compare'  => '=',
		);
	}

	// Setup members query
	$query = new BP_User_Query( array(
		'type'           => false, // Consider all registered users
		'xprofile_query' => array(
			array(
				'field_id' => $enrollment_field->id,
				'value'    => paco2017_bp_xprofile_get_enrollment_success_data_for_query(),
				'compare'  => 'IN',
			),
			$workshop_profile_query
		)
	) );

	if ( $query ) {
		$members = $query->results;
	}

	return $members;
}

/** Dashboard ***********************************************************/

/**
 * Modify the list of dashboard statuses in the Status widget
 *
 * @since 1.0.0
 *
 * @param array $statuses Statuses
 * @return array Statuses
 */
function paco2017_bp_members_dashboard_statuses( $statuses ) {

	// Enrolled members link to Pofiles
	if ( isset( $statuses['enrolled-count'] ) ) {
		$statuses['enrolled-count'] = preg_replace( "/href=\"(.*)\"/", 'href="'. esc_url( bp_get_members_directory_permalink() ) . '"', $statuses['enrolled-count'] );
	}

	return $statuses;
}

/** Query ***************************************************************/

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

	// Default the primary member query (without scope) to list Enrolled members only (enrolled scope)
	if ( 'members' === $context && ! array_key_exists( 'scope', wp_parse_args( $query_string ) ) ) {
		$query_string .= '&scope=' . paco2017_bp_members_get_enrolled_scope();
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
		$value   = paco2017_bp_xprofile_get_enrollment_success_data_for_query();
		$compare = 'IN';
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

	// Query by profile field
	if ( bp_is_active( 'xprofile' ) && isset( $qv['paco2017-xprofile'] ) ) {
		$args = wp_parse_args( $qv['paco2017-xprofile'], array(
			'value'   => null,
			'compare' => null
		) );

		// Get raw db field value
		if ( null === $args['value'] && isset( $args['user_id'] ) ) {
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
 * Modify the SQL clauses of the user query
 *
 * @since 1.0.0
 *
 * @global WPDB $wpdb
 *
 * @param array $sql SQL clauses
 * @param BP_User_Query $user_query
 * @return array SQL clauses
 */
function paco2017_bp_user_query_uid_clauses( $sql, $user_query ) {
	global $wpdb;

	// Get BuddyPress
	$bp = buddypress();

	// Get the query's vars
	$qv = $user_query->query_vars;

	// Ordering by Newest Enrolled
	if ( paco2017_bp_members_get_enrolled_scope() === $qv['type'] && $field = paco2017_bp_xprofile_get_enrollment_field() ) {

		// Join with profile data
		$sql['select'] .= $wpdb->prepare( " LEFT JOIN {$bp->profile->table_name_data} enrolled ON u.{$user_query->uid_name} = enrolled.user_id AND enrolled.field_id = %d", $field->id );

		/**
		 * Order by enrolled date.
		 *
		 * When enrollment is cancelled, the field's value is registered in the db as
		 * an empty serialized array. This has also an actual 'last_updated' recording.
		 * To circumvent this, first separate empty values from valid ones - then sort
		 * by date.
		 */
		$enrolled_data  = paco2017_bp_xprofile_get_enrollment_success_data_for_query( true );
		$sql['orderby'] = "ORDER BY ( CASE WHEN enrolled.value NOT IN ( $enrolled_data ) THEN 0 ELSE 1 END ) DESC, enrolled.last_updated";
		$sql['order']   = "DESC";
	}

	return $sql;
}
