<?php
/**
 * Template Name: Full Width
 * Template Post Type: post
 *
 * @author Vendidero
 * @version 1.0.0
 */

get_template_part( 'header-full-width' ); ?>

		<div id="primary" class="<?php echo ( ( vendipro()->widgets->get_widget_count( 'vp_blog' ) > 0 && vp_has_sidebar( 'vp_blog' ) ) ? 'has-sidebar' : '' );?>">

			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content-single', get_post_format() ); ?>

					<nav id="nav-single">
						<h3 class="assistive-text"><?php // _e( 'Post navigation', 'vendipro' ); ?></h3>
						<span class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&larr;</span> Previous', 'vendipro' ) ); ?></span>
						<span class="nav-next"><?php next_post_link( '%link', __( 'Next <span class="meta-nav">&rarr;</span>', 'vendipro' ) ); ?></span>
					</nav><!-- #nav-single -->

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #primary -->

	<?php vp_get_sidebar('blog'); ?>

<?php get_footer(); ?>