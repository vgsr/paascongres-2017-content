<?php

/**
 * Paco2017 Content Template Functions
 * 
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Query *********************************************************************/

/**
 * Add checks for plugin conditions to parse_query action
 *
 * @since 1.0.0
 *
 * @param WP_Query $posts_query
 */
function paco2017_parse_query( $posts_query ) {

	// Bail when this is not the main loop
	if ( ! $posts_query->is_main_query() )
		return;

	// Bail when filters are suppressed on this query
	if ( true === $posts_query->get( 'suppress_filters' ) )
		return;

	// Bail when in admin
	if ( is_admin() )
		return;

	// Get plugin
	$plugin = paco2017_content();

	// Get query variables
	$is_agenda       = $posts_query->get( paco2017_get_agenda_rewrite_id()       );
	$is_associations = $posts_query->get( paco2017_get_associations_rewrite_id() );
	$is_speakers     = $posts_query->get( paco2017_get_speakers_rewrite_id()     );

	// Agenda page
	if ( ! empty( $is_agenda ) ) {

		// 404 and bail when Agenda Items are not returned in query
		if ( ! paco2017_query_agenda_items() ) {
			$posts_query->set_404();
			return;
		}

		// Looking at the agenda page
		$posts_query->paco2017_is_agenda = true;

		// Make sure 404 is not set
		$posts_query->is_404 = false;

		// Correct is_home variable
		$posts_query->is_home = false;

		// Define query result
		$posts_query->found_posts   = $plugin->agenda_query->found_posts;
		$posts_query->max_num_pages = $plugin->agenda_query->max_num_pages;

	// Associations page
	} elseif ( ! empty( $is_associations ) ) {

		// 404 and bail when Associations are not returned in query
		if ( ! paco2017_query_associations() ) {
			$posts_query->set_404();
			return;
		}

		// Looking at the associations page
		$posts_query->paco2017_is_associations = true;

		// Make sure 404 is not set
		$posts_query->is_404 = false;

		// Correct is_home variable
		$posts_query->is_home = false;

		// Define query result
		$posts_query->found_posts   = $plugin->association_query->found_terms;
		$posts_query->max_num_pages = $plugin->association_query->max_num_pages;

	// Speakers page
	} elseif ( ! empty( $is_speakers ) ) {

		// 404 and bail when Speakers are not returned in query
		if ( ! paco2017_query_speakers() ) {
			$posts_query->set_404();
			return;
		}

		// Looking at the speakers page
		$posts_query->paco2017_is_speakers = true;

		// Make sure 404 is not set
		$posts_query->is_404 = false;

		// Correct is_home variable
		$posts_query->is_home = false;

		// Define query result
		$posts_query->found_posts   = $plugin->speaker_query->found_terms;
		$posts_query->max_num_pages = $plugin->speaker_query->max_num_pages;
	}
}

/**
 * Handle custom query vars at parse_query action
 *
 * @since 1.0.0
 *
 * @param WP_Query $posts_query
 */
function paco2017_parse_query_vars( $posts_query ) {

	// Bail when filters are suppressed on this query
	if ( true === $posts_query->get( 'suppress_filters' ) )
		return;

	// Get query details
	$post_type    = (array) $posts_query->get( 'post_type' );
	$is_lectures  = paco2017_get_lecture_post_type()  === reset( $post_type );
	$is_workshpos = paco2017_get_workshop_post_type() === reset( $post_type );

	// Lecture or Workshop query
	if ( $is_lectures || $is_workshpos ) {

		// Default to ordering by page number in menu_order, then title
		if ( ! $posts_query->get( 'orderby' ) ) {
			$posts_query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		}
	}
}

/**
 * Overwrite the main WordPress query
 *
 * @since 1.0.0
 *
 * @param string $request SQL query
 * @param WP_Query $query Query object
 * @return string SQL query
 */
function paco2017_filter_wp_query( $request, $query ) {
	global $wpdb;

	// Bail when this is not the main query
	if ( ! $query->is_main_query() )
		return $request;

	// Bail when not displaying custom query results
	if ( ! paco2017_has_custom_query() )
		return $request;

	// Query for nothing and your chicks for free
	$request = "SELECT 1 FROM {$wpdb->posts} WHERE 0=1";

	return $request;
}

