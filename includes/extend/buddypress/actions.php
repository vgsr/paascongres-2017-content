<?php

/**
 * Paco2017 Content BuddyPress Actions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Settings
add_filter( 'paco2017_admin_get_settings_sections', 'paco2017_bp_add_settings_sections' );
add_filter( 'paco2017_admin_get_settings_fields',   'paco2017_bp_add_settings_fields'   );
