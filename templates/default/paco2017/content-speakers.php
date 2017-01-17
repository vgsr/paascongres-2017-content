<?php

/**
 * Paco2017 Content Speakers Page Template
 *
 * @package Paco2017 Content
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Description
the_archive_description( '<p class="archive-description">', '</p>' );

?>

<div class="paco2017-content paco2017-speakers">

	<ul class="paco2017-speakers-items">

		<?php while ( paco2017_have_speakers() ) : paco2017_the_speaker(); ?>

		<li class="speaker-item <?php if ( paco2017_has_speaker_photo() ) echo 'has-avatar'; ?>">
			<div class="item-header">
				<?php if ( paco2017_has_speaker_photo() ) : ?>
				<div class="item-avatar"><?php paco2017_the_speaker_photo(); ?></div>
				<?php endif; ?>

				<h4 class="item-title"><?php paco2017_the_speaker_title(); ?></h4>
			</div>

			<div class="item-content"><?php
				paco2017_the_speaker_content();
				paco2017_the_speaker_objects_list();
			?></div>

			<?php edit_term_link(
				sprintf(
					/* translators: %s: Name of current post */
					__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'paco2017-content' ),
					paco2017_get_speaker_title()
				),
				'<p class="item-footer"><span class="edit-link">',
				'</span></p>',
				paco2017_get_speaker()
			); ?>
		</li>

		<?php endwhile; ?>

	</ul>

</div>
