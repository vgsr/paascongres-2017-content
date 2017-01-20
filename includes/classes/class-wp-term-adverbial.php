<?php

/**
 * Term Adverbial Class
 *
 * @since 0.1.0
 * @author Laurens Offereins <https://github.com/lmoffereins>
 *
 * @package Plugins/Terms/Metadata/Adverbial
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Term_Adverbial' ) ) :
/**
 * Main WP Term Adverbial class
 *
 * @since 0.1.0
 */
final class WP_Term_Adverbial extends WP_Term_Meta_UI {

	/**
	 * @var string Plugin version
	 */
	public $version = '0.1.0';

	/**
	 * @var string Database version
	 */
	public $db_version = 201701200001;

	/**
	 * @var string Metadata key
	 */
	public $meta_key = 'adverbial';

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
			'meta_key' => 'adverbial',
			'labels'   => array(),
		) );

		// Setup the meta key and labels
		$this->meta_key = $args['meta_key'];
		$this->labels   = wp_parse_args( $args['labels'], array(
			'singular'     => esc_html__( 'Adverbial',  'wp-term-adverbial' ),
			'plural'       => esc_html__( 'Adverbials', 'wp-term-adverbial' ),
			'description'  => sprintf( esc_html__( 'Assign a custom adverbial text to use the term in a natural sentence. Any occurences of %s will be replaced with the literal term name.', 'wp-term-adverbial' ), '<code>%s</code>' ),

			// Help tab
			'help_title'   => esc_html__( 'Term Adverbial', 'wp-term-adverbial' ),
			'help_content' => sprintf( esc_html__( "Terms can have adverbial texts to have them fit naturally in a sentence. To use the term's literal name in the text, insert the %s sequence.", 'wp-term-adverbial' ), '<code>%s</code>' ),
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
		wp_enqueue_script( 'term-adverbial', $this->url . 'assets/js/term-adverbial.js', array( 'jquery' ), $this->db_version, true );
	}

	/**
	 * Add help tabs for `adverbial` column
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
		return strip_tags( $data );
	}

	/**
	 * Output the value for the custom column
	 *
	 * @since 0.1.0
	 *
	 * @param string $empty
	 * @param string $custom_column
	 * @param int    $term_id
	 *
	 * @return mixed
	 */
	public function add_column_value( $empty = '', $custom_column = '', $term_id = 0 ) {

		// Bail if no taxonomy passed or not on the `meta_key` column
		if ( empty( $_REQUEST['taxonomy'] ) || ( $this->meta_key !== $custom_column ) || ! empty( $empty ) ) {
			return;
		}

		// Output HTML element
		$meta   = $this->get_meta( $term_id );
		$retval = $this->no_value;

		// Output HTML element in not empty
		if ( ! empty( $meta ) ) {
			$retval = $this->format_output( $meta, $term_id );
		}

		echo $retval;
	}

	/**
	 * Return the formatted output for the colomn row
	 *
	 * @since 0.1.0
	 *
	 * @param string $meta
	 * @param int $term_id
	 */
	protected function format_output( $meta, $term_id = 0 ) {
		return '<span class="term-adverbial" data-adverbial="' . esc_attr( $meta ) . '">' . esc_html( sprintf( $meta, get_term_field( 'name', $term_id ) ) ) . '</span>';
	}

	/**
	 * Output the form field for this metadata when adding a new term
	 *
	 * Don't HTML-escape the description text.
	 *
	 * @since 0.1.0
	 */
	public function add_form_field() {
		?>

		<div class="form-field term-<?php echo esc_attr( $this->meta_key ); ?>-wrap">
			<label for="term-<?php echo esc_attr( $this->meta_key ); ?>">
				<?php echo esc_html( $this->labels['singular'] ); ?>
			</label>

			<?php $this->form_field(); ?>

			<?php if ( ! empty( $this->labels['description'] ) ) : ?>

				<p class="description">
					<?php echo $this->labels['description']; ?>
				</p>

			<?php endif; ?>

		</div>

		<?php
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

		<input type="text" name="term-<?php echo esc_attr( $this->meta_key ); ?>" id="term-<?php echo esc_attr( $this->meta_key ); ?>" value="<?php echo esc_attr( $value ); ?>">

		<?php
	}
}

endif;
