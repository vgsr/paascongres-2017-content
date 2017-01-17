<?php

/**
 * Paco2017 Content Agenda Items List Template
 *
 * @package Paco2017 Content
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

?>

<ul class="paco2017-agenda-items">

	<?php while ( paco2017_have_agenda_items() ) : paco2017_the_agenda_item(); ?>

	<li class="agenda-item <?php if ( paco2017_is_agenda_related() ) echo 'is-related'; ?>">
		<div class="item-header">
			<h4 class="item-title">
				<?php
					if ( paco2017_is_agenda_related() ) :
						the_title( '<a href="' . esc_url( paco2017_get_agenda_related_url() ) . '">', '</a>' );
					else :
						the_title();
					endif;
				?>
			</h4>
			<span class="item-timeslot"><?php paco2017_the_agenda_timeslot(); ?></span>
		</div>

		<div class="item-content"><?php
			the_content();
		?></div>

		<?php edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'paco2017-content' ),
				get_the_title()
			),
			'<p class="item-footer"><span class="edit-link">',
			'</span></p>'
		); ?>
	</li>

	<?php endwhile; ?>

</ul>
