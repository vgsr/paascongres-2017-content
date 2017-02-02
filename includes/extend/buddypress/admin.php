<?php

/**
 * Paco2017 Content BuddyPress Admin Functions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Paco2017_BuddyPress_Admin' ) ) :
/**
 * The Paco2017 BuddyPress Admin class
 *
 * @since 1.0.0
 */
class Paco2017_BuddyPress_Admin {

	/**
	 * Setup this class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {

		// Core
		add_action( 'admin_menu',            array( $this, 'admin_menu'      ) );
		add_action( 'admin_head',            array( $this, 'admin_head'      ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// XProfile
		add_action( 'xprofile_admin_field_name_legend', array( $this, 'xprofile_field_states' ), 99 );
	}

	/** Public methods **************************************************/

	/**
	 * Setup admin menu pages
	 *
	 * @since 1.1.0
	 */
	public function admin_menu() {

		// Collect highlightable pages
		$hooks = array();

		// BP Settings page
		if ( paco2017_admin_page_has_settings( 'paco2017-buddypress' ) ) {
			$hooks[] = add_submenu_page(
				'paco2017',
				__( 'Paascongres BuddyPress Settings', 'paco2017-content' ),
				_x( 'Profiles', 'buddypress settings admin menu', 'paco2017-content' ),
				'paco2017_bp_admin_settings_page',
				'paco2017-buddypress',
				'paco2017_admin_page'
			);
		}

		// Register admin page hooks
		add_action( 'paco2017_admin_page-paco2017-buddypress', 'paco2017_bp_admin_settings_page' );

		foreach ( $hooks as $hook ) {
			add_action( "admin_head-{$hook}", array( $this, 'admin_menu_highlight' ) );
		}
	}

	/**
	 * Modify the highlighed menu for the current admin page
	 *
	 * @see Paco2017_Admin::admin_menu_highlight()
	 *
	 * @since 1.1.0
	 *
	 * @global string $parent_file
	 * @global string $submenu_file
	 */
	public function admin_menu_highlight() {
		global $parent_file, $submenu_file;

		// Highlight settings menu item
		if ( 'paco2017' === $parent_file ) {
			$parent_file  = 'paco2017';
			$submenu_file = 'paco2017-settings';
		}
	}

	/**
	 * Remove admin menu items
	 *
	 * @since 1.1.0
	 */
	public function admin_head() {
		remove_submenu_page( 'paco2017', 'paco2017-buddypress' );
	}

	/**
	 * Enqueue additional styles and scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		// Get current screen
		$screen = get_current_screen();

		// Define additional custom styles
		$css = array();

		// XProfile fields
		if ( 'users_page_bp-profile-setup' === $screen->id ) {
			$css[] = ".post-state { color: #555; font-weight: 600; }";
		}

		// Appens styles
		if ( ! empty( $css ) ) {
			wp_add_inline_style( 'paco2017-admin', implode( "\n", $css ) );
		}
	}

	/** XProfile ********************************************************/

	/**
	 * Add content to the profile field admin label
	 *
	 * @since 1.0.0
	 *
	 * @param BP_XProfile_Field $field
	 */
	public function xprofile_field_states( $field ) {

		$states = array();

		// Enrollment field
		if ( paco2017_bp_xprofile_is_enrollment_field( $field ) ) {
			$states['enrollment'] = esc_html_x( 'Enrollment Field', 'admin field label', 'paco2017-content' );
		}

		// Association field
		if ( paco2017_bp_xprofile_is_association_field( $field ) ) {
			$states['association'] = esc_html_x( 'Association Field', 'admin field label', 'paco2017-content' );
		}

		// A workshop field
		if ( paco2017_bp_xprofile_is_a_workshop_field( $field ) ) {
			$states['workshop1'] = esc_html_x( 'Workshop Field', 'admin field label', 'paco2017-content' );
		}

		// Define label wrap
		if ( ! empty( $states ) ) {
			printf( '<span class="post-state">&mdash; %s</span>', implode( ', ', $states ) );
		}
	}
}

/**
 * Setup the extension logic for BuddyPress
 *
 * @since 1.0.0
 *
 * @uses Paco2017_BuddyPress_Admin
 */
function paco2017_buddypress_admin() {
	paco2017_content()->extend->bp->admin = new Paco2017_BuddyPress_Admin;
}

endif; // class_exists