/**
 * Stop WordPress performing a DB query for its main loop
 *
 * @since 1.0.0
 *
 * @param null $retval Current return value
 * @param WP_Query $query Query object
 * @return null|array
 */
function paco2017_bypass_wp_query( $retval, $query ) {

	// Bail when this is not the main query
	if ( ! $query->is_main_query() )
		return $retval;

	// Bail when not displaying custom query results
	if ( ! paco2017_has_custom_query() )
		return $retval;

	// Return something other than a null value to bypass WP_Query
	return array();
}

/**
 * Return whether the current page uses a custom query
 *
 * @since 1.0.0
 *
 * @return bool Page uses custom query?
 */
function paco2017_has_custom_query() {

	// Define return value
	$retval = false;

	// Agenda page
	if ( paco2017_is_agenda() && paco2017_have_agenda_items()  ) {
		$retval = true;

	// Associations page
	} elseif ( paco2017_is_associations() && paco2017_have_associations() ) {
		$retval = true;

	// Speakers page
	} elseif ( paco2017_is_speakers()  && paco2017_have_speakers() ) {
		$retval = true;
	}

	return $retval;
}

/**
 * Modify the adjacent post's WHERE query clause
 *
 * Custom menu order is assumed to be set for the Lecture
 * and Workshop post types.
 *
 * @since 1.1.0
 *
 * @param string $where          The `WHERE` clause in the SQL.
 * @param bool   $in_same_term   Whether post should be in a same taxonomy term.
 * @param array  $excluded_terms Array of excluded term IDs.
 * @param string $taxonomy       Taxonomy. Used to identify the term used when `$in_same_term` is true.
 * @param WP_Post $post          WP_Post object. Added in WP 4.4
 * @return string WHERE clause
 */
function paco2017_get_adjacent_post_where( $where, $in_same_term, $excluded_terms, $taxonomy, $post = null ) {

	// Get the post
	$post = get_post( $post );

	// Menu-ordered post types
	if ( in_array( $post->post_type, array(
		paco2017_get_lecture_post_type(),
		paco2017_get_workshop_post_type()
	) ) ) {
		global $wpdb;

		$previous = ( 'get_previous_post_where' === current_filter() );
		$op = $previous ? '<' : '>';

		/**
		 * Replace the `p.post_date` WHERE clause with a comparison based
		 * on the menu order.
		 */
		$original = $wpdb->prepare( "WHERE p.post_date $op %s",  $post->post_date  );
		$improved = $wpdb->prepare( "WHERE p.menu_order $op %s", $post->menu_order );
		$where    = str_replace( $original, $improved, $where );
	}

	return $where;
}

/**
 * Modify the adjacent post's ORDER BY query clause
 *
 * Custom menu order is assumed to be set for the Lecture
 * and Workshop post types.
 *
 * @since 1.1.0
 *
 * @param string $order_by The `ORDER BY` clause in the SQL.
 * @param WP_Post $post    WP_Post object. Added in WP 4.4
 * @return string ORDER BY clause
 */
function paco2017_get_adjacent_post_sort( $order_by, $post = null ) {

	// Get the post
	$post = get_post( $post );

	// Menu-ordered post types
	if ( in_array( $post->post_type, array(
		paco2017_get_lecture_post_type(),
		paco2017_get_workshop_post_type()
	) ) ) {

		// Order by the post menu order
		$order_by = str_replace( 'p.post_date', 'p.menu_order', $order_by );
	}

	return $order_by;
}

/** Is_* **********************************************************************/

/**
 * Check if current page is a Lecture page
 *
 * @since 1.0.0
 *
 * @return bool Is it a Lecture page?
 */
function paco2017_is_lecture() {

	// Assume false
	$retval = false;

	// Single lecture
	if ( is_singular( paco2017_get_lecture_post_type() ) ) {
		$retval = true;
	}

	return (bool) $retval;
}

