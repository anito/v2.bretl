<?php
/**
 * Template for displaying content
 *
 * @author Vendidero
 * @version 1.0.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-post wrapper">
			<header class="entry-header">

				<?php if ( is_sticky() ) : ?>
					<hgroup>
						<h3 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<h3 class="entry-format hide"><?php _e( 'Featured', 'vendipro' ); ?></h3>
					</hgroup>
				<?php else : ?>
					<h3 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
				<?php endif; ?>

				<?php if ( 'post' == get_post_type() ) : ?>
				<div class="entry-meta">
					<?php vp_posted_on(); ?>
				</div><!-- .entry-meta -->
				<?php endif; ?>

			</header><!-- .entry-header -->

			<?php if ( is_search() ) : // Only display Excerpts for Search ?>

				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div>

			<?php else : ?>

			<div class="entry-content">

				<?php if ( has_post_thumbnail()) : ?>

					<div class="grid grid-2">

						<div class="column column-40">

							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
								<?php the_post_thumbnail( 'slider', array( 'class' => 'thumbnail' )); ?>
							</a>

						</div>

						<div class="column column-60">
							<?php if ( vp_get_option( 'blog_archive_the_content' ) ) : ?>

								<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'vendipro' ) ); ?>

							<?php else : ?>

								<?php the_excerpt(); ?>

							<?php endif; ?>
						</div>

					</div>

				<?php else : ?>

					<?php if ( vp_get_option( 'blog_archive_the_content' ) ) : ?>

						<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'vendipro' ) ); ?>

					<?php else : ?>

						<?php the_excerpt(); ?>

					<?php endif; ?>

				<?php endif; ?>

				<?php wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>', 'link_before' => '<span class="pag-item">', 'link_after' => '</span>' ) ); ?>

			</div>
			<?php endif; ?>

			<footer class="entry-meta">
				<?php $show_sep = false; ?>
				<?php if ( is_object_in_taxonomy( get_post_type(), 'category' ) ) : // Hide category text when not supported ?>
				<?php
					/* translators: used between list items, there is a space after the comma */
					$categories_list = get_the_category_list( __( ', ', 'vendipro' ) );
					if ( $categories_list ):
				?>
				<span class="cat-links">
					<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'vendipro' ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list );
					$show_sep = true; ?>
				</span>
				<?php endif; // End if categories ?>
				<?php endif; // End if is_object_in_taxonomy( get_post_type(), 'category' ) ?>
				<?php if ( is_object_in_taxonomy( get_post_type(), 'post_tag' ) ) : // Hide tag text when not supported ?>
				<?php
					/* translators: used between list items, there is a space after the comma */
					$tags_list = get_the_tag_list( '', __( ', ', 'vendipro' ) );
					if ( $tags_list ):
					if ( $show_sep ) : ?>
				<span class="sep"> | </span>
					<?php endif; // End if $show_sep ?>
				<span class="tag-links">
					<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'vendipro' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list );
					$show_sep = true; ?>
				</span>
				<?php endif; // End if $tags_list ?>
				<?php endif; // End if is_object_in_taxonomy( get_post_type(), 'post_tag' ) ?>

				<?php if ( comments_open() ) : ?>
				<?php if ( $show_sep ) : ?>
				<span class="sep"> | </span>
				<?php endif; // End if $show_sep ?>
				<span class="comments-link"><?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'vendipro' ) . '</span>', __( '<b>1</b> Reply', 'vendipro' ), __( '<b>%</b> Replies', 'vendipro' ) ); ?></span>
				<?php endif; // End if comments_open() ?>

				<?php edit_post_link( __( 'Edit', 'vendipro' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- .entry-meta -->
		</div>
	</article><!-- #post-<?php the_ID(); ?> -->
