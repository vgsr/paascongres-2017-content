<?php

/**
 * Paco2017 Content VGSR Functions
 *
 * @package Paco2017 Content
 * @subpackage VGSR
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Paco2017_VGSR' ) ) :
/**
 * The Paco2017 Content VGSR class
 *
 * @since 1.1.0
 */
class Paco2017_VGSR {

	/**
	 * Setup this class
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.1.0
	 */
	private function setup_actions() {

		/** BuddyPress **************************************************/

		add_action( 'bp_directory_members_item',    array( $this, 'members_item_member_type_badge' ) );
		add_action( 'bp_before_member_header_meta', array( $this, 'members_item_member_type_badge' ) );
	}

	/** Public methods **************************************************/

	/**
	 * Output the member-type badge for VGSR oud-leden
	 *
	 * @since 1.1.0
	 */
	public function members_item_member_type_badge() {

		// Displayed or looped member
		if ( bp_is_user() ) {
			$user_id = bp_displayed_user_id();
		} else {
			$user_id = bp_get_member_user_id();
		}

		// Bail when there's no member
		if ( empty( $user_id ) )
			return;

		// Signal oud-lid members for VGSR
		if ( is_user_vgsr() && is_user_oudlid( $user_id ) ) {
			$member_type = bp_get_member_type_object( vgsr_bp_oudlid_member_type() );
			echo '<i class="paco2017-badge member-type-badge">' . $member_type->labels['singular_name'] . '</i>';
		}
	}
}

/**
 * Setup the extension logic for BuddyPress
 *
 * @since 1.1.0
 *
 * @uses Paco2017_VGSR
 */
function paco2017_vgsr() {
	paco2017_content()->extend->vgsr = new Paco2017_VGSR;
}

endif; // class_exists