/**
 * Check if current page is the Lecture archive
 *
 * @since 1.0.0
 *
 * @return bool Is it the Lecture archive?
 */
function paco2017_is_lecture_archive() {

	// Assume false
	$retval = false;

	// Lecture post type archive
	if ( is_post_type_archive( paco2017_get_lecture_post_type() ) ) {
		$retval = true;
	}

	return (bool) $retval;
}

/**
 * Check if current page is a Workshop page
 *
 * @since 1.0.0
 *
 * @return bool Is it a Workshop page?
 */
function paco2017_is_workshop() {

	// Assume false
	$retval = false;

	// Single workshop
	if ( is_singular( paco2017_get_workshop_post_type() ) ) {
		$retval = true;
	}

	return (bool) $retval;
}

/**
 * Check if current page is the Workshop archive
 *
 * @since 1.0.0
 *
 * @return bool Is it the Workshop archive?
 */
function paco2017_is_workshop_archive() {

	// Assume false
	$retval = false;

	// Workshop post type archive
	if ( is_post_type_archive( paco2017_get_workshop_post_type() ) ) {
		$retval = true;
	}

	return (bool) $retval;
}

/**
 * Check if current page is the Workshop Category archive
 *
 * @since 1.0.0
 *
 * @return bool Is it the Workshop Category archive?
 */
function paco2017_is_workshop_category() {

	// Assume false
	$retval = false;

	// Workshop Category archive
	if ( is_tax( paco2017_get_workshop_cat_tax_id() ) ) {
		$retval = true;
	}

	return (bool) $retval;
}

/**
 * Check if current page is the Workshop Round archive
 *
 * @since 1.1.0
 *
 * @return bool Is it the Workshop Round archive?
 */
function paco2017_is_workshop_round() {

	// Assume false
	$retval = false;

	// Workshop Round archive
	if ( is_tax( paco2017_get_workshop_round_tax_id() ) ) {
		$retval = true;
	}

	return (bool) $retval;
}

/**
 * Check if current page is the Agenda page
 *
 * @since 1.0.0
 *
 * @global WP_Query $wp_query To check if WP_Query::paco2017_is_agenda is true
 * @return bool Is it the Agenda page?
 */
function paco2017_is_agenda() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( ! empty( $wp_query->paco2017_is_agenda ) && ( true === $wp_query->paco2017_is_agenda ) ) {
		$retval = true;
	}

	return (bool) $retval;
}

/**
 * Check if current page is the Associations page
 *
 * @since 1.0.0
 *
 * @return bool Is it the Associations page?
 */
function paco2017_is_associations() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( ! empty( $wp_query->paco2017_is_associations ) && ( true === $wp_query->paco2017_is_associations ) ) {
		$retval = true;
	}

	return (bool) $retval;
}

/**
 * Check if current page is the Speakers page
 *
 * @since 1.0.0
 *
 * @global WP_Query $wp_query To check if WP_Query::paco2017_is_speakers is true
 * @return bool Is it the Speakers page?
 */
function paco2017_is_speakers() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( ! empty( $wp_query->paco2017_is_speakers ) && ( true === $wp_query->paco2017_is_speakers ) ) {
		$retval = true;
	}

	return (bool) $retval;
}

/**
 * Modify the page's body class
 *
 * @since 1.0.0
 *
 * @param array $wp_classes Body classes
 * @param array $custom_classes Additional classes
 * @return array Body classes
 */
