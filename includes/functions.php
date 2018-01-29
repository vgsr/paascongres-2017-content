<?php

/**
 * Paco2017 Content Functions
 *
 * @package Paco2017 Content
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Versions ******************************************************************/

/**
 * Output the plugin version
 *
 * @since 1.1.0
 */
function paco2017_version() {
	echo paco2017_get_version();
}

	/**
	 * Return the plugin version
	 *
	 * @since 1.0.0
	 *
	 * @return string The plugin version
	 */
	function paco2017_get_version() {
		return paco2017_content()->version;
	}

/**
 * Output the plugin database version
 *
 * @since 1.1.0
 */
function paco2017_db_version() {
	echo paco2017_get_db_version();
}

	/**
	 * Return the plugin database version
	 *
	 * @since 1.1.0
	 *
	 * @return string The plugin version
	 */
	function paco2017_get_db_version() {
		return paco2017_content()->db_version;
	}

/**
 * Output the plugin database version directly from the database
 *
 * @since 1.1.0
 */
function paco2017_db_version_raw() {
	echo paco2017_get_db_version_raw();
}

	/**
	 * Return the plugin database version directly from the database
	 *
	 * @since 1.1.0
	 *
	 * @return string The current plugin version
	 */
	function paco2017_get_db_version_raw() {
		return get_option( '_paco2017_db_version', '' );
	}

/** Rewrite *******************************************************************/

/**
 * Return the rewrite ID for the Speaker taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speakers_rewrite_id'
 * @return string Rewrite ID
 */
function paco2017_get_speakers_rewrite_id() {
	return apply_filters( 'paco2017_get_speakers_rewrite_id', paco2017_get_speaker_tax_id() );
}

/**
 * Return the rewrite ID for the Agenda post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_agenda_rewrite_id'
 * @return string Rewrite ID
 */
function paco2017_get_agenda_rewrite_id() {
	return apply_filters( 'paco2017_get_agenda_rewrite_id', paco2017_get_agenda_post_type() );
}

/**
 * Return the rewrite ID for the Association taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_associations_rewrite_id'
 * @return string Rewrite ID
 */
function paco2017_get_associations_rewrite_id() {
	return apply_filters( 'paco2017_get_associations_rewrite_id', paco2017_get_association_tax_id() );
}

/**
 * Delete a blogs rewrite rules, so that they are automatically rebuilt on
 * the subsequent page load.
 *
 * @since 1.0.0
 */
function paco2017_delete_rewrite_rules() {
	delete_option( 'rewrite_rules' );
}

/**
 * Reduce the priority of the given rewrite rules permastruct
 *
 * @since 1.0.0
 *
 * @global WP_Rewrite $wp_rewrite
 *
 * @param string $rule_name The permastruct to reprioritize
 */
function paco2017_reduce_rewrite_rules_priority( $rule_name = '' ) {
	global $wp_rewrite;

	// Bail when the ruleset does not exist
	if ( ! array_key_exists( $rule_name, $wp_rewrite->extra_permastructs ) )
		return;

	// Get the current permastruct
	$args = $wp_rewrite->extra_permastructs[ $rule_name ];

	// Remove and append again at the end of the list
	remove_permastruct( $rule_name );
	$wp_rewrite->extra_permastructs[ $rule_name ] = $args;
}

/** Slugs *********************************************************************/

/**
 * Return the slug for the Lecture post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_lecture_slug'
 * @return string Slug
 */
function paco2017_get_lecture_slug() {
	return apply_filters( 'paco2017_get_lecture_slug', get_option( '_paco2017_lecture_slug', 'lectures' ) );
}

/**
 * Return the slug for the Workshop post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_slug'
 * @return string Slug
 */
function paco2017_get_workshop_slug() {
	return apply_filters( 'paco2017_get_workshop_slug', get_option( '_paco2017_workshop_slug', 'workshops' ) );
}

