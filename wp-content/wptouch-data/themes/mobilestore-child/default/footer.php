		<?php do_action( 'wptouch_body_bottom' ); ?>
		
		<?php get_template_part( 'footer-top' ); ?>
		
		<div class="<?php wptouch_footer_classes(); ?>">
			<div class="payments">
				<a href="/zahlungsarten/">
					<?php get_template_part( 'footer-payments' ); ?>
				</a>
			</div>
			<?php wptouch_footer(); ?>
		</div>
		
		<?php if ( !is_front_page() ) { ?>
			<a href="#" class="back-to-top"><?php _e( 'Back to top', 'wptouch-pro' ); ?></a>
		<?php } ?>

		<?php do_action( 'wptouch_language_insert' ); ?>

		<?php get_template_part( 'switch-link' ); ?>
	
	</div><!-- page wrapper -->
    <!--<div class="_pp_overlay" style="opacity: 0.8; height: 1738px; width: 1233px; display: none;"></div>-->
    <!-- modal-dialogue -->
    <div tabindex="0" id="modal-view" class="modal fade">
        <div class="modal-dialog modal-lg" role="document">initially needed by Modal</div>
    </div>
    <!-- /.modal -->
</body>
</html>