function paco2017_body_class( $wp_classes, $custom_classes = false ) {

	// Define local var
	$paco2017_classes = array();

	/** Post Types ************************************************************/

	if ( paco2017_is_lecture() ) {
		$paco2017_classes[] = 'paco2017-lecture';

	} elseif ( paco2017_is_lecture_archive() ) {
		$paco2017_classes[] = 'paco2017-lecture-archive';

	} elseif ( paco2017_is_workshop() ) {
		$paco2017_classes[] = 'paco2017-workshop';

	} elseif ( paco2017_is_workshop_archive() ) {
		$paco2017_classes[] = 'paco2017-workshop-archive';

	} elseif ( paco2017_is_workshop_category() ) {
		$paco2017_classes[] = 'paco2017-workshop-category';

	} elseif ( paco2017_is_workshop_round() ) {
		$paco2017_classes[] = 'paco2017-workshop-round';

	/** Pages *****************************************************************/

	} elseif ( paco2017_is_agenda() ) {
		$paco2017_classes[] = 'paco2017-agenda';

	} elseif ( paco2017_is_associations() ) {
		$paco2017_classes[] = 'paco2017-associations';

	} elseif ( paco2017_is_speakers() ) {
		$paco2017_classes[] = 'paco2017-speakers';
	}

	/** Clean up **************************************************************/

	// Add plugin class when on a plugin page
	if ( ! empty( $paco2017_classes ) ) {
		$paco2017_classes[] = 'paco2017';
	}

	// Merge WP classes with plugin classes and remove duplicates
	$classes = array_unique( array_merge( (array) $wp_classes, $paco2017_classes ) );

	return $classes;
}

/**
 * Use the is_() functions to return if on any plugin page
 *
 * @since 1.0.0
 *
 * @return bool On a plugin page
 */
function is_paascongres() {

	// Default to false
	$retval = false;

	/** Post Types ************************************************************/

	if ( paco2017_is_lecture() ) {
		$retval = true;

	} elseif ( paco2017_is_lecture_archive() ) {
		$retval = true;

	} elseif ( paco2017_is_workshop() ) {
		$retval = true;

	} elseif ( paco2017_is_workshop_archive() ) {
		$retval = true;

	} elseif ( paco2017_is_workshop_category() ) {
		$retval = true;

	} elseif ( paco2017_is_workshop_round() ) {
		$retval = true;

	/** Pages *****************************************************************/

	} elseif ( paco2017_is_agenda() ) {
		$retval = true;

	} elseif ( paco2017_is_associations() ) {
		$retval = true;

	} elseif ( paco2017_is_speakers() ) {
		$retval = true;
	}

	return $retval;
}

/** Theme *********************************************************************/

/**
 * Filter the theme's template for supporting themes
 *
 * @since 1.0.0
 *
 * @param string $template Path to template file
 * @return string Path to template file
 */
function paco2017_template_include_theme_supports( $template = '' ) {

	// Define local var
	$_template = '';

	// Agenda page
	if (     paco2017_is_agenda()       && ( $_template = paco2017_get_agenda_template()       ) ) :

	// Associations page
	elseif ( paco2017_is_associations() && ( $_template = paco2017_get_associations_template() ) ) :

	// Speakers page
	elseif ( paco2017_is_speakers()     && ( $_template = paco2017_get_speakers_template()     ) ) :
	endif;

	// Set included template file
	if ( ! empty( $_template ) ) {
		$template = paco2017_set_template_included( $_template );

		// Provide dummy post global, but theme compat is not active
		paco2017_theme_compat_reset_post();
		paco2017_set_theme_compat_active( false );
	}

	return $template;
}

/**
 * Set the included template
 *
 * @since 1.0.0
 *
 * @param string|bool $template Path to template file. Defaults to false.
 * @return string|bool Path to template file. False if empty.
 */
function paco2017_set_template_included( $template = false ) {
	paco2017_content()->theme_compat->paco2017_template = $template;

	return paco2017_content()->theme_compat->paco2017_template;
}

/**
 * Return whether a template is included
 *
 * @since 1.0.0
 *
 * @return bool Template is included.
 */
function paco2017_is_template_included() {
	return ! empty( paco2017_content()->theme_compat->paco2017_template );
}

/**
 * Retreive path to a template
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_{$type}_template'
 *
 * @param string $type Filename without extension.
 * @param array $templates Optional. Template candidates.
 * @return string Path to template file
 */
function paco2017_get_query_template( $type, $templates = array() ) {
	$type = preg_replace( '|[^a-z0-9-]+|', '', $type );

	// Fallback file
	if ( empty( $templates ) ) {
		$templates = array( "{$type}.php" );
	}

	// Locate template file
	$template = paco2017_locate_template( $templates );

	return apply_filters( "paco2017_{$type}_template", $template );
}

