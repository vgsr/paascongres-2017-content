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
 * Version:           1.0.0
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

		$this->version      = '1.0.0';

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

		// Languages
		$this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );

		/** Misc ********************************************************/

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
		require( $this->includes_dir . 'functions.php'    );
		require( $this->includes_dir . 'lectors.php'      );
		require( $this->includes_dir . 'workshops.php'    );
		require( $this->includes_dir . 'sub-actions.php'  );

		/** Classes *****************************************************/

		require( $this->includes_dir . 'classes/class-wp-term-meta-ui.php' );
		require( $this->includes_dir . 'classes/class-wp-term-colors.php'  );

		/** Widgets *****************************************************/

		require( $this->includes_dir . 'classes/class-paco2017-enrollments-widget.php'  );

		/** Admin *******************************************************/

		if ( is_admin() ) {
			require( $this->includes_dir . 'admin.php'     );
			require( $this->includes_dir . 'dashboard.php' );
			require( $this->includes_dir . 'settings.php'  );
		}

		/** Extend ******************************************************/

		require( $this->includes_dir . 'extend/buddypress/buddypress.php' );
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

		/** Lectors *****************************************************/

		register_post_type(
			paco2017_get_lector_post_type(),
			array(
				'labels'              => paco2017_get_lector_post_type_labels(),
				'supports'            => paco2017_get_lector_post_type_supports(),
				'description'         => __( 'Paascongres 2017 lectors', 'paco2017-content' ),
				'capabilities'        => paco2017_get_lector_post_type_caps(),
				'capability_type'     => array( 'paco2017_lector', 'paco2017_lectors' ),
				'hierarchical'        => false,
				'public'              => true,
				'has_archive'         => true,
				'rewrite'             => paco2017_get_lector_post_type_rewrite(),
				'query_var'           => true,
				'exclude_from_search' => false,
				'show_ui'             => current_user_can( 'paco2017_lector_admin' ),
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'show_in_rest'        => true,
				// 'taxonomies'          => array( 'paco2017_lector_category' ),
				'menu_icon'           => 'dashicons-businessman'
			)
		);

		/** Workshop ****************************************************/

		register_post_type(
			paco2017_get_workshop_post_type(),
			array(
				'labels'              => paco2017_get_workshop_post_type_labels(),
				'supports'            => paco2017_get_workshop_post_type_supports(),
				'description'         => __( 'Paascongres 2017 workshops', 'paco2017-content' ),
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
				'show_in_rest'        => true,
				// 'taxonomies'          => array( 'paco2017_workshop_category' ),
				'menu_icon'           => 'dashicons-admin-tools'
			)
		);

		/** Agenda ******************************************************/

		register_post_type(
			paco2017_get_agenda_post_type(),
			array(
				'labels'              => paco2017_get_agenda_post_type_labels(),
				'supports'            => paco2017_get_agenda_post_type_supports(),
				'description'         => __( 'Paascongres agenda items', 'paco2017-content' ),
				'capabilities'        => paco2017_get_agenda_post_type_caps(),
				'capability_type'     => array( 'paco2017_agenda', 'paco2017_agendas' ),
				'hierarchical'        => false,
				'public'              => true,
				'has_archive'         => false,
				'rewrite'             => false, // No rewriting necessary
				'query_var'           => false, // No query vars necessary
				'exclude_from_search' => true,
				'show_ui'             => current_user_can( 'paco2017_agenda_admin' ),
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'show_in_rest'        => true,
				'menu_icon'           => 'dashicons-calendar'
			)
		);
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
				'rewrite'               => false, // No rewriting necessary
				'query_var'             => false, // No query vars necessary
				'show_tagcloud'         => false,
				'show_in_quick_edit'    => true,
				'show_admin_column'     => false, // User taxonomies are not supported in WP
				'show_in_nav_menus'     => false,
				'show_ui'               => current_user_can( 'paco2017_association_admin' ),
				'meta_box_cb'           => false, // No metaboxing

				// Term meta
				'term_meta_color'       => true,
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
				'rewrite'               => false, // No rewriting necessary
				'query_var'             => false, // No query vars necessary
				'show_tagcloud'         => false,
				'show_in_quick_edit'    => true,
				'show_admin_column'     => true,
				'show_in_nav_menus'     => false,
				'show_ui'               => current_user_can( 'paco2017_conf_day_admin' ),
				'meta_box_cb'           => false, // No metaboxing

				// Term meta
				'term_meta_color'       => true,
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
				'term_meta_color'       => true,
			)
		);

		/** Meta ********************************************************/

		// Color
		add_filter( 'wp_term_color_get_taxonomies', function( $args ) {
			$args['term_meta_color'] = true;
			return $args;
		});

		new WP_Term_Colors( $this->file );
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
