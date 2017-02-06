<?php

/**
 * Paco2017 Content Enrollments Widget
 *
 * @package Paco2017 Content
 * @subpackage Widgets
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Paco2017_Enrollments_Widget' ) ) :
/**
 * The Paco2017 Content Enrollments Widget class
 *
 * @since 1.0.0
 */
class Paco2017_Enrollments_Widget extends WP_Widget {

	/**
	 * Setup this widget
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			false,
			_x( 'Paascongres Enrollments', 'widget title', 'paco2017-content' ),
			array(
				'description' => esc_html__( 'List the current enrollment status for all associations', 'paco2017-content' ),
				'classname'   => 'paco2017_enrollments_widget'
			)
		);
	}

	/**
	 * Register this widget
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		register_widget( 'Paco2017_Enrollments_Widget' );
	}

	/**
	 * Output the widget's contents
	 *
	 * @since 1.0.0
	 *
	 * @param array $widget_args Widget layout arguments
	 * @param array $instance Widget instance arguments
	 */
	public function widget( $widget_args, $instance ) {

		// Query enrolled users
		$users = paco2017_get_enrolled_users_by_association();

		// Bail when no associations were found
		if ( empty( $users ) )
			return;

		// Open widget
		echo $widget_args['before_widget'];

		// Display title
		if ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) {
			echo $widget_args['before_title'] . esc_html( $instance['title'] ) . $widget_args['after_title'];
		}

		echo '<dl>';

		foreach ( $users as $term_id => $enrolled ) {
			$term = get_term( $term_id );

			if ( ! $term || is_wp_error( $term ) )
				continue;

			echo '<dt class="paco2017-association-' . $term->term_id . '">' . $term->name . '</dt>';
			echo '<dd>' . count( $enrolled );
			
			if ( isset( $instance['per_total'] ) && $instance['per_total'] ) {
				echo ' / ' . $term->count;
			}

			echo '</dd>';
		}

		echo '</dl>';

		// Close widget
		echo $widget_args['after_widget'];
	}

	/**
	 * Output widget form elements
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Widget instance arguments
	 */
	public function form( $instance ) {

		// Define form defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = sanitize_text_field( $instance['title'] );

		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<?php
	}

	/**
	 * Sanitize widget update input before saving
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New widget instance input
	 * @param array $old_instance Previous widget instance
	 * @return array Sanitized widget input
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}
}

endif; // class_exists
