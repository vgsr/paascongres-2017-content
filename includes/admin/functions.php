<?php

/**
 * Paco2017 Content Admin Functions
 *
 * @package Paco2017 Content
 * @subpackage Administration
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Menu ****************************************************************/

/**
 * Modify the highlighed menu for the current admin page
 *
 * @since 1.1.0
 *
 * @global string $parent_file
 * @global string $submenu_file
 */
function paco2017_admin_menu_highlight() {
	global $parent_file, $submenu_file;

	// Get the screen
	$screen = get_current_screen();

	/**
	 * Tweak the post type and taxonomy subnav menus to show the right
	 * top menu and submenu item.
	 */

	// Main post types
	if ( in_array( $screen->post_type, array(
		paco2017_get_lecture_post_type(),
		paco2017_get_workshop_post_type(),
		paco2017_get_agenda_post_type(),
		paco2017_get_partner_post_type(),
	) ) ) {
		$parent_file  = 'paco2017';
		$submenu_file = "edit.php?post_type={$screen->post_type}";

	// Workshop specific taxonomies
	} elseif ( in_array( $screen->taxonomy, array(
		paco2017_get_workshop_cat_tax_id(),
		paco2017_get_workshop_round_tax_id(),
	) ) ) {
		$parent_file  = 'paco2017';
		$submenu_file = "edit.php?post_type=" . paco2017_get_workshop_post_type();

	// Agenda specific taxonomies
	} elseif ( in_array( $screen->taxonomy, array(
		paco2017_get_conf_day_tax_id(),
	) ) ) {
		$parent_file  = 'paco2017';
		$submenu_file = "edit.php?post_type=" . paco2017_get_agenda_post_type();

	// Partner specific taxonomies
	} elseif ( in_array( $screen->taxonomy, array(
		paco2017_get_partner_level_tax_id(),
	) ) ) {
		$parent_file  = 'paco2017';
		$submenu_file = "edit.php?post_type=" . paco2017_get_partner_post_type();

	// Speaker or Conference Location
	} elseif ( in_array( $screen->taxonomy, array(
		paco2017_get_speaker_tax_id(),
		paco2017_get_conf_location_tax_id(),
	) ) ) {
		$parent_file  = 'paco2017';
		$submenu_file = "edit-tags.php?taxonomy={$screen->taxonomy}";

	// User specific taxonomies
	} elseif ( in_array( $screen->taxonomy, array(
		paco2017_get_association_tax_id(),
	) ) ) {
		$parent_file  = 'paco2017';
		$submenu_file = "edit-tags.php?taxonomy={$screen->taxonomy}&post_type=user";

	// Default to settings
	} elseif ( 'paco2017' === $parent_file && null === $submenu_file ) {
		$parent_file  = 'paco2017';
		$submenu_file = 'paco2017-settings';
	}
}

/**
 * Add plugin admin submenu page for the given post type
 *
 * @since 1.1.0
 *
 * @param string $post_type Post type name
 * @param string $function Optional. Menu file or function. Defaults to the post type's edit.php
 * @return false|string Result from {@see add_submenu_page()}
 */
function paco2017_admin_submenu_post_type( $post_type = '', $function = '' ) {
	if ( ! $post_type_object = get_post_type_object( $post_type ) )
		return false;

	$menu_file = "edit.php?post_type={$post_type}";

	// Remove the default admin menu and its submenus, to prevent
	// the `$parent_file` override in `get_admin_page_parent()`
	remove_menu_page( $menu_file );
	unset( $GLOBALS['submenu'][ $menu_file ] );

	return add_submenu_page(
		'paco2017',
		$post_type_object->label,
		$post_type_object->labels->menu_name,
		$post_type_object->cap->edit_posts,
		! empty( $function ) ? $function : $menu_file
	);
}

/**
 * Add plugin admin submenu page for the given taxonomy
 *
 * @since 1.1.0
 *
 * @param string $taxonomy Taxonomy name
 * @param string $function Optional. Menu file or function. Defaults to the taxonomy's edit-tags.php
 * @return false|string Result from {@see add_submenu_page()}
 */
function paco2017_admin_submenu_taxonomy( $taxonomy = '', $function = '' ) {
	if ( ! $taxonomy = get_taxonomy( $taxonomy ) )
		return false;

	$menu_file = "edit-tags.php?taxonomy={$taxonomy->name}";

	return add_submenu_page(
		'paco2017',
		$taxonomy->labels->name,
		$taxonomy->labels->menu_name,
		$taxonomy->cap->manage_terms,
		! empty( $function ) ? $function : $menu_file
	);
}

/** Metaboxes ***********************************************************/

/**
 * Output the contents of the Lecture Details metabox
 *
 * @since 1.1.0
 *
 * @param WP_Post $post Current post object
 */
