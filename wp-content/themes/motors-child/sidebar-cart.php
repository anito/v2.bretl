<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="cart-sidebar off-canvas is--right is--active flex vbox">
	<?php
        get_template_part('custom-templates/sidebar-cart', 'header');
        get_template_part('custom-templates/sidebar-cart', 'totals');
        get_template_part('custom-templates/sidebar-cart', 'items');
	?>
</div>

