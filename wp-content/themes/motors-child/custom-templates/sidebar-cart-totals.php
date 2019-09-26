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
global $woocommerce;

?>
<div class="button--container">


    <a href="<?php echo wc_get_checkout_url() ?>" class="button button--checkout is--icon-right" title="<?php _e( 'Proceed to checkout', 'woocommerce' ); ?>">
        <?php _e( 'Proceed to checkout', 'woocommerce' ); ?>
        <i class="fa fa-angle-right"></i>
    </a>


    <a href="<?php echo wc_get_cart_url() ?>" class="button button--open-basket is--icon-right" title="<?php _e( 'View Cart', 'woocommerce' ); ?>">
        <?php _e( 'Go to Cart', 'vendipro' ); ?>
        <i class="fa fa-angle-right"></i>
    </a>


</div>
<div class="message-wrapper"></div>
<div class="bottom">
	<div class="cart-collaterals sidebar-cart-subtotals <?php  if ( WC()->cart->is_empty() ) { echo "empty"; } ?>">
		<?php

		if ( ! WC()->cart->is_empty() ) : ?>
		<span><?php echo _e( 'Subtotal', 'woocommerce' ); ?>:</span><span class="amount"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
		<?php else : ?>
		<span><?php echo _e( 'Your cart is currently empty.', 'woocommerce' ); ?></span>
		<?php endif; ?>

	</div>
</div>
