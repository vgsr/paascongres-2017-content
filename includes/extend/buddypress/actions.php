<?php

/**
 * Paco2017 Content BuddyPress Actions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Activity: Template
add_filter( 'bp_get_activity_show_filters_options', 'paco2017_bp_activity_filters_options', 10, 2 );
add_filter( 'bp_activity_can_comment',              '__return_false'                              );
add_filter( 'bp_activity_can_favorite',             '__return_false'                              );

// Members: Template
add_action( 'bp_members_directory_member_types',    'paco2017_bp_members_directory_tabs'                );
add_action( 'bp_members_directory_order_options',   'paco2017_bp_members_directory_order_options'       );
add_action( 'bp_before_members_loop',               'paco2017_bp_members_directory_details'             );
add_filter( 'bp_get_member_class',                  'paco2017_bp_get_member_class'                      );
add_filter( 'bp_member_name',                       'paco2017_bp_member_name',                    99    );
add_filter( 'bp_displayed_user_get_front_template', 'paco2017_bp_members_front_page_template'           );
add_action( 'bp_template_redirect',                 'paco2017_bp_members_block_member',            1    ); // Before bp_actions and bp_screens

// Members: Query
add_filter( 'bp_ajax_querystring',                  'paco2017_bp_ajax_query_string',              99, 2 );
add_filter( 'bp_after_has_members_parse_args',      'paco2017_bp_parse_has_members_args',         99    );
add_filter( 'bp_before_core_get_users_parse_args',  'paco2017_bp_parse_core_get_users_args',       1    );
add_action( 'bp_pre_user_query',                    'paco2017_bp_pre_user_query',                  5    );
add_action( 'bp_user_query_uid_clauses',            'paco2017_bp_user_query_uid_clauses',         10, 2 );

add_filter( 'paco2017_get_enrolled_users_count',           'paco2017_bp_get_enrolled_members_count'                  );
add_filter( 'paco2017_get_enrolled_users_for_association', 'paco2017_bp_get_enrolled_members_for_association', 10, 2 );

// XProfile
add_filter( 'bp_xprofile_get_groups',   'paco2017_bp_xprofile_no_edit_association_field', 10, 2 );
add_action( 'xprofile_data_after_save', 'paco2017_bp_xprofile_sync_association_term'            );

// Admin
if ( is_admin() ) {
	add_action( 'init', 'paco2017_buddypress_admin' );

	// Dashboard
	add_filter( 'paco2017_dashboard_statuses', 'paco2017_bp_members_dashboard_statuses' );

	// Settings
	add_filter( 'paco2017_admin_page_get_pages',        'paco2017_bp_add_settings_pages'    );
	add_filter( 'paco2017_admin_get_settings_sections', 'paco2017_bp_add_settings_sections' );
	add_filter( 'paco2017_admin_get_settings_fields',   'paco2017_bp_add_settings_fields'   );
}