/**
 * Return the slug for the Workshop Category taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_cat_slug'
 * @return string Slug
 */
function paco2017_get_workshop_cat_slug() {
	return apply_filters( 'paco2017_get_workshop_cat_slug', get_option( '_paco2017_workshop_cat_slug', 'category' ) );
}

/**
 * Return the slug for the Workshop Round taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_workshop_round_slug'
 * @return string Slug
 */
function paco2017_get_workshop_round_slug() {
	return apply_filters( 'paco2017_get_workshop_round_slug', get_option( '_paco2017_workshop_round_slug', 'round' ) );
}

/**
 * Return the slug for the Speaker taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_speakers_slug'
 * @return string Slug
 */
function paco2017_get_speakers_slug() {
	return apply_filters( 'paco2017_get_speakers_slug', get_option( '_paco2017_speakers_slug', 'speakers' ) );
}

/**
 * Return the slug for the Agenda post type
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_agenda_slug'
 * @return string Slug
 */
function paco2017_get_agenda_slug() {
	return apply_filters( 'paco2017_get_agenda_slug', get_option( '_paco2017_agenda_slug', 'agenda' ) );
}

/**
 * Return the slug for the Association taxonomy
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_associations_slug'
 * @return string Slug
 */
function paco2017_get_associations_slug() {
	return apply_filters( 'paco2017_get_associations_slug', get_option( '_paco2017_associations_slug', 'associations' ) );
}

/**
 * Sanitize permalink slugs when saving the settings page.
 *
 * @since 1.0.0
 *
 * @param string $slug
 * @return string
 */
function paco2017_sanitize_slug( $slug = '' ) {

	// Don't allow multiple slashes in a row
	$value = preg_replace( '#/+#', '/', str_replace( '#', '', $slug ) );

	// Strip out unsafe or unusable chars
	$value = esc_url_raw( $value );

	// esc_url_raw() adds a scheme via esc_url(), so let's remove it
	$value = str_replace( 'http://', '', $value );

	// Trim off first and last slashes.
	//
	// We already prevent double slashing elsewhere, but let's prevent
	// accidental poisoning of options values where we can.
	$value = ltrim( $value, '/' );
	$value = rtrim( $value, '/' );

	// Filter the result and return
	return apply_filters( 'paco2017_sanitize_slug', $value, $slug );
}

/** URLs **********************************************************************/

/**
 * Return the url for the Speakers page
 *
 * @since 1.0.0
 *
 * @return string Url
 */
function paco2017_get_speakers_url() {
	return home_url( user_trailingslashit( paco2017_get_speakers_slug() ) );
}

/**
 * Return the url for the Agenda page
 *
 * @since 1.0.0
 *
 * @return string Url
 */
function paco2017_get_agenda_url() {
	return home_url( user_trailingslashit( paco2017_get_agenda_slug() ) );
}

/**
 * Return the url for the Associations page
 *
 * @since 1.0.0
 *
 * @return string Url
 */
function paco2017_get_associations_url() {
	return home_url( user_trailingslashit( paco2017_get_associations_slug() ) );
}

/** Menus *********************************************************************/

/**
 * Return the available custom plugin nav menu items
 *
 * @since 1.0.0
 *
 * @return array Custom nav menu item data objects
 */
