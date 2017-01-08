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

add_action( 'init',                 'paco2017_init'                        );
add_action( 'widgets_init',         'paco2017_widgets_init'                );
add_action( 'rest_api_init',        'paco2017_rest_api_init'               );
add_action( 'registered_post_type', 'paco2017_registered_post_type'        );
add_action( 'registered_taxonomy',  'paco2017_registered_taxonomy'         );
add_action( 'after_setup_theme',    'paco2017_after_setup_theme'           );
add_filter( 'map_meta_cap',         'paco2017_map_meta_caps',        10, 4 );

/** Taxonomy ******************************************************************/

add_filter( 'get_terms', 'paco2017_get_terms',                 10, 4 );
add_filter( 'term_link', 'paco2017_get_association_term_link', 10, 3 );

/** Template ******************************************************************/

add_action( 'parse_query',                 'paco2017_parse_agenda_query',          2 ); // Early for overrides
add_action( 'wp_enqueue_scripts',          'paco2017_enqueue_styles'                 );
add_filter( 'get_the_archive_title',       'paco2017_get_the_archive_title'          );
add_filter( 'get_the_archive_description', 'paco2017_get_the_archive_description'    );
add_filter( 'the_content',                 'paco2017_agenda_page_content',         2 );
add_filter( 'the_content',                 'paco2017_speakers_page_content',       2 );

/** Widgets *******************************************************************/

add_action( 'paco2017_widgets_init', array( 'Paco2017_Enrollments_Widget', 'register' ) );

/** Admin *********************************************************************/

if ( is_admin() ) {
	add_action( 'init', 'paco2017_admin' );
}

/** Extend ********************************************************************/

add_action( 'bp_core_loaded', 'paco2017_buddypress' );
add_action( 'paco2017_init',  'paco2017_wpseo'      );
