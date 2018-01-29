<?php

/**
 * The Paascongres 2017 Content Plugin
 *
 * @package Paascongres 2017 Content
 * @subpackage Main
 */

/**
 * Plugin Name:       Paascongres 2017 Content
 * Description:       Content logic for the Paascongres 2017 event site
 * Plugin URI:        https://github.com/vgsr/paco2017-content/
 * Version:           1.0.1
 * Author:            Laurens Offereins
 * Author URI:        https://github.com/vgsr/
 * Text Domain:       paco2017-content
 * Domain Path:       /languages/
 * GitHub Plugin URI: vgsr/paco2017-content
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Paco2017_Content' ) ) :
/**
 * The main plugin class
 *
 * @since 1.0.0
 */
final class Paco2017_Content {

	/**
	 * Setup and return the singleton pattern
	 *
	 * @since 1.0.0
	 *
	 * @uses Paco2017_Content::setup_globals()
	 * @uses Paco2017_Content::setup_actions()
	 * @return The single Paascongres 2017 Content
	 */
	public static function instance() {

		// Store instance locally
		static $instance = null;

		if ( null === $instance ) {
			$instance = new Paco2017_Content;
			$instance->setup_globals();
			$instance->includes();
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Prevent the plugin class from being loaded more than once
	 */
	private function __construct() { /* Nothing to do */ }

	/** Private methods *************************************************/

	/**
	 * Setup default class globals
	 *
	 * @since 1.0.0
	 */
	private function setup_globals() {

		/** Versions ****************************************************/

		$this->version      = '1.0.1';
		$this->db_version   = 101;

		/** Paths *******************************************************/

		// Setup some base path and URL information
		$this->file         = __FILE__;
		$this->basename     = plugin_basename( $this->file );
		$this->plugin_dir   = plugin_dir_path( $this->file );
		$this->plugin_url   = plugin_dir_url ( $this->file );

		// Includes
		$this->includes_dir = trailingslashit( $this->plugin_dir . 'includes' );
		$this->includes_url = trailingslashit( $this->plugin_url . 'includes' );

		// Assets
		$this->assets_dir   = trailingslashit( $this->plugin_dir . 'assets' );
		$this->assets_url   = trailingslashit( $this->plugin_url . 'assets' );

		// Templates
		$this->themes_dir   = trailingslashit( $this->plugin_dir . 'templates' );
		$this->themes_url   = trailingslashit( $this->plugin_url . 'templates' );

		// Languages
		$this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );

		/** Identifiers *************************************************/

		// Post types
		$this->lecture_post_type     = apply_filters( 'paco2017_lecture_post_type',  'paco2017_lecture'  );
		$this->workshop_post_type    = apply_filters( 'paco2017_workshop_post_type', 'paco2017_workshop' );
		$this->agenda_post_type      = apply_filters( 'paco2017_agenda_post_type',   'paco2017_agenda'   );
		$this->partner_post_type     = apply_filters( 'paco2017_partner_post_type',  'paco2017_partner'  );

		// Taxonomies
		$this->association_tax_id    = apply_filters( 'paco2017_association_tax_id',    'paco2017_association'       );
		$this->speaker_tax_id        = apply_filters( 'paco2017_speaker_tax_id',        'paco2017_speaker'           );
		$this->workshop_cat_tax_id   = apply_filters( 'paco2017_workshop_cat_tax_id',   'paco2017_workshop_category' );
		$this->workshop_round_tax_id = apply_filters( 'paco2017_workshop_round_tax_id', 'paco2017_workshop_round'    );
		$this->conf_day_tax_id       = apply_filters( 'paco2017_conf_day_tax_id',       'paco2017_conf_day'          );
		$this->conf_location_tax_id  = apply_filters( 'paco2017_conf_location_tax_id',  'paco2017_conf_location'     );
		$this->partner_level_tax_id  = apply_filters( 'paco2017_partner_level_tax_id',  'paco2017_partner_level'     );

		/** Queries *****************************************************/

		$this->agenda_query      = new WP_Query();      // Main Agenda query
		$this->conf_day_query    = new WP_Term_Query(); // Main Conference Day query
		$this->speaker_query     = new WP_Term_Query(); // Main Speaker query
		$this->association_query = new WP_Term_Query(); // Main Association query

		/** Misc ********************************************************/

		$this->theme_compat = new stdClass();
		$this->extend       = new stdClass();
		$this->domain       = 'paco2017-content';
	}

	/**
	 * Include the required files
	 *
	 * @since 1.0.0
	 */
	private function includes() {

		/** Core ********************************************************/

		require( $this->includes_dir . 'actions.php'      );
		require( $this->includes_dir . 'agenda.php'       );
		require( $this->includes_dir . 'associations.php' );
		require( $this->includes_dir . 'capabilities.php' );
		require( $this->includes_dir . 'downloads.php'    );
		require( $this->includes_dir . 'functions.php'    );
		require( $this->includes_dir . 'lectures.php'     );
		require( $this->includes_dir . 'partners.php'     );
		require( $this->includes_dir . 'speakers.php'     );
		require( $this->includes_dir . 'sub-actions.php'  );
		require( $this->includes_dir . 'template.php'     );
		require( $this->includes_dir . 'theme-compat.php' );
		require( $this->includes_dir . 'users.php'        );
		require( $this->includes_dir . 'update.php'       );
		require( $this->includes_dir . 'workshops.php'    );

		/** Classes *****************************************************/

		require( $this->includes_dir . 'classes/class-wp-post-media.php'     );
		require( $this->includes_dir . 'classes/class-wp-setting-media.php'  );
		require( $this->includes_dir . 'classes/class-wp-term-meta-ui.php'   );
		require( $this->includes_dir . 'classes/class-wp-term-adverbial.php' );
		require( $this->includes_dir . 'classes/class-wp-term-colors.php'    );
		require( $this->includes_dir . 'classes/class-wp-term-date.php'      );
		require( $this->includes_dir . 'classes/class-wp-term-media.php'     );

		/** Widgets *****************************************************/

		require( $this->includes_dir . 'classes/class-paco2017-enrollments-widget.php'  );
		require( $this->includes_dir . 'classes/class-paco2017-partners-widget.php'     );

		/** Admin *******************************************************/

		if ( is_admin() ) {
			require( $this->includes_dir . 'admin/admin.php' );
		}

		/** Extend ******************************************************/

		require( $this->includes_dir . 'extend/buddypress/buddypress.php' );
		require( $this->includes_dir . 'extend/vgsr.php'                  );
		require( $this->includes_dir . 'extend/wordpress-seo.php'         );
	}

	/**
	 * Setup default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {

		// Add actions to plugin activation and deactivation hooks
		add_action( 'activate_'   . $this->basename, 'paco2017_activation'   );
		add_action( 'deactivate_' . $this->basename, 'paco2017_deactivation' );

		// Load textdomain
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 20 );

		// Register content
		add_action( 'paco2017_init', array( $this, 'register_post_types' ) );
		add_action( 'paco2017_init', array( $this, 'register_taxonomies' ) );

		// Permalinks
		add_action( 'paco2017_init', array( $this, 'add_rewrite_tags'  ), 20 );
		add_action( 'paco2017_init', array( $this, 'add_rewrite_rules' ), 30 );
	}

	/** Plugin **********************************************************/

	/**
	 * Load the translation file for current language. Checks the languages
	 * folder inside the plugin first, and then the default WordPress
	 * languages folder.
	 *
	 * Note that custom translation files inside the plugin folder will be
	 * removed on plugin updates. If you're creating custom translation
	 * files, please use the global language folder.
	 *
	 * @since 1.0.0
	 *
	 * @uses apply_filters() Calls 'plugin_locale' with {@link get_locale()} value
	 * @uses load_textdomain() To load the textdomain
	 * @uses load_plugin_textdomain() To load the textdomain
	 */
	public function load_textdomain() {

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale', get_locale(), $this->domain );
		$mofile        = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

		// Setup paths to current locale file
		$mofile_local  = $this->lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/paco2017-content/' . $mofile;

		// Look in global /wp-content/languages/paco2017-content folder
		load_textdomain( $this->domain, $mofile_global );

		// Look in local /wp-content/plugins/paco2017-content/languages/ folder
		load_textdomain( $this->domain, $mofile_local );

		// Look in global /wp-content/languages/plugins/
		load_plugin_textdomain( $this->domain );
	}

	/** Public methods **************************************************/

	/**
	 * Register initial plugin post types
	 *
	 * @since 1.0.0
	 */
	public function register_post_types() {

		/** Lecture ******************************************************/

		register_post_type(
			paco2017_get_lecture_post_type(),
			array(
				'labels'              => paco2017_get_lecture_post_type_labels(),
				'supports'            => paco2017_get_lecture_post_type_supports(),
				'description'         => esc_html__( 'Paascongres lectures', 'paco2017-content' ),
				'capabilities'        => paco2017_get_lecture_post_type_caps(),
				'capability_type'     => array( 'paco2017_lecture', 'paco2017_lectures' ),
				'hierarchical'        => false,
				'public'              => true,
				'has_archive'         => true,
				'rewrite'             => paco2017_get_lecture_post_type_rewrite(),
				'query_var'           => true,
				'exclude_from_search' => false,
				'show_ui'             => current_user_can( 'paco2017_lecture_admin' ),
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'menu_icon'           => 'dashicons-businessman',

				// REST API
				'show_in_rest'        => true,
				'rest_base'           => 'paascongres-lectures',
			)
		);

		/** Workshop ****************************************************/

		register_post_type(
			paco2017_get_workshop_post_type(),
			array(
				'labels'              => paco2017_get_workshop_post_type_labels(),
				'supports'            => paco2017_get_workshop_post_type_supports(),
				'description'         => esc_html__( 'Paascongres workshops', 'paco2017-content' ),
				'capabilities'        => paco2017_get_workshop_post_type_caps(),
				'capability_type'     => array( 'paco2017_workshop', 'paco2017_workshops' ),
				'hierarchical'        => false,
				'public'              => true,
				'has_archive'         => true,
				'rewrite'             => paco2017_get_workshop_post_type_rewrite(),
				'query_var'           => true,
				'exclude_from_search' => false,
				'show_ui'             => current_user_can( 'paco2017_workshop_admin' ),
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'menu_icon'           => 'dashicons-admin-tools',

				// REST API
				'show_in_rest'        => true,
				'rest_base'           => 'paascongres-workshops',
			)
		);

		/** Agenda ******************************************************/

		register_post_type(
			paco2017_get_agenda_post_type(),
			array(
				'labels'              => paco2017_get_agenda_post_type_labels(),
				'supports'            => paco2017_get_agenda_post_type_supports(),
				'description'         => esc_html__( 'Paascongres agenda items', 'paco2017-content' ),
				'capabilities'        => paco2017_get_agenda_post_type_caps(),
				'capability_type'     => array( 'paco2017_agenda', 'paco2017_agendas' ),
				'hierarchical'        => false,
				'public'              => true,
				'has_archive'         => false,
				'rewrite'             => false, // We have our own rewrite rules
				'query_var'           => false, // We have our own query vars
				'exclude_from_search' => true,
				'show_ui'             => current_user_can( 'paco2017_agenda_admin' ),
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'menu_icon'           => 'dashicons-schedule',

				// REST API
				'show_in_rest'        => true,
				'rest_base'           => 'paascongres-agenda',
			)
		);

		/** Partner *****************************************************/

		register_post_type(
			paco2017_get_partner_post_type(),
			array(
				'labels'              => paco2017_get_partner_post_type_labels(),
				'supports'            => paco2017_get_partner_post_type_supports(),
				'description'         => esc_html__( 'Paascongres partners', 'paco2017-content' ),
				'capabilities'        => paco2017_get_partner_post_type_caps(),
				'capability_type'     => array( 'paco2017_partner', 'paco2017_partners' ),
				'hierarchical'        => false,
				'public'              => true,
				'has_archive'         => false,
				'rewrite'             => false, // No rewriting necessary
				'query_var'           => false, // No query vars necessary
				'exclude_from_search' => true,
				'show_ui'             => current_user_can( 'paco2017_partner_admin' ),
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'menu_icon'           => 'dashicons-marker',

				// REST API
				'show_in_rest'        => true,
				'rest_base'           => 'paascongres-partners',
			)
		);

		/** Meta ********************************************************/

		wp_post_media( $this->file, 'logo', array(
			'post_type'  => paco2017_get_partner_post_type(),
			'element'    => '#partner_logo',
			'image_size' => 'paco2017-partner-logo',
			'labels'     => array(
				'setPostMedia'    => esc_html__( 'Set Partner Logo',    'paco2017-content' ),
				'postMediaTitle'  => esc_html__( 'Partner Logo',        'paco2017-content' ),
				'removePostMedia' => esc_html__( 'Remove Partner Logo', 'paco2017-content' ),
			),
		) );
	}

	/**
	 * Register initial plugin taxonomies
	 *
	 * @since 1.0.0
	 */
	public function register_taxonomies() {

		/** Association *************************************************/

		register_taxonomy(
			paco2017_get_association_tax_id(),
			'user',
			array(
				'labels'                => paco2017_get_association_tax_labels(),
				'capabilities'          => paco2017_get_association_tax_caps(),
				'update_count_callback' => '_update_generic_term_count',
				'hierarchical'          => false,
				'public'                => true,
				'rewrite'               => false, // We have our own rewrite rules
				'query_var'             => false, // We have our own query vars
				'show_tagcloud'         => false,
				'show_in_quick_edit'    => true,
				'show_admin_column'     => false, // User taxonomies are not supported in WP
				'show_in_nav_menus'     => false,
				'show_ui'               => current_user_can( 'paco2017_association_admin' ),
				'meta_box_cb'           => false, // No metaboxing

				// Term meta
				'term_meta_color'       => true,
				'term_meta_logo'        => true,

				// REST API
				'show_in_rest'          => true,
				'rest_base'             => 'paascongres-associations',
			)
		);

		/** Speakers ****************************************************/

		register_taxonomy(
			paco2017_get_speaker_tax_id(),
			array(
				paco2017_get_lecture_post_type(),
				paco2017_get_workshop_post_type(),
			),
			array(
				'labels'                => paco2017_get_speaker_tax_labels(),
				'capabilities'          => paco2017_get_speaker_tax_caps(),
				'update_count_callback' => '_update_post_term_count',
				'hierarchical'          => false,
				'public'                => true,
				'rewrite'               => false, // We have our own rewrite rules
				'query_var'             => false, // We have our own query vars
				'show_tagcloud'         => false,
				'show_in_quick_edit'    => true,
				'show_admin_column'     => true,
				'show_in_nav_menus'     => false,
				'show_ui'               => current_user_can( 'paco2017_speaker_admin' ),
				'meta_box_cb'           => false, // No metaboxing

				// Term meta
				'term_meta_photo'       => true,

				// REST API
				'show_in_rest'          => true,
				'rest_base'             => 'paascongres-speakers',
			)
		);

		/** Workshop Category *******************************************/

		register_taxonomy(
			paco2017_get_workshop_cat_tax_id(),
			paco2017_get_workshop_post_type(),
			array(
				'labels'                => paco2017_get_workshop_cat_tax_labels(),
				'capabilities'          => paco2017_get_workshop_cat_tax_caps(),
				'update_count_callback' => '_update_post_term_count',
				'hierarchical'          => false,
				'public'                => true,
				'rewrite'               => paco2017_get_workshop_cat_tax_rewrite(),
				'query_var'             => true,
				'show_tagcloud'         => false,
				'show_in_quick_edit'    => true,
				'show_admin_column'     => true,
				'show_in_nav_menus'     => true,
				'show_ui'               => current_user_can( 'paco2017_workshop_cat_admin' ),
				'meta_box_cb'           => false, // No metaboxing

				// Term meta
				'term_meta_color'       => true,
			)
		);

		/** Workshop Round **********************************************/

		register_taxonomy(
			paco2017_get_workshop_round_tax_id(),
			paco2017_get_workshop_post_type(),
			array(
				'labels'                => paco2017_get_workshop_round_tax_labels(),
				'capabilities'          => paco2017_get_workshop_round_tax_caps(),
				'update_count_callback' => '_update_post_term_count',
				'hierarchical'          => false,
				'public'                => true,
				'rewrite'               => paco2017_get_workshop_round_tax_rewrite(),
				'query_var'             => true,
				'show_tagcloud'         => false,
				'show_in_quick_edit'    => true,
				'show_admin_column'     => true,
				'show_in_nav_menus'     => true,
				'show_ui'               => current_user_can( 'paco2017_workshop_round_admin' ),
				'meta_box_cb'           => false, // No metaboxing

				// REST API
				'show_in_rest'          => true,
				'rest_base'             => 'paascongres-workshop-rounds',
			)
		);

		/** Conference Day **********************************************/

		register_taxonomy(
			paco2017_get_conf_day_tax_id(),
			paco2017_get_agenda_post_type(),
			array(
				'labels'                => paco2017_get_conf_day_tax_labels(),
				'capabilities'          => paco2017_get_conf_day_tax_caps(),
				'update_count_callback' => '_update_post_term_count',
				'hierarchical'          => false,
				'public'                => true,
				'rewrite'               => false, // We have our own rewrite rules
				'query_var'             => false, // We have our own query vars
				'show_tagcloud'         => false,
				'show_in_quick_edit'    => true,
				'show_admin_column'     => true,
				'show_in_nav_menus'     => false,
				'show_ui'               => current_user_can( 'paco2017_conf_day_admin' ),
				'meta_box_cb'           => false, // No metaboxing

				// Term meta
				'term_meta_color'       => true,
				'term_meta_date'        => true,

				// REST API
				'show_in_rest'          => true,
				'rest_base'             => 'paascongres-conference-days',
			)
		);

		/** Conference Location *****************************************/

		register_taxonomy(
			paco2017_get_conf_location_tax_id(),
			array(
				paco2017_get_agenda_post_type(),
				paco2017_get_workshop_post_type(),
			),
			array(
				'labels'                => paco2017_get_conf_location_tax_labels(),
				'capabilities'          => paco2017_get_conf_location_tax_caps(),
				'update_count_callback' => '_update_post_term_count',
				'hierarchical'          => false,
				'public'                => true,
				'rewrite'               => false, // No rewriting necessary
				'query_var'             => false, // No query vars necessary
				'show_tagcloud'         => false,
				'show_in_quick_edit'    => true,
				'show_admin_column'     => true,
				'show_in_nav_menus'     => false,
				'show_ui'               => current_user_can( 'paco2017_conf_location_admin' ),
				'meta_box_cb'           => false, // No metaboxing

				// Term meta
				'term_meta_adverbial'   => true,
				'term_meta_color'       => true,
			)
		);

		/** Partner Level ***********************************************/

		register_taxonomy(
			paco2017_get_partner_level_tax_id(),
			paco2017_get_partner_post_type(),
			array(
				'labels'                => paco2017_get_partner_level_tax_labels(),
				'capabilities'          => paco2017_get_partner_level_tax_caps(),
				'update_count_callback' => '_update_post_term_count',
				'hierarchical'          => false,
				'public'                => true,
				'rewrite'               => false, // No rewriting necessary
				'query_var'             => false, // No query vars necessary
				'show_tagcloud'         => false,
				'show_in_quick_edit'    => true,
				'show_admin_column'     => true,
				'show_in_nav_menus'     => false,
				'show_ui'               => current_user_can( 'paco2017_partner_level_admin' ),
				'meta_box_cb'           => false, // No metaboxing

				// Term meta
				'term_meta_color'       => true,
			)
		);

		/** Meta ********************************************************/

		new WP_Term_Adverbial( $this->file );
		new WP_Term_Colors( $this->file );
		new WP_Term_Date( $this->file );
		new WP_Term_Media( $this->file, array(
			'meta_key'   => 'logo',
			'image_size' => array( 300, 300 ),
			'labels'     => array(
				'singular' => esc_html__( 'Logo', 'paco2017-content' ),
				'plural'   => esc_html__( 'Logos', 'paco2017-content' ),
				'setTermMedia'    => esc_html__( 'Set %s logo', 'paco2017-content' ),
				'termMediaTitle'  => esc_html__( '%s logo', 'paco2017-content' ),
				'removeTermMedia' => esc_html__( 'Remove %s logo', 'paco2017-content' ),
			)
		) );
		new WP_Term_Media( $this->file, array(
			'meta_key'   => 'photo',
			'image_size' => array( 150, 150 ),
			'labels'     => array(
				'singular' => esc_html__( 'Photo', 'paco2017-content' ),
				'plural'   => esc_html__( 'Photos', 'paco2017-content' ),
				'setTermMedia'    => esc_html__( 'Set %s photo', 'paco2017-content' ),
				'termMediaTitle'  => esc_html__( '%s photo', 'paco2017-content' ),
				'removeTermMedia' => esc_html__( 'Remove %s photo', 'paco2017-content' ),
			)
		) );
	}

	/**
	 * Register plugin rewrite tags
	 *
	 * @since 1.0.0
	 */
	public function add_rewrite_tags() {
		add_rewrite_tag( '%' . paco2017_get_agenda_rewrite_id()       . '%', '([1]{1,})' ); // Agenda Page tag
		add_rewrite_tag( '%' . paco2017_get_associations_rewrite_id() . '%', '([1]{1,})' ); // Associations Page tag
		add_rewrite_tag( '%' . paco2017_get_speakers_rewrite_id()     . '%', '([1]{1,})' ); // Speakers Page tag
		add_rewrite_tag( '%' . paco2017_get_download_rewrite_id()     . '%', '([^/]+)'   ); // Download File tag
	}

	/**
	 * Register plugin rewrite rules
	 *
	 * Setup rules to create the following structures:
	 * - /{agenda}/
	 * - /{associations}/
	 * - /{speakers}/
	 * - /[downloads]/{file}/
	 *
	 * @since 1.0.0
	 */
	public function add_rewrite_rules() {

		// Priority
		$priority          = 'top';

		// Slugs
		$agenda_slug       = paco2017_get_agenda_slug();
		$associations_slug = paco2017_get_associations_slug();
		$speakers_slug     = paco2017_get_speakers_slug();
		$download_slug     = paco2017_get_download_slug();

		// Unique rewrite ID's
		$agenda_id         = paco2017_get_agenda_rewrite_id();
		$associations_id   = paco2017_get_associations_rewrite_id();
		$speakers_id       = paco2017_get_speakers_rewrite_id();
		$download_id       = paco2017_get_download_rewrite_id();

		// Generic rules
		$root_rule         = '/?$';
		$download_rule     = $download_slug . '/([^/]+)';

		/** Add *********************************************************/

		// Page rules
		add_rewrite_rule( $agenda_slug       . $root_rule, 'index.php?' . $agenda_id       . '=1',           $priority );
		add_rewrite_rule( $associations_slug . $root_rule, 'index.php?' . $associations_id . '=1',           $priority );
		add_rewrite_rule( $speakers_slug     . $root_rule, 'index.php?' . $speakers_id     . '=1',           $priority );

		// Download rule
		add_rewrite_rule( $download_rule     . $root_rule, 'index.php?' . $download_id     . '=$matches[1]', $priority );
	}
}

/**
 * Return single instance of this main plugin class
 *
 * @since 1.0.0
 * 
 * @return Paascongres 2017 Content
 */
function paco2017_content() {
	return Paco2017_Content::instance();
}

// Initiate plugin on load
paco2017_content();

endif; // class_exists
