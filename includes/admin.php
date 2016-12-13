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
		add_action( 'admin_menu',          array( $this, 'admin_menu'        )        );
		add_action( 'admin_init',          array( $this, 'register_settings' )        );
		add_filter( 'display_post_states', array( $this, 'post_states'       ), 10, 2 );
	}

	/** Public methods **************************************************/

	/**
	 * Register the admin menu pages
	 *
	 * @since 1.0.0
	 *
	 * @global array $submenu
	 */
	public function admin_menu() {

		// Define local variable
		$hooks = array( 'post-new.php', 'post.php' );

		// Main admin page
		add_menu_page(
			__( 'Paascongres 2017 Administration', 'paco2017-content' ),
			__( 'Paascongres', 'paco2017-content' ),
			'paco2017_admin_page',
			'paco2017',
			'paco2017_admin_page',
			'dashicons-megaphone',
			5
		);

		// Post type submenus
		foreach ( array( paco2017_get_lector_post_type(), paco2017_get_workshop_post_type() ) as $post_type ) {

			// Skip when post type does not exist
			if ( ! $post_type_object = get_post_type_object( $post_type ) )
				continue;

			$menu_file = "edit.php?post_type={$post_type}";

			// Remove the default admin menu and its submenus, to prevent
			// the `$parent_file` override in `get_admin_page_parent()`
			remove_menu_page( $menu_file );
			unset( $GLOBALS['submenu'][ $menu_file ] );

			// Add post type page as submenu
			$hooks[] = add_submenu_page(
				'paco2017',
				$post_type_object->label,
				$post_type_object->labels->menu_name,
				$post_type_object->cap->edit_posts,
				$menu_file
			);
		}

		// Settings page
		if ( paco2017_admin_page_has_settings( 'paco2017' ) ) {
			add_submenu_page(
				'paco2017',
				__( 'Paascongres 2017 Settings', 'paco2017-content' ),
				__( 'Settings', 'paco2017-content' ),
				'paco2017_admin_settings_page',
				'paco2017-settings',
				'paco2017_admin_page'
			);

			add_action( 'paco2017_admin_page-paco2017-settings', 'paco2017_admin_settings_page' );
		}

		foreach ( $hooks as $hook ) {
			add_action( "admin_head-{$hook}", array( $this, 'admin_menu_highlight' ) );
		}
	}

	/**
	 * Modify the highlighed menu for the current admin page
	 *
	 * @since 1.0.0
	 *
	 * @global string $parent_file
	 * @global string $submenu_file
	 * @global string $post_type
	 */
	public function admin_menu_highlight() {
		global $parent_file, $submenu_file, $post_type;

		// This tweaks the post type subnav menus to show the right top menu and submenu item.
		if ( in_array( $post_type, array( paco2017_get_lector_post_type(), paco2017_get_workshop_post_type() ) ) ) {
			$parent_file  = 'paco2017';
			$submenu_file = "edit.php?post_type={$post_type}";
		}
	}

	/**
	 * Register plugin settings
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {

		// Bail if no sections available
		$sections = paco2017_admin_get_settings_sections();
		if ( empty( $sections ) )
			return false;

		// Loop through sections
		foreach ( (array) $sections as $section_id => $section ) {

			// Only proceed if current user can see this section
			if ( ! current_user_can( $section_id ) )
				continue;

			// Only add section and fields if section has fields
			$fields = paco2017_admin_get_settings_fields_for_section( $section_id );
			if ( empty( $fields ) )
				continue;

			// Define section page
			if ( ! empty( $section['page'] ) ) {
				$page = $section['page'];
			} else {
				$page = 'paco2017';
			}

			// Add the section
			add_settings_section( $section_id, $section['title'], $section['callback'], $page );

			// Loop through fields for this section
			foreach ( (array) $fields as $field_id => $field ) {

				// Add the field
				if ( ! empty( $field['callback'] ) && ! empty( $field['title'] ) ) {
					add_settings_field( $field_id, $field['title'], $field['callback'], $page, $section_id, $field['args'] );
				}

				// Register the setting
				if ( ! empty( $field['sanitize_callback'] ) ) {
					register_setting( $page, $field_id, $field['sanitize_callback'] );
				}
			}
		}
	}

	/**
	 * Modify the list of post states
	 *
	 * @since 1.0.0
	 *
	 * @param array $states Post states
	 * @param WP_Post $post Post object
	 * @return array Post states
	 */
	public function post_states( $states, $post ) {


		// Mark the Houskeeping page
		if ( paco2017_get_housekeeping_page_id() === $post->ID ) {
			$states['housekeeping_page'] = __( 'Housekeeping', 'paco2017-content' );
		}

		return $states;
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
