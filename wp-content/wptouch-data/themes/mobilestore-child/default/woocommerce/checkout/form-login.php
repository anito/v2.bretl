<?php
/**
 * Checkout login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) return;

echo '<div class="login-wrap-box">';
if ( !isset( $info_message ) ) { $info_message = ''; }
$info_message .= '<a href="#" class="tappable login-toggle">' . __( 'Login now', 'wptouch-pro' ) . '</a>' . __( 'Shopped here before?', 'wptouch-pro' );
wc_print_notice( $info_message, 'notice' );
?>

<?php
	woocommerce_login_form(
		array(
			'message'  => __( 'Enter your login details in the boxes below. If you are a new customer please proceed to the Billing &amp; Shipping section.', 'wptouch-pro' ),
			'redirect' => get_permalink( wc_get_page_id( 'checkout' ) ),
			'hidden'   => true
		)
	);
?>

<?php
echo '</div>';
?>
