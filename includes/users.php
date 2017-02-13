<?php

/**
 * Paco2017 Content Users Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Return whether the user is enrolled
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_is_user_enrolled'
 *
 * @param int $user_id Optional. User ID. Defaults to the current user.
 * @return bool Is the user enrolled?
 */
function paco2017_is_user_enrolled( $user_id = 0 ) {

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	return (bool) apply_filters( 'paco2017_is_user_enrolled', false, $user_id );
}

/**
 * Output the enrolled users count
 *
 * @since 1.0.0
 */
function paco2017_enrolled_users_count() {
	echo paco2017_get_enrolled_users_count();
}

/**
 * Return the enrolled users count
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_enrolled_users_count'
 * @return int Enrolled user count
 */
function paco2017_get_enrolled_users_count() {
	$users = paco2017_get_enrolled_users();

	// Grouped list
	if ( count( $users ) !== count( $users, COUNT_RECURSIVE ) ) {
		$count = array_sum( array_map( 'count', $users ) );

	// Plain list
	} else {
		$count = count( $users );
	}

	return (int) apply_filters( 'paco2017_get_enrolled_users_count', $count );
}

/**
 * Return the enrolled users
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_enrolled_users'
 * @return array Enrolled users
 */
function paco2017_get_enrolled_users() {
	return (array) apply_filters( 'paco2017_get_enrolled_users', array() );
}

/**
 * Return the enrolled users by association
 *
 * @since 1.1.0
 *
 * @param int|array $term_id Optional. Association term to query the users for. Defaults to all associations.
 * @param array $args Optional. Additional user query arguments.
 * @return array Enrolled users by association
 */
function paco2017_get_enrolled_users_by_association( $term_id = 0, $args = array() ) {
	$users = paco2017_get_enrolled_users();
	$users_by_association = array();

	// Parse user query args
	$args = wp_parse_args( $args, array(
		'fields'    => 'ID',
		'include'   => $users,
		'tax_query' => array()
	) );

	// Walk associations
	foreach ( get_terms( array(
		'taxonomy' => paco2017_get_association_tax_id(),
		'include'  => $term_id ? (array) $term_id : array()
	) ) as $term ) {

		// Setup association tax_query
		$_args = $args;
		$_args['tax_query'][] = array(
			'taxonomy' => paco2017_get_association_tax_id(),
			'terms'    => array( $term->term_id )
		);

		// Query users
		$users_by_association[ $term->term_id ] = get_users( $_args );
	}

	return $users_by_association;
}

/**
 * Return the enrolled users from cache
 *
 * @since 1.1.0
 *
 * @return array Enrolled users IDs
 */
function paco2017_get_enrolled_users_cache() {
	return get_option( '_paco2017_enrolled_users', array() );
}

/**
 * Update the enrolled users cache
 *
 * @since 1.1.0
 *
 * @param array $users Current list of enrolled users IDs
 */
function paco2017_update_enrolled_users_cache( $users = array() ) {
	update_option( '_paco2017_enrolled_users', array_map( 'absint', (array) $users ) );
}

/**
 * Modify the user query before it is executed
 *
 * @since 1.1.0
 *
 * @global WPDB $wpdb
 * @param WP_User_Query $user_query
 */
function paco2017_pre_user_query( $user_query ) {
	global $wpdb;

	/**
	 * Taxonomy queries are not supported by default in WP_User_Query.
	 * Untill {@link https://core.trac.wordpress.org/ticket/31383} is put
	 * into place, run our own tax query parser.
	 */
	$tax_query = $user_query->get( 'tax_query' );
	if ( $tax_query ) {
		$tax_query   = new WP_Tax_Query( $tax_query );
		$tax_clauses = $tax_query->get_sql( $wpdb->users, 'ID' );

		if ( ! empty( $tax_clauses['join'] ) ) {
			$user_query->query_from  .= $tax_clauses['join'];
			$user_query->query_where .= $tax_clauses['where'];
		}

		// Remove the tax query vars to prevent duplicate parsing
		$user_query->set( 'tax_query', null );
	}
}

/** Admin-Bar *****************************************************************/

/**
 * Return whether to show the admin bar
 *
 * @since 1.0.0
 *
 * @param bool $show Whether to show the admin bar
 * @return bool Whether to show the admin bar
 */
