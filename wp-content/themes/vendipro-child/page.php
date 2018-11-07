<?php
/**
 * Template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @author 		Vendidero
 * @version     1.0.0
 */


get_header(); ?>

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