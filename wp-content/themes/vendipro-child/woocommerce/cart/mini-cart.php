<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 	3.3.0
 * 
 * required changes:
 * - class mini-cart added
 * - cart icon changed to fa-shopping-cart
 * - cart icon changed to icon-cart
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>
<div class="widget_shopping_cart_content mini-cart">
	<p class="cart-text">
		<a class="wc-forward cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php _e( 'Go to Cart', 'vendipro' );?></a>
		<span class="cart-price price total"><?php echo ( ! WC()->cart->is_empty() ) ? WC()->cart->get_cart_subtotal() : wc_price( 0 ); ?></span>
	</p>
	<a class="wc-forward cart-icon" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
		<i class="icon-cart cart-icon-img"></i>
		<span class="quantity cart-count"><?php echo is_callable( array( WC()->cart, 'get_cart_contents_count' ) ) ? WC()->cart->get_cart_contents_count() : sizeof( WC()->cart->get_cart() ); ?></span>
	</a>
</div>