<?php

/**
 * Paco2017 Content Associations Page Template
 *
 * @package Paco2017 Content
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Description
the_archive_description( '<p class="archive-description">', '</p>' );

?>

<div class="paco2017-content paco2017-associations">

	<ul class="paco2017-associations-items">

		<?php while ( paco2017_have_associations() ) : paco2017_the_association(); ?>

		<li class="association-item <?php if ( paco2017_has_association_logo() ) echo 'has-avatar'; ?>">
			<div class="item-header">
				<?php if ( paco2017_has_association_logo() ) : ?>
				<div class="item-avatar"><?php paco2017_the_association_logo(); ?></div>
				<?php endif; ?>

				<h4 class="item-title"><?php paco2017_the_association_title(); ?></h4>
			</div>

			<div class="item-content"><?php
				paco2017_the_association_content();
			?></div>

			<?php edit_term_link(
				sprintf(
					/* translators: %s: Name of current post */
					__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'paco2017-content' ),
					paco2017_get_association_title()
				),
				'<p class="item-footer"><span class="edit-link">',
				'</span></p>',
				paco2017_get_association()
			); ?>
		</li>

		<?php endwhile; ?>

	</ul>

</div>
