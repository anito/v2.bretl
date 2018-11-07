<?php
/**
 * Template to show product content within slider
 *
 * @author 		Vendidero
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $slider;

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

?>

<li <?php post_class( ); ?>>

	<a href="<?php the_permalink(); ?>">

		<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>

		<?php if ( $slider->show_meta() ) : ?>

			<div class="meta">

				<h3><?php the_title(); ?></h3>

				<?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>

			</div>

		<?php endif; ?>

	</a>

</li>