function paco2017_nav_menu_get_items() {

	// Try to return items from cache
	if ( ! empty( paco2017_content()->wp_nav_menu_items ) ) {
		return paco2017_content()->wp_nav_menu_items;
	} else {
		paco2017_content()->wp_nav_menu_items = new stdClass;
	}

	// Setup nav menu items
	$items = (array) apply_filters( 'paco2017_nav_menu_get_items', array(

		// Agenda page
		'agenda' => array(
			'title'       => esc_html__( 'Agenda page', 'paco2017-content' ),
			'type_label'  => esc_html_x( 'Paascongres Agenda page', 'menu type label', 'paco2017-content' ),
			'url'         => paco2017_get_agenda_url(),
			'is_current'  => paco2017_is_agenda(),
		),

		// Association page
		'association' => array(
			'title'       => esc_html__( 'Associations page', 'paco2017-content' ),
			'type_label'  => esc_html_x( 'Paascongres Associations page', 'menu type label', 'paco2017-content' ),
			'url'         => paco2017_get_associations_url(),
			'is_current'  => paco2017_is_associations(),
		),

		// Speaker page
		'speaker' => array(
			'title'       => esc_html__( 'Speakers page', 'paco2017-content' ),
			'type_label'  => esc_html_x( 'Paascongres Speakers page', 'menu type label', 'paco2017-content' ),
			'url'         => paco2017_get_speakers_url(),
			'is_current'  => paco2017_is_speakers(),
		),

		// Speaker page
		'magazine_download_link' => array(
			'title'       => esc_html__( 'Magazine download link', 'paco2017-content' ),
			'type_label'  => esc_html_x( 'Paascongres Magazine Download Link', 'menu type label', 'paco2017-content' ),
			'url'         => paco2017_get_magazine_download_url(),
		),
	) );

	// Set default arguments
	foreach ( $items as $object => &$item ) {
		$item = (object) wp_parse_args( $item, array(
			'id'          => "paco2017-{$object}",
			'object'      => $object,
			'title'       => '',
			'type'        => 'paco2017',
			'type_label'  => esc_html_x( 'Paascongres Page', 'menu type label', 'paco2017-content' ),
			'url'         => '',
			'is_current'  => false,
			'is_parent'   => false,
			'is_ancestor' => false,
		) );
	}

	// Assign items to global
	paco2017_content()->wp_nav_menu_items = $items;

	return $items;
}

/**
 * Setup details of nav menu item for plugin pages
 *
 * @since 1.0.0
 *
 * @param WP_Post $menu_item Nav menu item object
 * @return WP_Post Nav menu item object
 */
function paco2017_setup_nav_menu_item( $menu_item ) {

	// Plugin page
	if ( 'paco2017' === $menu_item->type ) {

		// This is a registered custom menu item
		if ( $item = wp_list_filter( paco2017_nav_menu_get_items(), array( 'object' => $menu_item->object ) ) ) {
			$item = reset( $item );

			// Item doesn't come from the DB
			if ( ! isset( $menu_item->post_type ) ) {
				$menu_item->ID = -1;
				$menu_item->db_id = 0;
				$menu_item->menu_item_parent = 0;
				$menu_item->object_id = -1;
				$menu_item->target = '';
				$menu_item->attr_title = '';
				$menu_item->description = '';
				$menu_item->classes = '';
				$menu_item->xfn = '';
			}

			// Set item classes
			if ( ! is_array( $menu_item->classes ) ) {
				$menu_item->classes = array();
			}

			// Set item details
			$menu_item->type_label = $item->type_label;
			$menu_item->url        = $item->url;

			// This is the current page
			if ( $item->is_current ) {
				$menu_item->classes[] = 'current_page_item';
				$menu_item->classes[] = 'current-menu-item';

			// This is the parent page
			} elseif ( $item->is_parent ) {
				$menu_item->classes[] = 'current_page_parent';
				$menu_item->classes[] = 'current-menu-parent';

			// This is an ancestor page
			} elseif ( $item->is_ancestor ) {
				$menu_item->classes[] = 'current_page_ancestor';
				$menu_item->classes[] = 'current-menu-ancestor';
			}
		}

		// Prevent rendering when the user has no access
		if ( empty( $menu_item->url ) ) {
			$menu_item->_invalid = true;
		}

		// Enable plugin filtering
		$menu_item = apply_filters( 'paco2017_setup_nav_menu_item', $menu_item );

		// Prevent rendering when the user has no access
		if ( empty( $menu_item->url ) ) {
			$menu_item->_invalid = true;
		}
	}

	return $menu_item;
}

