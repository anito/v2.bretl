<?php
/**
 * Template to show attachment content within slider
 *
 * @author 		Vendidero
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $slider;

?>

<li <?php post_class( ); ?>>

	<?php echo $slider->get_image_html(); ?>

	<?php if ( $slider->show_meta() ) : ?>

		<span class="meta"><?php echo $slider->get_meta_html(); ?></span>

	<?php endif; ?>

</li>