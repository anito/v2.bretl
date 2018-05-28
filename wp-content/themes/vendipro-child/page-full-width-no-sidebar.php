<?php
/**
 * Template Name: Full Width No Sidebar
 * Template Post Type: page
 *
 * @package WordPress
 * @subpackage Mobilestore
 * @since originated from page.php
 */

get_template_part( 'header-full-width' ); ?>

		<div id="primary">

			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
			
		</div><!-- #primary -->

<?php get_footer(); ?>