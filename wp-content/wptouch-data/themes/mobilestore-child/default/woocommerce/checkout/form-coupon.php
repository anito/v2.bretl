<?php
/**
 * Checkout coupon form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

if ( ! WC()->cart->coupons_enabled() ) {
	return;
}
?>

<form class="mobilestore_checkout_coupon checkout_coupon" method="post">
	<label for="coupon_code"><?php _e( 'Coupon', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button coupon-button" name="apply_coupon" value="<?php _e( 'Apply', 'wptouch-pro' ); ?>" />
</form>