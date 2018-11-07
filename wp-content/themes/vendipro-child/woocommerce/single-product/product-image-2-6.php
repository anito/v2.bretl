<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product;

$attachment_ids = $product->get_gallery_attachment_ids();

?>
<div class="images">

	<?php if ( $attachment_ids ) : ?>

	<div class="big flexslider">

		<ul class="slides">

		<?php

			$loop = 0;

			$classes = array( 'zoom' );

			foreach ( $attachment_ids as $attachment_id ) {

				$image_link = wp_get_attachment_url( $attachment_id );

				if ( ! $image_link )
					continue;

				$attachment_count = count( $product->get_gallery_attachment_ids() );

				if ( $attachment_count > 0 ) {
					$gallery = '[product-gallery]';
				} else {
					$gallery = '';
				}

				$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
				$image_title = esc_attr( get_the_title( $attachment_id ) );

				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<li><a href="%s" title="%s" class="%s" data-rel="prettyPhoto' . $gallery . '">%s</a></li>', $image_link, $image_title, implode( ' ', $classes ), $image ), $attachment_id, $post->ID );

				$loop++;
			}

		?>

		</ul>

	</div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_product_thumbnails' ); ?>

</div>