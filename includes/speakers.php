<?php

/**
 * Paco2017 Content Speaker Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Taxonomy ******************************************************************/

/**
 * Return the Speaker taxonomy
 *
 * @since 1.0.0
 *
 * @return string Taxonomy name
 */
function paco2017_get_speaker_tax_id() {
	return 'paco2017_speaker';
}

/**
 * Return the labels for the Speaker taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speaker_tax_labels'
 * @return array Speaker taxonomy labels
 */
function paco2017_get_speaker_tax_labels() {
	return apply_filters( 'paco2017_get_speaker_tax_labels', array(
		'name'          => __( 'Paascongres Speakers', 'paco2017-content' ),
		'menu_name'     => __( 'Speakers',             'paco2017-content' ),
		'singular_name' => __( 'Speaker',              'paco2017-content' ),
		'search_items'  => __( 'Search Speakers',      'paco2017-content' ),
		'popular_items' => null, // Disable tagcloud
		'all_items'     => __( 'All Speakers',         'paco2017-content' ),
		'no_items'      => __( 'No Speaker',           'paco2017-content' ),
		'edit_item'     => __( 'Edit Speaker',         'paco2017-content' ),
		'update_item'   => __( 'Update Speaker',       'paco2017-content' ),
		'add_new_item'  => __( 'Add New Speaker',      'paco2017-content' ),
		'new_item_name' => __( 'New Speaker Name',     'paco2017-content' ),
		'view_item'     => __( 'View Speaker',         'paco2017-content' )
	) );
}

/**
 * Modify the link returned for the given Speaker term
 *
 * @since 1.0.0
 *
 * @param string $link Term link
 * @param WP_Term $term Term object
 * @param string $taxonomy Taxonomy name
 * @return string Term link
 */
function paco2017_get_speaker_term_link( $link, $term, $taxonomy ) {
	
	// When this is a speaker term
	if ( paco2017_get_speaker_tax_id() === $taxonomy ) {

		// Link to the Speakers page
		$link = paco2017_get_speakers_url();
	}

	return $link;
}

/**
 * Act when the Speaker taxonomy has been registered
 *
 * @since 1.0.0
 */
function paco2017_registered_speaker_taxonomy() {
	add_action( 'paco2017_rest_api_init', 'paco2017_register_speaker_rest_fields' );
}

/**
 * Register REST fields for the Speaker taxonomy
 *
 * @since 1.0.0
 */
function paco2017_register_speaker_rest_fields() {

	// Filter Speaker terms
	add_filter( 'get_terms', 'paco2017_speaker_rest_get_terms', 10, 4 );

	// Get assets
	$lecture  = paco2017_get_lecture_post_type();
	$workshop = paco2017_get_workshop_post_type();

	// Add location to Lecture
	register_rest_field(
		$lecture,
		'speakers',
		array(
			'get_callback' => 'paco2017_get_object_rest_speakers'
		)
	);

	// Add location to Workshop
	register_rest_field(
		$workshop,
		'speakers',
		array(
			'get_callback' => 'paco2017_get_object_rest_speakers'
		)
	);
}

/**
 * Return the value for the 'speakers' workshop REST API field
 *
 * @since 1.0.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return array Location term(s)
 */
function paco2017_get_object_rest_speakers( $object, $field_name, $request ) {
	return wp_get_object_terms( $object['id'], paco2017_get_speaker_tax_id() );
}

/**
 * Modify the list of queried speaker terms in a REST request
 *
 * @since 1.0.0
 *
 * @param array $terms Queried terms
 * @param array $taxonomies Taxonomy names
 * @param array $query_vars Query variables
 * @param WP_Term_Query $term_query Term query object
 * @return array Terms
 */
function paco2017_speaker_rest_get_terms( $terms, $taxonomy, $query_vars, $term_query ) {

	// Bail when not querying terms whole
	if ( 'all' !== $query_vars['fields'] && 'all_with_object_id' !== $query_vars['fields'] )
		return $terms;

	// Queried conference days
	foreach ( $terms as $k => $term ) {

		// Skip when this is not one of ours
		if ( paco2017_get_speaker_tax_id() !== $term->taxonomy )
			continue;

		// Speaker photo
		$term->photo = paco2017_get_rest_image( paco2017_get_speaker_photo_id( $term ), array( 150, 150 ) );

		// Store modified term
		$terms[ $k ] = $term;
	}

	return $terms;
}

/**
 * Return whether the given post has any or the given Speaker
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current post.
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to any term.
 * @return bool Object has a/the Speaker
 */
