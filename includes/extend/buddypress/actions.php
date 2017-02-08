<?php

/**
 * Paco2017 Content BuddyPress Actions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Query *********************************************************************/

add_filter( 'posts_clauses', 'paco2017_bp_posts_clauses', 10, 2 );

/** Query: Members ************************************************************/

add_filter( 'bp_ajax_querystring',                         'paco2017_bp_ajax_query_string',                99, 2 );
add_filter( 'bp_after_has_members_parse_args',             'paco2017_bp_parse_has_members_args',           99    );
add_filter( 'bp_before_core_get_users_parse_args',         'paco2017_bp_parse_core_get_users_args',         1    );
add_action( 'bp_pre_user_query',                           'paco2017_bp_pre_user_query',                    5    );
add_action( 'bp_user_query_uid_clauses',                   'paco2017_bp_user_query_uid_clauses',           10, 2 );

add_filter( 'paco2017_is_user_enrolled',                   'paco2017_bp_xprofile_is_user_enrolled',        10, 2 );
add_filter( 'paco2017_get_enrolled_users_for_association', 'paco2017_bp_enrolled_members_for_association', 10, 2 );
add_filter( 'paco2017_get_workshop_enrolled_users',        'paco2017_bp_enrolled_members_for_workshop',    10, 2 );
add_filter( 'paco2017_get_user_workshops',                 'paco2017_bp_member_workshops',                 10, 2 );

/** Query: XProfile ***********************************************************/

add_filter( 'bp_xprofile_get_groups',                            'paco2017_bp_xprofile_no_edit_association_field', 10, 2 );
add_action( 'xprofile_data_after_save',                          'paco2017_bp_xprofile_sync_association_term'            );
add_action( 'xprofile_data_after_save',                          'paco2017_bp_xprofile_update_enrolled_users_cache'      );
add_filter( 'bp_xprofile_relationship_field_options_query_args', 'paco2017_bp_xprofile_workshop_options_args',     10, 3 );
add_filter( 'bp_xprofile_get_relationship_field_options',        'paco2017_bp_xprofile_workshop_options',          10, 4 );

/** Template: Activity ********************************************************/

add_filter( 'bp_get_activity_show_filters_options', 'paco2017_bp_activity_filters_options', 10, 2 );
add_filter( 'bp_activity_can_comment',              '__return_false'                              );
add_filter( 'bp_activity_can_favorite',             '__return_false'                              );

/** Template: Members *********************************************************/

add_action( 'bp_members_directory_member_types',    'paco2017_bp_members_directory_tabs'                );
add_action( 'bp_members_directory_order_options',   'paco2017_bp_members_directory_order_options'       );
add_action( 'bp_before_members_loop',               'paco2017_bp_members_directory_details'             );
add_filter( 'bp_members_pagination_count',          'paco2017_bp_members_pagination_count'              );
add_filter( 'bp_get_member_class',                  'paco2017_bp_get_member_class'                      );
add_filter( 'bp_member_name',                       'paco2017_bp_member_name',                    99    );
add_action( 'bp_directory_members_item',            'paco2017_bp_members_item_association_badge'        );
add_filter( 'bp_displayed_user_get_front_template', 'paco2017_bp_members_front_page_template'           );
add_action( 'bp_template_redirect',                 'paco2017_bp_members_block_member',            1    ); // Before bp_actions and bp_screens
add_action( 'bp_members_screen_display_profile',    'paco2017_bp_members_screen_display_profile'        );
add_action( 'bp_before_member_header_meta',         'paco2017_bp_members_item_association_badge'        );

/** Template: XProfile ********************************************************/

add_action( 'bp_before_profile_content',            'paco2017_the_advertorial'                              );
add_filter( 'paco2017_get_advertorial',             'paco2017_bp_xprofile_filter_advertorial',         2, 2 );
add_filter( 'bp_get_the_profile_field_description', 'paco2017_bp_xprofile_workshop_field_description'       );

/** Admin *********************************************************************/

if ( is_admin() ) {
	add_action( 'init', 'paco2017_buddypress_admin' );

	// Dashboard
	add_filter( 'paco2017_dashboard_statuses', 'paco2017_bp_members_dashboard_statuses' );

	// Settings
	add_filter( 'paco2017_admin_page_get_pages',        'paco2017_bp_add_settings_pages'    );
	add_filter( 'paco2017_admin_get_settings_sections', 'paco2017_bp_add_settings_sections' );
	add_filter( 'paco2017_admin_get_settings_fields',   'paco2017_bp_add_settings_fields'   );
}
