<?php

/**
 * Paco2017 Content Agenda Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Post Type *****************************************************************/

/**
 * Return the Agenda post type
 *
 * @since 1.0.0
 *
 * @return string Post type name
 */
function paco2017_get_agenda_post_type() {
	return 'paco2017_agenda';
}

/**
 * Return the labels for the Agenda post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_agenda_post_type_labels'
 * @return array Agenda post type labels
 */
function paco2017_get_agenda_post_type_labels() {
	return apply_filters( 'paco2017_get_agenda_post_type_labels', array(
		'name'                  => __( 'Paascongres Agenda Items',       'paco2017-content' ),
		'menu_name'             => __( 'Agenda',                         'paco2017-content' ),
		'singular_name'         => __( 'Agenda Item',                    'paco2017-content' ),
		'all_items'             => __( 'All Agenda Items',               'paco2017-content' ),
		'add_new'               => __( 'New Agenda Item',                'paco2017-content' ),
		'add_new_item'          => __( 'Create New Agenda Item',         'paco2017-content' ),
		'edit'                  => __( 'Edit',                           'paco2017-content' ),
		'edit_item'             => __( 'Edit Agenda Item',               'paco2017-content' ),
		'new_item'              => __( 'New Agenda Item',                'paco2017-content' ),
		'view'                  => __( 'View Agenda Item',               'paco2017-content' ),
		'view_item'             => __( 'View Agenda Item',               'paco2017-content' ),
		'view_items'            => __( 'View Agenda Items',              'paco2017-content' ), // Since WP 4.7
		'search_items'          => __( 'Search Agenda Items',            'paco2017-content' ),
		'not_found'             => __( 'No agenda items found',          'paco2017-content' ),
		'not_found_in_trash'    => __( 'No agenda items found in Trash', 'paco2017-content' ),
		'insert_into_item'      => __( 'Insert into agenda item',        'paco2017-content' ),
		'uploaded_to_this_item' => __( 'Uploaded to this agenda item',   'paco2017-content' ),
		'filter_items_list'     => __( 'Filter agenda items list',       'paco2017-content' ),
		'items_list_navigation' => __( 'Agenda Items list navigation',   'paco2017-content' ),
		'items_list'            => __( 'Agenda Items list',              'paco2017-content' ),
	) );
}

/**
 * Return an array of features the Agenda post type supports
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_agenda_post_type_supports'
 * @return array Agenda post type support
 */
function paco2017_get_agenda_post_type_supports() {
	return apply_filters( 'paco2017_get_agenda_post_type_supports', array(
		'title',
		'editor',
		'thumbnail',
	) );
}

/**
 * Act when the Agenda post type has been registered
 *
 * @since 1.0.0
 */
function paco2017_registered_agenda_post_type() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_agenda_rest_fields' );
}

/**
 * Register REST fields for the Agenda post type
 *
 * @since 1.0.0
 */
function paco2017_register_agenda_rest_fields() {

	// Get assets
	$agenda = paco2017_get_agenda_post_type();

	// Start Time
	register_rest_field(
		$agenda,
		'time_start',
		array(
			'get_callback' => 'paco2017_get_agenda_rest_meta'
		)
	);

	// End Time
	register_rest_field(
		$agenda,
		'time_end',
		array(
			'get_callback' => 'paco2017_get_agenda_rest_meta'
		)
	);
}

/**
 * Return the value for an agenda meta REST API field
 *
 * @since 1.0.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Day term(s)
 */
function paco2017_get_agenda_rest_meta( $object, $meta, $request ) {
	return get_post_meta( $object['id'], $meta, true );
}

/** Taxonomy: Conference Day **************************************************/

/**
 * Return the Conference Day taxonomy
 *
 * @since 1.0.0
 *
 * @return string Taxonomy name
 */
function paco2017_get_conf_day_tax_id() {
	return 'paco2017_conf_day';
}

/**
 * Return the labels for the Conference Day taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_tax_labels'
 * @return array Conference Day taxonomy labels
 */
function paco2017_get_conf_day_tax_labels() {
	return apply_filters( 'paco2017_get_conf_day_tax_labels', array(
		'name'          => __( 'Paascongres Conference Days', 'paco2017-content' ),
		'menu_name'     => __( 'Conference Days',             'paco2017-content' ),
		'singular_name' => __( 'Conference Day',              'paco2017-content' ),
		'search_items'  => __( 'Search Conference Days',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => __( 'All Conference Days',         'paco2017-content' ),
		'no_items'      => __( 'No Conference Day',           'paco2017-content' ),
		'edit_item'     => __( 'Edit Conference Day',         'paco2017-content' ),
		'update_item'   => __( 'Update Conference Day',       'paco2017-content' ),
		'add_new_item'  => __( 'Add New Conference Day',      'paco2017-content' ),
		'new_item_name' => __( 'New Conference Day Name',     'paco2017-content' ),
		'view_item'     => __( 'View Conference Day',         'paco2017-content' )
	) );
}

/**
 * Act when the Conference Day taxonomy has been registered
 *
 * @since 1.0.0
 */
function paco2017_registered_conf_day_taxonomy() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_conf_day_rest_fields' );
}

