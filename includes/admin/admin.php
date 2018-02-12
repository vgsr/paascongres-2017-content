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
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Setup default class globals
	 *
	 * @since 1.1.0
	 */
	private function setup_globals() {

		/** Paths *******************************************************/

		// Includes
		$this->admin_dir = trailingslashit( paco2017_content()->includes_dir . 'admin' );
		$this->admin_url = trailingslashit( paco2017_content()->includes_url . 'admin' );
	}

	/**
	 * Include the required files
	 *
	 * @since 1.1.0
	 */
	private function includes() {
		require( $this->admin_dir . 'accounts.php'  );
		require( $this->admin_dir . 'dashboard.php' );
		require( $this->admin_dir . 'functions.php' );
		require( $this->admin_dir . 'settings.php'  );
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {
		$association = paco2017_get_association_tax_id();
		$assoc_scrn  = "edit-{$association}";

		// Core
		add_action( 'admin_page_access_denied', array( $this, 'redirect_requests' )        );
		add_action( 'admin_init',               array( $this, 'register_settings' )        );
		add_action( 'admin_menu',               array( $this, 'admin_menu'        )        );
		add_action( 'admin_head',               array( $this, 'admin_head'        )        );
		add_filter( 'map_meta_cap',             array( $this, 'map_meta_caps'     ), 10, 4 );
		add_action( 'admin_enqueue_scripts',    array( $this, 'enqueue_scripts'   )        );

		// Posts
		add_filter( 'display_post_states',         array( $this, 'post_states'           ), 10, 2 );
		add_action( 'manage_posts_extra_tablenav', array( $this, 'manage_posts_tablenav' )        );
		add_filter( 'manage_posts_columns',        array( $this, 'posts_add_columns'     ), 10, 2 );
		add_filter( 'manage_posts_custom_column',  array( $this, 'posts_custom_column'   ), 10, 2 );
		add_action( 'add_meta_boxes',              array( $this, 'add_meta_boxes'        ), 10, 2 );
		add_action( 'save_post',                   array( $this, 'lecture_save_metabox'  ), 10, 2 );
		add_action( 'save_post',                   array( $this, 'workshop_save_metabox' ), 10, 2 );
		add_action( 'save_post',                   array( $this, 'agenda_save_metabox'   ), 10, 2 );
		add_action( 'save_post',                   array( $this, 'partner_save_metabox'  ), 10, 2 );

		// Terms
		add_filter( "manage_{$assoc_scrn}_columns",          array( $this, 'terms_add_columns'         )        );
		add_filter( "manage_{$assoc_scrn}_sortable_columns", array( $this, 'terms_sortable_columns'    )        );
		add_filter( "manage_{$association}_custom_column",   array( $this, 'terms_custom_column'       ), 11, 3 );
		add_filter( "bulk_actions-{$assoc_scrn}",            array( $this, 'terms_bulk_actions'        )        );
		add_filter( "handle_bulk_actions-{$assoc_scrn}",     array( $this, 'terms_handle_bulk_actions' ), 10, 3 );
		add_filter( "{$association}_row_actions",            array( $this, 'terms_row_actions'         ), 10, 2 );

		// Nav Menus
		add_action( 'load-nav-menus.php', array( $this, 'add_nav_menu_meta_box' ) );

		// Dashboard
		add_action( 'paco2017_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );

		// Users.php
		add_filter( 'manage_users_columns',       array( $this, 'users_add_columns'   )        );
		add_filter( 'manage_users_custom_column', array( $this, 'users_custom_column' ), 10, 3 );
		add_action( 'pre_user_query',             array( $this, 'pre_user_query'      )        );

		// AJAX
		add_action( 'wp_ajax_paco2017-delete-association-users', 'paco2017_admin_ajax_delete_association_users' );

		// Updater
		add_action( 'admin_init', 'paco2017_setup_updater', 999 );
	}

	/** Public methods **************************************************/

	/**
	 * Return whether we're not on a plugin admin page
	 *
	 * @since 1.1.0
	 *
	 * @return bool Is this not a plugin admin page?
	 */
	public function bail() {
		return ! ( is_admin() && 'paco2017' === get_current_screen()->parent_file );
	}

	/**
	 * Redirect specific admin requests
	 *
	 * @since 1.1.0
	 */
	public function redirect_requests() {
		$location = '';

		// Accounts page
		if ( paco2017_admin_is_current_page( 'paco2017-accounts' ) ) {

			// Redirect to plugin home page
			$location = add_query_arg( array( 'page' => 'paco2017' ), 'admin.php' );
		}

		// Catch location
		if ( $location ) {
			wp_safe_redirect( $location );
			exit;
		}
	}

	/**
	 * Register the admin menu pages
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {

		// Dashboard admin page
		$dashboard = add_menu_page(
			esc_html__( 'Paascongres Dashboard', 'paco2017-content' ),
			esc_html__( 'Paascongres', 'paco2017-content' ),
			'paco2017_admin_page',
			'paco2017',
			'paco2017_admin_page',
			'dashicons-megaphone',
			4
		);

		// Manage post types and taxonomies
		paco2017_admin_submenu_post_type( paco2017_get_lecture_post_type() );
		paco2017_admin_submenu_post_type( paco2017_get_workshop_post_type() );
		paco2017_admin_submenu_taxonomy( paco2017_get_speaker_tax_id() );
		paco2017_admin_submenu_post_type( paco2017_get_agenda_post_type() );
		paco2017_admin_submenu_taxonomy( paco2017_get_conf_location_tax_id() );
		paco2017_admin_submenu_post_type( paco2017_get_partner_post_type() );
		paco2017_admin_submenu_taxonomy( paco2017_get_association_tax_id(), sprintf( "edit-tags.php?taxonomy=%s&post_type=user", paco2017_get_association_tax_id() ) );

		// Settings page
		if ( paco2017_admin_page_has_settings( 'paco2017' ) ) {
			add_submenu_page(
				'paco2017',
				esc_html__( 'Paascongres Settings', 'paco2017-content' ),
				esc_html__( 'Settings', 'paco2017-content' ),
				'paco2017_admin_settings_page',
				'paco2017-settings',
				'paco2017_admin_page'
			);
		}

		// Partner settings page
		if ( paco2017_admin_page_has_settings( 'paco2017-partners' ) ) {
			add_submenu_page(
				'paco2017',
				esc_html__( 'Paascongres Partner Settings', 'paco2017-content' ),
				esc_html__( 'Partner Settings', 'paco2017-content' ),
				'paco2017_admin_partners_page',
				'paco2017-partners',
				'paco2017_admin_page'
			);
		}

		// Accounts settings page
		if ( current_user_can( 'paco2017_manage_accounts' ) ) {
			$accounts = add_submenu_page(
				'paco2017',
				esc_html__( 'Paascongres Accounts Settings', 'paco2017-content' ),
				esc_html__( 'Accounts Settings', 'paco2017-content' ),
				'paco2017_admin_accounts_page',
				'paco2017-accounts',
				'paco2017_admin_page'
			);
		}

		// Register admin page hooks
		add_action( "load-{$dashboard}",                     'paco2017_admin_load_dashboard_page' );
		add_action( "load-{$accounts}",                      'paco2017_admin_load_accounts_page'  );
		add_action( 'current_screen',                        'paco2017_admin_menu_highlight'      ); // Modify current screen data
		add_action( 'admin_head',                            'paco2017_admin_menu_highlight'      ); // Modify admin menu pointers
		add_action( 'paco2017_admin_page-paco2017',          'paco2017_admin_dashboard_page'      );
		add_action( 'paco2017_admin_page-paco2017-settings', 'paco2017_admin_settings_page'       );
		add_action( 'paco2017_admin_page-paco2017-partners', 'paco2017_admin_settings_page'       );
		add_action( 'paco2017_admin_page-paco2017-accounts', 'paco2017_admin_accounts_page'       );
	}

	/**
	 * Remove admin menu items
	 *
	 * @since 1.1.0
	 */
	public function admin_head() {
		remove_submenu_page( 'paco2017', 'paco2017-partners' );
		remove_submenu_page( 'paco2017', 'paco2017-accounts' );
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		// Bail when not on a plugin admin page
		if ( $this->bail() )
			return;

		wp_enqueue_script( 'paco2017-admin', paco2017_content()->assets_url . 'js/admin.js', array( 'common' ) );
		wp_enqueue_style( 'paco2017-admin', paco2017_content()->assets_url . 'css/admin.css', array( 'common' ) );

		/** Custom scripts ********************************************************/

		wp_localize_script( 'paco2017-admin', 'paco2017Admin', array(
			'l10n' => array(
				'aysDeleteAssociationUsers' => esc_html__( 'Caution: these accounts may be active on your site. Are you sure you want to delete the selected user accounts? This action is irreversible.', 'paco2017-content' )
			)
		) );

		/** Custom styles *********************************************************/

		// Define additional custom styles
		$css = array();

		// List columns
		$css[] = ".fixed .column-taxonomy-" . paco2017_get_association_tax_id()   .
		       ", .fixed .column-taxonomy-" . paco2017_get_speaker_tax_id() .
		       ", .fixed .column-taxonomy-" . paco2017_get_workshop_cat_tax_id() .
		       ", .fixed .column-taxonomy-" . paco2017_get_workshop_round_tax_id() .
		       ", .fixed .column-taxonomy-" . paco2017_get_conf_day_tax_id() .
		       ", .fixed .column-taxonomy-" . paco2017_get_conf_location_tax_id() .
		       ", .fixed .column-taxonomy-" . paco2017_get_partner_level_tax_id() . " { width: 10%; }";

		/** Associations **********************************************************/

		foreach ( get_terms( array(
			'taxonomy'   => paco2017_get_association_tax_id(),
			'hide_empty' => false
		) ) as $term ) {

			// Colors
			if ( $color = get_term_meta( $term->term_id, 'color', true ) ) {

				// Turn hex to rgb
				$_color    = sanitize_hex_color_no_hash( $color );
				$rgb       = array( hexdec( substr( $_color, 0, 2 ) ), hexdec( substr( $_color, 2, 2 ) ), hexdec( substr( $_color, 4, 2 ) ) );

				$css[] = ".paco2017_enrollments_widget .paco2017-association-{$term->term_id}, .paco2017_enrollments_widget .paco2017-association-{$term->term_id} + dd { border-bottom: 4px solid rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, .6); }";
			}
		}

		if ( ! empty( $css ) ) {
			wp_add_inline_style( 'paco2017-admin', implode( "\n", $css ) );
		}
	}

	/**
	 * Add plugin admin dashboard widgets
	 *
	 * @since 1.0.0
	 */
	public function add_dashboard_widgets() {
		wp_add_dashboard_widget( 'paco2017_dashboard_status', esc_html__( 'At a Glance' ), 'paco2017_dashboard_status' );
		wp_add_dashboard_widget( 'paco2017_dashboard_enrollments', esc_html__( 'Enrollments', 'paco2017-content' ), 'paco2017_dashboard_enrollments' );
		wp_add_dashboard_widget( 'dashboard_activity', esc_html__( 'Activity' ), 'wp_dashboard_site_activity' );
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

		/**
		 * Register Magazine Download media settings field.
		 *
		 * This should be registered (somewhat) early/everywhere, since
		 * its filters should also run in `admin_init` for admin-ajax.php.
		 */
		wp_setting_media( paco2017_content()->file, '_paco2017_magazine_download', array(
			'mime_type' => 'application/pdf',
			'labels'    => array(
				'setSettingMedia'    => esc_html__( 'Set magazine download file', 'paco2017-content' ),
				'settingMediaTitle'  => esc_html__( 'Magazine Download File', 'paco2017-content' ),
				'removeSettingMedia' => esc_html__( 'Remove magazine download file', 'paco2017-content' ),
			)
		) );
	}

	/**
	 * Modify the mapped caps for the meta capability
	 *
	 * @since 1.0.0
	 *
	 * @param array $caps Mapped caps
	 * @param string $cap Required meta capability
	 * @param int $user_id User ID
	 * @param array $args Additional arguments
	 * @return array Mapped caps
	 */
	public function map_meta_caps( $caps, $cap, $user_id = 0, $args = array() ) {

		switch ( $cap ) {

			// Admin pages
			case 'paco2017_admin_page' :
			case 'paco2017_admin_settings_page' :
			case 'paco2017_admin_partners_page' :
				break;

			// Accounts page
			case 'paco2017_manage_accounts' :
				$caps = array( 'delete_users' );
				break;
		}

		return $caps;
	}

	/** Posts ***********************************************************/

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

		// Mark the General Notices page
		if ( paco2017_get_general_notices_page_id() === $post->ID ) {
			$states['general_notices_page'] = esc_html__( 'General Notices', 'paco2017-content' );
		}

		return $states;
	}

	/**
	 * Output posts list management helper tools
	 *
	 * @since 1.0.0
	 *
	 * @param string $which Top or bottom
	 */
	public function manage_posts_tablenav( $which ) {

		// Bail when this is not the top tablenav
		if ( 'top' !== $which )
			return;

		switch ( get_current_screen()->post_type ) {

			// Workshop
			case paco2017_get_workshop_post_type() :

				// Display link to manage categories
				printf( '<div class="alignleft actions paco2017-workshop-cat-link"><a href="%s" class="page-title-action">%s</a></div>', 'edit-tags.php?taxonomy=' . paco2017_get_workshop_cat_tax_id(), esc_html__( 'Manage Workshop Categories', 'paco2017-content' ) );

				// Display link to manage rounds
				printf( '<div class="alignleft actions paco2017-workshop-round-link"><a href="%s" class="page-title-action">%s</a></div>', 'edit-tags.php?taxonomy=' . paco2017_get_workshop_round_tax_id(), esc_html__( 'Manage Workshop Rounds', 'paco2017-content' ) );

				break;

			// Agenda
			case paco2017_get_agenda_post_type() :

				// Display link to manage days
				printf( '<div class="alignleft actions paco2017-conf-day-link"><a href="%s" class="page-title-action">%s</a></div>', 'edit-tags.php?taxonomy=' . paco2017_get_conf_day_tax_id(), esc_html__( 'Manage Conference Days', 'paco2017-content' ) );

				break;

			// Partner
			case paco2017_get_partner_post_type() :

				// Display link to manage levels
				printf( '<div class="alignleft actions paco2017-partner-level-link"><a href="%s" class="page-title-action">%s</a></div>', 'edit-tags.php?taxonomy=' . paco2017_get_partner_level_tax_id(), esc_html__( 'Manage Partner Levels', 'paco2017-content' ) );

				break;
		}
	}

	/**
	 * Modify the list of columns in the posts list table
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Columns
	 * @param string $post_type Post type name
	 * @return array Columns
	 */
	public function posts_add_columns( $columns, $post_type ) {

		// Rename Speaker column
		$tax_key = 'taxonomy-' . paco2017_get_speaker_tax_id();
		if ( isset( $columns[ $tax_key ] ) ) {
			$columns[ $tax_key ] = esc_html__( 'Speaker', 'paco2017-content' );
		}

		// Rename Wokshop Category column
		$tax_key = 'taxonomy-' . paco2017_get_workshop_cat_tax_id();
		if ( isset( $columns[ $tax_key ] ) ) {
			$columns[ $tax_key ] = esc_html__( 'Category', 'paco2017-content' );
		}

		// Rename Wokshop Round column
		$tax_key = 'taxonomy-' . paco2017_get_workshop_round_tax_id();
		if ( isset( $columns[ $tax_key ] ) ) {
			$columns[ $tax_key ] = esc_html__( 'Round', 'paco2017-content' );
		}

		// Rename Conference Day column
		$tax_key = 'taxonomy-' . paco2017_get_conf_day_tax_id();
		if ( isset( $columns[ $tax_key ] ) ) {
			$columns[ $tax_key ] = esc_html__( 'Day', 'paco2017-content' );
		}

		// Rename Conference Location column
		$tax_key = 'taxonomy-' . paco2017_get_conf_location_tax_id();
		if ( isset( $columns[ $tax_key ] ) ) {
			$columns[ $tax_key ] = esc_html__( 'Location', 'paco2017-content' );
		}

		// Rename Partner Level column
		$tax_key = 'taxonomy-' . paco2017_get_partner_level_tax_id();
		if ( isset( $columns[ $tax_key ] ) ) {
			$columns[ $tax_key ] = esc_html__( 'Level', 'paco2017-content' );
		}

		// Workshop
		if ( paco2017_get_workshop_post_type() === $post_type ) {

			// Append Attendees
			$loc_pos = array_search( 'taxonomy-' . paco2017_get_conf_location_tax_id(), array_keys( $columns ) );
			if ( $loc_pos ) {
				$columns = array_slice( $columns, 0, $loc_pos + 1 ) + array(
					'attendees' => esc_html_x( 'Attendees', 'admin column name', 'paco2017-content' ),
				) + array_slice( $columns, $loc_pos + 1 );
			}
		}

		// Agenda
		if ( paco2017_get_agenda_post_type() === $post_type ) {

			// Append Time Start and Time End
			$day_pos = array_search( 'taxonomy-' . paco2017_get_conf_day_tax_id(), array_keys( $columns ) );
			if ( $day_pos ) {
				$columns = array_slice( $columns, 0, $day_pos + 1 ) + array(
					'time_start' => esc_html_x( 'Start', 'admin column name', 'paco2017-content' ),
					'time_end'   => esc_html_x( 'End',   'admin column name', 'paco2017-content' ),
				) + array_slice( $columns, $day_pos + 1 );
			}

			// Append related
			$loc_pos = array_search( 'taxonomy-' . paco2017_get_conf_location_tax_id(), array_keys( $columns ) );
			if ( $loc_pos ) {
				$columns = array_slice( $columns, 0, $loc_pos + 1 ) + array(
					'related'    => esc_html_x( 'Related', 'admin column name', 'paco2017-content' ),
				) + array_slice( $columns, $loc_pos + 1 );
			}
		}

		// Partner
		if ( paco2017_get_partner_post_type() === $post_type ) {

			// Append Partner URL and Logo
			$title_pos = array_search( 'title', array_keys( $columns ) );
			if ( $title_pos ) {

				// Insert before 'Title'
				$columns = array_slice( $columns, 0, $title_pos ) + array(
					'logo' => esc_html_x( 'Logo', 'admin column name', 'paco2017-content' ),
				) + array_slice( $columns, $title_pos );

				// Insert after 'Title'
				$columns = array_slice( $columns, 0, $title_pos + 2 ) + array(
					'partner_url' => esc_html_x( 'URL', 'admin column name', 'paco2017-content' ),
				) + array_slice( $columns, $title_pos + 2 );
			}
		}

		return $columns;
	}

	/**
	 * Output content of the posts list table columns
	 *
	 * @since 1.0.0
	 *
	 * @param string $column Column name
	 * @param int $post_id Post ID
	 */
	public function posts_custom_column( $column, $post_id ) {

		$post_type = get_post_type( $post_id );

		switch ( $post_type ) {

			// Workshop
			case paco2017_get_workshop_post_type() :
				switch ( $column ) {
					case 'attendees' :
						echo paco2017_get_workshop_enrolled_user_count( $post_id );

						// Display limit
						if ( $limit = paco2017_get_workshop_limit( $post_id ) ) {
							echo ' / ' . $limit;
						}
						break;
				}

				break;

			// Agenda Item
			case paco2017_get_agenda_post_type() :
				switch ( $column ) {
					case 'time_start' :
					case 'time_end' :
						$this->posts_custom_meta_column( $column, $post_id );
						break;
					case 'related' :
						echo paco2017_is_agenda_related( $post_id ) ? paco2017_get_agenda_related_link( $post_id ) : '&mdash;';
						break;
				}

				break;

			// Partner
			case paco2017_get_partner_post_type() :
				switch ( $column ) {
					case 'logo' :
						if ( $logo_id = paco2017_get_partner_logo_id( $post_id ) ) {
							echo wp_get_attachment_image( $logo_id, array( 38, 38 ) );
						}
						break;
					case 'partner_url' :
						$this->posts_custom_meta_column( $column, $post_id );
						break;
				}

				break;
		}
	}

	/**
	 * Output the default admin meta column content
	 *
	 * @since 1.1.0
	 *
	 * @param string $column Column name
	 * @param int $post_id Post ID
	 */
	public function posts_custom_meta_column( $column, $post_id ) {
		$meta = get_post_meta( $post_id, $column, true );
		echo ( ! empty( $meta ) ) ? $meta : '&mdash;';
	}

	/**
	 * Modify the post's metaboxes
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type Post type name
	 * @param WP_Post $post Current post object
	 */
	public function add_meta_boxes( $post_type, $post ) {

		// Lecture
		if ( paco2017_get_lecture_post_type() === $post_type ) {
			add_meta_box(
				'paco2017_lecture_details',
				esc_html__( 'Lecture Details', 'paco2017-content' ),
				'paco2017_admin_lecture_details_metabox',
				null,
				'side',
				'high'
			);
		}

		// Workshop
		if ( paco2017_get_workshop_post_type() === $post_type ) {
			add_meta_box(
				'paco2017_workshop_details',
				esc_html__( 'Workshop Details', 'paco2017-content' ),
				'paco2017_admin_workshop_details_metabox',
				null,
				'side',
				'high'
			);
		}

		// Agenda Item
		if ( paco2017_get_agenda_post_type() === $post_type ) {
			add_meta_box(
				'paco2017_agenda_details',
				esc_html__( 'Agenda Details', 'paco2017-content' ),
				'paco2017_admin_agenda_details_metabox',
				null,
				'side',
				'high'
			);
		}

		// Partner
		if ( paco2017_get_partner_post_type() === $post_type ) {
			add_meta_box(
				'paco2017_partner_details',
				esc_html__( 'Partner Details', 'paco2017-content' ),
				'paco2017_admin_partner_details_metabox',
				null,
				'side',
				'high'
			);
		}
	}

	/**
	 * Save when the Lecture's metabox is submitted
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 * @param WP_Post $post Post object
	 */
	public function lecture_save_metabox( $post_id, $post = 0 ) {

		// Bail when doing an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// Bail when not a post request
		if ( 'POST' != strtoupper( $_SERVER['REQUEST_METHOD'] ) )
			return;

		// Bail when nonce does not verify
		if ( empty( $_POST['lecture_details_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['lecture_details_metabox_nonce'], 'lecture_details_metabox' ) )
			return;

		// Get post type object
		$post_type_object = get_post_type_object( $post->post_type );

		// Bail when current user is not capable
		if ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) )
			return;

		/**
		 * Save posted inputs:
		 * - Speaker taxonomy
		 */

		foreach ( array(
			paco2017_get_speaker_tax_id(),
		) as $taxonomy ) {
			$_taxonomy = get_taxonomy( $taxonomy );

			if ( ! $_taxonomy || ! current_user_can( $_taxonomy->cap->assign_terms ) )
				continue;

			// Set taxonomy term
			if ( isset( $_POST["taxonomy-{$taxonomy}"] ) ) {
				wp_set_object_terms( $post_id, (int) $_POST["taxonomy-{$taxonomy}"], $taxonomy, false );

			// Remove taxonomy term
			} elseif ( $terms = wp_get_object_terms( $post_id ) ) {
				wp_remove_object_terms( $post_id, $terms, $taxonomy );
			}
		}
	}

	/**
	 * Save when the Workshop's metabox is submitted
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 * @param WP_Post $post Post object
	 */
	public function workshop_save_metabox( $post_id, $post = 0 ) {

		// Bail when doing an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// Bail when not a post request
		if ( 'POST' != strtoupper( $_SERVER['REQUEST_METHOD'] ) )
			return;

		// Bail when nonce does not verify
		if ( empty( $_POST['workshop_details_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['workshop_details_metabox_nonce'], 'workshop_details_metabox' ) )
			return;

		// Get post type object
		$post_type_object = get_post_type_object( $post->post_type );

		// Bail when current user is not capable
		if ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) )
			return;

		/**
		 * Save posted inputs:
		 * - Workshop Round taxonomy
		 * - Speaker taxonomy
		 * - Workshop Category taxonomy
		 * - Conference Location taxonomy
		 * - Workshop limit meta
		 */

		foreach ( array(
			paco2017_get_workshop_round_tax_id(),
			paco2017_get_speaker_tax_id(),
			paco2017_get_workshop_cat_tax_id(),
			paco2017_get_conf_location_tax_id(),
		) as $taxonomy ) {
			$_taxonomy = get_taxonomy( $taxonomy );

			if ( ! $_taxonomy || ! current_user_can( $_taxonomy->cap->assign_terms ) )
				continue;

			// Set taxonomy term
			if ( isset( $_POST["taxonomy-{$taxonomy}"] ) ) {
				wp_set_object_terms( $post_id, (int) $_POST["taxonomy-{$taxonomy}"], $taxonomy, false );

			// Remove taxonomy term
			} elseif ( $terms = wp_get_object_terms( $post_id ) ) {
				wp_remove_object_terms( $post_id, $terms, $taxonomy );
			}
		}

		// Meta
		foreach ( array(
			'workshop_limit' => 'limit',
		) as $posted_key => $meta ) {
			if ( isset( $_POST[ $posted_key ] ) ) {
				update_post_meta( $post_id, $meta, $_POST[ $posted_key ] );
			}
		}
	}

	/**
	 * Save when the Agenda Item's metabox is submitted
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 * @param WP_Post $post Post object
	 */
	public function agenda_save_metabox( $post_id, $post = 0 ) {

		// Bail when doing an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// Bail when not a post request
		if ( 'POST' != strtoupper( $_SERVER['REQUEST_METHOD'] ) )
			return;

		// Bail when nonce does not verify
		if ( empty( $_POST['agenda_details_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['agenda_details_metabox_nonce'], 'agenda_details_metabox' ) )
			return;

		// Get post type object
		$post_type_object = get_post_type_object( $post->post_type );

		// Bail when current user is not capable
		if ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) )
			return;

		/**
		 * Save posted inputs:
		 * - Conference Day taxonomy
		 * - Conference Location taxonomy
		 * - Time Start meta
		 * - Time End meta
		 * - Related
		 */

		foreach ( array(
			paco2017_get_conf_day_tax_id(),
			paco2017_get_conf_location_tax_id(),
		) as $taxonomy ) {
			$_taxonomy = get_taxonomy( $taxonomy );

			if ( ! $_taxonomy || ! current_user_can( $_taxonomy->cap->assign_terms ) )
				continue;

			// Set taxonomy term
			if ( isset( $_POST["taxonomy-{$taxonomy}"] ) ) {
				wp_set_object_terms( $post_id, (int) $_POST["taxonomy-{$taxonomy}"], $taxonomy, false );

			// Remove taxonomy term
			} elseif ( $terms = wp_get_object_terms( $post_id ) ) {
				wp_remove_object_terms( $post_id, $terms, $taxonomy );
			}
		}

		// Time Start & End
		foreach ( array( 'time_start', 'time_end' ) as $time_meta ) {

			$hours = isset( $_POST["agenda_{$time_meta}_hours"] ) ? (int) $_POST["agenda_{$time_meta}_hours"] : 0;
			$mins  = isset( $_POST["agenda_{$time_meta}_mins"]  ) ? (int) $_POST["agenda_{$time_meta}_mins"]  : 0;
			$hours = str_pad( $hours, 2, '0', STR_PAD_LEFT );
			$mins  = str_pad( $mins,  2, '0', STR_PAD_LEFT );

			$time  = "{$hours}:{$mins}";

			update_post_meta( $post_id, $time_meta, $time );
		}

		// Meta
		foreach ( array(
			'agenda_related' => 'related',
		) as $posted_key => $meta ) {
			if ( isset( $_POST[ $posted_key ] ) ) {
				update_post_meta( $post_id, $meta, $_POST[ $posted_key ] );
			}
		}
	}

	/**
	 * Save when the Partner Item's metabox is submitted
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 * @param WP_Post $post Post object
	 */
	public function partner_save_metabox( $post_id, $post = 0 ) {

		// Bail when doing an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// Bail when not a post request
		if ( 'POST' != strtoupper( $_SERVER['REQUEST_METHOD'] ) )
			return;

		// Bail when nonce does not verify
		if ( empty( $_POST['partner_details_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['partner_details_metabox_nonce'], 'partner_details_metabox' ) )
			return;

		// Get post type object
		$post_type_object = get_post_type_object( $post->post_type );

		// Bail when current user is not capable
		if ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) )
			return;

		/**
		 * Save posted inputs:
		 * - Partner Level taxonomy
		 * - Partner URL meta
		 */

		foreach ( array(
			paco2017_get_partner_level_tax_id(),
		) as $taxonomy ) {
			$_taxonomy = get_taxonomy( $taxonomy );

			if ( ! $_taxonomy || ! current_user_can( $_taxonomy->cap->assign_terms ) )
				continue;

			// Set taxonomy term
			if ( isset( $_POST["taxonomy-{$taxonomy}"] ) ) {
				wp_set_object_terms( $post_id, (int) $_POST["taxonomy-{$taxonomy}"], $taxonomy, false );

			// Remove taxonomy term
			} elseif ( $terms = wp_get_object_terms( $post_id ) ) {
				wp_remove_object_terms( $post_id, $terms, $taxonomy );
			}
		}

		// Meta
		foreach ( array(
			'partner_url' => 'partner_url',
		) as $posted_key => $meta ) {
			if ( isset( $_POST[ $posted_key ] ) ) {
				update_post_meta( $post_id, $meta, $_POST[ $posted_key ] );
			}
		}
	}

	/** Terms *********************************************************/

	/**
	 * Modify the list of columns in the terms list table
	 *
	 * @since 1.1.0
	 *
	 * @param  array $columns Columns
	 * @return array Columns
	 */
	public function terms_add_columns( $columns ) {

		// Association
		if ( paco2017_get_association_tax_id() === get_current_screen()->taxonomy ) {

			// Replace posts with users
			$column_keys = array_keys( $columns );
			$column_keys[ array_search( 'posts', $column_keys ) ] = 'users';
			$columns = array_combine( $column_keys, array_values( $columns ) );
		}

		return $columns;
	}

	/**
	 * Modify the list of sortable columns in the terms list table
	 *
	 * @since 1.1.0
	 *
	 * @param  array $columns Sortable columns
	 * @return array Sortable columns
	 */
	public function terms_sortable_columns( $columns ) {

		// Association
		if ( paco2017_get_association_tax_id() === get_current_screen()->taxonomy ) {

			// Replace posts with users
			$column_keys = array_keys( $columns );
			$column_keys[ array_search( 'posts', $column_keys ) ] = 'users';
			$columns = array_combine( $column_keys, array_values( $columns ) );
		}

		return $columns;
	}

	/**
	 * Output content of the terms list table columns
	 *
	 * @since 1.1.0
	 *
	 * @param  string $content Column content
	 * @param  string $column  Column name
	 * @param  int    $term_id Term ID
	 * @return string Column content
	 */
	public function terms_custom_column( $content, $column, $term_id ) {

		// Get term
		$taxonomy = get_current_screen()->taxonomy;
		$term     = get_term( $term_id, $taxonomy );

		// Association
		if ( paco2017_get_association_tax_id() === $taxonomy ) {

			switch ( $column ) {

				// Users count
				case 'users' :
					$content = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array( $taxonomy => $term_id ), 'users.php' ) ), $term->count );
					break;
			}
		}

		return $content;
	}

	/**
	 * Modify the bulk actions in the terms list table
	 *
	 * @since 1.1.0
	 *
	 * @param  array $actions Bulk actions
	 * @return array Bulk actions
	 */
	public function terms_bulk_actions( $actions ) {
		$taxonomy = get_current_screen()->taxonomy;

		// Association
		if ( paco2017_get_association_tax_id() === $taxonomy ) {

			// Delete accounts
			if ( current_user_can( 'paco2017_manage_accounts' ) ) {
				$actions['delete_accounts'] = esc_html__( 'Delete Accounts', 'paco2017-content' );
			}
		}

		return $actions;
	}

	/**
	 * Handle custom bulk actions for terms
	 *
	 * Nonce check is already applied before this filter is applied.
	 *
	 * @since 1.1.0
	 *
	 * @param  bool|string $location False or redirect url
	 * @param  string      $action   Action
	 * @param  array       $term_ids Selected term ids
	 * @return bool|string False or redirect url
	 */
	public function terms_handle_bulk_actions( $location, $action, $term_ids ) {

		// Deleting accounts
		if ( 'delete_accounts' === $action ) {
			$location = add_query_arg( array(
				'page'   => 'paco2017-accounts',
				'action' => 'delete-by-association',
				'terms'  => $term_ids,
			), 'admin.php' );

			// Do the redirection already
			wp_redirect( $location );
			exit;
		}

		return $location;
	}

	/**
	 * Modify the row actions in the terms list table
	 *
	 * @since 1.1.0
	 *
	 * @param  arrray  $actions Row actions
	 * @param  WP_Term $term    Term data
	 * @return array Row actions
	 */
	public function terms_row_actions( $actions, $term ) {
		$taxonomy = get_current_screen()->taxonomy;

		// Association
		if ( paco2017_get_association_tax_id() === $taxonomy ) {

			// Delete accounts
			if ( current_user_can( 'paco2017_manage_accounts' ) ) {
				$actions['delete_accounts'] = sprintf( '<a href="%s">%s</a>',
					// Simulate bulk action method through GET, for single term
					esc_url( add_query_arg( array(
						'page'   => 'paco2017-accounts',
						'action' => 'delete-by-association',
						'terms'  => array( $term->term_id )
					), 'admin.php' ) ),
					esc_html__( 'Delete Accounts', 'paco2017-content' )
				);
			}
		}

		return $actions;
	}

	/** Nav Menus *****************************************************/

	/**
	 * Register the plugin's nav menu metabox
	 *
	 * @since 1.0.0
	 */
	public function add_nav_menu_meta_box() {
		add_meta_box( 'add-paco2017-nav-menu', esc_html__( 'Paascongres', 'paco2017-content' ), 'paco2017_nav_menu_metabox', 'nav-menus', 'side', 'default' );
	}

	/** Users ***********************************************************/

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
			'taxonomy-' . paco2017_get_association_tax_id() => esc_html__( 'Association', 'paco2017-content' )
		) + array_slice( $columns, $pos );

		return $columns;
	}

	/**
	 * Modify content of the users list table columns
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Column content
	 * @param string $column Column name
	 * @param int $user_id User ID
	 * @return string Column content
	 */
	public function users_custom_column( $content, $column, $user_id ) {

		// Association
		if ( 'taxonomy-' . paco2017_get_association_tax_id() === $column ) {
			$association = wp_get_object_terms( $user_id, paco2017_get_association_tax_id() );

			if ( ! empty( $association ) ) {
				$url = add_query_arg( array( paco2017_get_association_tax_id() => urlencode( $association[0]->term_id ) ) );
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
		if ( is_admin() && 'users.php' === $pagenow && ! empty( $_REQUEST[ paco2017_get_association_tax_id() ] ) ) {

			// Setup profile query
			$tax_query = new WP_Tax_Query( array(
				array(
					'taxonomy' => paco2017_get_association_tax_id(),
					'terms'    => array( urldecode( $_REQUEST[ paco2017_get_association_tax_id() ] ) ),
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
