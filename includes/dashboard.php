<?php

/**
 * Paco2017 Content Dashboard Functions
 *
 * @package Paco2017 Content
 * @subpackage Administration
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Act when the Dashboard admin page is being loaded
 *
 * @see wp-admin/index.php
 *
 * @since 1.0.0
 */
function paco2017_admin_load_dashboard_page() {

	/** Load WordPress dashboard API */
	require_once( ABSPATH . 'wp-admin/includes/dashboard.php' );

	do_action( 'paco2017_dashboard_setup' );

	wp_enqueue_script( 'dashboard' );

	if ( wp_is_mobile() ) {
		wp_enqueue_script( 'jquery-touch-punch' );
	}
}

/**
 * Output the contents of the Dashboard admin page
 *
 * @see wp-admin/index.php
 *
 * @since 1.0.0
 */
function paco2017_admin_dashboard_page() { ?>

	<div id="dashboard-widgets-wrap">

		<?php wp_dashboard(); ?>

	</div><!-- dashboard-widgets-wrap -->

	<?php
}

/**
 * Output the contents of the Enrollment Status dashboard widget
 *
 * @since 1.0.0
 */
function paco2017_dashboard_status() {

	$blog_id = get_current_blog_id();

	// Get counts
	$user_count = wp_cache_get( $blog_id . '_user_count', 'blog-details' );
	if ( ! $user_count ) {
		$blog_users = get_users( array( 'blog_id' => $blog_id, 'fields' => 'ID' ) );
		$user_count = count( $blog_users );
		unset( $blog_users );
		wp_cache_set( $blog_id . '_user_count', $user_count, 'blog-details', 12 * HOUR_IN_SECONDS );
	};

	$lector_post_type   = paco2017_get_lector_post_type();
	$workshop_post_type = paco2017_get_workshop_post_type();
	$lector_count       = wp_count_posts( $lector_post_type   );
	$workshop_count     = wp_count_posts( $workshop_post_type );
	$lector_count       = $lector_count->publish;
	$workshop_count     = $workshop_count->publish;

	// Collect statuses to display
	$statuses = apply_filters( 'paco2017_dashboard_statuses', array(

		// Users
		'user-count'     => sprintf( '<a href="%s">%s</a>',
			esc_url( admin_url( 'users.php' ) ),
			sprintf( _n( '%s Account',  '%s Accounts',  $user_count,     'paco2017-content' ), $user_count     )
		),

		// Lectors
		'lector-count'   => sprintf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'post_type' => $lector_post_type   ), admin_url( 'edit.php' ) ) ),
			sprintf( _n( '%s Lector',   '%s Lectors',   $lector_count,   'paco2017-content' ), $lector_count   )
		),

		// Workshops
		'workshop-count' => sprintf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'post_type' => $workshop_post_type ), admin_url( 'edit.php' ) ) ),
			sprintf( _n( '%s Workshop', '%s Workshops', $workshop_count, 'paco2017-content' ), $workshop_count )
		),
	) );

	?>

	<div class="main">
		<?php if ( ! empty( $statuses ) ) : ?>

		<ul>
			<?php foreach ( $statuses as $status => $label ) : ?>

			<li class="<?php echo esc_attr( $status ); ?>"><?php echo $label; ?></li>

			<?php endforeach; ?>
		</ul>

		<?php else : ?>

		<p><?php esc_html_e( 'There is currently nothing to display here.', 'paco2017-content' ); ?></p>

		<?php endif; ?>
	</div>

	<?php
}

/**
 * Output the current Enrollments status per association
 *
 * @since 1.0.0
 *
 * @uses Paco2017_Enrollments_Widget
 */
function paco2017_dashboard_enrollments() {
	the_widget( 'Paco2017_Enrollments_Widget', array( 'per_total' => true ) );
}
