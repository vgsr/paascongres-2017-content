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
		add_filter( 'manage_users_columns',       array( $this, 'users_add_columns'   )        );
		add_filter( 'manage_users_custom_column', array( $this, 'users_custom_column' ), 10, 3 );
		add_action( 'pre_user_query',             array( $this, 'pre_user_query'      )        );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/** Public methods **************************************************/

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

		// users.php
		if ( 'users' === $screen->id ) {
			$css[] = ".fixed .column-paco2017_association { width: 10%; }";
		}

		// Appens styles
		if ( ! empty( $css ) ) {
			wp_add_inline_style( 'common', implode( "\n", $css ) );
		}
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

		// Association
		if ( paco2017_bp_xprofile_get_association_field() ) {
			$pos = array_search( 'role', array_keys( $columns ) );

			// Insert before the 'Role' column
			$columns = array_slice( $columns, 0, $pos ) + array(
				'paco2017_association' => esc_html__( 'Association', 'paco2017-content' )
			) + array_slice( $columns, $pos );
		}

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
			$assoc = paco2017_bp_xprofile_get_association_value( $user_id );

			if ( ! empty( $assoc ) ) {
				$url = add_query_arg( array( 'paco2017-association' => urlencode( $assoc ) ) );
				$content .= '<a href="' . esc_url( $url ) . '">' . esc_html( $assoc ) . '</a>';
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

			// Get the associated field
			if ( ! $field = paco2017_bp_xprofile_get_association_field() )
				return;

			// Setup profile query
			$profile_query = new BP_XProfile_Query( array(
				array(
					'field'   => $field->id,
					'value'   => urldecode( $_REQUEST['paco2017-association'] ),
					'compare' => '='
				)
			) );
			$profile_clauses = $profile_query->get_sql( $wpdb->users, 'ID' );

			// Append clauses
			$user_query->query_from  .= $profile_clauses['join'];
			$user_query->query_where .= $profile_clauses['where'];
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