/**
 * Register REST fields for the Conference Day taxonomy
 *
 * @since 1.0.0
 */
function paco2017_register_conf_day_rest_fields() {

	// Get assets
	$agenda = paco2017_get_agenda_post_type();

	// Add day to Agenda Item
	register_rest_field(
		$agenda,
		'conference_days',
		array(
			'get_callback' => 'paco2017_get_agenda_rest_conf_days'
		)
	);
}

/**
 * Return the value for the 'conference_days' agenda REST API field
 *
 * @since 1.0.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Day term(s)
 */
function paco2017_get_agenda_rest_conf_days( $object, $field_name, $request ) {
	return wp_get_object_terms( $object['id'], paco2017_get_conf_day_tax_id() );
}

/**
 * Return whether the given post has any or the given Conference Day
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to any term.
 * @return bool Object has a/the Conference Day
 */
function paco2017_object_has_conf_day( $post = 0, $term = 0 ) {
	return has_term( $term, paco2017_get_conf_day_tax_id(), $post );
}

/** Taxonomy: Conference Location *********************************************/

/**
 * Return the Conference Location taxonomy
 *
 * @since 1.0.0
 *
 * @return string Taxonomy name
 */
function paco2017_get_conf_location_tax_id() {
	return 'paco2017_conf_location';
}

/**
 * Return the labels for the Conference Location taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_location_tax_labels'
 * @return array Conference Location taxonomy labels
 */
function paco2017_get_conf_location_tax_labels() {
	return apply_filters( 'paco2017_get_conf_location_tax_labels', array(
		'name'          => __( 'Paascongres Conference Locations', 'paco2017-content' ),
		'menu_name'     => __( 'Locations',                        'paco2017-content' ),
		'singular_name' => __( 'Conference Location',              'paco2017-content' ),
		'search_items'  => __( 'Search Conference Locations',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => __( 'All Conference Locations',         'paco2017-content' ),
		'no_items'      => __( 'No Conference Location',           'paco2017-content' ),
		'edit_item'     => __( 'Edit Conference Location',         'paco2017-content' ),
		'update_item'   => __( 'Update Conference Location',       'paco2017-content' ),
		'add_new_item'  => __( 'Add New Conference Location',      'paco2017-content' ),
		'new_item_name' => __( 'New Conference Location Name',     'paco2017-content' ),
		'view_item'     => __( 'View Conference Location',         'paco2017-content' )
	) );
}

/**
 * Act when the Conference Location taxonomy has been registered
 *
 * @since 1.0.0
 */
function paco2017_registered_conf_location_taxonomy() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_conf_location_rest_fields' );
}

/**
 * Register REST fields for the Conference Location taxonomy
 *
 * @since 1.0.0
 */
function paco2017_register_conf_location_rest_fields() {

	// Get assets
	$workshop = paco2017_get_workshop_post_type();
	$agenda   = paco2017_get_agenda_post_type();

	// Add location to Workshop
	register_rest_field(
		$workshop,
		'conference_locations',
		array(
			'get_callback' => 'paco2017_get_agenda_rest_conf_locations'
		)
	);

	// Add location to Agenda Item
	register_rest_field(
		$agenda,
		'conference_locations',
		array(
			'get_callback' => 'paco2017_get_agenda_rest_conf_locations'
		)
	);
}

/**
 * Return the value for the 'conference_locations' agenda REST API field
 *
 * @since 1.0.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Location term(s)
 */
function paco2017_get_agenda_rest_conf_locations( $object, $field_name, $request ) {
	return wp_get_object_terms( $object['id'], paco2017_get_conf_location_tax_id() );
}

/**
 * Return whether the given post has any or the given Conference Location
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to any term.
 * @return bool Object has a/the Conference Location
 */
function paco2017_object_has_conf_location( $post = 0, $term = 0 ) {
	return has_term( $term, paco2017_get_conf_location_tax_id(), $post );
}

/** Query *********************************************************************/

/**
 * Setup and run the Agenda Items query
 *
 * @since 1.0.0
 *
 * @param array $args Query arguments.
 * @return bool Has the query returned any results?
 */
