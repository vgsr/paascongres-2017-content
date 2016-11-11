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

		// Core
		require( $this->includes_dir . 'actions.php'      );
		require( $this->includes_dir . 'capabilities.php' );
		require( $this->includes_dir . 'functions.php'    );
		require( $this->includes_dir . 'lectores.php'     );
		require( $this->includes_dir . 'workshops.php'    );
		require( $this->includes_dir . 'sub-actions.php'  );

		// Extend
		require( $this->includes_dir . 'extend/buddypress/buddypress.php' );
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

		/** Lectores ****************************************************/

		register_post_type(
			paco2017_get_lector_post_type(),
			array(
				'labels'              => paco2017_get_lector_post_type_labels(),
				'supports'            => paco2017_get_lector_post_type_supports(),
				'description'         => __( 'Paascongres 2017 lectores', 'paco2017-content' ),
				'capabilities'        => paco2017_get_lector_post_type_caps(),
				'capability_type'     => array( 'paco2017_lector', 'paco2017_lectores' ),
				'hierarchical'        => false,
				'public'              => true,
				'has_archive'         => true,
				'rewrite'             => paco2017_get_lector_post_type_rewrite(),
				'query_var'           => true,
				'exclude_from_search' => false,
				'show_ui'             => current_user_can( 'paco2017_lector_admin' ),
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				// 'taxonomies'          => array( 'paco2017_lector_category' ),
				// 'menu_icon'           => 'dashicons-format-aside'
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
				// 'taxonomies'          => array( 'paco2017_workshop_category' ),
				// 'menu_icon'           => 'dashicons-format-aside'
			)
		);
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
