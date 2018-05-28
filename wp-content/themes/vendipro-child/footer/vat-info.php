<?php
/**
 * Footer global tax info
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanized/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php if ( get_option('woocommerce_gzd_small_enterprise') != 'yes' ) : ?>
	<p class="footer-info vat-info"><?php echo ( get_option( 'woocommerce_tax_display_shop' ) == 'incl' ) ? __( 'All prices incl. VAT.', 'woocommerce-germanized' ) : __( 'All prices excl. VAT.', 'woocommerce-germanized' ) ?> zzgl. <span><a href="/versand/" style="text-decoration: underline !important;"> Versand</a></span></p>
	<p class="footer-info sale-info">Copyright &copy; <?php echo date('Y'); ?> <span>CGD Sokatsch</span><sup>&reg;</sup>
        <span class="">Design &amp; supervision CGD Sokatsch powered by <a href="https://webpremiere.de" target="_blank" alt="WebPremiere" class="logo-webpremiere"><img height="25" style="margin-left: -5px;" src="https://webpremiere.de/files/public-docs/logos/webpremiere-logo-3.svg" alt="webPremiere"/></a></span>
	</p>
<?php else : ?>
	<p class="footer-info vat-info"><?php echo wc_gzd_get_small_business_notice(); ?></p>
<?php endif; ?>