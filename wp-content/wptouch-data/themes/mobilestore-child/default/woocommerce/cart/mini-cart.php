<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

	<div class="actions">
		<?php if ( !is_cart() ) { ?>
			<a href="<?php mobilestore_woo_cart_url(); ?>" class="button edit_button"><?php _e( 'edit cart', 'wptouch-pro' ); ?></a>
		<?php } ?>
		<?php if ( !is_checkout() ) { ?>
			<a href="<?php mobilestore_woo_checkout_url(); ?>" class="button checkout_button"><?php _e( 'checkout', 'wptouch-pro' ); ?></a>
		<?php } ?>
	</div>

<?php endif; ?>

<ul class="cart_list product_list_widget <?php echo $args['list_class']; ?>">

	<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

		<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

					$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
					$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );

					$classes = 'product-' . $product_id;
					if ( isset( $cart_item[ 'variation_id' ] ) ) {
						$classes .= ' variation-' . $cart_item[ 'variation_id' ];
					}

					?>
					<li class="<?php echo $classes; ?>">
						<a href="<?php echo get_permalink( $product_id ); ?>">
							<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . '<p class="product-title">' . $product_name . '</p>'; ?>

							<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>

							<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
						</a>
					</li>
					<?php
				}
			}
		?>

	<?php else : ?>

		<li class="empty"><?php _e( 'No products in the cart.', 'woocommerce' ); ?></li>

	<?php endif; ?>

</ul><!-- end product list -->

<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

	<p class="total test"><span class="subtotal"><?php _e( 'Subtotal', 'woocommerce' ); ?>:</span> <?php echo WC()->cart->get_cart_subtotal(); ?></p>

	<?php //do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>