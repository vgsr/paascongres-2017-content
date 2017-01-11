<?php

/**
 * Term Date Class
 *
 * @since 0.1.0
 * @author Laurens Offereins <https://github.com/lmoffereins>
 *
 * @package Plugins/Terms/Metadata/Date
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Term_Date' ) ) :
/**
 * Main WP Term Date class
 *
 * @since 0.1.0
 */
final class WP_Term_Date extends WP_Term_Meta_UI {

	/**
	 * @var string Plugin version
	 */
	public $version = '0.1.0';

	/**
	 * @var string Database version
	 */
	public $db_version = 201612230001;

	/**
	 * @var string Metadata key
	 */
	public $meta_key = 'date';

	/**
	 * Hook into queries, admin screens, and more!
	 *
	 * @since 0.1.0
	 *
	 * @param string $file Path to the root plugin file
	 * @param array $args Class construction arguments
	 */
	public function __construct( $file = '', $args = array() ) {

		// Parse the defaults
		$args = wp_parse_args( $args, array(
			'meta_key' => 'date',
			'labels'   => array(),
		) );

		// Setup the meta key and labels
		$this->meta_key = $args['meta_key'];
		$this->labels   = wp_parse_args( $args['labels'], array(
			'singular'     => esc_html__( 'Date',  'wp-term-date' ),
			'plural'       => esc_html__( 'Dates', 'wp-term-date' ),
			'description'  => esc_html__( 'Assign a custom date to separate each item in time.', 'wp-term-date' ),

			// Help tab
			'help_title'   => esc_html__( 'Term Date', 'wp-term-date' ),
			'help_content' => esc_html__( 'Terms can have unique dates to help separate them from each other in time.', 'wp-term-date' ),
		) );

		// Call the parent and pass the file
		parent::__construct( $file );
	}

	/** Assets ****************************************************************/

	/**
	 * Enqueue quick-edit JS
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		// Enqueue the date picker
		wp_enqueue_script( 'jquery-ui-datepicker' );

		// Enqueue fancy dateing; includes quick-edit
		wp_enqueue_script( 'term-date', $this->url . 'assets/js/term-date.js',  array( 'jquery-ui-datepicker', 'jquery' ), $this->db_version, true );
		wp_enqueue_style( 'term-date', $this->url . 'assets/css/term-date.css', array(), $this->db_version );
	}

	/**
	 * Add help tabs for `date` column
	 *
	 * @since 0.1.0
	 */
	public function help_tabs() {
		get_current_screen()->add_help_tab(array(
			'id'      => "wp_term_{$this->meta_key}_help_tab",
			'title'   => $this->labels['help_title'],
			'content' => '<p>' . $this->labels['help_content'] . '</p>',
		) );
	}

	/**
	 * Method for sanitizing meta data
	 *
	 * @since 0.1.0
	 *
	 * @param   mixed $data
	 * @return  mixed
	 */
	public function sanitize_callback( $data = '' ) {
		$data = DateTime::createFromFormat( 'd-m-Y', $data );
		return $data->format( 'Y-m-d 00:00:00' );
	}

	/**
	 * Return the formatted output for the colomn row
	 *
	 * @since 0.1.0
	 *
	 * @param string $meta
	 */
	protected function format_output( $meta = '' ) {
		return '<span class="term-date" data-date="' . esc_attr( mysql2date( 'd-m-Y', $meta ) ) . '">' . mysql2date( get_option( 'date_format' ), $meta ) . '</span>';
	}

	/**
	 * Output the form field
	 *
	 * @since 0.1.0
	 *
	 * @param  $term
	 */
	protected function form_field( $term = '' ) {

		// Get the meta value
		$value = isset( $term->term_id )
			?  $this->get_meta( $term->term_id )
			: ''; ?>

		<input type="text" name="term-<?php echo esc_attr( $this->meta_key ); ?>" id="term-<?php echo esc_attr( $this->meta_key ); ?>" value="<?php echo esc_attr( mysql2date( 'd-m-Y', $value ) ); ?>">

		<?php
	}
}

endif;