/**
 * Locate and return the Agenda page template
 *
 * @since 1.0.0
 *
 * @return string Path to template file
 */
function paco2017_get_agenda_template() {
	$templates = array(
		'paco2017-agenda.php',    // Current Agenda page
		'paascongres-agenda.php', // Agenda page
	);

	return paco2017_get_query_template( 'paco2017-agenda', $templates );
}

/**
 * Locate and return the Associations page template
 *
 * @since 1.0.0
 *
 * @return string Path to template file
 */
function paco2017_get_associations_template() {
	$templates = array(
		'paco2017-associations.php',    // Current Associations page
		'paascongres-associations.php', // Associations page
	);

	return paco2017_get_query_template( 'paco2017-associations', $templates );
}

/**
 * Locate and return the Speakers page template
 *
 * @since 1.0.0
 *
 * @return string Path to template file
 */
function paco2017_get_speakers_template() {
	$templates = array(
		'paco2017-speakers.php',    // Current Speakers page
		'paascongres-speakers.php', // Speakers page
	);

	return paco2017_get_query_template( 'paco2017-speakers', $templates );
}

/**
 * Locate and return the generic plugin page template
 *
 * @since 1.0.0
 *
 * @return string Path to template file
 */
function paco2017_get_theme_compat_template() {
	$templates = array(
		'paco2017-compat.php',
		'paascongres.php'
	);

	// Prefer page.php for page'd posts
	if ( $GLOBALS['post']->is_page ) {
		$templates[] = 'page.php';
	}

	// Append generic templates
	$templates = array_merge( $templates, array(
		'generic.php',
		'single.php',
		'page.php',
		'index.php'
	) );

	return paco2017_get_query_template( 'paco2017-compat', $templates );
}

/** Archives ******************************************************************/

/**
 * Modify the document title parts for plugin pages
 *
 * @since 1.0.0
 *
 * @param array $title Title parts
 * @return array Title parts
 */
function paco2017_document_title_parts( $title = array() ) {

	// Define local var
	$_title = '';

	// Agenda page
	if ( paco2017_is_agenda() ) {
		$_title = esc_html_x( 'Agenda', 'agenda page title', 'paco2017-content' );

	// Associations page
	} elseif ( paco2017_is_associations() ) {
		$_title = esc_html_x( 'Associations', 'associations page title', 'paco2017-content' );

	// Speakers page
	} elseif ( paco2017_is_speakers() ) {
		$_title = esc_html_x( 'Speakers', 'speakers page title', 'paco2017-content' );
	}

	// Overwrite document title
	if ( ! empty( $_title ) ) {
		$title['title'] = $_title;
	}

	return $title;
}

/**
 * Return the plugin archive title
 *
 * @since 1.0.0
 *
 * @param string $title Archive title
 * @return string Archive title
 */
function paco2017_get_the_archive_title( $title = '' ) {

	// Reset post type archive title, without the 'Archives: ' prefix
	if ( paco2017_is_lecture_archive() || paco2017_is_workshop_archive() ) {
		$title = post_type_archive_title( '', false );
	}

	return $title;
}

/**
 * Return the plugin archive description
 *
 * @since 1.0.0
 *
 * @param string $description Archive description
 * @return string Archive description
 */
function paco2017_get_the_archive_description( $description = '' ) {

	// Lectures archive
	if ( paco2017_is_lecture_archive() ) {
		$description = get_option( '_paco2017_lecture_archive_desc', '' );

	// Workshops archive
	} elseif ( paco2017_is_workshop_archive() ) {
		$description = get_option( '_paco2017_workshop_archive_desc', '' );

	// Agenda page
	} elseif ( paco2017_is_agenda() ) {
		$description = get_option( '_paco2017_agenda_page_desc', '' );

	// Speakers page
	} elseif ( paco2017_is_speakers() ) {
		$description = get_option( '_paco2017_speakers_page_desc', '' );

	// Associations page
	} elseif ( paco2017_is_associations() ) {
		$description = get_option( '_paco2017_associations_page_desc', '' );
	}

	return $description;
}
