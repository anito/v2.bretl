<?php $logo_main = get_theme_mod('logo', get_template_directory_uri() . '/assets/images/tmp/logo.png'); ?>
<?php
	$post_id = get_the_id();
	$show_title_box = get_post_meta( $post_id, 'title', true );
	if( $show_title_box === 'hide' && !is_front_page()) {
		$has_no_title_class = "has-no-title";
	} else {
		$has_no_title_class = '';
	}
	$fixed_header = get_theme_mod('header_sticky', true);
	if(!empty($fixed_header) and $fixed_header) {
		$fixed_header_class = 'header-service-fixed';
	} else {
		$fixed_header_class = '';
	}
	
	$transparent_header = get_post_meta($post_id, 'transparent_header', true);
	
	if(empty($transparent_header)) {
		$transparent_header_class = 'service-transparent-header';
	} else {
		$transparent_header_class = '';
	}

?>

<div class="header-service <?php echo esc_attr($fixed_header_class.' '.$transparent_header_class.' '.$has_no_title_class); ?>">
	<div class="container">
		<!--Logo-->
		<div class="service-logo-main">
			<?php if(empty($logo_main)): ?>
				<a class="blogname" href="<?php echo esc_url(home_url('/')); ?>" title="<?php esc_attr_e('Home', 'motors'); ?>">
					<h1><?php echo esc_attr(get_bloginfo('name')) ?></h1>
				</a>
			<?php else: ?>
				<a class="bloglogo" href="<?php echo esc_url(home_url('/')); ?>">
					<img
						src="<?php echo esc_url( $logo_main ); ?>"
						style="width: <?php echo get_theme_mod( 'logo_width', '138' ); ?>px;"
						title="<?php esc_attr_e('Home', 'motors'); ?>"
					    alt="<?php esc_attr_e('Logo', 'motors'); ?>"
					/>
				</a>
			<?php endif; ?>
		</div>

		<div class="header-service-right clearfix">
			<?php 
				$service_header_label = get_theme_mod('service_header_label', esc_html__('Make an Appointment', 'motors'));
				$service_header_link  = get_theme_mod('service_header_link', '#appointment-form');
			?>	
			
			<div class="service-mobile-menu-trigger visible-sm visible-xs">
				<span></span>
				<span></span>
				<span></span>
			</div>		
			
			<?php if(!empty($service_header_label) and !empty($service_header_link)): ?>
				<a href="<?php echo esc_url($service_header_link); ?>" class="button_3d white service-header-appointment heading-font">
					<div class="default-state">
						<i class="stm-service-icon-appointment_calendar"></i><?php stm_dynamic_string_translation_e('Service Header Label', $service_header_label); ?>
						<span class="active-state">
							<i class="stm-service-icon-appointment_calendar"></i><?php stm_dynamic_string_translation_e('Service Header Label', $service_header_label); ?>
						</span>
					</div>
				</a>
			<?php endif; ?>
		</div>

		<div class="header-service-right clearfix">
			<ul class="header-menu clearfix">
				<?php
				wp_nav_menu( array(
						'menu'              => 'primary',
						'theme_location'    => 'primary',
						'depth'             => 3,
						'container'         => true,
						'menu_class'        => 'service-header-menu clearfix',
						'items_wrap'        => '%3$s',
						'fallback_cb' => false
					)
				);
				?>
			</ul>
		</div>

	</div><!-- container -->
	<?php get_template_part('partials/header/header-nav'); ?>
</div><!-- header-service -->