<?php

/**
 * The WP Post Image dropin class
 *
 * @package WP Post Image
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Post_Image' ) ) :
/**
 * The WP Post Image class
 *
 * @since 1.0.0
 */
class WP_Post_Image {

	/**
	 * @var string Plugin version
	 */
	protected $version = '1.0.0';

	/**
	 * @var string Metadata key
	 */
	protected $meta_key = '';

	/**
	 * @var array Array of labels
	 */
	protected $labels = array(
		'setPostImage'    => '',
		'postImageTitle'  => '',
		'removePostImage' => '',
	);

	/**
	 * @var array Array of labels
	 */
	public $image_size = '';

	/**
	 * @var string Ajax action name
	 */
	public $ajax_action = '';

	/**
	 * Setup this class
	 *
	 * @since 1.0.0
	 */
	public function __construct( $file, $args = array() ) {

		// Setup plugin
		$this->file       = $file;
		$this->url        = plugin_dir_url(  $this->file );
		$this->path       = plugin_dir_path( $this->file );
		$this->basename   = plugin_basename( $this->file );

		$this->setup_details( $args );
		$this->setup_actions();
	}

	/**
	 * Define class details
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Post image arguments
	 */
	private function setup_details( $args = array() ) {

		// Setup object data
		$args = wp_parse_args( $args, array(
			'meta_key'   => 'image',
			'post_type'  => array(),
			'labels'     => array(),
			'image_size' => array(),
			'element'    => '',
		) );

		// Define labels
		$this->meta_key   = $args['meta_key'];
		$this->post_type  = ! empty( $args['post_type'] ) ? (array) $args['post_type'] : get_post_types( array( 'public' => true ) );
		$this->labels     = wp_parse_args( $args['labels'], array(
			'setPostImage'    => esc_html__( 'Set %s image', 'wp-post-image' ),
			'postImageTitle'  => esc_html__( 'Post image', 'wp-post-image' ),
			'removePostImage' => esc_html__( 'Remove %s image', 'wp-post-image' ),
		) );
		$this->image_size = $args['image_size'];
		$this->element    = $args['element'];

		// Define image identifiers
		$this->name        = 'postImage_'  . esc_attr( $this->meta_key );
		$this->key         = 'post-image-' . esc_attr( $this->meta_key );
		$this->ajax_action = $this->key;
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {
		add_action( "wp_ajax_{$this->ajax_action}", array( $this, 'save_image_input' )        );
		add_filter( 'media_view_settings',          array( $this, 'media_settings'   ), 10, 2 );
	}

	/** Public methods **************************************************/

	/**
	 * Return the post's post image ID
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 * @return int Post image ID, 0 when not found.
	 */
	public function get_meta( $post_id ) {
		return (int) get_post_meta( $post_id, $this->meta_key, true );
	}

	/**
	 * Return the collection of details of the current post image
	 *
	 * @since 1.0.0
	 *
	 * @return array Post image details
	 */
	public function get_image_data() {
		$data = array_intersect_key( get_object_vars( $this ), array_flip( array(
			'key', 'name', 'labels'
		) ) );

		$data = array_merge( $data, array(
			'metaKey'    => $this->meta_key,
			'parentEl'   => $this->element,
			'ajaxAction' => $this->ajax_action,
		) );

		return $data;
	}

	/**
	 * Modify the post's media settings for the post image
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Media settings
	 * @param WP_Post $post Post object
	 * @return array Media settings
	 */
	public function media_settings( $settings, $post ) {

		// Add post image ID to the post's media settings
		if ( is_a( $post, 'WP_Post' ) && in_array( $post->post_type, $this->post_type ) ) {

			// Define post image collection
			if ( ! isset( $settings['post']['postImages'] ) || ! is_array( $settings['post']['postImages'] ) ) {
				$settings['post']['postImages'] = array();
			}

			$attachment_id = $this->get_meta( $post->ID );
			$settings['post']['postImages'][ $this->meta_key ] = $attachment_id ? $attachment_id : -1;
		}

		return $settings;
	}

	/**
	 * Output the post image input HTML
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 */
	public function post_image_input_html( $post_id ) {
		echo '<span class="wp-post-image">' . $this->_image_input_html( $post_id ) . '</span>';

		wp_enqueue_media( array( 'post' => $post_id ) );
		wp_enqueue_script( 'wp-post-image', $this->url . 'assets/js/wp-post-image.js', array( 'media-editor' ), $this->version, true );
		wp_enqueue_style(  'wp-post-image', $this->url . 'assets/css/wp-post-image.css', array(), $this->version );

		// Add script to setup the js instance
		wp_add_inline_script( 'wp-post-image', "
/* global wp */
jQuery(document).ready( function( $ ) {
	if ( typeof wp.media.wpPostImages === 'undefined' )
		return;

	// Setup image selector
	if ( $( '.wp-post-image', '" . $this->element . "' ).length ) {
		wp.media.wpPostImages( " . json_encode( $this->get_image_data() ) . " );
	}
} );
" );
	}

	/**
	 * Return the post image input HTML
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 * @return string Input HTML
	 */
	private function _image_input_html( $post_id = 0 ) {

		// Define local variables
		$post             = get_post( $post_id );
		$post_type_object = get_post_type_object( $post->post_type );
		$set_action_text  = sprintf( $this->labels['setPostImage'], $post_type_object->labels->singular_name );
		$set_image_link   = '<span class="hide-if-no-js"><a title="%s" href="#" class="wp-post-image-set">%s</a></span>';

		$content = sprintf( $set_image_link,
			esc_attr( $set_action_text ),
			esc_html( $set_action_text )
		);

		$attachment_id = $this->get_meta( $post->ID );

		// This post has an image
		if ( $attachment_id && wp_attachment_is_image( $attachment_id ) ) {

			// Get image in predefined width for admin metabox
			$image_html = wp_get_attachment_image( $attachment_id, 'medium' );

			if ( ! empty( $image_html ) ) {
				$remove_action_text = sprintf( $this->labels['removePostImage'], $post_type_object->labels->singular_name );
				$remove_image_link  = '<span class="hide-if-no-js"><a href="#" class="wp-post-image-remove" title="%s"><span class="screen-reader-text">%s</span></a></span>';

				$content = sprintf( $set_image_link,
					esc_attr( $set_action_text ),
					$image_html
				) . sprintf( $remove_image_link,
					esc_attr( $remove_action_text ),
					esc_html( $remove_action_text )
				);
			}
		}

		return $content;
	}

	/**
	 * Save a post image input
	 *
	 * @since 1.0.0
	 *
	 * @see wp_ajax_set_post_thumbnail()
	 */
	public function save_image_input() {
		$json = ! empty( $_REQUEST['json'] ); // New-style request

		$post_ID = intval( $_POST['post_id'] );

		if ( ! $post = get_post( $post_ID ) )
			wp_die( -1 );
		if ( ! current_user_can( 'edit_post', $post_ID ) )
			wp_die( -1 );

		$attachment_id = intval( $_POST['post_image_id'] );

		if ( $json ) {
			check_ajax_referer( "update-post_{$post_ID}" );
		} else {
			check_ajax_referer( "{$this->key}-{$post_ID}" );
		}

		// Delete post image
		if ( $attachment_id == '-1' ) {
			if ( delete_post_meta( $post_ID, $this->meta_key ) ) {
				$return = $this->_image_input_html( $post_ID );
				$json ? wp_send_json_success( $return ) : wp_die( $return );
			} else {
				wp_die( 0 );
			}
		}

		// Update post image
		if ( update_post_meta( $post_ID, $this->meta_key, $attachment_id ) ) {

			// Maybe resize the image
			$this->maybe_resize_image( $attachment_id );

			$return = $this->_image_input_html( $post_ID );
			$json ? wp_send_json_success( $return ) : wp_die( $return );
		}

		wp_die( 0 );
	}

	/**
	 * Check whether to generate a new sized version of the image
	 *
	 * Do this when an appropriately sized version may not exist for previously
	 * uploaded images.
	 *
	 * @since 1.0.0
	 *
	 * @param int $attachment_id Post ID
	 */
	public function maybe_resize_image( $attachment_id ) {

		// Get requested size. Append crop parameter when missing
		$requested_size = $this->image_size;
		if ( is_array( $requested_size ) && 2 == count( $requested_size ) ) {
			$requested_size[] = false;
		}

		// Find the requested size of the image
		$size_found = image_get_intermediate_size( $attachment_id, $requested_size );
		if ( $size_found ) {
			// Get the size values only for comparison
			$size_found = array( $size_found['width'], $size_found['height'], false );
		}

		// Bail when the requested image size already exists
		if ( $size_found && ( is_string( $requested_size ) || $size_found == $requested_size ) )
			return;

		// Get the requested image size dimensions
		if ( is_string( $requested_size ) ) {
			if ( has_image_size( $requested_size ) ) {
				$sizes = wp_get_additional_image_sizes(); // Since WP 4.7
				$new_size = array_values( $sizes[ $requested_size ] ); // array( width, height, crop )
			} else {
				$new_size = false;
			}
		} else {
			$new_size = $requested_size;
		}

		// Bail when no dimensions are found
		if ( ! $new_size )
			return;

		$editor = wp_get_image_editor( get_attached_file( $attachment_id ) );
		if ( is_wp_error( $editor ) )
			return $editor;

		$resized = $editor->resize( $new_size[0], $new_size[1], $new_size[2] );
		if ( is_wp_error( $resized ) )
			return $resized;

		$destination = $editor->generate_filename();
		$saved = $editor->save( $destination );

		if ( is_wp_error( $saved ) )
			return $saved;

		// Save new image size in attachment metadata
		$metadata = wp_get_attachment_metadata( $attachment_id );
		unset( $saved['path'] );
		$metadata['sizes'][ is_string( $requested_size ) ? $requested_size : $this->meta_key ] = $saved;
		wp_update_attachment_metadata( $attachment_id, $metadata );

		return $destination;
	}
}

/**
 * Setup a new post image instance
 *
 * @since 1.0.0
 *
 * @param string $file File path from which the image is constructed
 * @param string $meta_key Name of the image's meta key
 * @param array $args For definition, {@see WP_Post_Image}
 */
function wp_post_image( $file, $meta_key, $args = array() ) {

	// Require defined meta key
	if ( empty( $meta_key ) )
		return;

	// Define global collection of post images
	if ( ! isset( $GLOBALS['wp_post_image'] ) ) {
		$GLOBALS['wp_post_image'] = array();
	}

	// Add meta key to the object arguments
	$args['meta_key'] = $meta_key;

	// Instantiate new post image
	$GLOBALS['wp_post_image'][ $meta_key ] = new WP_Post_Image( $file, $args );
}

/**
 * Output the post image input for the given post's image
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post_id Post object or ID
 * @param string $meta_key Post image's meta key name
 */
function wp_post_image_input( $post_id, $meta_key ) {
	if ( isset( $GLOBALS['wp_post_image'][ $meta_key ] ) ) {
		$GLOBALS['wp_post_image'][ $meta_key ]->post_image_input_html( $post_id );
	}
}

endif; // class_exists
