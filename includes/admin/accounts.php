<?php

/**
 * Paco2017 Content Accounts Functions
 *
 * @package Paco2017 Content
 * @subpackage Administration
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Act when the Accounts admin page is being loaded
 *
 * @since 1.1.0
 */
function paco2017_admin_load_accounts_page() {

	// Get action
	$action   = isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ? $_REQUEST['action'] : '';
	$location = '';

	// Redirect action-less requests to plugin admin home
	if ( ! $action ) {
		wp_redirect( add_query_arg( 'page', 'paco2017', admin_url( 'admin.php' ) ) );
		exit;
	}

	// Switch actions?

	if ( $location ) {
		wp_safe_redirect( $location );
		exit;
	}
}

/**
 * Output the contents of the Accounts admin page
 *
 * @since 1.1.0
 */
function paco2017_admin_accounts_page() {
	$action = isset( $_GET['action'] ) ? $_GET['action'] : ''; ?>

	<h2><?php esc_html_e( 'Accounts', 'paco2017-content' ); ?></h2>

	<?php switch ( $action ) {

	case 'delete-by-association' :
		$terms = isset( $_GET['terms'] ) ? array_map( 'intval', (array) $_GET['terms'] ) : array();
		$terms = array_map( 'get_term', $terms );
		$terms = array_filter( $terms );

		if ( ! $terms ) {
			echo '<p class="error">' . esc_html__( 'There are no valid associations selected.', 'paco2017-content' ) . '</p>';
			break;
		}

		?>

		<p><?php esc_html_e( 'You are about to delete all member accounts related to the following associations. Please confirm your actions.', 'paco2017-content' ); ?></p>

		<ul id="delete-association-members">
			<?php foreach ( $terms as $term ) :

				// Use raw count
				$user_count = paco2017_get_association_user_count( $term );

				// Skip empty associations
				if ( ! $user_count ) {
					continue;
				}
			?>

			<li>
				<?php /* translators: 1. Association name, 2. Account count */ ?>
				<button type="button" class="button-secondary delete-me" data-term_id="<?php echo $term->term_id; ?>"><?php printf( _n( 'Delete %2$d %1$s account', 'Delete %2$d %1$s accounts', $user_count, 'paco2017-content' ), $term->name, $user_count ); ?></button>
				<?php wp_nonce_field( "paco2017-accounts_delete-{$term->term_id}", "_ajax_nonce-{$term->term_id}", false ); ?>
				<span class="spinner"></span>
			</li>

			<?php endforeach; ?>
		</ul>

		<?php

		break;
	}
}

/**
 * AJAX handler for deleting user accounts
 *
 * @since 1.1.0
 */
function paco2017_admin_ajax_delete_association_users() {
	$term_id = isset( $_POST['term_id'] ) ? $_POST['term_id'] : 0;

	check_ajax_referer( "paco2017-accounts_delete-{$term_id}" );

	// Bail when the user is not capable
	if ( ! current_user_can( 'paco2017_manage_accounts' ) ) {
		wp_send_json_error( array(
			'message' => esc_html__( 'You are not allowed to delete user accounts.', 'paco2017-content' )
		) );
	}

	// Delete 25 users per cycle
	add_filter( 'paco2017_delete_association_users', function( $users ) {
		return array_slice( $users, 0, 25 );
	});

	// Get association, delete users, fetch new user count
	$term    = get_term( $term_id );
	$deleted = paco2017_delete_association_users( $term );
	$users   = paco2017_get_association_user_count( $term );

	// Deleting failed
	if ( ! $deleted ) {
		wp_send_json_error( array(
			'message' => esc_html__( 'Deleting user accounts failed.', 'paco2017-content' )
		) );
	}

	// Still with users, keep deleting
	if ( $users ) {
		$response = array(
			/* translators: 1. Association name, 2. Account count */
			'message' => sprintf( _n( 'Delete %2$d %1$s account', 'Delete %2$d %1$s accounts', $users, 'paco2017-content' ), $term->name, $users ),
			'left'    => $users,
			'nonce'   => wp_create_nonce( "paco2017-accounts_delete-{$term->term_id}" )
		);

	// Deleting is done
	} else {
		$response = array(
			'message' => sprintf( esc_html__( 'Deleting accounts for %s was successful!', 'paco2017-content' ), $term->name )
		);
	}

	wp_send_json_success( $response );
}