function paco2017_object_has_speaker( $post = 0, $term = 0 ) {
	return has_term( $term, paco2017_get_speaker_tax_id(), $post );
}

/** Query *********************************************************************/

/**
 * Setup and run the Speakers query
 *
 * @since 1.0.0
 *
 * @param array $args Query arguments.
 * @return bool Has the query returned any results?
 */
function paco2017_query_speakers( $args = array() ) {

	// Get query object
	$query = paco2017_content()->speaker_query;

	// Reset query defaults
	$query->in_the_loop  = false;
	$query->current_term = -1;
	$query->term_count   = 0;
	$query->term         = null;
	$query->terms        = array();

	// Define query args
	$r = wp_parse_args( $args, array(
		'taxonomy'        => paco2017_get_speaker_tax_id(),
		'number'          => 0,
		'paged'           => 0,
		'fields'          => 'all',
		'hide_empty'      => true
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
	return paco2017_have_speakers();
}

/**
 * Return whether the query has Speakers to loop over
 *
 * @since 1.0.0
 *
 * @return bool Query has Speakers
 */
function paco2017_have_speakers() {

	// Get query object
	$query = paco2017_content()->speaker_query;

	// Get array keys
	$term_keys = array_keys( $query->terms );

	// Current element is not the last
	$has_next = $query->term_count && $query->current_term < end( $term_keys );

	// We're in the loop when there are still elements
	if ( ! $has_next ) {
		$query->in_the_loop = false;

		// Clean up after the loop
		paco2017_rewind_speakers();
	}

	return $has_next;
}

/**
 * Setup next Speaker in the current loop
 *
 * @since 1.0.0
 *
 * @return bool Are we still in the loop?
 */
function paco2017_the_speaker() {

	// Get query object
	$query = paco2017_content()->speaker_query;

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
function paco2017_rewind_speakers() {

	// Get query object
	$query = paco2017_content()->speaker_query;

	// Reset current term index
	$query->current_term = -1;

	if ( $query->term_count > 0 ) {
		$query->term = $query->terms[0];
	}
}

/**
 * Return whether we're in the Speaker loop
 *
 * @since 1.0.0
 *
 * @return bool Are we in the Speaker loop?
 */
function paco2017_in_the_speaker_loop() {
	return isset( paco2017_content()->speaker_query->in_the_loop ) ? paco2017_content()->speaker_query->in_the_loop : false;
}

/** Template ******************************************************************/

/**
 * Return the Speaker item term
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $item Optional. Term object or ID. Defaults to the current term.
 * @param string $by Optional. Method to fetch term through `get_term_by()`. Defaults to 'id'.
 * @return WP_Term|false Speakers term object or False when not found.
 */
function paco2017_get_speaker( $item = 0, $by = 'id' ) {

	// Default empty parameter to the item in the loop
	if ( empty( $item ) && paco2017_in_the_speaker_loop() ) {
		$item = paco2017_content()->speaker_query->term;

	// Default to the post's item
	} elseif ( empty( $item ) && paco2017_object_has_speaker() ) {
		$terms = wp_get_object_terms( get_the_ID(), paco2017_get_speaker_tax_id() );
		$item  = $terms[0];

	// Get the term by id or slug
	} elseif ( ! $item instanceof WP_Term ) {
		$item = get_term_by( $by, $item, paco2017_get_speaker_tax_id() );
	}

	// Reduce error to false
	if ( ! $item || is_wp_error( $item ) ) {
		$item = false;
	}

	return $item;
}

/**
 * Output the Speaker title
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 */
function paco2017_the_speaker_title( $term = 0 ) {
	echo paco2017_get_speaker_title( $term );
}

/**
 * Return the Speaker title
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speaker_title'
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @return string Term title
 */
function paco2017_get_speaker_title( $term = 0 ) {
	$term  = paco2017_get_speaker( $term );
	$title = '';

	if ( $term ) {
		$title = get_term_field( 'name', $term );
	}

	return apply_filters( 'paco2017_get_speaker_title', $title, $term );
}

/**
 * Output the Speaker content
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 */
function paco2017_the_speaker_content( $term = 0 ) {
	echo paco2017_get_speaker_content( $term );
}

/**
 * Return the Speaker content
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speaker_content'
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @return string Term content
 */
function paco2017_get_speaker_content( $term = 0 ) {
	$term    = paco2017_get_speaker( $term );
	$content = '';

	if ( $term ) {
		$content = get_term_field( 'description', $term );
	}

	return apply_filters( 'paco2017_get_speaker_content', $content, $term );
}

/**
 * Return the Speaker objects
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speaker_objects'
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @return array Term object ids
 */
function paco2017_get_speaker_objects( $term = 0 ) {
	$term    = paco2017_get_speaker( $term );
	$objects = array();

	if ( $term && $term->count ) {
		$objects = get_objects_in_term( $term->term_id, $term->taxonomy );
		$objects = array_map( 'intval', array_values( $objects ) );
	}

	return (array) apply_filters( 'paco2017_get_speaker_objects', $objects, $term );
}

/**
 * Output the Speaker objects list
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 */
function paco2017_the_speaker_objects_list( $term = 0 ) {
	echo paco2017_get_speaker_objects_list( $term );
}

/**
 * Return the Speaker objects list
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speaker_objects_list'
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @return string Term objects list
 */
function paco2017_get_speaker_objects_list( $term = 0 ) {
	$term    = paco2017_get_speaker( $term );
	$objects = paco2017_get_speaker_objects( $term );
	$list    = '';

	if ( $term && $objects ) {
		$query = new WP_Query( array(
			'post_type'      => paco2017_get_taxonomy_types( paco2017_get_speaker_tax_id() ),
			'post__in'       => $objects,
			'posts_per_page' => -1
		) );

		if ( $query->have_posts() ) : ob_start(); ?>

			<ul class="item-objects">

				<?php while ( $query->have_posts() ) : $query->the_post(); ?>

				<li <?php post_class( 'item-object' ); ?>>
					<span class="item-object-title"><?php the_title( '<a href="' . get_permalink() . '">', '</a>' ); ?></span>
				</li>

				<?php endwhile; ?>

			</ul>

		<?php

		$list = ob_get_clean();

		wp_reset_postdata();
		endif;
	}

	return apply_filters( 'paco2017_get_speaker_objects_list', $list, $term, $objects );
}

/**
 * Output the Speaker photo attachment ID
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 */
function paco2017_the_speaker_photo_id( $term = 0 ) {
	echo paco2017_get_speaker_photo_id( $term );
}

/**
 * Return the Speaker photo attachment ID
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speaker_photo_id'
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @return int Term photo attachment ID
 */
function paco2017_get_speaker_photo_id( $term = 0 ) {
	$term     = paco2017_get_speaker( $term );
	$photo_id = 0;

	if ( $term ) {
		$photo_id = (int) get_term_meta( $term->term_id, 'photo', true );
	}

	return (int) apply_filters( 'paco2017_get_speaker_photo_id', $photo_id, $term );
}

/**
 * Return whether the Speaker has a photo
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @return bool Has the speaker a term photo?
 */
function paco2017_has_speaker_photo( $term = 0 ) {
	return (bool) paco2017_get_speaker_photo_id( $term );
}

/**
 * Output the Speaker photo
 *
 * @since 1.0.0
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @param string|array $size Optional. Attachment image size. Defaults to 'thumbnail'.
 * @param array $args Optional. Attachment image arguments for {@see wp_get_attachment_image()}.
 */
function paco2017_the_speaker_photo( $term = 0, $size = 'thumbnail', $args = array() ) {
	echo paco2017_get_speaker_photo( $term, $size );
}

/**
 * Return the Speaker photo
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speaker_photo'
 *
 * @param WP_Term|int $term Optional. Term object or ID. Defaults to the current term.
 * @param string|array $size Optional. Attachment image size. Defaults to 'thumbnail'.
 * @param array $args Optional. Attachment image arguments for {@see wp_get_attachment_image()}.
 * @return string Term photo
 */
function paco2017_get_speaker_photo( $term = 0, $size = 'thumbnail', $args = array() ) {
	$term  = paco2017_get_speaker( $term );
	$image = '';

	if ( $term ) {
		$photo_id = paco2017_get_speaker_photo_id( $term );
		$photo    = wp_get_attachment_image( $photo_id, $size, false, $args );
	}

	return apply_filters( 'paco2017_get_speaker_photo', $photo, $term );
}

/**
 * Modify the content of the current post
 *
 * @since 1.0.0
 *
 * @param string $content Post content
 * @return string Post content
 */
function paco2017_speaker_post_content( $content ) {

	// The Speaker info
	if ( is_single() && paco2017_object_has_speaker() ) {
		$content .= paco2017_buffer_template_part( 'info', 'speaker' );
	}

	return $content;
}