function paco2017_admin_lecture_details_metabox( $post ) {

	// Get taxonomies
	$speaker_tax  = paco2017_get_speaker_tax_id();

	?>

	<div class="paco2017_object_details">

	<p>
		<label for="taxonomy-<?php echo $speaker_tax; ?>"><?php esc_html_e( 'Speaker:', 'paco2017-content' ); ?></label>
		<?php
			$spkr_terms = wp_get_object_terms( $post->ID, $speaker_tax, array( 'fields' => 'ids' ) );

			wp_dropdown_categories( array(
				'name'             => "taxonomy-{$speaker_tax}",
				'taxonomy'         => $speaker_tax,
				'hide_empty'       => false,
				'selected'         => $spkr_terms ? $spkr_terms[0] : 0,
				'show_option_none' => esc_html__( '&mdash; No Speaker &mdash;', 'paco2017-content' ),
			) );
		?>
	</p>

	</div>

	<?php wp_nonce_field( 'lecture_details_metabox', 'lecture_details_metabox_nonce' ); ?>

	<?php
}

/**
 * Output the contents of the Workshop Details metabox
 *
 * @since 1.1.0
 *
 * @param WP_Post $post Current post object
 */
function paco2017_admin_workshop_details_metabox( $post ) {

	// Get taxonomies
	$workshop_round_tax = paco2017_get_workshop_round_tax_id();
	$speaker_tax        = paco2017_get_speaker_tax_id();
	$workshop_cat_tax   = paco2017_get_workshop_cat_tax_id();
	$conf_loc_tax       = paco2017_get_conf_location_tax_id();

	$limit = get_post_meta( $post->ID, 'limit', true );

	?>

	<div class="paco2017_object_details">

	<p>
		<label for="taxonomy-<?php echo $workshop_round_tax; ?>"><?php esc_html_e( 'Round:', 'paco2017-content' ); ?></label>
		<?php
			$round_terms = wp_get_object_terms( $post->ID, $workshop_round_tax, array( 'fields' => 'ids' ) );

			wp_dropdown_categories( array(
				'name'             => "taxonomy-{$workshop_round_tax}",
				'taxonomy'         => $workshop_round_tax,
				'hide_empty'       => false,
				'selected'         => $round_terms ? $round_terms[0] : 0,
				'show_option_none' => esc_html__( '&mdash; No Round &mdash;', 'paco2017-content' ),
			) );
		?>
	</p>

	<p>
		<label for="taxonomy-<?php echo $speaker_tax; ?>"><?php esc_html_e( 'Speaker:', 'paco2017-content' ); ?></label>
		<?php
			$spkr_terms = wp_get_object_terms( $post->ID, $speaker_tax, array( 'fields' => 'ids' ) );

			wp_dropdown_categories( array(
				'name'             => "taxonomy-{$speaker_tax}",
				'taxonomy'         => $speaker_tax,
				'hide_empty'       => false,
				'selected'         => $spkr_terms ? $spkr_terms[0] : 0,
				'show_option_none' => esc_html__( '&mdash; No Speaker &mdash;', 'paco2017-content' ),
			) );
		?>
	</p>

	<p>
		<label for="taxonomy-<?php echo $workshop_cat_tax; ?>"><?php esc_html_e( 'Category:', 'paco2017-content' ); ?></label>
		<?php
			$cat_terms = wp_get_object_terms( $post->ID, $workshop_cat_tax, array( 'fields' => 'ids' ) );

			wp_dropdown_categories( array(
				'name'             => "taxonomy-{$workshop_cat_tax}",
				'taxonomy'         => $workshop_cat_tax,
				'hide_empty'       => false,
				'selected'         => $cat_terms ? $cat_terms[0] : 0,
				'show_option_none' => esc_html__( '&mdash; No Category &mdash;', 'paco2017-content' ),
			) );
		?>
	</p>

	<p>
		<label for="taxonomy-<?php echo $conf_loc_tax; ?>"><?php esc_html_e( 'Location:', 'paco2017-content' ); ?></label>
		<?php
			$loc_terms = wp_get_object_terms( $post->ID, $conf_loc_tax, array( 'fields' => 'ids' ) );

			wp_dropdown_categories( array(
				'name'             => "taxonomy-{$conf_loc_tax}",
				'taxonomy'         => $conf_loc_tax,
				'hide_empty'       => false,
				'selected'         => $loc_terms ? $loc_terms[0] : 0,
				'show_option_none' => esc_html__( '&mdash; No Location &mdash;', 'paco2017-content' ),
			) );
		?>
	</p>

	<p>
		<label for="workshop_limit"><?php esc_html_e( 'Attendee Limit:', 'paco2017-content' ); ?></label>
		<input type="number" name="workshop_limit" id="workshop_limit" value="<?php echo esc_attr( $limit ); ?>" />
	</p>

	</div>

	<?php wp_nonce_field( 'workshop_details_metabox', 'workshop_details_metabox_nonce' ); ?>

	<?php
}

/**
 * Output the contents of the Agenda Details metabox
 *
 * @since 1.1.0
 *
 * @param WP_Post $post Current post object
 */
