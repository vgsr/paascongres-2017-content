<?php

/**
 * The WP Setting Media dropin class
 *
 * @package WP Setting Media
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Setting_Media' ) ) :
/**
 * The WP Setting Media class
 *
 * @since 1.0.0
 */
class WP_Setting_Media {

	/**
	 * @var string Plugin version
	 */
	protected $version = '1.0.0';

	/**
	 * @var string Metadata key
	 */
	protected $setting_key;

	/**
	 * @var string|array Attachment mime type
	 */
	protected $mime_type = 'image';

	/**
	 * @var array Array of labels
	 */
	protected $labels = array(
		'setSettingMedia'    => '',
		'settingMediaTitle'  => '',
		'removeSettingMedia' => '',
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
	 * @param array $args Setting media arguments
	 */
	private function setup_details( $args = array() ) {

		// Setup object data
		$args = wp_parse_args( $args, array(
			'setting'    => '',
			'mime_type'  => 'image',
			'labels'     => array(),
			'image_size' => array(),
			'element'    => '',
		) );

		// Define labels
		$this->setting_key = $args['setting'];
		$this->mime_type   = $args['mime_type'];
		$this->labels      = wp_parse_args( $args['labels'], array(
			'setSettingMedia'    => esc_html__( 'Set setting image', 'wp-setting-media' ),
			'settingMediaTitle'  => esc_html__( 'Setting image', 'wp-setting-media' ),
			'removeSettingMedia' => esc_html__( 'Remove setting image', 'wp-setting-media' ),
			'error'              => esc_html__( 'Could not set that as the setting image. Try a different attachment.', 'wp-setting-media' ),
		) );
		$this->image_size  = $args['image_size'];
		$this->element     = ! empty( $args['element'] ) ? $args['element'] : '#' . $args['setting'];
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {
		add_action( "wp_ajax_{$this->setting_key}_settings", array( $this, 'ajax_update'    ) );
		add_filter( 'media_view_settings',                   array( $this, 'media_settings' ) );
	}

	/** Public methods **************************************************/

	/**
	 * Return the setting's post media ID
	 *
	 * @since 1.0.0
	 *
	 * @return int Post media ID, 0 when not found.
	 */
	public function get_setting() {
		return (int) get_option( $this->setting_key, false );
	}

	/**
	 * Return whether the setting's attachment has an image to display
	 *
	 * @since 1.0.0
	 *
	 * @return bool Setting media has image
	 */
	public function media_has_image() {
		$attachment_id = $this->get_setting();
		$is_image = $attachment_id && wp_attachment_is_image( $attachment_id );

		return $is_image;
	}

	/**
	 * Return the collection of details of the current setting media
	 *
	 * @since 1.0.0
	 *
	 * @return array Setting media details
	 */
	public function get_js_data() {
		$data = array(
			'name'       => 'settingMedia_'  . esc_attr( $this->setting_key ),
			'key'        => 'setting-media-' . esc_attr( $this->setting_key ),
			'settingKey' => $this->setting_key,
			'mimeType'   => $this->mime_type,
			'l10n'       => $this->labels,
			'parentEl'   => $this->element,
			'ajaxAction' => $this->setting_key .'_settings',
		);

		return $data;
	}

	/**
	 * Modify the setting's media settings for the setting media
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Media settings
	 * @return array Media settings
	 */
	public function media_settings( $settings ) {

		// Define setting medias collection
		if ( ! isset( $settings['settingMedias'] ) || ! is_array( $settings['settingMedias'] ) ) {
			$settings['settingMedias'] = array();
		}

		$attachment_id = $this->get_setting();
		$settings['settingMedias'][ $this->setting_key ] = array(
			'media' => $attachment_id ? $attachment_id : -1,
			'nonce' => wp_create_nonce( "update-setting_{$this->setting_key}" )
		);

		return $settings;
	}

	/**
	 * Output the setting media input HTML
	 *
	 * @since 1.0.0
	 */
	public function setting_media_input_html() {
		echo '<p id="' . $this->setting_key . '"><span class="wp-setting-media' . ( $this->media_has_image() ? ' has-image' : '' ) . '">' . $this->_media_input_html() . '</span></p>';

		// Enqueue media scripts
		wp_enqueue_media();
		wp_enqueue_script( 'setting-media', $this->url . 'assets/js/setting-media.js', array( 'media-editor' ), $this->version, true );
		wp_enqueue_style(  'setting-media', $this->url . 'assets/css/setting-media.css', array(), $this->version );

		// Add script to setup the js instance
		wp_add_inline_script( 'setting-media', "
/* global wp */
jQuery(document).ready( function( $ ) {
	if ( typeof wp.media.wpSettingMedia === 'undefined' )
		return;

	// Setup media selector
	if ( $( '.wp-setting-media', '" . $this->element . "' ).length ) {
		wp.media.wpSettingMedia( " . json_encode( $this->get_js_data() ) . " );
	}
} );
" );
	}

	/**
	 * Return the setting media input HTML
	 *
	 * @since 1.0.0
	 *
	 * @return string Input HTML
	 */
	private function _media_input_html() {

		// Define local variables
		$set_action_text  = $this->labels['setSettingMedia'];
		$set_media_link   = '<span class="hide-if-no-js"><a title="%s" href="#" class="wp-setting-media-set">%s</a></span>';

		$content = sprintf( $set_media_link,
			esc_attr( $set_action_text ),
			esc_html( $set_action_text )
		);

		$attachment_id = $this->get_setting();

		// This setting has an attachment
		if ( $attachment_id ) {
			$att_html = '';

			if ( $this->media_has_image() ) {
				// Get image in predefined width for setting field
				$att_html = wp_get_attachment_image( $attachment_id, array( 150, 150 ) );
			} else {
				$att_html = get_the_title( $attachment_id );
			}

			if ( ! empty( $att_html ) ) {
				$remove_action_text = $this->labels['removeSettingMedia'];
				$remove_media_link  = ' <span class="hide-if-no-js delete"><a href="#" class="wp-setting-media-remove aria-button-if-js" aria-label="%s"><span class="screen-reader-text">' . __( 'Delete' ) . '</span></a></span>';

				$content = sprintf( $set_media_link,
					esc_attr( $set_action_text ),
					$att_html
				) . sprintf( $remove_media_link,
					esc_attr( $remove_action_text )
				);
			}
		}

		// Provide a setting input field
		$set_media_input = '<input type="hidden" name="%s" value="%s">';
		$content .= sprintf( $set_media_input,
			$this->setting_key,
			(int) $attachment_id
		);

		return $content;
	}

	/**
	 * Save a setting media input on AJAX update
	 *
	 * @since 1.0.0
	 *
	 * @see wp_ajax_set_post_thumbnail()
	 */
	public function ajax_update() {
		$json = ! empty( $_REQUEST['json'] ); // New-style request

		if ( $_POST['setting_key'] !== $this->setting_key )
			wp_die( -1 );

		$attachment_id = intval( $_POST['setting_media_id'] );

		if ( $json ) {
			check_ajax_referer( "update-setting_{$this->setting_key}" );
		} else {
			check_ajax_referer( "wp-setting-media-set_{$this->setting_key}" );
		}

		// Delete setting media
		if ( $attachment_id == '-1' ) {
			if ( delete_option( $this->setting_key ) ) {
				$return = $this->ajax_get_return_data( false );
				$json ? wp_send_json_success( $return ) : wp_die( $return );
			} else {
				wp_die( 0 );
			}
		}

		// Update setting media
		if ( update_option( $this->setting_key, $attachment_id ) ) {

			// Maybe resize the image
			if ( $this->media_has_image() ) {
				$this->maybe_resize_image( $attachment_id );
			}

			$return = $this->ajax_get_return_data();
			$json ? wp_send_json_success( $return ) : wp_die( $return );
		}

		wp_die( 0 );
	}

	/**
	 * Return the AJAX update return data
	 *
	 * @since 1.0.0
	 *
	 * @param bool $update Optional. Whether the setting was updated or deleted
	 * @return array Return data
	 */
	public function ajax_get_return_data( $update = true ) {
		return array(
			'html'          => $this->_media_input_html(),
			'setImageClass' => $update ? $this->media_has_image() : false,
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
		$metadata['sizes'][ is_string( $requested_size ) ? $requested_size : $this->setting_key ] = $saved;
		wp_update_attachment_metadata( $attachment_id, $metadata );

		return $destination;
	}
}

/**
 * Setup a new setting media instance
 *
 * @since 1.0.0
 *
 * @param string $file File path from which the media is constructed
 * @param string $setting Name of the media's meta key
 * @param array $args For definition, {@see WP_Setting_Media}
 */
function wp_setting_media( $file, $setting, $args = array() ) {

	// Require defined meta key
	if ( empty( $setting ) )
		return;

	// Define global collection of setting medias
	if ( ! isset( $GLOBALS['wp_setting_media'] ) ) {
		$GLOBALS['wp_setting_media'] = array();
	}

	// Add meta key to the object arguments
	$args['setting'] = $setting;

	// Instantiate new setting media
	$GLOBALS['wp_setting_media'][ $setting ] = new WP_Setting_Media( $file, $args );
}

/**
 * Output the setting media input for the given media field
 *
 * @since 1.0.0
 *
 * @param array $args Field arguments
 */
function wp_setting_media_field( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'setting'     => '',
		'description' => '',
	) );

	// Bail when missing the setting parameter
	if ( empty( $args['setting'] ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'The setting parameter should not be empty.', 'wp-setting-media' ), '1.0.0' );
		return;

	// Bail when the setting media was not registered properly
	} elseif ( ! isset( $GLOBALS['wp_setting_media'][ $args['setting'] ] ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( __( 'The setting media was not registered using %s.', 'wp-setting-media' ), '<code>wp_setting_media()</code>' ), '1.0.0' );
		return;
	}

	if ( isset( $GLOBALS['wp_setting_media'][ $args['setting'] ] ) ) {
		$GLOBALS['wp_setting_media'][ $args['setting'] ]->setting_media_input_html();

		if ( ! empty( $args['description'] ) ) {
			echo '<p class="description">' . $args['description'] . '</p>';
		}
	}
}

endif; // class_exists
