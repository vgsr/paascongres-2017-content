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
	$enrolled_count = paco2017_get_enrolled_users_count();
	$user_count = wp_cache_get( $blog_id . '_user_count', 'blog-details' );
	if ( ! $user_count ) {
		$blog_users = get_users( array( 'blog_id' => $blog_id, 'fields' => 'ID' ) );
		$user_count = count( $blog_users );
		unset( $blog_users );
		wp_cache_set( $blog_id . '_user_count', $user_count, 'blog-details', 12 * HOUR_IN_SECONDS );
	};

	// Assets
	$lecture     = paco2017_get_lecture_post_type();
	$workshop    = paco2017_get_workshop_post_type();
	$agenda      = paco2017_get_agenda_post_type();
	$speaker     = paco2017_get_speaker_tax_id();
	$conf_day    = paco2017_get_conf_day_tax_id();
	$conf_loc    = paco2017_get_conf_location_tax_id();
	$association = paco2017_get_association_tax_id();

	// Counts
	$lecture_count     = wp_count_posts( $lecture     );
	$workshop_count    = wp_count_posts( $workshop    );
	$agenda_count      = wp_count_posts( $agenda      );
	$speaker_count     = wp_count_terms( $speaker     );
	$conf_day_count    = wp_count_terms( $conf_day    );
	$conf_loc_count    = wp_count_terms( $conf_loc    );
	$association_count = wp_count_terms( $association );

	// Collect statuses to display
	$statuses = apply_filters( 'paco2017_dashboard_statuses', array(

		// Enrolled
		'enrolled-count' => sprintf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'enrolled' => 1 ), admin_url( 'users.php' ) ) ),
			sprintf( _n( '%s Enrolled', '%s Enrolled', $enrolled_count, 'paco2017-content' ), $enrolled_count )
		),

		// Users
		'user-count' => sprintf( '<a href="%s">%s</a>',
			esc_url( admin_url( 'users.php' ) ),
			sprintf( _n( '%s Account', '%s Accounts', $user_count, 'paco2017-content' ), $user_count )
		),

		// Association
		'association-count' => sprintf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'taxonomy' => $association, 'post_type' => 'user' ), admin_url( 'edit-tags.php' ) ) ),
			sprintf( _n( '%s Association', '%s Associations', $association_count, 'paco2017-content' ), $association_count )
		),

		// Lectures
		'lecture-count' => sprintf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'post_type' => $lecture ), admin_url( 'edit.php' ) ) ),
			sprintf( _n( '%s Lecture', '%s Lectures', $lecture_count->publish, 'paco2017-content' ), $lecture_count->publish )
		),

		// Workshops
		'workshop-count' => sprintf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'post_type' => $workshop ), admin_url( 'edit.php' ) ) ),
			sprintf( _n( '%s Workshop', '%s Workshops', $workshop_count->publish, 'paco2017-content' ), $workshop_count->publish )
		),

		// Speakers
		'speaker-count' => sprintf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'taxonomy' => $speaker ), admin_url( 'edit-tags.php' ) ) ),
			sprintf( _n( '%s Speaker', '%s Speakers', $speaker_count, 'paco2017-content' ), $speaker_count )
		),

		// Conference Days
		'conf_day-count' => sprintf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'taxonomy' => $conf_day ), admin_url( 'edit-tags.php' ) ) ),
			sprintf( _n( '%s Day', '%s Days', $conf_day_count, 'paco2017-content' ), $conf_day_count )
		),

		// Agenda Items
		'agenda-count' => sprintf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'post_type' => $agenda ), admin_url( 'edit.php' ) ) ),
			sprintf( _n( '%s Agenda Item', '%s Agenda Items', $agenda_count->publish, 'paco2017-content' ), $agenda_count->publish )
		),

		// Conference Locations
		'conf_location-count' => sprintf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'taxonomy' => $conf_loc ), admin_url( 'edit-tags.php' ) ) ),
			sprintf( _n( '%s Location', '%s Locations', $conf_loc_count, 'paco2017-content' ), $conf_loc_count )
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

		<p class="description"><?php esc_html_e( 'There is currently nothing to display here.', 'paco2017-content' ); ?></p>

		<?php endif; ?>
	</div>

	<?php
}

/**
 * Output the current Enrollments status overall and per association
 *
 * @since 1.0.0
 *
 * @uses Paco2017_Enrollments_Widget
 */
function paco2017_dashboard_enrollments() { ?>

	<div class="main">
		<span><?php paco2017_enrolled_users_count(); ?></span>
	</div>

	<?php

	// Display per-association enrollments
	the_widget( 'Paco2017_Enrollments_Widget', array( 'per_total' => true ) );
}
