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
	<p class="footer-info vat-info"><?php echo ( get_option( 'woocommerce_tax_display_shop' ) == 'incl' ) ? __( 'All prices incl. VAT.', 'woocommerce-germanized' ) : __( 'All prices excl. VAT.', 'woocommerce-germanized' ) ?> zzgl. <span><a href="/versandarten/" style="color:#d30001;"> Versandkosten</a></span></p>
	<p class="footer-info sale-info">Copyright &copy; 2017 <span>HA Lehmann</span><sup>&reg;</sup>
	<span class="">Design &amp; supervision by HA Lehmann</span>
	</p>
<?php else : ?>
	<p class="footer-info vat-info"><?php echo wc_gzd_get_small_business_notice(); ?></p>
<?php endif; ?>