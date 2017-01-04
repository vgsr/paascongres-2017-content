<?php

/**
 * Paco2017 Content BuddyPress Admin Functions
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Paco2017_BuddyPress_Admin' ) ) :
/**
 * The Paco2017 BuddyPress Admin class
 *
 * @since 1.0.0
 */
class Paco2017_BuddyPress_Admin {

	/**
	 * Setup this class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Define default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// XProfile
		add_action( 'xprofile_admin_field_name_legend', array( $this, 'xprofile_field_admin_label' ), 99 );
		add_action( 'xprofile_field_before_submitbox',  array( $this, 'xprofile_details_metabox'   )     );
		add_action( 'xprofile_fields_saved_field',      array( $this, 'xprofile_save_metabox'      )     );
	}

	/** Public methods **************************************************/

	/**
	 * Enqueue additional styles and scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		// Get current screen
		$screen = get_current_screen();

		// Define additional custom styles
		$css = array();

		// XProfile fields
		if ( 'users_page_bp-profile-setup' === $screen->id ) {
			$css[] = ".post-state { color: #555; font-weight: 600; }";
		}

		// Appens styles
		if ( ! empty( $css ) ) {
			wp_add_inline_style( 'paco2017-admin', implode( "\n", $css ) );
		}
	}

	/** XProfile ********************************************************/

	/**
	 * Add content to the profile field admin label
	 *
	 * @since 1.0.0
	 *
	 * @param BP_XProfile_Field $field
	 */
	public function xprofile_field_admin_label( $field ) {

		// Define label wrap
		$wrap = '<span class="post-state">&mdash; %s</span>';

		// Enrollment field
		if ( paco2017_bp_xprofile_is_enrollment_field( $field ) ) {
			printf( $wrap, esc_html_x( 'Enrollment Field', 'admin field label', 'paco2017-content' ) );

		// Association field
		} elseif ( paco2017_bp_xprofile_is_association_field( $field ) ) {
			printf( $wrap, esc_html_x( 'Association Field', 'admin field label', 'paco2017-content' ) );
		}
	}

	/**
	 * Output the profile field Details metabox
	 *
	 * @since 1.0.0
	 *
	 * @param BP_XProfile_Field $field
	 */
	public function xprofile_details_metabox( $field ) {

		// Bail when this is the primary field
		if ( 1 === $field->id )
			return;

		?>

		<div id="paco2017-details-div" class="postbox">
			<h2><?php _e( 'Paascongres Details', 'paco2017-content' ); ?></h2>
			<div class="inside">
				<div>
					<ul>
						<li>
							<label for="paco2017-enrollment-complete">
								<input type="checkbox" id="paco2017-enrollment-complete" name="paco2017-enrollment-complete" value="1" <?php checked( bp_xprofile_get_meta( $field->id, 'field', 'paco2017_enrollment_complete' ) ); ?> />
								<?php esc_html_e( 'Require field for enrollment completion', 'buddypress' ); ?>
							</label>
						</li>
					</ul>
				</div>
			</div>

			<input type="hidden" name="paco2017-field-details" value="1" />
		</div>

		<?php
	}

	/**
	 * Save the contents of the profile field Details metabox
	 *
	 * @since 1.0.0
	 *
	 * @param BP_XProfile_Field $field
	 */
	public function xprofile_save_metabox( $field ) {

		// Bail when the metabox was not submitted
		if ( ! isset( $_POST['paco2017-field-details'] ) )
			return;

		// Walk field activity options
		foreach ( array(
			'paco2017-enrollment-complete' => 'paco2017_enrollment_complete',
		) as $option => $meta_key ) {

			// Define meta value
			$value = (int) ( isset( $_POST[ $option ] ) && $_POST[ $option ] );

			// Update field meta
			bp_xprofile_update_meta( $field->id, 'field', $meta_key, $value );
		}
	}
}

/**
 * Setup the extension logic for BuddyPress
 *
 * @since 1.0.0
 *
 * @uses Paco2017_BuddyPress_Admin
 */
function paco2017_buddypress_admin() {
	paco2017_content()->extend->bp->admin = new Paco2017_BuddyPress_Admin;
}

endif; // class_exists
