<?php

/**
 * Paco2017 Content Speaker Info Template
 *
 * @package Paco2017 Content
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Bail when there's no description
if ( ! $description = paco2017_get_speaker_content() )
	return;

?>

<div class="speaker-info <?php if ( paco2017_has_speaker_photo() ) echo 'has-avatar'; ?>">
	<div class="speaker-header">
		<?php if ( paco2017_has_speaker_photo() ) : ?>
		<div class="speaker-avatar"><?php paco2017_the_speaker_photo(); ?></div>
		<?php endif; ?>

		<h4 class="speaker-title"><?php printf( __( 'About %s', 'paco2017-content' ), paco2017_get_speaker_title() ); ?></h4>
	</div>

	<div class="speaker-content">
		<?php echo $description; ?>
		<div class="speaker-link">
			<a href="<?php echo esc_url( get_term_link( paco2017_get_speaker() ) ); ?>">
				<?php _e( 'View all speakers at this conference <span class="meta-nav">&rarr;</span>', 'paco2017-content' ); ?>
			</a>
		</div>
	</div>
</div>
