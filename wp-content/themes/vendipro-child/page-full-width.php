<?php
/**
 * Template Name: Full Width
 * Template Post Type: page
 *
 * @package WordPress
 * @subpackage Mobilestore
 * @since originated from page.php
 */

get_template_part( 'header-full-width' ); ?>

		<div id="primary" class="<?php echo ( ( vendipro()->widgets->get_widget_count( 'vp_blog' ) > 0 && vp_has_sidebar( 'vp_blog' ) ) ? 'has-sidebar' : '' );?>">

			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
			
		</div><!-- #primary -->

	<?php vp_get_sidebar( 'blog' ); ?>

<?php get_footer(); ?>