function paco2017_query_agenda_items( $args = array() ) {

	// Get query object
	$query = paco2017_content()->agenda_query;

	// Reset query defaults
	$query->in_the_loop  = false;
	$query->current_post = -1;
	$query->post_count   = 0;
	$query->post         = null;
	$query->posts        = array();

	// Define query args
	$query_args = wp_parse_args( $args, array(
		'paco2017_conf_day' => false,
		'post_type'         => paco2017_get_agenda_post_type(),
		'posts_per_page'    => -1,
		'paged'             => 1,
		'fields'            => 'all'
	) );

	// Run query to get the posts
	$query->query( $query_args );

	// Return whether the query has returned results
	return $query->have_posts();
}

/**
 * Return whether the query has Agenda Items to loop over
 *
 * @since 1.0.0
 *
 * @return bool Query has Agenda Items
 */
function paco2017_have_agenda_items() {

	// Has query a next post?
	$has_next = paco2017_content()->agenda_query->have_posts();

	// Clean up after ourselves
	if ( ! $has_next ) {
		wp_reset_postdata();
	}

	return $has_next;
}

/**
 * Setup next Agenda Item in the current loop
 *
 * @since 1.0.0
 *
 * @return bool Are we still in the loop?
 */
function paco2017_the_agenda_item() {
	return paco2017_content()->agenda_query->the_post();
}

/**
 * Return whether we're in the Agenda loop
 *
 * @since 1.0.0
 *
 * @return bool Are we in the Agenda loop?
 */
function paco2017_in_the_agenda_item_loop() {
	return paco2017_content()->agenda_query->in_the_loop;
}

/**
 * For the posts query parse the Agenda item options
 *
 * @since 1.0.0
 *
 * @param WP_Query $posts_query
 */
function paco2017_parse_agenda_query( $posts_query ) {

	// Bail when filters are suppressed on this query
	if ( true === $posts_query->get( 'suppress_filters' ) )
		return;

	// Bail when in admin
	if ( is_admin() )
		return;

	// Bail when this is not an Agenda query
	if ( ! in_array( paco2017_get_agenda_post_type(), (array) $posts_query->get( 'post_type', array() ) ) )
		return;

	// Always require items with a start time
	$meta_query   = (array) $posts_query->get( 'meta_query', array() );
	$meta_query['time_start'] = array(
		'key'     => 'time_start',
		'compare' => 'EXISTS'
	);
	$posts_query->set( 'meta_query', $meta_query );

	// Default to ordering by start time
	if ( ! $posts_query->get( 'orderby' ) ) {
		$posts_query->set( 'orderby', 'time_start' );
		$posts_query->set( 'order',   'ASC'        );
	}

	// By Conference Day
	if ( $day = $posts_query->get( 'paco2017_conf_day' ) ) {
		$tax_query   = (array) $posts_query->get( 'tax_query', array() );
		$tax_query[] = array(
			'taxonomy' => paco2017_get_conf_day_tax_id(),
			'terms'    => array( $day )
		);
		$posts_query->set( 'tax_query', $tax_query );
	}
}

/** Template ******************************************************************/

/**
 * Return whether the given post is the Agenda page
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @return bool Is this the Agenda page?
 */
function paco2017_is_agenda_page( $post = 0 ) {
	$post = get_post( $post );
	$is   = $post && paco2017_get_agenda_page_id() === $post->ID;

	return $is;
}

/**
 * Modify the content of the Agenda page
 *
 * @since 1.0.0
 *
 * @param string $content Post content
 * @return string Post content
 */
function paco2017_agenda_page_content( $content ) {

	// The Agenda page
	if ( is_page() && paco2017_is_agenda_page() ) {
		$content .= paco2017_get_agenda_content();
	}

	return $content;
}

/**
 * Return the Agenda's HTML content
 *
 * @since 1.0.0
 *
 * @return string Agenda HTML content
 */
