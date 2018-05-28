<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product, $woocommerce;
$product = wc_get_product( $product_id );
$orderby = 'rand';

?>
<div class="vp-modal prettyphoto-modal-load" id="vp-modal">
	<div class="vp-add-to-cart-success">
		<h3 class="no-textify"><?php _e( 'Thank you!', 'vendipro' ); ?></h3>
		<p><?php echo sprintf( __( '%s was added successfully to your cart.', 'vendipro' ), get_the_title( $product_id ) ); ?></p>
		<p class="button-wrap">
			<a class="button button-secondary button-close vp-modal-close" href="#"><?php _e( 'Continue Shopping', 'vendipro' ); ?></a>
			<a class="button button-go-to-cart" href="<?php echo wc_get_cart_url(); ?>"><?php _e( 'Go to Cart', 'vendipro' ); ?></a>
		</p>

		<?php if ( function_exists( 'woocommerce_upsell_display' ) ) : ?>
			<?php woocommerce_upsell_display(); ?>
		<?php else : ?>
			<?php woocommerce_get_template( 'single-product/up-sells.php', array(
			        'posts_per_page' => 3,
                    'orderby' => apply_filters( 'woocommerce_upsells_orderby', $orderby ),
                    'columns' => 1,
            ) ); ?>
		<?php endif; ?>

	</div>
</div>