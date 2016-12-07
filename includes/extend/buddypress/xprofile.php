<?php

/**
 * Paco2017 Content BuddyPress XProfile Functions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

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