function paco2017_get_agenda_content() {

	// Agenda post type
	$post_type = paco2017_get_agenda_post_type();

	// Bail when there are no agenda items
	if ( empty( wp_count_posts( $post_type )->publish ) )
		return $content;

	// Get conference days
	$conf_days = get_terms( array(
		'taxonomy'   => paco2017_get_conf_day_tax_id(),
		'hide_empty' => false
	) );
	$conf_day_item_count = array_sum( wp_list_pluck( $conf_days, 'count' ) );

	ob_start(); ?>

	<div class="paco2017-content paco2017-agenda">

		<?php if ( ! empty( $conf_days ) && ! empty( $conf_day_item_count ) ) : ?>

		<ul class="paco2017-conference-days">

			<?php foreach ( $conf_days as $conf_day ) : ?>

			<li class="conference-day">

				<h3 class="day-title"><?php paco2017_the_conf_day_title( $conf_day ); ?></h3>

				<?php if ( paco2017_has_conf_day_date( $conf_day ) ) : ?>
					<p class="day-date"><?php paco2017_the_conf_day_date( $conf_day ); ?></p>
				<?php endif; ?>

				<?php if ( paco2017_query_agenda_items( array( 'paco2017_conf_day' => $conf_day->term_id ) ) ) : ?>

				<?php paco2017_the_agenda_items_list(); ?>

				<?php else : ?>

				<p><?php esc_html_e( 'There are no agenda items scheduled for this day.', 'paco2017-content' ); ?></p>

				<?php endif; ?>

			</li>

			<?php endforeach; ?>

		</ul>

		<?php elseif ( paco2017_query_agenda_items() ) : ?>

		<?php paco2017_the_agenda_items_list(); ?>

		<?php else : ?>

		<p><?php esc_html_e( 'There are no agenda items scheduled.', 'paco2017-content' ); ?></p>

		<?php endif; ?>

	</div>

	<?php

	$agenda = ob_get_clean();

	return apply_filters( 'paco2017_get_agenda_content', $agenda, $conf_days );
}

/**
 * Output the HTML markup for the Agenda Items list
 *
 * Make sure `paco2017_query_agenda_items()` is called before calling this.
 *
 * @since 1.0.0
 */
function paco2017_the_agenda_items_list() { ?>

	<ul class="paco2017-agenda-items">

		<?php while ( paco2017_have_agenda_items() ) : paco2017_the_agenda_item(); ?>

		<li class="agenda-item">
			<div class="item-header">
				<span class="item-title"><?php the_title(); ?></span>
				<span class="item-timeslot"><?php paco2017_the_agenda_timeslot(); ?></span>
			</div>

			<div class="item-content"><?php
				the_content();
			?></div>

			<?php edit_post_link(
				sprintf(
					/* translators: %s: Name of current post */
					__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'paco2017-content' ),
					get_the_title()
				),
				'<p class="item-footer"><span class="edit-link">',
				'</span></p>'
			); ?>
		</li>

		<?php endwhile; ?>

	</ul>

	<?php
}

/**
 * Return the Agenda Item
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current post.
 * @return WP_Post|false Agenda Item post object or False when not found.
 */
function paco2017_get_agenda_item( $item = 0 ) {

	// Get the post
	$item = get_post( $item );

	// Return false when this is not an Agenda Item
	if ( ! $item || paco2017_get_agenda_post_type() !== $item->post_type ) {
		$item = false;
	}

	return $item;
}

/**
 * Output the Agenda Item's start time
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return string Agenda Item start time
 */
function paco2017_the_agenda_item_start_time( $item = 0 ) {
	echo paco2017_get_agenda_item_start_time( $item );
}

/**
 * Return the Agenda Item's start time
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_agenda_item_start_time'
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return string Agenda Item start time
 */
function paco2017_get_agenda_item_start_time( $item = 0 ) {
	$item = paco2017_get_agenda_item( $item );
	$time = '';

	if ( $item ) {
		$time = get_post_meta( $item->ID, 'time_start', true );
	}

	return apply_filters( 'paco2017_get_agenda_item_start_time', $time, $item );
}

/**
 * Output the Agenda Item's end time
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return string Agenda Item end time
 */
function paco2017_the_agenda_item_end_time( $item = 0 ) {
	echo paco2017_get_agenda_item_end_time( $item );
}

/**
 * Return the Agenda Item's end time
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_agenda_item_end_time'
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return string Agenda Item end time
 */
function paco2017_get_agenda_item_end_time( $item = 0 ) {
	$item = paco2017_get_agenda_item( $item );
	$time = '';

	if ( $item ) {
		$time = get_post_meta( $item->ID, 'time_end', true );
	}

	return apply_filters( 'paco2017_get_agenda_item_end_time', $time, $item );
}

/**
 * Output the Agenda Item's timeslot
 * 
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 */
function paco2017_the_agenda_timeslot( $item = 0 ) {
	echo paco2017_get_agenda_timeslot( $item );
}

/**
 * Output the Agenda Item's timeslot
 * 
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return string Agenda Item's timeslot
 */
function paco2017_get_agenda_timeslot( $item = 0 ) {
	$item = paco2017_get_agenda_item( $item );
	$timeslot = '';

	if ( $item ) {
		$timeslot  = paco2017_get_agenda_item_start_time( $item );
		$timeslot .= '&ndash;';
		$timeslot .= paco2017_get_agenda_item_end_time( $item );
	}

	return $timeslot;
}

