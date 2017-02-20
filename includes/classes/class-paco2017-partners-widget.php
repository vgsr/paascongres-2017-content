<?php

/**
 * Paco2017 Content Partners Widget
 *
 * @package Paco2017 Content
 * @subpackage Widgets
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Paco2017_Partners_Widget' ) ) :
/**
 * The Paco2017 Content Partners Widget class
 *
 * @since 1.1.0
 */
class Paco2017_Partners_Widget extends WP_Widget {

	/**
	 * Setup this widget
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		parent::__construct(
			false,
			_x( 'Paascongres Partners', 'widget title', 'paco2017-content' ),
			array(
				'description' => esc_html__( 'Display the partner sponsor links', 'paco2017-content' ),
				'classname'   => 'paco2017_partners_widget'
			)
		);
	}

	/**
	 * Register this widget
	 *
	 * @since 1.1.0
	 */
	public static function register() {
		register_widget( 'Paco2017_Partners_Widget' );
	}

	/**
	 * Output the widget's contents
	 *
	 * @since 1.1.0
	 *
	 * @param array $widget_args Widget layout arguments
	 * @param array $instance Widget instance arguments
	 */
	public function widget( $widget_args, $instance ) {

		// Parse defaults
		$instance = wp_parse_args( $instance, array(
			'posts_per_page' => -1,
		) );

		// Setup query
		$partners = new WP_Query( array(
			'post_type'      => paco2017_get_partner_post_type(),
			'posts_per_page' => $instance['posts_per_page'],
			'orderby'        => 'rand',
			'meta_query'     => array(
				array(
					'key'     => 'logo',
					'compare' => 'EXISTS'
				),
				array(
					'key'     => 'partner_url',
					'compare' => 'EXISTS'
				)
			)
		) );

		// Bail when there are no posts
		if ( ! $partners->have_posts() )
			return;

		// Open widget
		echo $widget_args['before_widget'];

		// Display title
		if ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) {
			echo $widget_args['before_title'] . esc_html( $instance['title'] ) . $widget_args['after_title'];
		}

		// Walk posts
		while ( $partners->have_posts() ) : $partners->the_post(); ?>

		<p class="partner-logo">
			<a href="<?php paco2017_the_partner_url(); ?>" target="_blank"><?php echo paco2017_the_partner_logo(); ?></a>
		</p>

		<?php endwhile;

		wp_reset_postdata();

		// Close widget
		echo $widget_args['after_widget'];
	}

	/**
	 * Output widget form elements
	 *
	 * @since 1.1.0
	 *
	 * @param array $instance Widget instance arguments
	 */
	public function form( $instance ) {

		// Define form defaults
		$instance       = wp_parse_args( (array) $instance, array( 'title' => '', 'post_per_page' => '' ) );
		$title          = sanitize_text_field( $instance['title'] );
		$posts_per_page = sanitize_text_field( $instance['posts_per_page'] );

		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php esc_html_e( 'Number:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="number" value="<?php echo esc_attr( $posts_per_page ); ?>" /></p>

		<?php
	}

	/**
	 * Sanitize widget update input before saving
	 *
	 * @since 1.1.0
	 *
	 * @param array $new_instance New widget instance input
	 * @param array $old_instance Previous widget instance
	 * @return array Sanitized widget input
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		// Only parse numbers
		if ( is_numeric( $new_instance['posts_per_page'] ) ) {
			$instance['posts_per_page'] = absint( $new_instance['posts_per_page'] );
		} else {
			unset( $instance['posts_per_page'] );
		}

		return $instance;
	}
}

endif; // class_exists
