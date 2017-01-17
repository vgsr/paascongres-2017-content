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
		'paco2017_conf_day' => paco2017_get_conf_day(),
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
			'terms'    => array( is_a( $day, 'WP_Term' ) ? $day->term_id : $day )
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
 * Modify the content of an Agenda item
 *
 * @since 1.0.0
 *
 * @param string $content Post content
 * @return string Post content
 */
function paco2017_agenda_post_content( $content ) {

	// An Agenda item with related object
	if ( paco2017_is_agenda_item() && paco2017_is_agenda_related() ) {
		$content .= ' <a href="' . esc_url( paco2017_get_agenda_related_url() ) . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'paco2017-content' ) . '</a>';

	// An object related with Agenda item
	} elseif ( paco2017_agenda_is_object_related() ) {
		$content = paco2017_agenda_get_object_related_item_info() . $content;
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

	// Bail when there are no agenda items
	if ( empty( wp_count_posts( paco2017_get_agenda_post_type() )->publish ) )
		return '';

	ob_start(); ?>

	<div class="paco2017-content paco2017-agenda">

		<?php if ( paco2017_query_conf_days() ) : ?>

		<ul class="paco2017-conference-days">

			<?php while ( paco2017_have_conf_days() ) : paco2017_the_conf_day() ?>

			<li class="conference-day">

				<h3 class="day-title"><?php paco2017_the_conf_day_title(); ?></h3>

				<?php if ( paco2017_has_conf_day_date() ) : ?>
					<p class="day-date"><?php paco2017_the_conf_day_date(); ?></p>
				<?php endif; ?>

				<?php if ( paco2017_query_agenda_items() ) : ?>

				<?php paco2017_the_agenda_items_list(); ?>

				<?php else : ?>

				<p><?php esc_html_e( 'There are no items scheduled for this day.', 'paco2017-content' ); ?></p>

				<?php endif; ?>

			</li>

			<?php endforeach; ?>

		</ul>

		<?php elseif ( paco2017_query_agenda_items() ) : ?>

		<?php paco2017_the_agenda_items_list(); ?>

		<?php else : ?>

		<p><?php esc_html_e( 'There are no items scheduled.', 'paco2017-content' ); ?></p>

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

		<li class="agenda-item <?php if ( paco2017_is_agenda_related() ) echo 'is-related'; ?>">
			<div class="item-header">
				<h4 class="item-title">
					<?php
						if ( paco2017_is_agenda_related() ) :
							the_title( '<a href="' . esc_url( paco2017_get_agenda_related_url() ) . '">', '</a>' );
						else :
							the_title();
						endif;
					?>
				</h4>
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
 * Return the HTML markup for the object's related Agenda Item info
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return string Agenda Item info HTML content
 */
function paco2017_agenda_get_object_related_item_info( $item = 0 ) {

	// Bail when there's no Agenda item
	if ( ! $item = paco2017_get_agenda_item( $item ) )
		return;

	ob_start(); ?>

	<div class="agenda-info"><p><?php
		if ( paco2017_has_conf_day_date( $item ) ) {
			printf(
				__( 'This item is scheduled for %1$s at %2$s.', 'paco2017-content' ),
				paco2017_get_conf_day_date( $item ),
				paco2017_get_agenda_item_start_time( $item )
			);
		} else {
			printf(
				__( 'This item is scheduled at %2$s.', 'paco2017-content' ),
				paco2017_get_agenda_item_start_time( $item )
			);
		}
	?></p></div>

	<?php

	return ob_get_clean();
}

/**
 * Return whether the current page is an Agenda Item
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current post.
 * @return bool Is this an Agenda Item?
 */
function paco2017_is_agenda_item( $post = 0 ) {
	if ( ! $post = get_post( $post ) )
		return false;

	return ( paco2017_get_agenda_post_type() === $post->post_type );
}

/**
 * Return the Agenda Item
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current post.
 * @return WP_Post|bool Agenda Item post object or False when not found.
 */
function paco2017_get_agenda_item( $item = 0 ) {

	// Get the post
	$item = get_post( $item );

	// Return false when this is not an Agenda Item
	if ( ! $item || paco2017_get_agenda_post_type() !== $item->post_type ) {

		// Try to fetch the item from related object
		if ( $item && paco2017_agenda_is_object_related( $item ) ) {
			$item = paco2017_agenda_get_object_related_item( $item );
		} else {
			$item = false;
		}
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

/**
 * Output the Agenda Item's related object ID
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 */
function paco2017_the_agenda_related_id( $item = 0 ) {
	echo paco2017_get_agenda_related_id( $item );
}

/**
 * Return the Agenda Item's related object ID
 *
 * @since 1.0.0
 *
 * @uses apply_filters Calls 'paco2017_get_agenda_related_id'
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return int Related object ID
 */
function paco2017_get_agenda_related_id( $item = 0 ) {
	$item    = paco2017_get_agenda_item( $item );
	$related = 0;

	if ( $item ) {
		$related = (int) get_post_meta( $item->ID, 'related', true );
	}

	return (int) apply_filters( 'paco2017_get_agenda_related_id', $related, $item );
}

/**
 * Return whether the Agenda Item is related to an object
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 */
function paco2017_is_agenda_related( $item = 0 ) {
	return (bool) paco2017_get_agenda_related_id( $item );
}

/**
 * Return the Agenda Item's related post object
 *
 * @since 1.0.0
 *
 * @uses apply_filters Calls 'paco2017_get_agenda_related'
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return WP_Post Related post object
 */
function paco2017_get_agenda_related( $item = 0 ) {
	$item    = paco2017_get_agenda_item( $item );
	$related = paco2017_get_agenda_related_id( $item );

	if ( $item && $related ) {
		$related = get_post( $related );
	}

	return apply_filters( 'paco2017_get_agenda_related', $related, $item );
}

/**
 * Return the Agenda Item ID for the related object
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @return int Related Agenda Item ID. 
 */
function paco2017_agenda_get_object_related_item_id( $post = 0 ) {
	$item    = paco2017_agenda_get_object_related_item( $post );
	$item_id = 0;

	if ( $item ) {
		$item_id = $item->ID;
	}

	return $item_id;
}

/**
 * Return the Agenda Item object for the related object
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_agenda_get_object_related_item'
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @return WP_Post Related Agenda Item object.
 */
function paco2017_agenda_get_object_related_item( $post = 0 ) {
	if ( ! $post = get_post( $post ) )
		return false;

	$item = false;

	if ( $query = new WP_Query( array(
		'post_type'  => paco2017_get_agenda_post_type(),
		'meta_query' => array(
			array(
				'key'   => 'related',
				'value' => $post->ID
			)
		)
	) ) ) {
		if ( $query->posts ) {
			$item = $query->posts[0];
		}
	}

	return apply_filters( 'paco2017_agenda_get_object_related_item', $item, $post );
}

/**
 * Return whether the object is related to an/the given Agenda Item
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return bool Is object related to an/the Agenda Item?
 */
function paco2017_agenda_is_object_related( $post = 0, $item = null ) {
	$related = paco2017_agenda_get_object_related_item( $post );

	if ( $related && null !== $item ) {
		$item = paco2017_get_agenda_item( $item );

		if ( $item ) {
			$related = ( $related->ID === $item->ID );
		}
	}

	return (bool) $related;
}

/**
 * Output the Agenda Item's related object url
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 */
function paco2017_the_agenda_related_url( $item = 0 ) {
	echo paco2017_get_agenda_related_url( $item );
}

/**
 * Return the Agenda Item's related object url
 *
 * @since 1.0.0
 *
 * @uses apply_filters Calls 'paco2017_get_agenda_related_url'
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return string Related post object url
 */
function paco2017_get_agenda_related_url( $item = 0 ) {
	$item    = paco2017_get_agenda_item( $item );
	$related = paco2017_get_agenda_related( $item );
	$url     = '';

	if ( $item && $related ) {
		$url = get_permalink( $related );
	}

	return apply_filters( 'paco2017_get_agenda_related_url', $url, $related, $item );
}

/**
 * Output the Agenda Item's related object link
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 */
function paco2017_the_agenda_related_link( $item = 0 ) {
	echo paco2017_get_agenda_related_link( $item );
}

/**
 * Return the Agenda Item's related object link
 *
 * @since 1.0.0
 *
 * @uses apply_filters Calls 'paco2017_get_agenda_related_link'
 *
 * @param WP_Post|int $item Optional. Post object or ID. Defaults to the current item.
 * @return string Related post object link
 */
function paco2017_get_agenda_related_link( $item = 0 ) {
	$item    = paco2017_get_agenda_item( $item );
	$related = paco2017_get_agenda_related( $item );
	$link     = '';

	if ( $item && $related ) {
		$link = '<a href="' . esc_url( get_permalink( $related ) ) . '">' . get_the_title( $related ) . '</a>';
	}

	return apply_filters( 'paco2017_get_agenda_related_link', $link, $related, $item );
}

/**
 * Retrieve or display list of Agenda relatable pages as a dropdown (select list).
 *
 * @since 1.0.0
 *
 * @param array|string $args {@see wp_dropdown_pages()}
 * @return string HTML content, if not displaying.
 */
function paco2017_dropdown_agenda_pages( $args = '' ) {
	$defaults = array(
		'depth' => 0,
		'selected' => 0, 'echo' => 1,
		'name' => 'page_id', 'id' => '',
		'class' => '',
		'show_option_none' => '', 'show_option_no_change' => '',
		'option_none_value' => '',
		'value_field' => 'ID',
		'post_type' => array(),
	);

	$r = wp_parse_args( $args, $defaults );

	if ( empty( $r['post_type'] ) ) {
		$r['post_type'] = array( 'page', paco2017_get_lecture_post_type() );
	}

	// Exclude params before querying
	$q = array_diff_key( $r, array_flip( array( 'depth', 'selected', 'echo', 'name', 'id' ) ) );
	$pages = new WP_Query( $q );
	$pages = $pages->posts;

	$output = '';
	// Back-compat with old system where both id and name were based on $name argument
	if ( empty( $r['id'] ) ) {
		$r['id'] = $r['name'];
	}

	if ( ! empty( $pages ) ) {
		$class = '';
		if ( ! empty( $r['class'] ) ) {
			$class = " class='" . esc_attr( $r['class'] ) . "'";
		}

		$output = "<select name='" . esc_attr( $r['name'] ) . "'" . $class . " id='" . esc_attr( $r['id'] ) . "'>\n";
		if ( $r['show_option_no_change'] ) {
			$output .= "\t<option value=\"-1\">" . $r['show_option_no_change'] . "</option>\n";
		}
		if ( $r['show_option_none'] ) {
			$output .= "\t<option value=\"" . esc_attr( $r['option_none_value'] ) . '">' . $r['show_option_none'] . "</option>\n";
		}
		$output .= walk_page_dropdown_tree( $pages, $r['depth'], $r );
		$output .= "</select>\n";
	}

	/**
	 * Filters the HTML output of a list of pages as a drop down.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output HTML output for drop down list of pages.
	 * @param array  $r      The parsed arguments array.
	 * @param array  $pages  List of WP_Post objects returned by `WP_Query`
	 */
	$html = apply_filters( 'wp_dropdown_pages', $output, $r, $pages );

	if ( $r['echo'] ) {
		echo $html;
	}
	return $html;
}

/** Query: Conference Day *****************************************************/

/**
 * Setup and run the Conference Day query
 *
 * @since 1.0.0
 *
 * @param array $args Query arguments.
 * @return bool Has the query returned any results?
 */
function paco2017_query_conf_days( $args = array() ) {

	// Get query object
	$query = paco2017_content()->conf_day_query;

	// Reset query defaults
	$query->in_the_loop  = false;
	$query->current_term = -1;
	$query->term_count   = 0;
	$query->term         = null;
	$query->terms        = array();

	// Define query args
	$r = wp_parse_args( $args, array(
		'taxonomy'        => paco2017_get_conf_day_tax_id(),
		'number'          => 0,
		'paged'           => 0,
		'fields'          => 'all',
		'hide_empty'      => false
	) );

	// Pagination
	if ( (int) $r['number'] > 0 ) {
		$r['paged'] = absint( $r['paged'] );
		if ( $r['paged'] == 0 ) {
			$r['paged'] = 1;
		}
		$r['offset'] = absint( ( $r['paged'] - 1 ) * (int) $r['number'] );
	} else {
		$r['number'] = 0;
	}

	// Run query to get the taxonomy terms
	$query->query( $r );

	// Set query results
	$query->term_count = count( $query->terms );
	if ( $query->term_count > 0 ) {
		$query->term = $query->terms[0];
	}

	// Determine the total term count
	if ( isset( $r['offset'] ) && ! $query->term_count < $r['number'] ) {
		$query->found_terms = paco2017_query_terms_found_rows( $r );
	} else {
		$query->found_terms = $query->term_count;
	}
	if ( $query->found_terms > $query->term_count ) {
		$query->max_num_pages = (int) ceil( $query->found_terms / $r['number'] );
	} else {
		$query->max_num_pages = 1;
	}

	// Return whether the query has returned results
	return paco2017_have_conf_days();
}

/**
 * Return whether the query has Conference Days to loop over
 *
 * @since 1.0.0
 *
 * @return bool Query has Conference Days
 */
function paco2017_have_conf_days() {

	// Get query object
	$query = paco2017_content()->conf_day_query;

	// Get array keys
	$term_keys = array_keys( $query->terms );

	// Current element is not the last
	$has_next = $query->term_count && $query->current_term < end( $term_keys );

	// We're in the loop when there are still elements
	if ( ! $has_next ) {
		$query->in_the_loop = false;

		// Clean up after the loop
		paco2017_rewind_conf_days();
	}

	return $has_next;
}

/**
 * Setup next Conference Day in the current loop
 *
 * @since 1.0.0
 *
 * @return bool Are we still in the loop?
 */
function paco2017_the_conf_day() {

	// Get query object
	$query = paco2017_content()->conf_day_query;

	// We're looping
	$query->in_the_loop = true;

	// Increase current term index
	$query->current_term++;

	// Get next term in list
	$query->term = $query->terms[ $query->current_term ];

	return $query->term;
}

/**
 * Rewind the speakers and reset term index
 *
 * @since 1.0.0
 */
function paco2017_rewind_conf_days() {

	// Get query object
	$query = paco2017_content()->conf_day_query;

	// Reset current term index
	$query->current_term = -1;

	if ( $query->term_count > 0 ) {
		$query->term = $query->terms[0];
	}
}

/**
 * Return whether we're in the Conference Day loop
 *
 * @since 1.0.0
 *
 * @return bool Are we in the Conference Day loop?
 */
function paco2017_in_the_conf_day_loop() {
	return isset( paco2017_content()->conf_day_query->in_the_loop ) ? paco2017_content()->conf_day_query->in_the_loop : false;
}

/** Template: Conference Day **************************************************/

/**
 * Return the Conference Day item term
 *
 * @since 1.0.0
 *
 * @param WP_Term|int|WP_Post $item Optional. Term object or ID or post object. Defaults to the current term or post.
 * @param string $by Optional. Method to fetch term through `get_term_by()`. Defaults to 'id'.
 * @return WP_Term|false Conference Day term object or False when not found.
 */
function paco2017_get_conf_day( $item = 0, $by = 'id' ) {

	// Default empty parameter to the item in the loop
	if ( empty( $item ) && paco2017_in_the_conf_day_loop() ) {
		$item = paco2017_content()->conf_day_query->term;

	// Default to the current post's item
	} elseif ( empty( $item ) && paco2017_object_has_conf_day() ) {
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
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_conf_day_title( $term = 0 ) {
	echo paco2017_get_conf_day_title( $term );
}

/**
 * Return the Conference Day title
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_title'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return string Term title
 */
function paco2017_get_conf_day_title( $term = 0 ) {
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
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_conf_day_content( $term = 0 ) {
	echo paco2017_get_conf_day_content( $term );
}

/**
 * Return the Conference Day content
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_content'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return string Term content
 */
function paco2017_get_conf_day_content( $term = 0 ) {
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
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 */
function paco2017_the_conf_day_date_string( $term = 0 ) {
	echo paco2017_get_conf_day_date_string( $term );
}

/**
 * Return the Conference Day mysql date string
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_date_string'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return int Term mysql date string
 */
function paco2017_get_conf_day_date_string( $term = 0 ) {
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
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @param string $format Optional. Date format. Defaults to the date format option.
 */
function paco2017_the_conf_day_date( $term = 0, $format = null ) {
	echo paco2017_get_conf_day_date( $term, $format );
}

/**
 * Return the Conference Day date
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_conf_day_date'
 *
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @param string $format Optional. Date format. Defaults to the date format option.
 * @return int Term date
 */
function paco2017_get_conf_day_date( $term = 0, $format = null ) {
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
 * @param WP_Term|int|WP_Post $term Optional. Term object or ID or related post object. Defaults to the current term.
 * @return bool Term has a date
 */
function paco2017_has_conf_day_date( $term = 0 ) {
	return (bool) paco2017_get_conf_day_date_string( $term );
}