/** Template: Conference Day **************************************************/

/**
 * Return the Conference Day item term
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $item Term object or ID or post object.
 * @param string $by Optional. Method to fetch term through `get_term_by()`. Defaults to 'id'.
 * @return WP_Term|false Conference Day term object or False when not found.
 */
function paco2017_get_conf_day( $item, $by = 'id' ) {

	// Default to the current post's item
	if ( empty( $item ) && paco2017_object_has_conf_day() ) {
		$terms = wp_get_object_terms( get_the_ID(), paco2017_get_conf_day_tax_id() );
		$item  = $terms[0];

	// Default to the provided post's item
	} elseif ( is_a( $item, 'WP_Post' ) && paco2017_object_has_conf_day( $item ) ) {
		$terms = wp_get_object_terms( $item->ID, paco2017_get_conf_day_tax_id() );
		$item  = $terms[0];

	// Get the term by id or slug
	} elseif ( ! $item instanceof WP_Term ) {
		$item = get_term_by( $by, $item, paco2017_get_conf_day_tax_id() );
	}

	// Reduce error to false
	if ( ! $item || is_wp_error( $item ) ) {
		$item = false;
	}

	return $item;
}

/**
 * Output the Conference Day title
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Term object or ID or related post object.
 */
function paco2017_the_conf_day_title( $term ) {
	echo paco2017_get_conf_day_title( $term );
}

/**
 * Return the Conference Day title
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_title'
 *
 * @param WP_Term|int|WP_Post $term Term object or ID or related post object.
 * @return string Term title
 */
function paco2017_get_conf_day_title( $term ) {
	$term  = paco2017_get_conf_day( $term );
	$title = '';

	if ( $term ) {
		$title = get_term_field( 'name', $term );
	}

	return apply_filters( 'paco2017_get_conf_day_title', $title, $term );
}

/**
 * Output the Conference Day content
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Term object or ID or related post object.
 */
function paco2017_the_conf_day_content( $term ) {
	echo paco2017_get_conf_day_content( $term );
}

/**
 * Return the Conference Day content
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_content'
 *
 * @param WP_Term|int|WP_Post $term Term object or ID or related post object.
 * @return string Term content
 */
function paco2017_get_conf_day_content( $term ) {
	$term    = paco2017_get_conf_day( $term );
	$content = '';

	if ( $term ) {
		$content = get_term_field( 'description', $term );
	}

	return apply_filters( 'paco2017_get_conf_day_content', $content, $term );
}

/**
 * Output the Conference Day mysql date string
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Term object or ID or related post object.
 */
function paco2017_the_conf_day_date_string( $term ) {
	echo paco2017_get_conf_day_date_string( $term );
}

/**
 * Return the Conference Day mysql date string
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_date_string'
 *
 * @param WP_Term|int|WP_Post $term Term object or ID or related post object.
 * @return int Term mysql date string
 */
function paco2017_get_conf_day_date_string( $term ) {
	$term = paco2017_get_conf_day( $term );
	$date = 0;

	if ( $term ) {
		$date = get_term_meta( $term->term_id, 'date', true );
	}

	return apply_filters( 'paco2017_get_conf_day_date_string', $date, $term );
}

/**
 * Output the Conference Day date
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Term object or ID or related post object.
 * @param string $format Optional. Date format. Defaults to the date format option.
 */
function paco2017_the_conf_day_date( $term, $format = null ) {
	echo paco2017_get_conf_day_date( $term, $format );
}

/**
 * Return the Conference Day date
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_date'
 *
 * @param WP_Term|int|WP_Post $term Term object or ID or related post object.
 * @param string $format Optional. Date format. Defaults to the date format option.
 * @return int Term date
 */
function paco2017_get_conf_day_date( $term, $format = null ) {
	$term        = paco2017_get_conf_day( $term );
	$date_string = paco2017_get_conf_day_date_string( $term );
	$date        = '';

	// Default to the date format option
	if ( null === $format ) {
		$format = get_option( 'date_format' );
	}

	if ( $term && $date_string ) {
		$date = mysql2date( $format, $date_string );
	}

	return apply_filters( 'paco2017_get_conf_day_date', $date, $term, $format );
}

/**
 * Return whether the Conference Day has a date
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $term Term object or ID or related post object.
 * @return bool Term has a date
 */
function paco2017_has_conf_day_date( $term ) {
	return (bool) paco2017_get_conf_day_date_string( $term );
}
