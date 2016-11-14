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

		// General
		add_filter( 'bp_map_meta_caps', array( $this, 'map_meta_cap' ), 20, 4 );

		// VGSR
		add_action( 'vgsr_loaded', array( $this, 'setup_vgsr_actions' ) );
	}

	/** Public methods **************************************************/

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

		// Get VGSR
		$vgsr = vgsr();

		// Undo hiding BP for non-vgsr		
		remove_action( 'bp_init', array( $vgsr->extend->bp, 'hide_bp' ), 1 );
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
