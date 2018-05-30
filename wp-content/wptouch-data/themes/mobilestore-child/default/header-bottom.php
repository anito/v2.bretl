<?php
	$settings = mobilestore_get_settings();
	global $wptouch_pro;
?>
<!-- Pushit Left Menu -->
<nav class="pushit pushit-left">
	<div id="menu-left" class="slide-menu">
	<?php if ( $settings->mobilestore_show_categories && function_exists( 'wptouch_fdn_hierarchical_cat_list' ) ) { ?>
		<div class="wptouch-menu menu categories">
			<h3><?php _e( 'Product Categories', 'wptouch-pro' ); ?></h3>
			<?php wptouch_fdn_hierarchical_cat_list( 99, false, 'product_cat', '<ul class="menu-tree">', '</ul>' ); ?>
		</div>
	<?php } ?>
	<?php if ( $settings->mobilestore_show_recently_viewed && function_exists( 'is_shop' ) ) { ?>
		<div class="recently-viewed <?php if( !isset( $_COOKIE['woocommerce_recently_viewed'] ) ) { echo 'empty'; };?>">
			<h3><?php _e( 'Recently Viewed', 'wptouch-pro' ); ?></h3>
			<?php the_widget( 'WC_Widget_Recently_Viewed', array( 'title' => '', 'number' => 5 ) ); ?>
		</div>
	<?php } ?>
		<div class="wptouch-menu menu">
			<h3><?php _e( 'Site Menu', 'wptouch-pro' ); ?></h3>
			<?php if ( wptouch_has_menu( 'primary_menu' ) ) { wptouch_show_menu( 'primary_menu' ); } ?>
		</div>
	</div>
</nav>

<?php if ( function_exists( 'woocommerce_mini_cart' ) && !mobilestore_cart_icon_link() ) { ?>
<!-- Pushit Right Menu -->
<nav class="pushit pushit-right">
	<div id="menu-right" class="cart">
		<?php woocommerce_mini_cart(); ?>
	</div>
</nav>
<?php } ?>
<!-- Back Button for Web-App Mode -->
<div class="wptouch-icon-arrow-left wptouch-icon-left-open-big back-button tappable"><!-- css-button --></div>

<!-- Main Page Content -->
<div class="page-wrapper">
	<div id="header-area">
		<div id="header-title-logo">
			<a href="<?php wptouch_bloginfo( 'url' ); ?>">
				<?php if ( foundation_has_logo_image() ) { ?>
					<img id="header-logo" src="<?php foundation_the_logo_image(); ?>" alt="logo image" />
				<?php } else { ?>
					<h1 class="heading-font"><span><?php wptouch_bloginfo( 'site_title' ); ?></span></h1>
				<?php } ?>
			</a>
			<?php if ( mobilestore_header_type_large() ) { ?>
				<h4 class="heading-font"><?php wptouch_bloginfo( 'description' ); ?></h4>
			<?php } ?>
		</div>

		<a class="menu-btn menu-icon button tappable no-ajax" data-menu-target="menu-left">
				<i class="wptouch-icon-menu"></i>
			<?php if ( mobilestore_is_tablet() ) { ?>
				<?php _e( 'Menu', 'wptouch-pro' ); ?>
			<?php } ?>
		</a>

<?php if ( function_exists( 'is_cart' ) ) { ?>
		<?php if ( !is_cart() | !is_checkout() ) { ?>
		<?php
			global $woocommerce;
			$cart_count = $woocommerce->cart->get_cart_contents_count();
		?>

		<?php if ( mobilestore_cart_icon_link() ) { ?>
			<a href="<?php mobilestore_woo_cart_url(); ?>" class="cart-btn button tappable no-ajax<?php if ( $cart_count > 0 ) { echo ' ' . 'filled animated'; } ?>" data-menu-target="menu-right">
		<?php } else { ?>
			<a href="#" class="menu-btn cart-btn button tappable no-ajax<?php if ( $cart_count > 0 ) { echo ' ' . 'filled animated'; } ?>" data-menu-target="menu-right">
			<?php } ?>
				<i class="wptouch-icon-basket"></i>
			<?php if ( mobilestore_is_tablet() ) { ?>
				<?php _e( 'Cart', 'wptouch-pro' ); ?>
			<?php } ?>
		</a>
		<?php } ?>
<?php } ?>
	</div>
<?php if ( function_exists( 'is_shop' ) ) { ?>
	<?php if ( is_shop() || is_product_category() || is_product() || is_product_tag() || is_search() || 1 ) { ?>
		<div id="wptouch-search-inner">
			<form method="get" id="searchform" action="<?php wptouch_bloginfo( 'search_url' ); ?>/">
				<input type="text" name="s" id="search-text" value="<?php if ( isset( $_GET[ 's' ] ) ) { echo esc_attr( $_GET['s'] ); } ?>" placeholder="<?php _e( 'Search the store', 'wptouch-pro' ); ?>&hellip;" title="<?php _e( 'Search the store', 'wptouch-pro' ); ?>&hellip;" />
				<input type="hidden" name="post_type" value="product" />
				<!-- <input name="submit" type="submit" id="search-submit" value="<?php _e( 'Search', 'wptouch-pro' ); ?>" /> -->
			</form>
			<div class="recent-searches">
				<h4 class="body-font"><?php _e( 'Recent Searches', 'wptouch-pro' ); ?> <span><i class="wptouch-icon-cancel-circled"></i> <?php _e( 'Clear', 'wptouch-pro' ); ?></span></h4>
				<ul>
				</ul>
			</div>
		</div>
	<?php } ?>
<?php } ?>