/**
 * Add custom plugin pages to the available nav menu items
 *
 * @see wp_nav_menu_item_post_type_meta_box()
 *
 * @since 1.0.0
 *
 * @global int        $_nav_menu_placeholder
 * @global int|string $nav_menu_selected_id
 *
 * @param string $object Not used.
 * @param array  $box {
 *     Post type menu item meta box arguments.
 *
 *     @type string       $id       Meta box 'id' attribute.
 *     @type string       $title    Meta box title.
 *     @type string       $callback Meta box display callback.
 *     @type WP_Post_Type $args     Extra meta box arguments (the post type object for this meta box).
 * }
 */
function paco2017_nav_menu_metabox( $object, $box ) {
	global $nav_menu_selected_id;

	$walker = new Walker_Nav_Menu_Checklist();
	$args   = array( 'walker' => $walker );

	?>
	<div id="paco2017-menu" class="posttypediv">

		<div id="tabs-panel-paco2017-menu" class="tabs-panel tabs-panel-active">
			<ul id="paco2017-menu-checklist" class="categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', paco2017_nav_menu_get_items() ), 0, (object) $args ); ?>
			</ul>
		</div><!-- /.tabs-panel -->

		<p class="button-controls wp-clearfix">
			<span class="list-controls">
				<a href="<?php
					echo esc_url( add_query_arg(
						array(
							'selectall' => 1,
						),
						remove_query_arg( array(
							'action',
							'customlink-tab',
							'edit-menu-item',
							'menu-item',
							'page-tab',
							'_wpnonce',
						) )
					));
				?>#paco2017-menu" class="select-all aria-button-if-js"><?php _e( 'Select All' ); ?></a>
			</span>

			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-paco2017-menu-item" id="submit-paco2017-menu" />
				<span class="spinner"></span>
			</span>
		</p>

	</div><!-- /.posttypediv -->
	<?php
}

/**
 * Set plugin item navs for the Customizer
 *
 * @since 1.0.0
 *
 * @param array $item_types Nav item types
 * @return array Nav item types
 */
function paco2017_customize_nav_menu_set_item_types( $item_types ) {

	// Plugin pages
	$item_types['paco2017'] = array(
		'title'      => esc_html__( 'Paascongres', 'paco2017-content' ),
		'type_label' => esc_html_x( 'Paascongres Page', 'menu type label', 'paco2017-content' ),
		'type'       => 'paco2017',
		'object'     => 'paco2017_nav'
	);

	return $item_types;
}

/**
 * Add custom plugin pages to the available menu items in the Customizer
 *
 * @since 1.0.0
 *
 * @param array $items The array of menu items.
 * @param string $type The object type.
 * @param string $object The object name.
 * @param int $page The current page number.
 * @return array Menu items
 */
function paco2017_customize_nav_menu_available_items( $items, $type, $object, $page ) {

	// Plugin pages - first query only
	if ( 'paco2017' === $type && 0 === $page ) {

		// Add plugin items
		foreach ( paco2017_nav_menu_get_items() as $item ) {
			$items[] = (array) $item; 
		}
	}

	return $items;
}

/**
 * Add custom plugin pages to the searched menu items in the Customizer
 *
 * @since 1.0.0
 *
 * @param array $items The array of menu items.
 * @param array $args Includes 'pagenum' and 's' (search) arguments.
 * @return array Menu items
 */
