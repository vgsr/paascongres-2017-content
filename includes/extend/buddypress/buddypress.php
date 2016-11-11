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
	}

	/** Public methods **************************************************/

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
