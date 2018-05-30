<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" class="cart">

<?php do_action( 'woocommerce_before_cart_table' ); ?>

<?php do_action( 'woocommerce_before_cart_contents' ); ?>

	<ul class="cart_list product_list_widget">
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

						<?php echo WC()->cart->get_item_data( $cart_item ); ?>

					<?php
	       				// Backorder notification
	       				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
	       					echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
					?>

						<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
					</a>
					<?php
						echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&times;</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
					?>
					<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = '<div class="lucky-person quantity buttons_added"><a class="button minus delete"><i class="wptouch-icon-cancel"></i></a><input type="hidden" class="qty" value="1" style="display: none;"></div>' . sprintf( '<input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
						} else {
							$product_quantity = woocommerce_quantity_input( array(
								'input_name'  => "cart[{$cart_item_key}][qty]",
								'input_value' => $cart_item['quantity'],
								'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
								'min_value'   => '0'
							), $_product, false );
						}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
					?>

				</li>
				<?php
			}
		}
	?>
	</ul><!-- end product list -->

	<?php do_action( 'woocommerce_cart_contents' ); ?>

	<input type="submit" id="update-button" class="button" name="update_cart" value="<?php _e( 'Update cart', 'woocommerce' ); ?>" />

	<?php wp_nonce_field( 'woocommerce-cart' ); ?>

	<?php do_action( 'woocommerce_after_cart_contents' ); ?>

	<?php do_action( 'woocommerce_after_cart_table' ); ?>

	<?php if ( WC()->cart->coupons_enabled() ) { ?>
		<div class="coupon">
			<label for="coupon_code"><?php _e( 'Coupon', 'woocommerce' ); ?>:</label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button coupon-button" name="apply_coupon" value="<?php _e( 'Apply', 'wptouch-pro' ); ?>" />

			<?php do_action('woocommerce_cart_coupon'); ?>
		</div>
	<?php } ?>

</form>

<div class="cart-collaterals">

	<?php if ( version_compare( WC()->version, '2.3', '<' ) ) { ?>
		<?php woocommerce_cart_totals(); ?>
	<?php } else { ?>
		<?php do_action( 'woocommerce_cart_collaterals' ); ?>
	<?php } ?>

	<?php //woocommerce_shipping_calculator(); ?>

	<?php if ( version_compare( WC()->version, '2.3', '<' ) ) { ?>
		<form action="<?php mobilestore_woo_checkout_url(); ?>"method="post">
			<input type="submit" class="checkout-button button alt wc-forward" name="proceed" value="<?php _e( 'Proceed to Checkout', 'woocommerce' ); ?>" />
		</form>
	<?php } else { ?>
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	<?php } ?>

</div>

<?php do_action( 'woocommerce_after_cart' ); ?>