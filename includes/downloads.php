<?php

/**
 * Paco2017 Content Download Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Rewrite *******************************************************************/

/**
 * Return the slug for the plugin's downloads
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_download_slug'
 * @return string Slug
 */
function paco2017_get_download_slug() {
	return apply_filters( 'paco2017_get_download_slug', get_option( '_paco2017_download_slug', 'paco-downloads' ) );
}

/**
 * Return the rewrite ID for the plugin's downloads
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_download_rewrite_id'
 * @return string Rewrite ID
 */
function paco2017_get_download_rewrite_id() {
	return apply_filters( 'paco2017_get_download_rewrite_id', 'paco_download' );
}

/**
 * Return the url for the plugin's downloadable attachment
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_download_url'
 *
 * @param WP_Post|int $attachment Post object or ID
 * @return string|bool Attachment download url or false when attachment was not found
 */
function paco2017_get_download_url( $attachment ) {

	// Bail when this is not an attachment
	if ( ! $attachment = paco2017_get_download_attachment( $attachment ) )
		return false;

	$url = home_url( user_trailingslashit( trailingslashit( paco2017_get_download_slug() ) . $attachment->post_name ) );

	return apply_filters( 'paco2017_get_download_url', $url, $attachment );
}

/**
 * Process any download request
 *
 * @since 1.0.0
 */
function paco2017_parse_download_request() {

	// Bail when this is not a download request
	if ( empty( $GLOBALS['wp']->query_vars[ paco2017_get_download_rewrite_id() ] ) )
		return;

	$attachment = paco2017_get_download_attachment();

	// Bail when attachment access is denied
	if ( ! paco2017_check_download_access( $attachment ) )
		return;

	// Bail when the file is not found
	if ( ! $file = paco2017_get_download_file( $attachment ) )
		return;

	paco2017_setup_download_environment();

	// Update download count
	paco2017_update_download_count( $attachment );

	// Setup request headers
	nocache_headers();
	header( 'Robots: none' );
	header( 'Content-Type: ' . paco2017_get_download_mime_type( $file, $attachment ) );
	header( 'Content-Description: File Transfer' );
	header( 'Content-Disposition: inline; filename="' . paco2107_get_download_filename( $file, $attachment ) . '"' );
	header( 'Accept-Ranges: bytes' );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Content-Length: ' . @filesize( $file ) );

	// Lighttpd Server
	if ( stristr( getenv( 'SERVER_SOFTWARE' ), 'lighttpd' ) ) {
		header( "X-LIGHTTPD-send-file: {$file}" );

	// NginX or Cherokee Server
	} elseif ( stristr( getenv( 'SERVER_SOFTWARE' ), 'nginx' ) || stristr( getenv( 'SERVER_SOFTWARE' ), 'cherokee' ) ) {

		// We need a path relative to the domain
		$file = str_ireplace( realpath( $_SERVER['DOCUMENT_ROOT'] ), '', $file );
		header( "X-Accel-Redirect: /{$file}" );
	}

	paco2017_deliver_download_file( $file );

	die();
}

/**
 * Return the requested attachment object
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $attachment Optional. Post object or ID. Defaults to the requested download.
 * @return WP_Post|bool Attachment object or False when not found.
 */
function paco2017_get_download_attachment( $attachment = 0 ) {

	$rewrite_id = paco2017_get_download_rewrite_id();

	// Default to the requested
	if ( empty( $attachment ) && ! empty( $GLOBALS['wp']->query_vars[ $rewrite_id ] ) ) {
		$attachment = get_page_by_path( $GLOBALS['wp']->query_vars[ $rewrite_id ] );
	}

	$attachment = get_post( $attachment );

	if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
		$attachment = false;
	}

	return $attachment;
}

/**
 * Return whether the current user has access to the download file
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_check_download_access'
 *
 * @param WP_Post|int $attachment Optional. Post object or ID. Defaults to the requested download.
 * @param int $user_id Optional. User ID. Defaults to the current user
 * @return bool Has the user access?
 */
function paco2017_check_download_access( $attachment = 0, $user_id = 0 ) {

	// Bail when this is not an attachment
	if ( ! $attachment = paco2017_get_download_attachment( $attachment ) )
		return false;

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	return apply_filters( 'paco2017_check_download_access', true, $attachment, $user_id );
}

/**
 * Return the requested attachment file path
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $attachment Optional. Post object or ID. Defaults to the requested download.
 * @return string|bool Attachment file path or False when not found.
 */
function paco2017_get_download_file( $attachment = 0 ) {

	// Bail when this is not an attachment
	if ( ! $attachment = paco2017_get_download_attachment( $attachment ) )
		return false;

	// Get filtered attachment file
	$file = get_attached_file( $attachment->ID );

	if ( ! file_exists( $file ) )
		return false;

	return $file;
}

/**
 * Return the requested attachment file mime type
 *
 * @since 1.0.0
 *
 * @param string $file Path to the file
 * @param WP_Post|int $attachment Optional. Post object or ID. Defaults to the requested download.
 * @return string Attachment file mime type
 */
