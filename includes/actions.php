<?php

/**
 * Paco2017 Content Actions
 *
 * @package Paco2017 Content
 * @subpackage Hooks
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Sub-actions ***************************************************************/

add_action( 'init',                        'paco2017_init'                              );
add_action( 'widgets_init',                'paco2017_widgets_init'                      );
add_action( 'rest_api_init',               'paco2017_rest_api_init'                     );
add_action( 'registered_post_type',        'paco2017_registered_post_type'              );
add_action( 'registered_taxonomy',         'paco2017_registered_taxonomy'               );
add_action( 'after_setup_theme',           'paco2017_after_setup_theme'                 );
add_filter( 'map_meta_cap',                'paco2017_map_meta_caps',              10, 4 );

/** Utility *******************************************************************/

add_action( 'paco2017_activation',         'paco2017_delete_rewrite_rules'              );
add_action( 'paco2017_deactivation',       'paco2017_delete_rewrite_rules'              );

/** Taxonomy ******************************************************************/

add_filter( 'get_terms',                   'paco2017_rest_get_terms',             10, 4 );
add_filter( 'term_link',                   'paco2017_get_association_term_link',  10, 3 );
add_filter( 'term_link',                   'paco2017_get_speaker_term_link',      10, 3 );

/** Query *********************************************************************/

add_action( 'parse_query',                 'paco2017_parse_query',                 2    ); // Early for overrides
add_action( 'parse_query',                 'paco2017_parse_agenda_query',         20    );
add_filter( 'posts_request',               'paco2017_filter_wp_query',            10, 2 );
add_filter( 'posts_pre_query',             'paco2017_bypass_wp_query',            10, 2 ); // Since WP 4.6
add_action( 'parse_request',               'paco2017_parse_download_request'            );

/** Template ******************************************************************/

add_action( 'paco2017_after_setup_theme',  'paco2017_load_theme_functions'              );
add_filter( 'document_title_parts',        'paco2017_document_title_parts'              ); // Since WP 4.4
add_filter( 'body_class',                  'paco2017_body_class'                        );
add_action( 'wp_enqueue_scripts',          'paco2017_enqueue_styles'                    );
add_filter( 'get_the_archive_title',       'paco2017_get_the_archive_title'             );
add_filter( 'get_the_archive_description', 'paco2017_get_the_archive_description'       );

// Content filters
add_filter( 'the_content', 'paco2017_workshop_post_content'      );
add_filter( 'the_content', 'paco2017_agenda_pre_post_content', 2 );
add_filter( 'the_content', 'paco2017_agenda_post_content'        );
add_filter( 'the_content', 'paco2017_speaker_post_content'       );

// Theme Compat
add_filter( 'template_include', 'paco2017_template_include_theme_supports', 10 );
add_filter( 'template_include', 'paco2017_template_include_theme_compat',   12 );

/** Magazine ******************************************************************/

add_filter( 'paco2017_check_download_access', 'paco2017_magazine_check_download_access',  10, 3 );
add_filter( 'paco2017_get_atf_url',           'paco2017_magazine_get_theme_download_url'        );

/** Menu **********************************************************************/

add_filter( 'customize_nav_menu_available_item_types', 'paco2017_customize_nav_menu_set_item_types'         );
add_filter( 'customize_nav_menu_available_items',      'paco2017_customize_nav_menu_available_items', 10, 4 );
add_filter( 'customize_nav_menu_searched_items',       'paco2017_customize_nav_menu_searched_items',  10, 2 );
add_filter( 'wp_setup_nav_menu_item',                  'paco2017_setup_nav_menu_item'                       );

/** Widgets *******************************************************************/

add_action( 'paco2017_widgets_init', array( 'Paco2017_Enrollments_Widget', 'register' ) );

/** Admin *********************************************************************/

if ( is_admin() ) {
	add_action( 'init', 'paco2017_admin' );
}

/** Extend ********************************************************************/

add_action( 'bp_core_loaded', 'paco2017_buddypress' );
add_action( 'paco2017_init',  'paco2017_wpseo'      );