function paco2017_customize_nav_menu_searched_items( $items, $args ) {

	// Define search context
	$search = strtolower( $args['s'] );
	$_items = paco2017_nav_menu_get_items();
	$titles = wp_list_pluck( $_items, 'title' );
	$words  = array( 'paascongres', 'paco' );

	// Search query matches a part of the item titles
	foreach ( array_keys( array_filter( $titles, function( $title ) use ( $search ) {
		return false !== strpos( strtolower( $title ), $search );
	}) ) as $item_key ) {
		$items[] = (array) $_items[ $item_key ];
		unset( $_items[ $item_key ] );
	}

	// Search query matches a part of the provided words
	if ( array_filter( $words, function( $word ) use ( $search ) {
		return false !== strpos( $word, $search );
	}) ) {

		// Append all custom items
		foreach ( $_items as $item ) {
			$items[] = (array) $item;
		}
	}

	return $items;
}

/** Options *******************************************************************/

/**
 * Return the enrollment deadline date setting
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_enrollment_deadline'
 *
 * @param string $format Optional. Date format to return.
 * @return string Enrollment deadline date
 */
function paco2017_get_enrollment_deadline( $format = '' ) {
	$date = get_option( '_paco2017_enrollment_deadline', false );

	// Format the date
	if ( $format ) {
		$date = mysql2date( $format, $date );
	}

	return apply_filters( 'paco2017_get_enrollment_deadline', $date, $format );
}

/**
 * Return the contact email setting
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_get_contact_email'
 * @return string Contact email address
 */
function paco2017_get_contact_email() {
	return apply_filters( 'paco2017_get_contact_email', get_option( '_paco2017_contact_email' ), '' );
}

/**
 * Output the contact email link
 *
 * @since 1.1.0
 *
 * @param string $link_text Optional. The link text to display.
 */
function paco2017_the_contact_email_link( $link_text = '' ) {
	echo paco2017_get_contact_email_link( $link_text );
}

/**
 * Return the contact email link
 *
 * @since 1.1.0
 *
 * @uses apply_filters() Calls 'paco2017_contact_email_link'
 *
 * @param string $link_text Optional. The link text to display.
 * @return string Contact email link
 */
function paco2017_get_contact_email_link( $link_text = '' ) {
	$email = paco2017_get_contact_email();
	$link  = '';

	if ( $email ) {
		$text = $link_text ? $link_text : $email;
		$link = '<a href="mailto:' . $email . '">' . $text . '</a>';
	}

	return apply_filters( 'paco2017_get_contact_email_link', $link, $link_text, $email );
}

/**
 * Return the page ID of the General Notices page setting
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_general_notices_page_id'
 * @return int Page ID
 */
function paco2017_get_general_notices_page_id() {
	return (int) apply_filters( 'paco2017_get_general_notices_page_id', get_option( '_paco2017_general_notices_page', 0 ) );
}

/**
 * Return the page ID of the Magazine page setting
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_magazine_page_id'
 * @return int Page ID
 */
function paco2017_get_magazine_page_id() {
	return (int) apply_filters( 'paco2017_get_magazine_page_id', get_option( '_paco2017_magazine_page', 0 ) );
}

/** Magazine ******************************************************************/

/**
 * Return whether this is the Magazine page
 *
 * @since 1.0.0
 *
 * @return bool Is it the Magazine page?
 */
function paco2017_is_magazine_page() {
	return is_page() && get_the_ID() === paco2017_get_magazine_page_id();
}

/**
 * Return the attachment ID of the Magazine download file
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'paco2017_get_magazine_download'
 * @return int Attachment ID
 */
function paco2017_get_magazine_download() {
	return (int) apply_filters( 'paco2017_get_magazine_download', get_option( '_paco2017_magazine_download', false ) );
}

/**
 * Return the Magazine download url
 *
 * @since 1.0.0
 *
 * @return string Magazine url
 */
function paco2017_get_magazine_download_url() {

	// Require user to be logged in
	$attachment_id = paco2017_get_magazine_download();
	$url = '';

	// Download file is defined
	if ( $attachment_id ) {
		if ( is_user_logged_in() ) {
			$url = paco2017_get_download_url( $attachment_id );
		} else {
			$url = wp_login_url( paco2017_get_download_url( $attachment_id ) );
		}
	}

	return $url;
}

