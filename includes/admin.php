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
		$this->setup_actions();
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

		// Dashboard
		add_action( 'paco2017_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );

		// Users.php
		add_filter( 'manage_users_columns',       array( $this, 'users_add_columns'   )        );
		add_filter( 'manage_users_custom_column', array( $this, 'users_custom_column' ), 10, 3 );
		add_action( 'pre_user_query',             array( $this, 'pre_user_query'      )        );
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

		// Dashboard admin page
		$dashboard = add_menu_page(
			__( 'Paascongres 2017 Dashboard', 'paco2017-content' ),
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

		// Manage Associations
		if ( $taxonomy = get_taxonomy( paco2017_get_association_tax_id() ) ) {

			// Add taxonomy page as submenu
			add_submenu_page(
				'paco2017',
				$taxonomy->labels->name,
				$taxonomy->labels->menu_name,
				$taxonomy->cap->manage_terms,
				"edit-tags.php?taxonomy={$taxonomy->name}&post_type=user"
			);

			// List menu pagesuffixes to highlight
			$hooks[] = 'edit-tags.php';
			$hooks[] = 'term.php';
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
		}

		// Register admin page hooks
		add_action( "load-{$dashboard}",                     'paco2017_admin_load_dashboard_page' );
		add_action( 'paco2017_admin_page-paco2017',          'paco2017_admin_dashboard_page'      );
		add_action( 'paco2017_admin_page-paco2017-settings', 'paco2017_admin_settings_page'       );

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
	 */
	public function admin_menu_highlight() {
		global $parent_file, $submenu_file;

		// Get the screen
		$screen = get_current_screen();

		// This tweaks the post type subnav menus to show the right top menu and submenu item.
		if ( in_array( $screen->post_type, array( paco2017_get_lector_post_type(), paco2017_get_workshop_post_type() ) ) ) {
			$parent_file  = 'paco2017';
			$submenu_file = "edit.php?post_type={$screen->post_type}";
		}

		// This tweaks the taxonomy subnav menus to show the right top menu and submenu item.
		if ( in_array( $screen->taxonomy, array( paco2017_get_association_tax_id() ) ) ) {
			$parent_file  = 'paco2017';
			$submenu_file = "edit-tags.php?taxonomy={$screen->taxonomy}&post_type=user";
		}
	}

	/**
	 * Add plugin admin dashboard widgets
	 *
	 * @since 1.0.0
	 */
	public function add_dashboard_widgets() {
		wp_add_dashboard_widget( 'paco2107_status', __( 'At a Glance' ), 'paco2017_dashboard_status' );
		wp_add_dashboard_widget( 'paco2017_enrollments', __( 'Enrollments', 'paco2017-content' ), 'paco2017_dashboard_enrollments' );
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

	/**
	 * Modify the list of columns in the users list table
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Columns
	 * @return array Columns
	 */
	public function users_add_columns( $columns ) {

		// Put user Association before the Role column
		$pos = array_search( 'role', array_keys( $columns ) );

		// Insert before the 'Role' column
		$columns = array_slice( $columns, 0, $pos ) + array(
			'paco2017_association' => esc_html__( 'Association', 'paco2017-content' )
		) + array_slice( $columns, $pos );

		return $columns;
	}

	/**
	 * Output content of the users list table columns
	 *
	 * @since 1.0.0
	 *
	 * @param string $column Column name
	 * @param int $user_id User ID
	 * @return string Column content
	 */
	public function users_custom_column( $content, $column, $user_id ) {

		// Association
		if ( 'paco2017_association' === $column ) {
			$association = wp_get_object_terms( $user_id, paco2017_get_association_tax_id() );

			if ( ! empty( $association ) ) {
				$url = add_query_arg( array( 'paco2017-association' => urlencode( $association[0]->term_id ) ) );
				$content .= '<a href="' . esc_url( $url ) . '">' . $association[0]->name . '</a>';
			} else {
				$content = '&mdash;';
			}
		}

		return $content;
	}

	/**
	 * Modify the admin's user query
	 *
	 * @since 1.0.0
	 *
	 * @global WPDB   $wpdb
	 * @global string $pagenow
	 *
	 * @param WP_User_Query $user_query
	 */
	public function pre_user_query( $user_query ) {
		global $wpdb, $pagenow;

		// Filter by Association
		if ( is_admin() && 'users.php' === $pagenow && ! empty( $_REQUEST['paco2017-association'] ) ) {

			// Setup profile query
			$tax_query = new WP_Tax_Query( array(
				array(
					'taxonomy' => paco2017_get_association_tax_id(),
					'terms'    => array( urldecode( $_REQUEST['paco2017-association'] ) ),
				)
			) );
			$tax_clauses = $tax_query->get_sql( $wpdb->users, 'ID' );

			// Append clauses
			$user_query->query_from  .= $tax_clauses['join'];
			$user_query->query_where .= $tax_clauses['where'];
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
