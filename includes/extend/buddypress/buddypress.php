<?php

/**
 * Paco2017 Content BuddyPress Functions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Paco2017_BuddyPress' ) ) :
/**
 * The Paco2017 Content BuddyPress class
 *
 * @since 1.0.0
 */
class Paco2017_BuddyPress {

	/**
	 * Setup this class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
	}

	/**
	 * Define default class globals
	 *
	 * @since 1.0.0
	 */
	private function setup_globals() {

		// Load Paco2017
		$paco = paco2017_content();

		/** Paths *******************************************************/

		// Base
		$this->base_dir   = trailingslashit( $paco->includes_dir . 'extend/buddypress' );
		$this->base_url   = trailingslashit( $paco->includes_url . 'extend/buddypress' );

		// Themes
		$this->themes_dir = trailingslashit( $this->base_dir . 'templates' );
		$this->themes_url = trailingslashit( $this->base_url . 'templates' );
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {

		/**
		 * Ideas
		 * - Toon vereniging badge
		 * - Toon aangemeld ja/nee badge
		 * - Front page met customizable content
		 */

		// Register plugin template directory
		bp_register_template_stack( function() {
			return $this->themes_dir;
		}, 8 );

		// General limitations
		add_action( 'bp_init',          array( $this, 'hide_component_parts'     ),  5    );
		add_action( 'bp_init',          array( $this, 'hide_component_parts_nav' ), 99    );
		add_filter( 'bp_map_meta_caps', array( $this, 'map_meta_cap'             ), 20, 4 );

		// VGSR
		add_action( 'vgsr_loaded', array( $this, 'setup_vgsr_actions' ) );
	}

	/** Public methods **************************************************/

	/**
	 * Prevent the user from being exposed to certain component parts
	 *
	 * @since 1.0.0
	 */
	public function hide_component_parts() {

		// Get BuddyPress
		$bp = buddypress();

		// Define hidden component parts
		$components = array(
			'activity' => array(
				'setup_admin_bar',
				'setup_nav'
			),
			'xprofile' => array(
				'bp_settings_admin_nav' => array( 'setup_settings_admin_nav', 2 )
			),
		);

		// Hide profile navigation on other's profiles
		if ( ! bp_is_my_profile() ) {
			/**
			 * Unhooking the 'setup_nav' action has a reverse effect when primary
			 * nav items are used outside of the displayed member profile navigation
			 * element. See for example `bp_nav_menu_get_loggedin_pages()`.
			 *
			 * $components['xprofile'][] = 'setup_nav';
			 *
			 * Instead, the member navigation items are edited to not show for
			 * the displayed user. See `::hide_component_parts_after_init()`.
			 */
		}

		// Walk components
		foreach ( $components as $component => $parts ) {

			// Skip inactive components
			if ( ! bp_is_active( $component ) )
				continue;

			// Map 'xprofile' to 'profile'
			if ( 'xprofile' === $component ) {
				$component = 'profile';
			}

			// Get the class responsible
			$class = $bp->{$component};

			// Unhook the component parts
			foreach ( $parts as $hook => $part ) {

				// Setup hook args from array
				if ( is_array( $part ) ) {
					$priority = $part[1];
					$part     = $part[0];
				} else {

					// Define priority. Most hook at 10
					$priority = ( 'setup_admin_bar' === $part ) ? $class->adminbar_myaccount_order : 10;

					// Default hook to bp_{part}
					if ( is_numeric( $hook ) ) {
						$hook = "bp_{$part}";
					}
				}

				// Remove the hook (filters and actions alike)
				remove_action( $hook, array( $class, $part ), $priority );
			}
		}
	}

	/**
	 * Prevent the user from being exposed to certain component parts, after 'bp_init'
	 *
	 * @since 1.0.0
	 */
	public function hide_component_parts_nav() {

		// Get BuddyPress
		$bp = buddypress();

		// Members: hide Profile nav tabs on another member's profile
		$bp->members->nav->edit_nav( array( 'show_for_displayed_user' => false ), bp_get_profile_slug() );

		// Settings
		remove_all_actions( 'bp_notification_settings' ); // Eliminates need for Email (admin) nav
		bp_core_remove_subnav_item( bp_get_settings_slug(), 'notifications' );
		bp_core_remove_subnav_item( bp_get_settings_slug(), 'profile'       ); // See BP_XProfile_Component::setup_settings_nav()
	}

	/**
	 * Modify the mapped capabilities
	 *
	 * @since 1.0.0
	 *
	 * @param array $caps Mapped caps
	 * @param string $cap Required cap
	 * @param int $user_id User ID
	 * @param array $args Arguments
	 * @return array Mapped caps
	 */
	public function map_meta_cap( $caps, $cap, $user_id, $args ) {

		switch ( $cap ) {
			case 'bp_xprofile_change_field_visibility' :

				// Prevent chaning field visibility for non-admins
				if ( ! user_can( $user_id, 'bp_moderate' ) ) {
					$caps = array( 'do_not_allow' );
				}

				break;
		}

		return $caps;
	}

	/** VGSR ************************************************************/

	/**
	 * Setup actions and filters for the VGSR plugin
	 *
	 * @since 1.0.0
	 */
	public function setup_vgsr_actions() {

		// Still hide it all for guest users, so bail
		if ( ! is_user_logged_in() )
			return;

		// Undo hiding BP for non-vgsr		
		remove_action( 'bp_init', array( vgsr()->extend->bp, 'hide_bp' ), 1 );
	}
}

/**
 * Setup the extension logic for BuddyPress
 *
 * @since 1.0.0
 *
 * @uses Paco2017_BuddyPress
 */
function paco2017_buddypress() {
	paco2017_content()->extend->bp = new Paco2017_BuddyPress;
}

endif; // class_exists
