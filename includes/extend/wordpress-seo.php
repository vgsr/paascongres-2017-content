<?php

/**
 * Paco2017 Content WP SEO Functions
 *
 * @package Paco2017 Content
 * @subpackage WP SEO
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Paco2017_WPSEO' ) ) :
/**
 * The Paco2017 WP SEO class
 *
 * @since 1.0.0
 */
class Paco2017_WPSEO {

	/**
	 * Setup this class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Bail when WP SEO is not active. Checking the constant,
		// because the plugin has no init sub-action of its own.
		if ( ! defined( 'WPSEO_VERSION' ) )
			return;

		$this->setup_actions();
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {

		// Plugin objects
		$association = paco2017_get_association_tax_id();

		// Admin
		add_filter( "manage_edit-{$association}_columns", array( $this, 'admin_remove_columns'   ), 99    );
		add_action( 'option_wpseo_titles',                array( $this, 'admin_remove_metaboxes' ), 10, 2 );
		add_action( 'site_option_wpseo_titles',           array( $this, 'admin_remove_metaboxes' ), 10, 2 );
	}

	/** Public methods **************************************************/

	/**
	 * Modify the admin list table columns
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Admin columns
	 * @return array Admin columns
	 */
	public function admin_remove_columns( $columns ) {

		// Walk registered columns
		foreach ( $columns as $column => $label ) {

			// Remove WP SEO column
			if ( false !== strpos( $column, 'wpseo' ) ) {
				unset( $columns[ $column ] );
			}
		}

		return $columns;
	}

	/**
	 * Modify the wpseo_titles option value
	 *
	 * @since 1.0.0
	 *
	 * @param array $value Option value
	 * @param string $option Option name
	 * @return array Option value
	 */
	public function admin_remove_metaboxes( $value, $option ) {

		// Plugin objects
		$association = paco2017_get_association_tax_id();

		// Override metabox setting
		$value["hideeditbox-tax-{$association}"] = true;

		return $value;
	}
}

/**
 * Setup the extension logic for BuddyPress
 *
 * @since 1.0.0
 *
 * @uses Paco2017_WPSEO
 */
function paco2017_wpseo() {
	paco2017_content()->extend->wpseo = new Paco2017_WPSEO;
}

endif; // class_exists
