<?php

/**
 * Paco2017 Content BuddyPress Functions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Return whether the user is enrolled
 *
 * BP equivalent of `paco2017_is_user_enrolled()`, except that it defaults
 * the user to the displayed user for BuddyPress pages.
 *
 * @since 1.1.0
 *
 * @param int $user_id Optional. User ID. Defaults to the displeyd user.
 * @return bool Is the user enrolled?
 */
function paco2017_bp_is_user_enrolled( $user_id = 0 ) {

	// Default to the displayed user
	if ( empty( $user_id ) ) {
		$user_id = bp_displayed_user_id();
	}

	return paco2017_is_user_enrolled( $user_id );
}

/**
 * Return whether two users are in the same association
 *
 * BP equivalent of `paco2017_users_in_same_association()`, except that it
 * defaults the first user to the displayed user for BuddyPress pages.
 *
 * @since 1.1.0
 *
 * @param int $user1_id Optional. User ID. Defaults to the displayed user.
 * @param int $user2_id Optional. User ID. Defaults to the current user.
 * @return bool Are users in the same association?
 */
function paco2017_bp_users_in_same_association( $user1_id = 0, $user2_id = 0 ) {

	// Default to the displayed user
	if ( empty( $user1_id ) ) {
		$user1_id = bp_displayed_user_id();
	}

	return paco2017_users_in_same_association( $user1_id, $user2_id );
}

/**
 * Return the user's association title
 *
 * @since 1.1.0
 *
 * @param int $user_id Optional. Uesr ID. Defaults to the displayed or current user.
 * @return string User association title
 */
function paco2017_bp_get_association_title( $user_id = 0 ) {

	// Default to the displayed user
	if ( empty( $user_id ) ) {
		$user_id = bp_displayed_user_id();
	}

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	// Bail when there was no user found
	if ( ! $user_id ) {
		return '';
	}

	return paco2017_get_association_title( get_userdata( $user_id ) );
}