function paco2017_get_download_mime_type( $file, $attachment = 0 ) {

	// Get the attachment
	$attachment = paco2017_get_download_attachment( $attachment );

	// When on mobile, always do octet-stream
	if ( wp_is_mobile() ) {
		$mime = 'application/octet-stream';

	// Use the attachment's mime type
	} elseif ( $attachment && ! empty( $attachment->post_mime_type ) ) {
		$mime = $attachment->post_mime_type;

	// Determine based on file extension
	} else {
		$parts = explode( '.', $file );
		$ext   = end( $parts );
		$t     = wp_get_mime_types();

		if ( isset( $t[ $ext ] ) ) {
			$mime = $t[ $ext ];
		} else {
			foreach ( array_fill_keys( $t, function( $ext ) {
				return strpos( $ext, '|' );
			}) as $t_ext => $t_mime ) {
				if ( in_array( $ext, explode( '|', $t_ext ) ) ) {
					$mime = $t_mime;
					break;
				}
			}
		}
	}

	return $mime;
}

/**
 * Return the requested attachment file mime type
 *
 * @since 1.0.0
 *
 * @param string $file Path to the file
 * @param WP_Post|int $attachment Optional. Post object or ID. Defaults to the requested download.
 * @return string Attachment file mime type
 */
function paco2107_get_download_filename( $file, $attachment = 0 ) {

	// Get the attachment
	$attachment = paco2017_get_download_attachment( $attachment );

	// Define filename based on attachment's title
	if ( $attachment ) {
		$name  = get_the_title( $attachment );
		$parts = explode( '.', $file );
		$name .= '.' . end( $parts );

	// Default to the file's name
	} else {
		$name = basename( $file );
	}

	return apply_filters( 'paco2107_get_download_filename', $name, $file, $attachment );
}

/**
 * Setup server environment globals for the download process
 *
 * @since 1.0.0
 */
function paco2017_setup_download_environment() {
	$disabled = explode( ',', ini_get( 'disable_functions' ) );

	if ( ! in_array( 'set_time_limit', $disabled ) && ! ini_get( 'safe_mode' ) ) {
		@set_time_limit( 0 );
	}

	if ( function_exists( 'get_magic_quotes_runtime' ) && get_magic_quotes_runtime() && version_compare( phpversion(), '5.4', '<' ) ) {
		set_magic_quotes_runtime( 0 );
	}

	@session_write_close();

	if ( function_exists( 'apache_setenv' ) ) {
		@apache_setenv( 'no-gzip', 1 );
	}

	@ini_set( 'zlib.output_compression', 'Off' );
}

/**
 * Deliver the download file
 *
 * @since 1.0.0
 *
 * @param string $file Path to the file to deliver
 */
function paco2017_deliver_download_file( $file ) {

	// Serve the local file in chunks
	if ( paco2017_is_download_local_file( $file ) ) {
		paco2017_read_download_file_chunked( $file );

	// Redirect to the file
	} else {
		header( "Location: {$file}" );
	}
}

/**
 * Return whether the given file is hosted locally
 *
 * @since 1.0.0
 *
 * @param string $file File url or path
 * @return bool Is the file hosted locally?
 */
function paco2017_is_download_local_file( $file ) {
	$home_url = preg_replace( '#^https?://#', '', home_url() );
	$file_url = preg_replace( '#^(https?|file)://#', '', $file );

	$is_local_url     = 0 === strpos( $file, $home_url );
	$is_local_path    = 0 === strpos( $file, ABSPATH );
	$is_relative_path = 0 === strpos( $file, '/' );

	return ( $is_local_url || $is_local_path || $is_relative_path );
}

/**
 * Read the file in chunks to enable large file downloads
 *
 * @since 1.0.0
 *
 * @param string $file Path to the file to read
 * @return bool File was succesfully read and closed
 */
function paco2017_read_download_file_chunked( $file ) {

	// Define local variable(s) and start reading the file
	$chunksize = 1024 * 1024;
	$buffer    = '';
	$handle    = @fopen( $file, 'r' );

	if ( $size = @filesize( $file ) ) {
		header( "Content-Length: {$size}" );
	}

	// Bail when the file could not be opened
	if ( false === $handle ) {
		return false;
	}

	// Output file contents per chunksize
	while ( ! @feof( $handle ) ) {
		$buffer = @fread( $handle, $chunksize );
		echo $buffer;
	}

	$status = @fclose( $handle );

	return $status;
}

/**
 * Bump the download count of the given attachment
 * 
 * @since 1.0.0
 *
 * @param WP_Post|int $attachment Optional. Post object or ID. Defaults to the requested download.
 * @param int $user_id Optional. User ID to update for. Defaults to the current user
 * @return bool Update success
 */
function paco2017_update_download_count( $attachment = 0, $user_id = 0 ) {

	// Bail when this is not an attachment
	if ( ! $attachment = paco2017_get_download_attachment( $attachment ) )
		return false;

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$count = (array) get_post_meta( $attachment->ID, 'paco2017_download_count', true );
	$count[ $user_id ][] = time();

	return update_post_meta( $attachment->ID, 'paco2017_download_count', $count );
}
