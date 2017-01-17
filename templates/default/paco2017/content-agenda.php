<?php

/**
 * Paco2017 Content Agenda Page Template
 *
 * @package Paco2017 Content
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Description
the_archive_description( '<p class="archive-description">', '</p>' );

?>

<div class="paco2017-content paco2017-agenda">

	<?php if ( paco2017_query_conf_days() ) : ?>

	<ul class="paco2017-conference-days">

		<?php while ( paco2017_have_conf_days() ) : paco2017_the_conf_day() ?>

		<li class="conference-day">

			<h3 class="day-title"><?php paco2017_the_conf_day_title(); ?></h3>

			<?php if ( paco2017_has_conf_day_date() ) : ?>

			<p class="day-date"><?php paco2017_the_conf_day_date(); ?></p>

			<?php endif; ?>

			<?php if ( paco2017_query_agenda_items() ) : ?>

			<?php paco2017_get_template_part( 'content', 'agenda-items' ); ?>

			<?php else : ?>

			<p><?php esc_html_e( 'There are no items scheduled for this day.', 'paco2017-content' ); ?></p>

			<?php endif; ?>

		</li>

		<?php endwhile; ?>

	</ul>

	<?php elseif ( paco2017_query_agenda_items() ) : ?>

	<?php paco2017_get_template_part( 'content', 'agenda-items' ); ?>

	<?php else : ?>

	<p><?php esc_html_e( 'There are no items scheduled.', 'paco2017-content' ); ?></p>

	<?php endif; ?>

</div>
