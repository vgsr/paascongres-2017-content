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
		$this->includes();
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

		// Assets
		$this->assets_dir = trailingslashit( $this->base_dir . 'assets' );
		$this->assets_url = trailingslashit( $this->base_url . 'assets' );

		// Themes
		$this->themes_dir = trailingslashit( $this->base_dir . 'templates' );
		$this->themes_url = trailingslashit( $this->base_url . 'templates' );
	}

	/**
	 * Include the required files
	 *
	 * @since 1.0.0
	 */
	private function includes() {
		require( $this->base_dir . 'actions.php'  );
		require( $this->base_dir . 'activity.php' );
		require( $this->base_dir . 'members.php'  );
		require( $this->base_dir . 'settings.php' );
		require( $this->base_dir . 'xprofile.php' );

		// Admin
		if ( is_admin() ) {
			require( $this->base_dir . 'admin.php' );
		}
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {

		// For guests, hide BuddyPress entirely
		if ( ! get_current_user_id() ) {
			add_filter( 'bp_active_components', '__return_empty_array', 99 );
			return;
		}

		/**
		 * Ideas
		 * - Front page met customizable content
		 */

		// Register plugin template directory
		bp_register_template_stack( function() {
			return $this->themes_dir;
		}, 8 );

		// General limitations
		add_action( 'bp_init',               array( $this, 'hide_components_parts' ),  5    );
		add_action( 'bp_enqueue_scripts',    array( $this, 'enqueue_scripts'       ), 90    );
		add_filter( 'bp_map_meta_caps',      array( $this, 'map_meta_cap'          ), 20, 4 );
		add_filter( 'bp_get_the_body_class', array( $this, 'body_class'            ), 10, 4 );

		// Unhide BuddyPress from VGSR
		if ( function_exists( 'vgsr' ) ) {
			$vgsr_bp = vgsr()->extend->bp;

			// Unhook some VGSR logic
			remove_action( 'bp_core_loaded',                    array( $vgsr_bp, 'hide_buddypress'            ), 20 );
			remove_action( 'bp_members_directory_member_types', array( $vgsr_bp, 'add_members_directory_tabs' )     );
		}
	}

	/** Public methods **************************************************/

	/**
	 * Prevent the user from being exposed to certain component parts
	 *
	 * @since 1.0.0
	 */
	public function hide_components_parts() {

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
			 * element. See for example {@see bp_nav_menu_get_loggedin_pages()}.
			 *
			 * // This does not what we want
			 * $components['xprofile'][] = 'setup_nav';
			 *
			 * Instead, the member navigation items are edited to not show for the
			 * displayed user, while still applying for the current user.
			 */
			add_action( 'bp_setup_nav', array( $this, 'hide_components_nav' ), 99 );
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
	 * Prevent the user from being exposed to certain component navs.
	 *
	 * This runs after BP has registered all nav and admin nav items.
	 *
	 * @since 1.0.0
	 */
	public function hide_components_nav() {

		// Get BuddyPress
		$bp = buddypress();

		// Collect nav items
		$items = array();

		// Profile component
		if ( bp_is_active( 'xprofile' ) ) {

			// Members: hide Profile nav tabs on another member's profile
			$items[] = $bp->members->nav->edit_nav( array( 'show_for_displayed_user' => false ), bp_get_profile_slug() );
			$items  += $bp->members->nav->get_secondary( array( 'parent_slug' => bp_get_profile_slug() ), false );
		}

		// Unhook screen functions of the edited navs
		foreach ( $items as $item ) {
			if ( isset( $item->screen_function ) && is_callable( $item->screen_function ) ) {
				remove_action( 'bp_screens', $item->screen_function, 3 );
			}
		}
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'paco2017-buddypress', $this->assets_url . 'css/paco2017-buddypress.css' );

		// Define customs styles
		$css = array();

		// Association styles
		foreach ( get_terms( array(
			'taxonomy' => paco2017_get_association_tax_id(),
		) ) as $term ) {

			// Colors
			if ( $color = get_term_meta( $term->term_id, 'color', true ) ) {

				// Turn hex to rgb
				$_color    = sanitize_hex_color_no_hash( $color );
				$rgb       = array( hexdec( substr( $_color, 0, 2 ) ), hexdec( substr( $_color, 2, 2 ) ), hexdec( substr( $_color, 4, 2 ) ) );

				// Dark text on light backgrounds
				$textcolor = ( $rgb[0] > 200 || $rgb[1] > 200 || $rgb[2] > 200 ) ? 'color: inherit' : '';

				// Define badge label and color
				$css[] = ".paco2017-association-{$term->term_id} .paco2017-association-badge:before { content: '{$term->name}'; }";
				$css[] = ".paco2017-association-{$term->term_id} .paco2017-association-badge { border-color: {$color}; background-color: rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.6); {$textcolor} }";
			}
		}

		// Append styles
		if ( ! empty( $css ) ) {
			wp_add_inline_style( 'paco2017-buddypress', implode( "\n", $css ) );
		}

		/** Companion Styles ********************************************/

		$template  = get_template();
		$companion = $this->assets_url . "css/paco2017-bp-{$template}.css";

		if ( wp_style_is( "bp-{$template}" ) && file_exists( $companion ) ) {
			wp_enqueue_style( "paco2017-bp-{$template}", $companion, array( "bp-{$template}" ) );
		}
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

	/**
	 * Modify BP's body classes
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Array of body classes
	 * @param array $bp_classes Array of BP classes
	 * @param array $wp_classes Array of WP classes
	 * @param array $custom_classes Array of custom classes
	 * @return array Body classes
	 */
	public function body_class( $classes, $bp_classes, $wp_classes, $custom_classes ) {

		// Single member
		if ( bp_is_user() ) {

			// Add displayed user's association class
			if ( $association = paco2017_bp_xprofile_get_association_value( bp_displayed_user_id() ) ) {
				$classes[] = 'paco2017-association-' . $association;
			}
		}

		return $classes;
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
