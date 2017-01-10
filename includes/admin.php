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

		// Core
		add_action( 'admin_menu',            array( $this, 'admin_menu'        )        );
		add_action( 'admin_init',            array( $this, 'register_settings' )        );
		add_filter( 'map_meta_cap',          array( $this, 'map_meta_caps'     ), 10, 4 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts'   )        );

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

		// Collect highlightable pages
		$hooks = array(
			// Post type
			'post-new.php',
			'post.php',
			// Taxonomy
			'edit-tags.php',
			'term.php',
		);

		// Dashboard admin page
		$dashboard = add_menu_page(
			__( 'Paascongres 2017 Dashboard', 'paco2017-content' ),
			__( 'Paascongres', 'paco2017-content' ),
			'paco2017_admin_page',
			'paco2017',
			'paco2017_admin_page',
			'dashicons-megaphone',
			4
		);

		// Manage Lectures
		$hooks[] = $this->admin_submenu_post_type( paco2017_get_lecture_post_type() );

		// Manage Workshops
		$hooks[] = $this->admin_submenu_post_type( paco2017_get_workshop_post_type() );

		// Manage Speakers
		$this->admin_submenu_taxonomy( paco2017_get_speaker_tax_id() );

		// Manage Agenda
		$hooks[] = $this->admin_submenu_post_type( paco2017_get_agenda_post_type() );

		// Manage Locations
		$this->admin_submenu_taxonomy( paco2017_get_conf_location_tax_id() );

		// Manage Partners
		$hooks[] = $this->admin_submenu_post_type( paco2017_get_partner_post_type() );

		// Manage Associations
		$association = paco2017_get_association_tax_id();
		$this->admin_submenu_taxonomy( $association, "edit-tags.php?taxonomy={$association}&post_type=user" );

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

		/**
		 * Tweak the post type and taxonomy subnav menus to show the right
		 * top menu and submenu item.
		 */

		if ( in_array( $screen->post_type, array(
			paco2017_get_lecture_post_type(),
			paco2017_get_workshop_post_type(),
			paco2017_get_agenda_post_type(),
			paco2017_get_partner_post_type(),
		) ) ) {
			$parent_file  = 'paco2017';
			$submenu_file = "edit.php?post_type={$screen->post_type}";
		}

		// Workshop Category
		if ( in_array( $screen->taxonomy, array(
			paco2017_get_workshop_cat_tax_id(),
		) ) ) {
			$parent_file  = 'paco2017';
			$submenu_file = "edit.php?post_type=" . paco2017_get_workshop_post_type();
		}

		// Conference Day
		if ( in_array( $screen->taxonomy, array(
			paco2017_get_conf_day_tax_id(),
		) ) ) {
			$parent_file  = 'paco2017';
			$submenu_file = "edit.php?post_type=" . paco2017_get_agenda_post_type();
		}

		// Speaker or Conference Location
		if ( in_array( $screen->taxonomy, array(
			paco2017_get_speaker_tax_id(),
			paco2017_get_conf_location_tax_id(),
		) ) ) {
			$parent_file  = 'paco2017';
			$submenu_file = "edit-tags.php?taxonomy={$screen->taxonomy}";
		}

		// Association
		if ( in_array( $screen->taxonomy, array(
			paco2017_get_association_tax_id(),
		) ) ) {
			$parent_file  = 'paco2017';
			$submenu_file = "edit-tags.php?taxonomy={$screen->taxonomy}&post_type=user";
		}
	}

	/**
	 * Add plugin admin submenu page for the given post type
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type Post type name
	 * @param string $function Optional. Menu file or function. Defaults to the post type's edit.php
	 * @return false|string Result from {@see add_submenu_page()}
	 */
	public function admin_submenu_post_type( $post_type = '', $function = '' ) {
		if ( ! $post_type_object = get_post_type_object( $post_type ) )
			return false;

		$menu_file = "edit.php?post_type={$post_type}";

		// Remove the default admin menu and its submenus, to prevent
		// the `$parent_file` override in `get_admin_page_parent()`
		remove_menu_page( $menu_file );
		unset( $GLOBALS['submenu'][ $menu_file ] );

		return add_submenu_page(
			'paco2017',
			$post_type_object->label,
			$post_type_object->labels->menu_name,
			$post_type_object->cap->edit_posts,
			! empty( $function ) ? $function : $menu_file
		);
	}

	/**
	 * Add plugin admin submenu page for the given taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy Taxonomy name
	 * @param string $function Optional. Menu file or function. Defaults to the taxonomy's edit-tags.php
	 * @return false|string Result from {@see add_submenu_page()}
	 */
	public function admin_submenu_taxonomy( $taxonomy = '', $function = '' ) {
		if ( ! $taxonomy = get_taxonomy( $taxonomy ) )
			return false;

		$menu_file = "edit-tags.php?taxonomy={$taxonomy->name}";

		return add_submenu_page(
			'paco2017',
			$taxonomy->labels->name,
			$taxonomy->labels->menu_name,
			$taxonomy->cap->manage_terms,
			! empty( $function ) ? $function : $menu_file
		);
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'paco2017-admin', paco2017_content()->assets_url . 'css/admin.css', array( 'common' ) );

		// Define additional custom styles
		$css = array();

		// List columns
		$css[] = ".fixed .column-" . paco2017_get_association_tax_id() .
		       ", .fixed .column-taxonomy-" . paco2017_get_speaker_tax_id() .
		       ", .fixed .column-taxonomy-" . paco2017_get_workshop_cat_tax_id() .
		       ", .fixed .column-taxonomy-" . paco2017_get_conf_day_tax_id() .
		       ", .fixed .column-taxonomy-" . paco2017_get_conf_location_tax_id() . " { width: 10%; }";

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
		wp_add_dashboard_widget( 'paco2017_dashboard_status', __( 'At a Glance' ), 'paco2017_dashboard_status' );
		wp_add_dashboard_widget( 'paco2017_dashboard_enrollments', __( 'Enrollments', 'paco2017-content' ), 'paco2017_dashboard_enrollments' );
		wp_add_dashboard_widget( 'dashboard_activity', __( 'Activity' ), 'wp_dashboard_site_activity' );
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
			case 'paco2017_admin_page':
			case 'paco2017_admin_settings_page':
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

		// Mark the Agenda page
		if ( paco2017_get_agenda_page_id() === $post->ID ) {
			$states['agenda_page'] = __( 'Agenda', 'paco2017-content' );

		// Mark the Speakers page
		} elseif ( paco2017_get_speakers_page_id() === $post->ID ) {
			$states['speakers_page'] = __( 'Speakers', 'paco2017-content' );

		// Mark the Housekeeping page
		} elseif ( paco2017_get_housekeeping_page_id() === $post->ID ) {
			$states['housekeeping_page'] = __( 'Housekeeping', 'paco2017-content' );
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

				// Display link to manage days
				printf( '<div class="alignleft actions paco2017-workshop-cat-link"><a href="%s" class="page-title-action">%s</a></div>', 'edit-tags.php?taxonomy=' . paco2017_get_workshop_cat_tax_id(), esc_html__( 'Manage Workshop Categories', 'paco2017-content' ) );

				break;

			// Agenda
			case paco2017_get_agenda_post_type() :

				// Display link to manage days
				printf( '<div class="alignleft actions paco2017-conf-day-link"><a href="%s" class="page-title-action">%s</a></div>', 'edit-tags.php?taxonomy=' . paco2017_get_conf_day_tax_id(), esc_html__( 'Manage Conference Days', 'paco2017-content' ) );

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
		$spkr_key = 'taxonomy-' . paco2017_get_speaker_tax_id();
		if ( isset( $columns[ $spkr_key ] ) ) {
			$columns[ $spkr_key ] = esc_html__( 'Speaker', 'paco2017-content' );
		}

		// Rename Wokshop Category column
		$cat_key = 'taxonomy-' . paco2017_get_workshop_cat_tax_id();
		if ( isset( $columns[ $cat_key ] ) ) {
			$columns[ $cat_key ] = esc_html__( 'Category', 'paco2017-content' );
		}

		// Rename Conference Day column
		$day_key = 'taxonomy-' . paco2017_get_conf_day_tax_id();
		if ( isset( $columns[ $day_key ] ) ) {
			$columns[ $day_key ] = esc_html__( 'Day', 'paco2017-content' );
		}

		// Rename Conference Location column
		$loc_key = 'taxonomy-' . paco2017_get_conf_location_tax_id();
		if ( isset( $columns[ $loc_key ] ) ) {
			$columns[ $loc_key ] = esc_html__( 'Location', 'paco2017-content' );
		}

		// Agenda
		if ( paco2017_get_agenda_post_type() === $post_type ) {

			// Append Time Start and Time End
			$day_pos = array_search( 'taxonomy-' . paco2017_get_conf_day_tax_id(), array_keys( $columns ) );
			if ( $day_pos ) {
				$columns = array_slice( $columns, 0, $day_pos + 1 ) + array(
					'time_start' => esc_html_x( 'Start',   'agenda meta column name', 'paco2017-content' ),
					'time_end'   => esc_html_x( 'End',     'agenda meta column name', 'paco2017-content' ),
				) + array_slice( $columns, $day_pos + 1 );
			}

			// Append related
			$loc_pos = array_search( 'taxonomy-' . paco2017_get_conf_location_tax_id(), array_keys( $columns ) );
			if ( $loc_pos ) {
				$columns = array_slice( $columns, 0, $loc_pos + 1 ) + array(
					'related'    => esc_html_x( 'Related', 'agenda meta column name', 'paco2017-content' ),
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
					'logo' => esc_html__( 'Logo', 'paco2017-content' ),
				) + array_slice( $columns, $title_pos );

				// Insert after 'Title'
				$columns = array_slice( $columns, 0, $title_pos + 2 ) + array(
					'partner_url' => esc_html__( 'URL', 'paco2017-content' ),
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

			// Agenda Items
			case paco2017_get_agenda_post_type() :
				switch ( $column ) {
					case 'time_start' :
					case 'time_end' :
						$meta = get_post_meta( $post_id, $column, true );
						echo ( ! empty( $meta ) ) ? $meta : '&mdash;';

						break;
					case 'related' :
						echo paco2017_is_agenda_related( $post_id ) ? paco2017_get_agenda_related_link( $post_id ) : '&mdash;';

						break;
				}

				break;

			// Partners
			case paco2017_get_partner_post_type() :
				switch ( $column ) {
					case 'logo' :
						if ( $logo_id = paco2017_partner_get_logo_id( $post_id ) ) {
							echo wp_get_attachment_image( $logo_id, array( 38, 38 ) );
						}

						break;
					case 'partner_url' :
						$meta = get_post_meta( $post_id, $column, true );
						echo ( ! empty( $meta ) ) ? $meta : '&mdash;';

						break;
				}

				break;
		}
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
				array( $this, 'lecture_details_metabox' ),
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
				array( $this, 'workshop_details_metabox' ),
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
				array( $this, 'agenda_details_metabox' ),
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
				array( $this, 'partner_details_metabox' ),
				null,
				'side',
				'high'
			);
		}
	}

	/**
	 * Output the contents of the Lecture Details metabox
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Current post object
	 */
	public function lecture_details_metabox( $post ) {

		// Get taxonomies
		$speaker_tax  = paco2017_get_speaker_tax_id();

		?>

		<div class="paco2017_object_details">

		<p>
			<label for="taxonomy-<?php echo $speaker_tax; ?>"><?php esc_html_e( 'Speaker:', 'paco2017-content' ); ?></label>
			<?php
				$spkr_terms = wp_get_object_terms( $post->ID, $speaker_tax, array( 'fields' => 'ids' ) );

				wp_dropdown_categories( array(
					'name'             => "taxonomy-{$speaker_tax}",
					'taxonomy'         => $speaker_tax,
					'hide_empty'       => false,
					'selected'         => $spkr_terms ? $spkr_terms[0] : 0,
					'show_option_none' => esc_html__( '&mdash; No Speaker &mdash;', 'paco2017-content' ),
				) );
			?>
		</p>

		</div>

		<?php wp_nonce_field( 'lecture_details_metabox', 'lecture_details_metabox_nonce' ); ?>

		<?php
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

			// Set Article Edition
			if ( isset( $_POST["taxonomy-{$taxonomy}"] ) ) {
				wp_set_object_terms( $post_id, (int) $_POST["taxonomy-{$taxonomy}"], $taxonomy, false );

			// Remove Article Edition
			} elseif ( $terms = wp_get_object_terms( $post_id ) ) {
				wp_remove_object_terms( $post_id, $terms, $taxonomy );
			}
		}
	}

	/**
	 * Output the contents of the Workshop Details metabox
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Current post object
	 */
	public function workshop_details_metabox( $post ) {

		// Get taxonomies
		$speaker_tax      = paco2017_get_speaker_tax_id();
		$workshop_cat_tax = paco2017_get_workshop_cat_tax_id();
		$conf_loc_tax     = paco2017_get_conf_location_tax_id();

		?>

		<div class="paco2017_object_details">

		<p>
			<label for="taxonomy-<?php echo $speaker_tax; ?>"><?php esc_html_e( 'Speaker:', 'paco2017-content' ); ?></label>
			<?php
				$spkr_terms = wp_get_object_terms( $post->ID, $speaker_tax, array( 'fields' => 'ids' ) );

				wp_dropdown_categories( array(
					'name'             => "taxonomy-{$speaker_tax}",
					'taxonomy'         => $speaker_tax,
					'hide_empty'       => false,
					'selected'         => $spkr_terms ? $spkr_terms[0] : 0,
					'show_option_none' => esc_html__( '&mdash; No Speaker &mdash;', 'paco2017-content' ),
				) );
			?>
		</p>

		<p>
			<label for="taxonomy-<?php echo $workshop_cat_tax; ?>"><?php esc_html_e( 'Category:', 'paco2017-content' ); ?></label>
			<?php
				$cat_terms = wp_get_object_terms( $post->ID, $workshop_cat_tax, array( 'fields' => 'ids' ) );

				wp_dropdown_categories( array(
					'name'             => "taxonomy-{$workshop_cat_tax}",
					'taxonomy'         => $workshop_cat_tax,
					'hide_empty'       => false,
					'selected'         => $cat_terms ? $cat_terms[0] : 0,
					'show_option_none' => esc_html__( '&mdash; No Category &mdash;', 'paco2017-content' ),
				) );
			?>
		</p>

		<p>
			<label for="taxonomy-<?php echo $conf_loc_tax; ?>"><?php esc_html_e( 'Location:', 'paco2017-content' ); ?></label>
			<?php
				$loc_terms = wp_get_object_terms( $post->ID, $conf_loc_tax, array( 'fields' => 'ids' ) );

				wp_dropdown_categories( array(
					'name'             => "taxonomy-{$conf_loc_tax}",
					'taxonomy'         => $conf_loc_tax,
					'hide_empty'       => false,
					'selected'         => $loc_terms ? $loc_terms[0] : 0,
					'show_option_none' => esc_html__( '&mdash; No Location &mdash;', 'paco2017-content' ),
				) );
			?>
		</p>

		</div>

		<?php wp_nonce_field( 'workshop_details_metabox', 'workshop_details_metabox_nonce' ); ?>

		<?php
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
		 * - Speaker taxonomy
		 * - Workshop Category taxonomy
		 * - Conference Location taxonomy
		 */

		foreach ( array(
			paco2017_get_speaker_tax_id(),
			paco2017_get_workshop_cat_tax_id(),
			paco2017_get_conf_location_tax_id(),
		) as $taxonomy ) {
			$_taxonomy = get_taxonomy( $taxonomy );

			if ( ! $_taxonomy || ! current_user_can( $_taxonomy->cap->assign_terms ) )
				continue;

			// Set Article Edition
			if ( isset( $_POST["taxonomy-{$taxonomy}"] ) ) {
				wp_set_object_terms( $post_id, (int) $_POST["taxonomy-{$taxonomy}"], $taxonomy, false );

			// Remove Article Edition
			} elseif ( $terms = wp_get_object_terms( $post_id ) ) {
				wp_remove_object_terms( $post_id, $terms, $taxonomy );
			}
		}
	}

	/**
	 * Output the contents of the Agenda Details metabox
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Current post object
	 */
	public function agenda_details_metabox( $post ) {

		// Get taxonomies
		$conf_day_tax = paco2017_get_conf_day_tax_id();
		$conf_loc_tax = paco2017_get_conf_location_tax_id();

		// Define time input template
		$time_input = '<input type="number" class="time-part" name="%1$s" id="%1$s" step="1" min="0" max="%2$s" value="%3$s" />';

		?>

		<div class="paco2017_object_details">

		<p>
			<label for="taxonomy-<?php echo $conf_day_tax; ?>"><?php esc_html_e( 'Day:', 'paco2017-content' ); ?></label>
			<?php
				$day_terms = wp_get_object_terms( $post->ID, $conf_day_tax, array( 'fields' => 'ids' ) );

				wp_dropdown_categories( array(
					'name'             => "taxonomy-{$conf_day_tax}",
					'taxonomy'         => $conf_day_tax,
					'hide_empty'       => false,
					'selected'         => $day_terms ? $day_terms[0] : 0,
					'show_option_none' => esc_html__( '&mdash; No Day &mdash;', 'paco2017-content' ),
				) );
			?>
		</p>

		<p>
			<label for="agenda_time_start_hours"><?php esc_html_e( 'Time Start:', 'paco2017-content' ); ?></label>
			<?php
				$start = get_post_meta( $post->ID, 'time_start', true );
				printf( "<span>%s:%s</span>",
					sprintf( $time_input, 'agenda_time_start_hours', 23, strtok( $start, ':' ) ),
					sprintf( $time_input, 'agenda_time_start_mins',  59, substr( $start, strpos( $start, ':' ) + 1 ) )
				);
			?>
		</p>

		<p>
			<label for="agenda_time_end_hours"><?php esc_html_e( 'Time End:', 'paco2017-content' ); ?></label>
			<?php
				$end = get_post_meta( $post->ID, 'time_end', true );
				printf( "<span>%s:%s</span>",
					sprintf( $time_input, 'agenda_time_end_hours', 23, strtok( $end, ':' ) ),
					sprintf( $time_input, 'agenda_time_end_mins',  59, substr( $end, strpos( $end, ':' ) + 1 ) )
				);
			?>
		</p>

		<p>
			<label for="taxonomy-<?php echo $conf_loc_tax; ?>"><?php esc_html_e( 'Location:', 'paco2017-content' ); ?></label>
			<?php
				$loc_terms = wp_get_object_terms( $post->ID, $conf_loc_tax, array( 'fields' => 'ids' ) );

				wp_dropdown_categories( array(
					'name'             => "taxonomy-{$conf_loc_tax}",
					'taxonomy'         => $conf_loc_tax,
					'hide_empty'       => false,
					'selected'         => $loc_terms ? $loc_terms[0] : 0,
					'show_option_none' => esc_html__( '&mdash; No Location &mdash;', 'paco2017-content' ),
				) );
			?>
		</p>

		<p>
			<label for="agenda_related"><?php esc_html_e( 'Related:', 'paco2017-content' ); ?></label>
			<?php
				paco2017_dropdown_agenda_pages( array(
					'name'             => 'agenda_related',
					'selected'         => paco2017_get_agenda_related_id( $post ),
					'show_option_none' => esc_html__( '&mdash; No Relation &mdash;', 'paco2017-content' ),
				) );
			?>
		</p>

		</div>

		<?php wp_nonce_field( 'agenda_details_metabox', 'agenda_details_metabox_nonce' ); ?>

		<?php
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

			// Set Article Edition
			if ( isset( $_POST["taxonomy-{$taxonomy}"] ) ) {
				wp_set_object_terms( $post_id, (int) $_POST["taxonomy-{$taxonomy}"], $taxonomy, false );

			// Remove Article Edition
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
	 * Output the contents of the Partner Details metabox
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Current post object
	 */
	public function partner_details_metabox( $post ) {
		$url = get_post_meta( $post->ID, 'partner_url', true );
		$post_type_object = get_post_type_object( $post->post_type );

		?>

		<div class="paco2017_object_details">

		<p id="partner_logo">
			<label for="partner_logo"><?php esc_html_e( 'Logo:', 'paco2017-content' ); ?></label>
			<?php if ( function_exists( 'wp_post_image_input' ) ) wp_post_image_input( $post, 'logo' ); ?>
		</p>

		<p>
			<label for="partner_url"><?php esc_html_e( 'Partner URL:', 'paco2017-content' ); ?></label>
			<input type="text" name="partner_url" id="partner_url" value="<?php echo esc_attr( $url ); ?>" />
		</p>

		</div>

		<?php wp_nonce_field( 'partner_details_metabox', 'partner_details_metabox_nonce' ); ?>

		<?php
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
		 * - Partner URL meta
		 */

		// Meta
		foreach ( array(
			'partner_url' => 'partner_url',
		) as $posted_key => $meta ) {
			if ( isset( $_POST[ $posted_key ] ) ) {
				update_post_meta( $post_id, $meta, $_POST[ $posted_key ] );
			}
		}
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
			'paco2017_association' => esc_html__( 'Association', 'paco2017-content' )
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
