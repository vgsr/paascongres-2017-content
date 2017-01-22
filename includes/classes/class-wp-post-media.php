<?php

/**
 * The WP Post Media dropin class
 *
 * @package WP Post Media
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Post_Media' ) ) :
/**
 * The WP Post Media class
 *
 * @since 1.0.0
 */
class WP_Post_Media {

	/**
	 * @var string Plugin version
	 */
	protected $version = '1.0.0';

	/**
	 * @var string Metadata key
	 */
	protected $meta_key = 'media';

	/**
	 * @var string|array Attachment mime type
	 */
	protected $mime_type = 'image';

	/**
	 * @var array Array of labels
	 */
	protected $labels = array(
		'setPostMedia'    => '',
		'postMediaTitle'  => '',
		'removePostMedia' => '',
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
	 * @param array $args Post media arguments
	 */
	private function setup_details( $args = array() ) {

		// Setup object data
		$args = wp_parse_args( $args, array(
			'meta_key'   => 'media',
			'mime_type'  => 'image',
			'post_type'  => array(),
			'labels'     => array(),
			'image_size' => array(),
			'element'    => '',
		) );

		// Define labels
		$this->meta_key   = $args['meta_key'];
		$this->mime_type  = $args['mime_type'];
		$this->post_type  = ! empty( $args['post_type'] ) ? (array) $args['post_type'] : get_post_types( array( 'public' => true ) );
		$this->labels     = wp_parse_args( $args['labels'], array(
			'setPostMedia'    => esc_html__( 'Set %s image', 'wp-post-media' ),
			'postMediaTitle'  => esc_html__( '%s image', 'wp-post-media' ),
			'removePostMedia' => esc_html__( 'Remove %s image', 'wp-post-media' ),
			'error'           => esc_html__( 'Could not set that as the %s image. Try a different attachment.', 'wp-post-media' ),
		) );
		$this->image_size = $args['image_size'];
		$this->element    = $args['element'];
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {
		add_action( "wp_ajax_{$this->meta_key}_posts", array( $this, 'ajax_update'    )        );
		add_filter( 'media_view_settings',             array( $this, 'media_settings' ), 10, 2 );
	}

	/** Public methods **************************************************/

	/**
	 * Return the post's post media ID
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 * @return int Post media ID, 0 when not found.
	 */
	public function get_meta( $post_id ) {
		return (int) get_post_meta( $post_id, $this->meta_key, true );
	}

	/**
	 * Return whether the post's attachment has an image to display
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post|int $post_id Post object or ID
	 * @return bool Post media has image
	 */
	public function media_has_image( $post_id ) {
		if ( is_a( $post_id, 'WP_Post' ) ) {
			$post_id = $post_id->ID;
		}

		$attachment_id = $this->get_meta( $post_id );
		$is_image = $attachment_id && wp_attachment_is_image( $attachment_id );

		return $is_image;
	}

	/**
	 * Return the collection of details of the current post media
	 *
	 * @since 1.0.0
	 *
	 * @return array Post media details
	 */
	public function get_js_data() {

		// Parse the current screen's post type labels
		$post_type = get_post_type_object( get_current_screen()->post_type );
		$labels   = array_combine(
			array_keys( $this->labels ),
			array_map( 'sprintf', $this->labels, array_fill( 0, count( $this->labels ), $post_type->labels->singular_name ) )
		);

		$data = array(
			'name'       => 'postMedia_'  . esc_attr( $this->meta_key ),
			'key'        => 'post-media-' . esc_attr( $this->meta_key ),
			'metaKey'    => $this->meta_key,
			'mimeType'   => $this->mime_type,
			'l10n'       => $labels,
			'parentEl'   => $this->element,
			'ajaxAction' => $this->meta_key .'_posts',
		);

		return $data;
	}

	/**
	 * Modify the post's media settings for the post media
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Media settings
	 * @param WP_Post $post Post object
	 * @return array Media settings
	 */
	public function media_settings( $settings, $post ) {

		// Add post media ID to the post's media settings
		if ( is_a( $post, 'WP_Post' ) && in_array( $post->post_type, $this->post_type ) ) {

			// Define post media collection
			if ( ! isset( $settings['post']['postMedias'] ) || ! is_array( $settings['post']['postMedias'] ) ) {
				$settings['post']['postMedias'] = array();
			}

			$attachment_id = $this->get_meta( $post->ID );
			$settings['post']['postMedias'][ $this->meta_key ] = $attachment_id ? $attachment_id : -1;
		}

		return $settings;
	}

	/**
	 * Output the post media input HTML
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 */
	public function post_media_input_html( $post_id ) {
		echo '<span class="wp-post-media' . ( $this->media_has_image( $post_id ) ? ' has-image' : '' ) . '">' . $this->_image_input_html( $post_id ) . '</span>';

		wp_enqueue_media( array( 'post' => $post_id ) );
		wp_enqueue_script( 'post-media', $this->url . 'assets/js/post-media.js', array( 'media-editor' ), $this->version, true );
		wp_enqueue_style(  'post-media', $this->url . 'assets/css/post-media.css', array(), $this->version );

		// Add script to setup the js instance
		wp_add_inline_script( 'post-media', "
/* global wp */
jQuery(document).ready( function( $ ) {
	if ( typeof wp.media.wpPostMedia === 'undefined' )
		return;

	// Setup media selector
	if ( $( '.wp-post-media', '" . $this->element . "' ).length ) {
		wp.media.wpPostMedia( " . json_encode( $this->get_js_data() ) . " );
	}
} );
" );
	}

	/**
	 * Return the post media input HTML
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
		$set_action_text  = sprintf( $this->labels['setPostMedia'], $post_type_object->labels->singular_name );
		$set_media_link   = '<span class="hide-if-no-js"><a title="%s" href="#" class="wp-post-media-set">%s</a></span>';

		$content = sprintf( $set_media_link,
			esc_attr( $set_action_text ),
			esc_html( $set_action_text )
		);

		$attachment_id = $this->get_meta( $post->ID );

		// This post has an attachment
		if ( $attachment_id ) {
			$att_html = '';

			if ( $this->media_has_image( $post->ID ) ) {
				// Get image in predefined width for admin metabox
				$att_html = wp_get_attachment_image( $attachment_id, 'medium' );
			} else {
				$att_html = get_the_title( $attachment_id );
			}

			if ( ! empty( $att_html ) ) {
				$remove_action_text = sprintf( $this->labels['removePostMedia'], $post_type_object->labels->singular_name );
				$remove_media_link  = ' <span class="hide-if-no-js delete"><a href="#" class="wp-post-media-remove aria-button-if-js" aria-label="%s"><span class="screen-reader-text">' . __( 'Delete' ) . '</span></a></span>';

				$content = sprintf( $set_media_link,
					esc_attr( $set_action_text ),
					$att_html
				) . sprintf( $remove_media_link,
					esc_attr( $remove_action_text )
				);
			}
		}

		return $content;
	}

	/**
	 * Save a post media input on AJAX update
	 *
	 * @since 1.0.0
	 *
	 * @see wp_ajax_set_post_thumbnail()
	 */
	public function ajax_update() {
		$json = ! empty( $_REQUEST['json'] ); // New-style request

		$post_ID = intval( $_POST['post_id'] );

		if ( ! $post = get_post( $post_ID ) )
			wp_die( -1 );
		if ( ! current_user_can( 'edit_post', $post_ID ) )
			wp_die( -1 );

		$attachment_id = intval( $_POST['post_media_id'] );

		if ( $json ) {
			check_ajax_referer( "update-post_{$post_ID}" );
		} else {
			check_ajax_referer( "wp-post-media-set_{$this->meta_key}-{$post_ID}" );
		}

		// Delete post media
		if ( $attachment_id == '-1' ) {
			if ( delete_post_meta( $post_ID, $this->meta_key ) ) {
				$return = $this->ajax_get_return_data( $post_ID, false );
				$json ? wp_send_json_success( $return ) : wp_die( $return );
			} else {
				wp_die( 0 );
			}
		}

		// Update post media
		if ( update_post_meta( $post_ID, $this->meta_key, $attachment_id ) ) {

			// Maybe resize the image
			if ( $this->media_has_image( $post_ID ) ) {
				$this->maybe_resize_image( $attachment_id );
			}

			$return = $this->ajax_get_return_data( $post_ID );
			$json ? wp_send_json_success( $return ) : wp_die( $return );
		}

		wp_die( 0 );
	}

	/**
	 * Return the AJAX update return data
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 * @param bool $update Optional. Whether the post was updated or deleted
	 * @return array Return data
	 */
	public function ajax_get_return_data( $post_id, $update = true ) {
		return array(
			'html'          => $this->_image_input_html( $post_id ),
			'setImageClass' => $update ? $this->media_has_image( $post_id ) : false,
		);
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
 * Setup a new post media instance
 *
 * @since 1.0.0
 *
 * @param string $file File path from which the media is constructed
 * @param string $meta_key Name of the media's meta key
 * @param array $args For definition, {@see WP_Post_Media}
 */
function wp_post_media( $file, $meta_key, $args = array() ) {

	// Require defined meta key
	if ( empty( $meta_key ) )
		return;

	// Define global collection of post medias
	if ( ! isset( $GLOBALS['wp_post_media'] ) ) {
		$GLOBALS['wp_post_media'] = array();
	}

	// Add meta key to the object arguments
	$args['meta_key'] = $meta_key;

	// Instantiate new post media and store in global for later use
	$GLOBALS['wp_post_media'][ $meta_key ] = new WP_Post_Media( $file, $args );
}

/**
 * Output the post media input field for the given post's media
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post_id Post object or ID
 * @param string $meta_key Post media's meta key name
 */
function wp_post_media_field( $post_id, $meta_key ) {
	if ( isset( $GLOBALS['wp_post_media'][ $meta_key ] ) ) {
		$GLOBALS['wp_post_media'][ $meta_key ]->post_media_input_html( $post_id );
	}
}

endif; // class_exists