/**
 * Modify whether the Magazine file can be downloaded
 *
 * @since 1.0.0
 *
 * @param bool $access Access granted
 * @param WP_Post $attachment Attachment object
 * @param int $user_id User ID
 * @return bool Can file be downloaded?
 */
function paco2017_magazine_check_download_access( $access, $attachment, $user_id ) {

	// This is the magazine attachment. Allow site members only.
	if ( paco2017_get_magazine_download() === $attachment->ID ) {
		$access = is_user_member_of_blog( $user_id );
	}

	return $access;
}

/**
 * Modify the theme's Magazine download url
 *
 * @since 1.0.0
 *
 * @param string $url Optional. Default url when no magazine is available.
 * @return string Magazine url
 */
function paco2017_magazine_get_theme_download_url( $url = '' ) {

	// Provide the download url when on the Magazine page
	if ( paco2017_is_magazine_page() && paco2017_get_magazine_download() ) {
		$url = paco2017_get_magazine_download_url();
	}

	return $url;
}

/** Taxonomy ******************************************************************/

/**
 * Modify the list of queried terms in a REST request
 *
 * @since 1.0.0
 *
 * @param array $terms Queried terms
 * @param array $taxonomies Taxonomy names
 * @param array $query_vars Query variables
 * @param WP_Term_Query $term_query Term query object
 * @return array Terms
 */
function paco2017_rest_get_terms( $terms, $taxonomies, $query_vars, $term_query ) {

	// Bail when not querying terms whole
	if ( 'all' !== $query_vars['fields'] && 'all_with_object_id' !== $query_vars['fields'] )
		return $terms;

	// Bail when not in a REST request
	if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST )
		return $terms;

	// Get taxonomies
	$taxes = array(
		paco2017_get_workshop_cat_tax_id(),
		paco2017_get_workshop_round_tax_id(),
		paco2017_get_speaker_tax_id(),
		paco2017_get_conf_day_tax_id(),
		paco2017_get_conf_location_tax_id(),
		paco2017_get_association_tax_id(),
	);

	// Bail when not querying one of the defined taxonomies
	if ( ! array_intersect( $taxes, (array) $taxonomies ) )
		return $terms;

	// Get taxonomy data
	$taxes = array_filter( array_map( 'get_taxonomy', $taxes ) );
	$taxes = array_combine( wp_list_pluck( $taxes, 'name' ), $taxes );
	$metas = array( 'color', 'date' );

	// Queried conference days
	foreach ( $terms as $k => $term ) {

		// Skip when this is not one of ours
		if ( ! in_array( $term->taxonomy, array_keys( $taxes ) ) )
			continue;

		// Add term meta when supported
		foreach ( $metas as $meta ) {
			$meta_key = "term_meta_{$meta}";

			if ( ! isset( $taxes[ $term->taxonomy ]->{$meta_key} ) || true !== $taxes[ $term->taxonomy ]->{$meta_key} )
				continue;

			$term->{$meta} = get_term_meta( $term->term_id, $meta, true );
		}

		// Store modified term
		$terms[ $k ] = $term;
	}

	return $terms;
}

/**
 * Return the types that have registered this taxonomy
 *
 * @since 1.0.0
 *
 * @param string $taxonomy Taxonomy name
 * @return array Taxonomy types
 */
function paco2017_get_taxonomy_types( $taxonomy = 'category' ){
	global $wp_taxonomies;

	return isset( $wp_taxonomies[ $taxonomy ] ) ? $wp_taxonomies[ $taxonomy ]->object_type : array();
}

/**
 * Return the total found rows for the term query arguments
 *
 * @since 1.0.0
 *
 * @param array $query_args Original term query arguments.
 * @return int Total found rows
 */