function paco2017_admin_agenda_details_metabox( $post ) {

	// Get taxonomies
	$conf_day_tax = paco2017_get_conf_day_tax_id();
	$conf_loc_tax = paco2017_get_conf_location_tax_id();

	// Define time input template
	$time_input = '<input type="number" class="time-part" name="%1$s" id="%1$s" step="1" min="0" max="%2$s" value="%3$s" />';

	?>

	<div class="paco2017_object_details">

	<p>
		<label for="taxonomy-<?php echo $conf_day_tax; ?>"><?php esc_html_e( 'Day:', 'paco2017-content' ); ?></label>
		<?php
			$day_terms = wp_get_object_terms( $post->ID, $conf_day_tax, array( 'fields' => 'ids' ) );

			wp_dropdown_categories( array(
				'name'             => "taxonomy-{$conf_day_tax}",
				'taxonomy'         => $conf_day_tax,
				'hide_empty'       => false,
				'selected'         => $day_terms ? $day_terms[0] : 0,
				'show_option_none' => esc_html__( '&mdash; No Day &mdash;', 'paco2017-content' ),
			) );
		?>
	</p>

	<p>
		<label for="agenda_time_start_hours"><?php esc_html_e( 'Time Start:', 'paco2017-content' ); ?></label>
		<?php
			$start = get_post_meta( $post->ID, 'time_start', true );
			printf( "<span>%s:%s</span>",
				sprintf( $time_input, 'agenda_time_start_hours', 23, strtok( $start, ':' ) ),
				sprintf( $time_input, 'agenda_time_start_mins',  59, substr( $start, strpos( $start, ':' ) + 1 ) )
			);
		?>
	</p>

	<p>
		<label for="agenda_time_end_hours"><?php esc_html_e( 'Time End:', 'paco2017-content' ); ?></label>
		<?php
			$end = get_post_meta( $post->ID, 'time_end', true );
			printf( "<span>%s:%s</span>",
				sprintf( $time_input, 'agenda_time_end_hours', 23, strtok( $end, ':' ) ),
				sprintf( $time_input, 'agenda_time_end_mins',  59, substr( $end, strpos( $end, ':' ) + 1 ) )
			);
		?>
	</p>

	<p>
		<label for="taxonomy-<?php echo $conf_loc_tax; ?>"><?php esc_html_e( 'Location:', 'paco2017-content' ); ?></label>
		<?php
			$loc_terms = wp_get_object_terms( $post->ID, $conf_loc_tax, array( 'fields' => 'ids' ) );

			wp_dropdown_categories( array(
				'name'             => "taxonomy-{$conf_loc_tax}",
				'taxonomy'         => $conf_loc_tax,
				'hide_empty'       => false,
				'selected'         => $loc_terms ? $loc_terms[0] : 0,
				'show_option_none' => esc_html__( '&mdash; No Location &mdash;', 'paco2017-content' ),
			) );
		?>
	</p>

	<p>
		<label for="agenda_related"><?php esc_html_e( 'Related:', 'paco2017-content' ); ?></label>
		<?php
			paco2017_dropdown_agenda_pages( array(
				'name'             => 'agenda_related',
				'selected'         => paco2017_get_agenda_related_id( $post ),
				'show_option_none' => esc_html__( '&mdash; No Relation &mdash;', 'paco2017-content' ),
			) );
		?>
	</p>

	</div>

	<?php wp_nonce_field( 'agenda_details_metabox', 'agenda_details_metabox_nonce' ); ?>

	<?php
}

/**
 * Output the contents of the Partner Details metabox
 *
 * @since 1.1.0
 *
 * @param WP_Post $post Current post object
 */
function paco2017_admin_partner_details_metabox( $post ) {

	// Get taxonomies
	$level_tax = paco2017_get_partner_level_tax_id();

	$url = get_post_meta( $post->ID, 'partner_url', true );
	$post_type_object = get_post_type_object( $post->post_type );

	?>

	<div class="paco2017_object_details">

	<p id="partner_logo">
		<label for="partner_logo"><?php esc_html_e( 'Logo:', 'paco2017-content' ); ?></label>
		<?php if ( function_exists( 'wp_post_media_field' ) ) wp_post_media_field( $post, 'logo' ); ?>
	</p>

	<p>
		<label for="partner_url"><?php esc_html_e( 'Partner URL:', 'paco2017-content' ); ?></label>
		<input type="text" name="partner_url" id="partner_url" value="<?php echo esc_attr( $url ); ?>" />
	</p>

	<p>
		<label for="taxonomy-<?php echo $level_tax; ?>"><?php esc_html_e( 'Level:', 'paco2017-content' ); ?></label>
		<?php
			$lvl_terms = wp_get_object_terms( $post->ID, $level_tax, array( 'fields' => 'ids' ) );

			wp_dropdown_categories( array(
				'name'             => "taxonomy-{$level_tax}",
				'taxonomy'         => $level_tax,
				'hide_empty'       => false,
				'selected'         => $lvl_terms ? $lvl_terms[0] : 0,
				'show_option_none' => esc_html__( '&mdash; No Level &mdash;', 'paco2017-content' ),
			) );
		?>
	</p>

	</div>

	<?php wp_nonce_field( 'partner_details_metabox', 'partner_details_metabox_nonce' ); ?>

	<?php
}