function paco2017_show_admin_bar( $show ) {

	// Hide admin bar for non-vgsr users
	if ( $show && function_exists( 'vgsr' ) && ! is_user_vgsr() ) {
		$show = false;
	}

	return $show;
}

/** Login *********************************************************************/

/**
 * In order to keep the user registering or logging in at the site in
 * the right (sub)domain, we modify and add the `redirect_to` login form
 * parameters.
 */

/**
 * Act at the login init
 *
 * @since 1.1.0
 */
function paco2017_login_init() {

	// Route to local url
	add_filter( 'network_site_url', 'paco2017_login_network_site_url',  1, 3 );
	add_action( 'resetpass_form',   'paco2017_login_redirect_to_input'       );
	add_filter( 'login_redirect',   'paco2017_login_redirect',         10, 3 );

	// Message
	add_action( 'login_message', 'paco2017_login_message' );
}

/**
 * Modify the network site url to return the local url
 *
 * @since 1.1.0
 *
 * @param string $url Network site url
 * @param string $path Route path
 * @param string $scheme Url scheme
 * @return string Site url
 */
function paco2017_login_network_site_url( $url, $path, $scheme ) {
	return site_url( $path, $scheme );
}

/**
 * Redirect back to $url when attempting to use the login page
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_login_redirect'
 *
 * @param string $url The url
 * @param string $raw_url Raw url
 * @param object $user User object
 */
function paco2017_login_redirect( $url, $raw_url, $user ) {

	// Raw redirect_to was passed, so use it
	if ( ! empty( $raw_url ) ) {
		$url = $raw_url;

	// $url was manually set in wp-login.php to redirect to admin
	} elseif ( admin_url() === $url ) {
		$url = site_url();

	// $url is empty
	} elseif ( empty( $url ) ) {
		$url = site_url();
	}

	return apply_filters( 'paco2017_login_redirect', $url, $raw_url, $user );
}

/**
 * Output the login redirect_to hidden input
 *
 * @since 1.1.0
 */
function paco2017_login_redirect_to_input() { ?>

	<input type="hidden" name="redirect_to" value="<?php echo esc_url( site_url() ); ?>" />

	<?php
}

/**
 * Modify the expiration time for the pw reset action
 *
 * Uses the enrollment deadline setting's value. Defaults to a week.
 *
 * @since 1.1.0
 *
 * @return int Experiation time in secs
 */
function paco2017_password_reset_expiration() {

	// Use Enrollment Deadline date
	if ( $date = paco2017_get_enrollment_deadline( 'Y-m-d' ) ) {
		$time = strtotime( $date ) - time();

	// Default to week timespan
	} else {
		$time = WEEK_IN_SECONDS;
	}

	return $time;
}

/**
 * Add an enrollment description message to the login form
 *
 * @since 1.1.0
 *
 * @param string $message Login message
 * @return string Login message
 */
function paco2017_login_message( $message ) {

	// Start output buffer
	ob_start(); ?>

	<div id="paco2017-login-message">
		<p class="message">
			<?php if ( paco2017_get_contact_email() ) : ?>
				<?php printf( esc_html__( "To enroll for the conference, you have to login with the credentials that were sent to you. If not, use your email address to request a new password or let us know at %s.", 'paco2017-content' ), paco2017_get_contact_email_link() ); ?>
			<?php else : ?>
				<?php esc_html_e( "To enroll for the conference, you have to login with the credentials that were sent to you. If not, use your email address to request a new password or let us know.", 'paco2017-content' ); ?>
			<?php endif; ?>
		</p>

		<p class="message">
			<?php if ( $date = paco2017_get_enrollment_deadline( get_option( 'date_format' ) ) ) : ?>
				<?php printf( esc_html__( 'Take note that you can change your enrollment details up to the deadline on %s.', 'paco2017-content' ), '<strong>' . $date . '</strong>' ); ?>
			<?php else : ?>
				<?php esc_html_e( 'Take note that you can change your enrollment details up to the deadline date.', 'paco2017-content' ); ?>
			<?php endif; ?>
		</p>
	</div>

	<?php

	// Append to message
	$message .= ob_get_clean();

	return $message;
}
