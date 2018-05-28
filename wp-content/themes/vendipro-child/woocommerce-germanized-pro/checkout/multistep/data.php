<?php
/**
 * Checkout Order Step Customer Data
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$payment_gateway = false;
$gateways = WC()->payment_gateways()->get_available_payment_gateways();
$method = WC()->session->get( 'chosen_payment_method' );

if ( $method && isset( $gateways[ $method ] ) )
	$payment_gateway = $gateways[ $method ];

?>

<div class="woocommerce-gzpd-checkout-verify-data column column-30">

	<div class="addresses">

		<div class="data-container">

			<header class="title">
					<h5><?php _e( 'Billing details', 'woocommerce' ); ?> <a href="#step-address" class="edit step-trigger" data-href="address"><?php echo _x( 'edit', 'multistep', 'woocommerce-germanized-pro' ); ?></a></h5>
			</header>

			<div class="data-container-inner">

				<address>
					<?php
						if ( ! $multistep->get_formatted_billing_address() ) {
							_e( 'N/A', 'woocommerce' );
						} else {
							echo $multistep->get_formatted_billing_address();
						}
					?>
					<?php 
						if ( WC()->checkout->get_value( 'billing_email' ) )  {
							echo "<br/>" . WC()->checkout->get_value( 'billing_email' );
						}
					?>
				</address>

			</div>

		</div>

		<div class="data-container">

			<header class="title">
				<h5><?php _e( 'Shipping address', 'woocommerce' ); ?> <a href="#step-address" class="edit step-trigger" data-href="address"><?php echo _x( 'edit', 'multistep', 'woocommerce-germanized-pro' ); ?></a></h5>
			</header>

			<div class="data-container-inner">

				<address>
					<?php
						if ( ! $multistep->get_formatted_shipping_address() ) {
							echo _x( 'Same as billing address', 'multistep', 'woocommerce-germanized-pro' );
						} else {
							echo $multistep->get_formatted_shipping_address();
						}
					?>
				</address>

			</div>

		</div>

		<?php if ( $payment_gateway ) : ?>

			<div class="data-container">

				<header class="title">
					<h5><?php echo _x( 'Payment Method', 'multistep', 'woocommerce-germanized-pro' ); ?> <a href="#step-payment" class="edit step-trigger" data-href="payment"><?php echo _x( 'edit', 'multistep', 'woocommerce-germanized-pro' ); ?></a></h5>
				</header>
	 
				<div class="data-container-inner">

					<p class="wc-gzdp-payment-gateway"><?php echo $payment_gateway->get_title(); ?></p>

				</div>

			</div>

		<?php endif; ?>

	</div><!-- /.col2-set -->

</div>