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

// Members
add_action( 'bp_pre_user_query',                   'paco2017_bp_pre_user_query',             5 );
add_action( 'bp_members_directory_member_types',   'paco2017_bp_members_directory_tabs'        );
add_filter( 'bp_after_has_members_parse_args',     'paco2017_bp_parse_has_members_args',    99 );
add_filter( 'bp_before_core_get_users_parse_args', 'paco2017_bp_parse_core_get_users_args',  1 );
add_action( 'bp_template_redirect',                'paco2017_bp_members_block_member',       1 ); // Before bp_actions and bp_screens