<?php do_action( 'mobilestore_pre_sort_content' ); ?>
<?php
if ( function_exists( 'is_shop' ) && is_shop() && !is_search() ) {
	if ( mobilestore_is_tablet() ) {
		$query_args = array( 'post_type' => 'slide', 'numberposts' => 5, 'orderby' => 'menu_order', 'order' => 'ASC'  );
		$slides = get_posts( $query_args );
		if ( count( $slides ) > 0 ) {
?>
	<div class="product-slider">
		<ul class="products owl-carousel">
<?php

		foreach ( $slides as $slide ) {
			$image = get_the_post_thumbnail( $slide->ID, 'full' );
			$link = get_post_meta( $slide->ID, '_wooslider_url', true );
			if ( $link ) {
				echo '<a href="' . $link . '">';
			}
			echo $image;
			if ( $link ) {
				echo '</a>';
			}
		}

?>
		</ul>
	</div>
<?php
		}

		$categories = $settings->mobilestore_featured_categories_tablet;
/*	} else {
		$categories = array( $settings->mobilestore_featured_categories_smartphone );
*/	}

	$image_settings = wc_get_image_size( 'shop_catalog' );

	if ( $settings->mobilestore_show_top_products_carousel ) {
?>
	<div class="product-carousel">
		<h2><?php echo __( 'Most Popular Products', 'wptouch-pro' ); ?></h2>
		<ul class="products owl-carousel<?php if ( $image_settings[ 'crop' ] ) { echo ' cropped'; } ?>">
			<?php mobilestore_list_top_selling_products(); ?>
		</ul>
	</div>
<?php
	}

	if ( isset( $categories ) && count( $categories ) > 0 ) {
		foreach( $categories as $category_slug ) {
			$category = get_term_by( 'slug', $category_slug, 'product_cat' );
?>
	<div class="product-carousel">
		<h2><?php echo sprintf( __( '%s: Most Popular', 'wptouch-pro' ), $category->name ); ?></h2>
		<ul class="products carousel<?php if ( $image_settings[ 'crop' ] ) { echo ' cropped'; } ?>">
			<?php mobilestore_list_category_products( $category->slug ); ?>
		</ul>
	</div>
<?php
	}
}
?>
<?php } ?>

<?php if ( ( is_home() || is_front_page() ) && ( !function_exists( 'is_shop' ) || !is_shop() ) )  { ?>
	<?php if ( function_exists( 'foundation_featured_slider' ) ) { ?>
		<?php foundation_featured_slider(); ?>
	<?php } ?>
<?php } ?>

<?php
	if ( is_single() && ( !function_exists( 'woocommerce_mini_cart' ) || ( !is_shop() && !is_product() ) ) ) {
		if ( ( $home = get_option( 'page_for_posts' ) ) && $home > 0 ) {
			// Blog index is custom set!
			echo '<a id="blog-back" href="' . get_permalink( $home ) . '">' . __( 'Back to blog index', 'wptouch-pro' ) . '</a>';
		}
	}
?>
<?php if ( function_exists( 'is_shop' ) ) { ?>
	<?php if ( ( is_shop() || is_product_category() || is_product_taxonomy() || is_product_tag() || is_search() ) && !is_single() && !is_checkout() && !is_cart() && !is_account_page() ) { ?>
		<div class="sort-filter-wrap clearfix">
			<span class="layout-buttons">
				<i class="wptouch-icon-layout-tiles layout-masonry"></i>
				<i class="wptouch-icon-layout-list layout-list"></i>
			</span>
			<div class="sort-filter-buttons">
				<button class="button show-hide-toggle" data-effect-target="sorting-div"><i class="wptouch-icon-arrow-combo"></i> <?php _e( 'sorting','wptouch-pro' ); ?></button>
				<?php if ( mobilestore_have_filters() ) { ?><button class="button show-hide-toggle" data-effect-target="filtering-div"><i class="wptouch-icon-filter"></i> <?php _e( 'filter by','wptouch-pro' ); ?></button><?php } ?>
			</div>
			<div class="sorting <?php if ( isset( $_GET['orderby'] ) ) { echo 'sorted'; } ?>" id="sorting-div">
				<?php woocommerce_catalog_ordering(); ?>
			</div>
			<?php if ( mobilestore_have_filters() ) { ?>
				<div class="filtering" id="filtering-div">
					<?php mobilestore_do_filters(); ?>
				</div>
			<?php } ?>
				</div>
			<?php if ( strpos($_SERVER['REQUEST_URI'],'filter' ) ) { echo mobilestore_show_active_filters(); } ?>
	<?php } ?>
<?php } ?>