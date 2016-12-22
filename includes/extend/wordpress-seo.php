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
		$agenda      = paco2017_get_agenda_post_type();
		$association = paco2017_get_association_tax_id();
		$conf_day    = paco2017_get_conf_day_tax_id();
		$conf_loc    = paco2017_get_conf_location_tax_id();

		// Admin
		add_filter( "manage_{$agenda}_posts_columns",     array( $this, 'admin_remove_columns'   ), 99    );
		add_filter( "manage_edit-{$association}_columns", array( $this, 'admin_remove_columns'   ), 99    );
		add_filter( "manage_edit-{$conf_day}_columns",    array( $this, 'admin_remove_columns'   ), 99    );
		add_filter( "manage_edit-{$conf_loc}_columns",    array( $this, 'admin_remove_columns'   ), 99    );
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
		$agenda      = paco2017_get_agenda_post_type();
		$association = paco2017_get_association_tax_id();
		$conf_day    = paco2017_get_conf_day_tax_id();
		$conf_loc    = paco2017_get_conf_location_tax_id();

		// Override metabox setting
		$value["hideeditbox-{$agenda}"]          = true;
		$value["hideeditbox-tax-{$association}"] = true;
		$value["hideeditbox-tax-{$conf_day}"]    = true;
		$value["hideeditbox-tax-{$conf_loc}"]    = true;

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
