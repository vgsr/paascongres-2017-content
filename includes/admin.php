<?php

/**
 * Paco2017 Content Admin Functions
 *
 * @package Paco2017 Content
 * @subpackage Administration
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Paco2017_Admin' ) ) :
/**
 * The Paco2017 Admin class
 *
 * @since 1.0.0
 */
class Paco2017_Admin {

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

	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/** Public methods **************************************************/

	/**
	 * Register the admin menu pages
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {
		global $submenu;

		// Define local variable
		$hooks = array( 'post-new.php' );

		// Main admin page
		$this->admin_page = add_menu_page( __( 'Paascongres 2017 Administration', 'paco2017-content' ), __( 'Paascongres', 'paco2017-content' ), 'manage_paco2017', 'paco2017', 'paco2017_admin_page', 'dashicons-megaphone', 5 );

		// Post type submenus
		foreach ( array( paco2017_get_lector_post_type(), paco2017_get_workshop_post_type() ) as $post_type ) {

			// Skip when post type does not exist
			if ( ! $post_type_object = get_post_type_object( $post_type ) )
				continue;

			$menu_file = "edit.php?post_type={$post_type}";

			// Remove the default admin menu
			remove_menu_page( $menu_file );
			unset( $submenu[ $menu_file ] ); // To prevent the `$parent_file` override in `get_admin_page_parent()`

			// Add post type page as submenu
			$hooks[] = add_submenu_page( 'paco2017', $post_type_object->label, $post_type_object->labels->menu_name, $post_type_object->cap->edit_posts, $menu_file );
		}

		// Settings page
		$this->settings_page = add_submenu_page( 'paco2017', __( 'Paascongres 2017 Settings', 'paco2017-content' ), __( 'Settings', 'paco2017-content' ), 'manage_paco2017', 'paco2017-settings', 'paco2017_admin_settings_page' );

		foreach ( $hooks as $hook ) {
			add_action( "admin_head-{$hook}", array( $this, 'admin_menu_highlight' ) );
		}
	}

	/**
	 * Modify the highlighed menu for the current admin page
	 *
	 * @since 1.0.0
	 */
	public function admin_menu_highlight() {
		global $parent_file, $submenu_file, $post_type;

		// This tweaks the post type subnav menus to show the right top menu and submenu item.
		if ( in_array( $post_type, array( paco2017_get_lector_post_type(), paco2017_get_workshop_post_type() ) ) ) {
			$parent_file  = 'paco2017';
			$submenu_file = "edit.php?post_type={$post_type}";
		}
	}
}

/**
 * Setup the extension logic for BuddyPress
 *
 * @since 1.0.0
 *
 * @uses Paco2017_Content_Admin
 */
function paco2017_admin() {
	paco2017_content()->admin = new Paco2017_Admin;
}

endif; // class_exists
