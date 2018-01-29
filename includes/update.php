<?php

/**
 * Paco2017 Updater
 *
 * @package Paco2017
 * @subpackage Updater
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * If there is no raw DB version, this is the first installation
 *
 * @since 1.1.0
 *
 * @return bool True if update, False if not
 */
function paco2017_is_install() {
	return ! paco2017_get_db_version_raw();
}

/**
 * Compare the plugin version to the DB version to determine if updating
 *
 * @since 1.1.0
 *
 * @return bool True if update, False if not
 */
function paco2017_is_update() {
	$raw    = (int) paco2017_get_db_version_raw();
	$cur    = (int) paco2017_get_db_version();
	$retval = (bool) ( $raw < $cur );
	return $retval;
}

/**
 * Determine if the plugin is being activated
 *
 * Note that this function currently is not used in the plugin core and is here
 * for third party plugins to use to check for the plugin activation.
 *
 * @since 1.1.0
 *
 * @return bool True if activating the plugin, false if not
 */
function paco2017_is_activation( $basename = '' ) {
	global $pagenow;

	$plugin = paco2017_content();
	$action = false;

	// Bail when not in admin/plugins
	if ( ! ( is_admin() && ( 'plugins.php' === $pagenow ) ) ) {
		return false;
	}

	if ( ! empty( $_REQUEST['action'] ) && ( '-1' !== $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' !== $_REQUEST['action2'] ) ) {
		$action = $_REQUEST['action2'];
	}

	// Bail when not activating
	if ( empty( $action ) || ! in_array( $action, array( 'activate', 'activate-selected' ) ) ) {
		return false;
	}

	// The plugin(s) being activated
	if ( $action === 'activate' ) {
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	} else {
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	}

	// Set basename if empty
	if ( empty( $basename ) && ! empty( $plugin->basename ) ) {
		$basename = $plugin->basename;
	}

	// Bail when no basename
	if ( empty( $basename ) ) {
		return false;
	}

	// Is the plugin being activated?
	return in_array( $basename, $plugins );
}

/**
 * Determine if the plugin is being deactivated
 *
 * @since 1.1.0
 * 
 * @return bool True if deactivating plugin, false if not
 */
function paco2017_is_deactivation( $basename = '' ) {
	global $pagenow;

	$plugin = paco2017_content();
	$action = false;

	// Bail when not in admin/plugins
	if ( ! ( is_admin() && ( 'plugins.php' === $pagenow ) ) ) {
		return false;
	}

	if ( ! empty( $_REQUEST['action'] ) && ( '-1' !== $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' !== $_REQUEST['action2'] ) ) {
		$action = $_REQUEST['action2'];
	}

	// Bail when not deactivating
	if ( empty( $action ) || ! in_array( $action, array( 'deactivate', 'deactivate-selected' ) ) ) {
		return false;
	}

	// The plugin(s) being deactivated
	if ( $action === 'deactivate' ) {
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	} else {
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	}

	// Set basename if empty
	if ( empty( $basename ) && ! empty( $plugin->basename ) ) {
		$basename = $plugin->basename;
	}

	// Bail when no basename
	if ( empty( $basename ) ) {
		return false;
	}

	// Is the plugin being deactivated?
	return in_array( $basename, $plugins );
}

/**
 * Update the DB to the latest version
 *
 * @since 1.1.0
 */
function paco2017_version_bump() {
	update_site_option( '_paco2017_db_version', paco2017_get_db_version() );
}

/**
 * Setup the plugin updater
 *
 * @since 1.1.0
 */
function paco2017_setup_updater() {

	// Bail when no update needed
	if ( ! paco2017_is_update() )
		return;

	// Call the automated updater
	paco2017_version_updater();
}

/**
 * Plugin's version updater looks at what the current database version is, and
 * runs whatever other code is needed.
 *
 * This is most-often used when the data schema changes, but should also be used
 * to correct issues with the plugin meta-data silently on software update.
 *
 * @since 1.1.0
 */
function paco2017_version_updater() {

	// Get the raw database version
	$raw_db_version = (int) paco2017_get_db_version_raw();

	/** 1.0 Branch ********************************************************/

	// 1.0, 1.0.1, 1.1
	if ( $raw_db_version < 110 ) {
		paco2017_update_to_110();
	}

	/** All done! *********************************************************/

	// Bump the version
	paco2017_version_bump();
}

/** Upgrade Routines ******************************************************/

/**
 * 1.1.0 update routine
 *
 * - Rename site settings.
 *
 * @since 1.1.0
 *
 * @global $wpdb WPDB
 */
function paco2017_update_to_110() {
	global $wpdb;

	// Renaming map for site options
	$options = array(
		'_paco2017_housekeeping_page' => '_paco2017_general_notices_page'
	);

	// Rename site options
	foreach ( $options as $prev => $next ) {
		$wpdb->update(
			$wpdb->options,
			array( 'option_name' => $next ), // Set data
			array( 'option_name' => $prev ), // Where
			array( '%s' ), // Set data format
			array( '%s' ) // Where format
		);
	}
}