function paco2017_query_terms_found_rows( $query_args ) {

	// Remove paging arguments
	unset( $query_args['offset'], $query_args['paged'] );

	// Define count query args
	$query_args['fields'] = 'count';
	$query_args['number'] = -1;

	// Run count query
	$count = get_terms( $query_args['taxonomy'], $query_args );

	return (int) $count;
}

/** Rest **********************************************************************/

/**
 * Return the rest value for an attachment image
 *
 * @since 1.0.0
 *
 * @param int $attachment_id Attachment ID
 * @param string|array $size Optional. Image size name or dimensions. Default to 'thumbnail'.
 * @return array|bool Image data or False when not found.
 */
function paco2017_get_rest_image( $attachment_id, $size = 'thumbnail' ) {

	// Bail when the parameter is not an attachment image
	if ( ! wp_attachment_is_image( $attachment_id ) )
		return false;

	$image = wp_get_attachment_image_src( $attachment_id, $size );
	if ( $image ) {
		unset( $image[3] ); // Remove is_intermediate

		// Provide array with keys
		$image = array_combine( array( 'src', 'width', 'height' ), $image );
		$image['id'] = $attachment_id;
	} else {
		$image = false;
	}

	return $image;
}

/**
 * Return the REST post meta value for the given object's field name
 *
 * @since 1.1.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return mixed Post meta value
 */
function paco2017_get_rest_post_meta( $object, $field_name, $request ) {
	return get_post_meta( $object['id'], $field_name, true );
}

/**
 * Return the REST term meta value for the given object's field name
 *
 * @since 1.1.0
 *
 * @param array $object Request object
 * @param string $field_name Request field name
 * @param WP_REST_Request $request Current REST request
 * @return mixed Term meta value
 */
function paco2017_get_rest_term_meta( $object, $field_name, $request ) {
	return get_term_meta( $object['id'], $field_name, true );
}

/** Theme *********************************************************************/

/**
 * Return whether the given background color requires a light text color
 *
 * @link Calculation of perceptive luminance. http://stackoverflow.com/a/1855903/3601434
 *
 * @since 1.0.0
 *
 * @param array $rgb Array of RGB color values
 * @return bool Whether a lighter textcolor is required
 */
function paco2017_light_textcolor_for_background( $rgb ) {
	$luminance = 1 - ( 0.299 * $rgb[0] + 0.587 * $rgb[1] + 0.114 * $rgb[2] ) / 255;

	return $luminance > .5;
}

/**
 * Enqueue plugin styles
 *
 * @since 1.0.0
 */
function paco2017_enqueue_styles() {

	wp_enqueue_style( 'paco2017-content', paco2017_content()->assets_url . 'css/paco2017-content.css' );

	// Define additional custom styles
	$template = get_template();
	$css = array();

	/** Associations **********************************************************/

	foreach ( get_terms( array(
		'taxonomy'   => paco2017_get_association_tax_id(),
		'hide_empty' => false
	) ) as $term ) {

		// Colors
		if ( $color = get_term_meta( $term->term_id, 'color', true ) ) {

			// Turn hex to rgb
			$_color    = sanitize_hex_color_no_hash( $color );
			$rgb       = array( hexdec( substr( $_color, 0, 2 ) ), hexdec( substr( $_color, 2, 2 ) ), hexdec( substr( $_color, 4, 2 ) ) );

			$css[] = ".paco2017_enrollments_widget .paco2017-association-{$term->term_id}, .paco2017_enrollments_widget .paco2017-association-{$term->term_id} + dd { border-bottom: 4px solid rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, .6); }";
		}
	}

	/** Speakers **************************************************************/

	foreach ( paco2017_get_taxonomy_types( paco2017_get_speaker_tax_id() ) as $post_type ) {
		$label = get_post_type_object( $post_type )->labels->singular_name;
		$css[] = ".paco2017-speakers .type-{$post_type} .item-object-title:before { content: '{$label}: '; }";
	}

	/** Advertorials **********************************************************/

	$css[] = ".paco2017-advertorial:before { content: '[" . __( 'Advertorial', 'paco2017-content' ) . "]'; }";

	/** Theme Specific ********************************************************/

	if ( 'twentyseventeen' === $template ) {
		$css[] = ".colors-dark .paco2017-info:not(.speaker-info) { color: #aaa; }";
		$css[] = ".speaker-info { margin-top: 2em; padding-top: 2em; border-top: 1px solid #eee; }";
		$css[] = ".colors-dark .speaker-info { border-top-color: #333; }";

		$css[] = ".colors-dark .paco2017-advertorial { color: #555; }";
	}

	if ( ! empty( $css ) ) {
		wp_add_inline_style( 'paco2017-content', implode( "\n", $css ) );
	}
}

