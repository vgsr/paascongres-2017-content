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
function paco2017_dashboard_status() { ?>

	<div class="main">
		<p><?php _e( 'Hello World!', 'paco2017-content' ); ?></p>
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
