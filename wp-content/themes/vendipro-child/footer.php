<?php
/**
 * Template for displaying Footer
 *
 * @author Vendidero
 * @version 1.0.0
 */
?>
<?php
/**
 * edit the gzd footer infos here: /child theme/footer/vat-info.php
 *
 */
?>
</div>
<footer id="bottom">
    <div class="container">
        <div class="widgets" id="widgets-footer">
            <div class="grid grid-<?php echo vendipro()->widgets->get_widget_count('vp_footer'); ?>">
                <?php dynamic_sidebar('vp_footer'); ?>
            </div>
        </div>
        <div class="payments">
            <a href="/zahlungsarten/">
                <?php //get_footer("payments")?>
            </a>
        </div>
        <div class="footer-msg"><?php do_action('woocommerce_gzd_footer_msg'); ?></div>
    </div>
</footer>
<div id="menu-right"><?php get_sidebar('cart'); ?></div>
<!--<div class="_pp_overlay" style="opacity: 0.8; height: 1738px; width: 1233px; display: none;"></div>-->
<!-- modal-dialogue -->
<div tabindex="0" id="modal-view" class="modal fade">
    <div class="modal-dialog modal-lg" role="document">initially needed by Modal</div>
</div>
<!-- /.modal -->
<?php wp_footer(); ?>

</body>
</html>