/** Autoembed *****************************************************************/

/**
 * Initializes {@link Paco2017_Embed} after everything is loaded.
 *
 * @since 1.1.0
 */
function paco2017_embed_init() {

	// Get the plugin
	$plugin = paco2017_content();

	if ( empty( $plugin->embed ) ) {
		require_once( $plugin->includes_dir . 'classes/class-paco2017-embed.php' );

		$plugin->embed = new Paco2017_Embed();
	}
}

/**
 * Run WP's autoembeds converter on the provided content
 *
 * @since 1.1.0
 *
 * @param string $content Content
 * @return string Content
 */
function paco2017_content_autoembed( $content ) {
	$content = paco2017_content()->embed->run_shortcode( $content );
	$content = paco2017_content()->embed->autoembed( $content );

	return $content;
}

/**
 * Return the autoembed cache for the option content's cachekey
 *
 * @since 1.1.0
 *
 * @param string $cache    Empty initial cache value.
 * @param int    $id       ID that the caching is for.
 * @param string $cachekey Key to use for the caching in the database.
 * @return string Option cache
 */
function paco2017_get_option_autoembed_cache( $cache, $id, $cachekey ) {

	// Get option's cache
	if ( get_option( $id ) && $option = get_option( "{$id}-{$cachekey}" ) ) {
		$cache = $option;
	}

	return $cache;
}

/**
 * Update the autombed cache for the option content's cachekey
 *
 * @since 1.1.0
 *
 * @param string $cache    Newly cached HTML markup for embed.
 * @param string $cachekey Key to use for the caching in the database.
 * @param int    $id       ID to do the caching for.
 */
function paco2017_update_option_autoembed_cache( $cache, $cachekey, $id ) {
	update_option( "{$id}-{$cachekey}", $cache );
}

/** Utility *******************************************************************/

/**
 * Determine if this plugin is being deactivated
 *
 * @since 1.0.0
 *
 * @param string $basename Optional. Plugin basename to check for.
 * @return bool True if deactivating the plugin, false if not
 */
function paco2017_is_deactivation( $basename = '' ) {
	global $pagenow;

	$plugin = paco2017_content();
	$action = false;

	// Bail if not in admin/plugins
	if ( ! ( is_admin() && ( 'plugins.php' === $pagenow ) ) ) {
		return false;
	}

	if ( ! empty( $_REQUEST['action'] ) && ( '-1' !== $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' !== $_REQUEST['action2'] ) ) {
		$action = $_REQUEST['action2'];
	}

	// Bail if not deactivating
	if ( empty( $action ) || ! in_array( $action, array( 'deactivate', 'deactivate-selected' ) ) ) {
		return false;
	}

	// The plugin(s) being deactivated
	if ( $action === 'deactivate' ) {
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	} else {
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	}

	// Set basename if empty
	if ( empty( $basename ) && ! empty( $plugin->basename ) ) {
		$basename = $plugin->basename;
	}

	// Bail if no basename
	if ( empty( $basename ) ) {
		return false;
	}

	// Is bbPress being deactivated?
	return in_array( $basename, $plugins );
}
