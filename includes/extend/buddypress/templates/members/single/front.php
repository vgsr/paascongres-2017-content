<?php

/**
 * BuddyPress - Single Member Front Template
 *
 * @package Paco2017 Content
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

?>

<?php if ( paco2017_bp_is_user_enrolled() ) : ?>

	<p><?php printf( esc_html__( 'Yes! %s will be attending the conference!', 'paco2017-content' ), bp_get_user_firstname() ); ?></p>

	<?php if ( ! paco2017_is_user_enrolled() ) : ?>

	<p><?php printf( esc_html__( "So you haven't decided yet whether you will attend the conference? Go ahead, ask %s what is so great about going to this event!", 'paco2017-content' ) ); ?></p>

	<?php endif; ?>

	<?php if ( paco2017_bp_users_in_same_association() ) : ?>

		<?php if ( paco2017_bp_get_member_presence() ) : ?>

			<p><?php printf( esc_html__( '%s has planned to be present during the following parts of the conference:', 'paco2017-content' ), bp_get_user_firstname() ); ?></p>

			<?php paco2017_bp_the_member_presence_list(); ?>

		<?php endif; ?>

	<?php endif; ?>

	<?php if ( paco2017_get_user_workshops( bp_displayed_user_id() ) ) : ?>

		<p><?php printf( esc_html__( '%s has subscribed to the following workshops:', 'paco2017-content' ), bp_get_user_firstname() ); ?></p>

		<?php paco2017_the_user_workshops_list( bp_displayed_user_id() ); ?>

	<?php else : ?>

		<p><?php printf( esc_html__( '%s has not yet subscribed to any workshop.', 'paco2017-content' ), bp_get_user_firstname() ); ?></p>

	<?php endif; ?>

<?php else : ?>

	<?php if ( paco2017_is_user_enrolled() ) : ?>

	<p><?php printf( esc_html__( '%1$s is not yet enrolled. Since you have already decided to attend the conference, perhaps you can persuade %1$s to join you. If you need any help in winning this person over, here are some tips to assist you:', 'paco2017-content' ), bp_get_user_firstname() ); ?></p>

	<?php else : ?>

	<p><?php printf( esc_html__( '%1$s is not yet enrolled. Would you happen to know whether %1$s is interested in the theme of the conference or prefers to be in the company of 200+ amicae amicique? Perhaps you can decide to join us together. Here are some tips that may win you over:', 'paco2017-content' ), bp_get_user_firstname() ); ?></p>

	<?php endif; ?>

	<ul>
		<li><a href="<?php echo esc_url( paco2017_get_magazine_download_url() ); ?>"><?php _e( 'The conference magazine', 'paco2017-content' ); ?></a></li>
		<li><a href="https://www.youtube.com/watch?v=al1kWFj--5E" target="_blank">We moeten megazuinig zijn op onze planeet</a></li>
	</ul>

<?php endif; ?>
