<?php


/**
 * WooCommerce Extra Feature Related Products
 * --------------------------
 *
 * Change number of related products on product page
 * Set your own value for 'posts_per_page'
 *
 */
function woo_related_products_limit() {
	global $product;

	$args['posts_per_page'] = 4;
	return $args;
}

/*
 * wc_remove_related_products
 * 
 * Clear the query arguments for related products so none show.
 * Add this code to your theme functions.php file.  
 */

// wc_remove_related_products
// Löschen des Abfrage-Arguments, um keine ähnlichen Produkte anzuzeigen.

function wc_remove_related_products( $args ) {
	return array();
}

// Function to add custom javascript
function add_scripts() {
	wp_register_script('readmore', get_stylesheet_directory_uri() . '/js/node_modules/readmore-js/readmore.js', array('jquery'), '1.0', true);
	wp_enqueue_script('readmore');
	if( ! IS_DEV_MODE || !IS_PRODUCTION ) {
		// Register analyticstracking.php file (Google Analytics)
		wp_register_script('google-analytics', get_stylesheet_directory_uri() . '/js/analyticstracking.js', false, '1.0', true);
		// Enqueue the registered script file
		wp_enqueue_script('google-analytics');
	}
	
	$translation_array = array(
		'login_toggle_start' => __( 'Login now', 'wptouch-pro' ),
		'login_toggle_close' => __( 'Close', 'wptouch-pro' ),
		'company_name_link' => __( 'Add Company Name', 'wptouch-pro' ),
		'order_notes_link' => __( 'Add Order Notes', 'wptouch-pro' ),
		'<a href="%s">Edit your password and account details</a>.' => __( '<a href="%s">Passwort und Konto-Daten bearbeiten</a>.', 'wptouch-pro' ),
		'cvc' => __( 'CVC', 'wptouch-pro' )
	);
	wp_localize_script( 'my-mobilestore-js', 'translated_strings', $translation_array );
	wp_register_script( 'my-mobilestore-js', get_stylesheet_directory_uri() . '/js/my_mobilestore.js', array( 'jquery', 'mobilestore-libraries-js' ), MOBILESTORE_THEME_VERSION, true );
    
    wp_enqueue_script('fancybox', get_stylesheet_directory_uri() . '/js/fancybox/jquery.fancybox.js', false, false, true);
    wp_enqueue_script('fancybox-helper', get_stylesheet_directory_uri() . '/js/fancybox-helper.js', false, false, true);
    
    wp_enqueue_style('fancybox', get_stylesheet_directory_uri() . '/css/fancybox/jquery.fancybox.css');
    wp_enqueue_style('fancy-metaslider', get_stylesheet_directory_uri() . '/css/fancy-metaslider.css');
    
    /*
     * Twitter Bootstrap
     */
    
    wp_register_script('bootstrap', get_stylesheet_directory_uri() . '/node_modules/bootstrap/dist/js/bootstrap.js', array('jquery'), false, true);
//    wp_enqueue_script('bootstrap');
//    wp_enqueue_style('my-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap/index.css');
//    wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/node_modules/bootstrap/dist/css/bootstrap.css');
    
	wp_localize_script( 'my-mobilestore-js', 'translated_strings', $translation_array );
	wp_enqueue_script( 'my-mobilestore-js' );
	// Enqueue the registered script file
	wp_register_script('main', get_stylesheet_directory_uri() . '/js/main.js', false, '1.0', true);
	// Enqueue the registered script file
	wp_enqueue_script('main');
}

// Function to remove javascript
function remove_scripts() {
	wp_dequeue_script( 'mobilestore-js' );
}

function remove_magic_plus() {
	remove_action("magictoolbox_WooCommerce_MagicZoomPlus_config_page_menu");
    remove_action('WooCommerce_MagicZoomPlus_load_admin_scripts');
    remove_action('WooCommerce_MagicZoomPlus_load_frontend_scripts');
	remove_action('wp_ajax_WooCommerce_MagicZoomPlus_import', 'WooCommerce_MagicZoomPlus_import');
    remove_action('wp_ajax_WooCommerce_MagicZoomPlus_export', 'WooCommerce_MagicZoomPlus_export');
    remove_action('wp_ajax_magictoolbox_WooCommerce_MagicZoomPlus_set_license', 'magictoolbox_WooCommerce_MagicZoomPlus_set_license');
    remove_filter( 'magictoolbox_WooCommerce_MagicZoomPlus_magictoolbox_wc_ajax_variation_threshold', 10, 2 );
    remove_action ("magictoolbox_WooCommerce_MagicZoomPlus_start_parsing",10);
    remove_action ("magictoolbox_WooCommerce_MagicZoomPlus_end_parsing",10);
    remove_action ("magictoolbox_WooCommerce_MagicZoomPlus_start_alternative_parsing",11);
    remove_action ("magictoolbox_WooCommerce_MagicZoomPlus_end_alternative_parsing",11);
	remove_action( 'WooCommerce_MagicZoomPlus_get_packed_js', 10, 2 );
	add_action("wp_footer", "magictoolbox_WooCommerce_MagicZoomPlus_add_src_to_footer", $priority);
    remove_action("magictoolbox_WooCommerce_MagicZoomPlus_add_options_script", 10001);
	remove_action( 'WooCommerceMagicZoomPlus_welcome_license_do_redirect' );
	remove_action('WooCommerce_MagicZoomPlus_add_admin_src_to_menu_page');
	remove_action( 'WooCommerceMagicZoomPlus_welcome_license_do_redirect' );
	
}
/**
 * Manipulates the sale badge to show percentage instead of just SALE
 *  
 * @param string $html current sale badge html
 * @param object $post current post object
 * @param object $product current product object
 * @return string sale badge html containing percentage
 */
function set_sale_flash( $html, $post, $product ) {
	$html = '';
	if ( $product->get_type() != 'variable' ) {
		$regular = $product->get_regular_price();
		if ( $regular > 0 ) {
			$sale = $product->get_sale_price();
			$perc = round( 100 - ( (100 / $regular) * $sale ) );
			$html = '<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . "<i class='sale-badge badge'> -"  . $perc . '%</i></span>';
		}
	} else {
		$regular_min = $product->get_variation_regular_price( 'min' );
		$regular_max = $product->get_variation_regular_price( 'max' );
		$sale_min = $product->get_variation_sale_price( 'min' );
		$sale_max = $product->get_variation_sale_price( 'max' );
		if ( $regular_min > 0 && $regular_max > 0 ) {
			$perc_min = round( 100 - ( (100 / $regular_min) * $sale_min ) );
			$perc_max = round( 100 - ( (100 / $regular_max) * $sale_max ) );
			$html = '<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . "<i class='sale-badge badge'> -"  . ( ( $perc_min != $perc_max ) ? ( $perc_min . '-' . $perc_max ) : $perc_max ) . '%</i></span>';
		}
	}
	return $html;
}

// override parent functions at a higher prio and include is_product_taxonomy
function wptouch_mobilestore_output_content_wrapper_end_override() {
	echo '</div>';
	$settings = mobilestore_get_settings();
	if ( $settings->mobilestore_product_pagination == 'ajax' && ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag()  ) ) {
		global $wp_query;
		if ( get_next_posts_page_link( $wp_query->max_num_pages ) ) {
			echo '<a class="load-more-products-link no-ajax" href="#" rel="' . get_next_posts_page_link($wp_query->max_num_pages) . '">';
			_e( 'view more products', 'wptouch-pro' );
			echo '</a>';
		}
	}
}
function wptouch_maybe_stop_responsive_images_override() {
	if ( is_shop() || is_product_category() || is_product_tag() || is_search() || is_page() || is_product() || is_product_taxonomy() ) {
		add_filter( 'wp_get_attachment_image_attributes', 'wptouch_mobilestore_no_responsive_thumbnails' );
		remove_filter( 'post_thumbnail_html', 'wptouch_media_inject_attachment_id', 10 );
		remove_filter( 'post_thumbnail_html', 'wp_make_content_images_responsive', 20 );
	}
}
function mobilestore_products_per_page_override( $query ) {
	$settings = mobilestore_get_settings();
	set_query_var( 'posts_per_page', $settings->mobilestore_products_per_page );
}
function child_remove_parent_function() {
    remove_action( 'woocommerce_after_main_content', 'wptouch_mobilestore_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_after_main_content', 'wptouch_mobilestore_output_content_wrapper_end_override', 10 );
	
    remove_action( 'wp', 'wptouch_maybe_stop_responsive_images' );
	add_action( 'wp', 'wptouch_maybe_stop_responsive_images_override' );
	
	remove_action( 'pre_get_posts', 'mobilestore_products_per_page', 30 );
	add_action( 'pre_get_posts', 'mobilestore_products_per_page_override', 30 );
}
function remove_styles() {
    wp_dequeue_style( 'nb-styles' );
    wp_dequeue_style( 'pac-styles' );
    wp_dequeue_style( 'pac-layout-styles' );
}
function archive_term_image() {
	
	if ( is_product_category() ) {
		global $wp_query;
		
		$cat = $wp_query->get_queried_object();
		$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
		
	} elseif ( function_exists( 'may_be_filtered_post' )  && may_be_filtered_post() ) {
		
		$thumbnail_id = get_woocommerce_term_meta( get_main_category_id(), 'thumbnail_id', true );
		
	}
	if ( !empty( $thumbnail_id ) ) {
		
		$thumbnail_post = get_post( $thumbnail_id );
		$image = wp_get_attachment_url( $thumbnail_id );
		if ( $image ) { ?>
			<div class="cat-thumb">
				<img src="<?php echo $image ?>" alt="" />
			</div>
		<?php
		}
	}
	
}

add_action( 'init', 'child_remove_parent_function' );
//add_action('wp_print_scripts', 'remove_magic_plus');
add_action('wp_print_scripts', 'remove_scripts');
add_action('wp_enqueue_scripts', 'add_scripts');
//add_filter('woocommerce_related_products_args','wc_remove_related_products', 10);
add_filter( 'woocommerce_is_attribute_in_product_name', function () { return false; } );#show meta for product like Größe XXL
add_filter( 'woocommerce_output_related_products_args', 'woo_related_products_limit' );
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 20;' ), 999 );
add_filter( 'woocommerce_sale_flash', 'set_sale_flash', 1, 3 );
add_action( 'wp_enqueue_scripts', 'remove_styles', 30 );

// we dont need the the cat-thumb overlay
// remove_action() must be called inside a function and cannot be called directly in your plugin or theme.
add_action( 'woocommerce_before_main_content', 'remove_new_archive_term_image_action' );
function remove_new_archive_term_image_action() {
	remove_action( 'woocommerce_before_main_content', 'new_archive_term_image', 20 );
	add_action( 'woocommerce_before_main_content', 'archive_term_image', 20 );
}

add_action('woocommerce_archive_description', 'get_brands_template', 1);
function get_brands_template() {
	wc_get_template( 'templ/badge/brand.php' );
}
add_action('woocommerce_archive_description', 'replace_brands_description', 0);
function replace_brands_description() {
	if( \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::is_brand_archive_page() ) {
		remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
		add_action('woocommerce_archive_description', function() {
			wc_format_content( wc_get_template( 'templ/brand/description_line.php' ) );
		}, 10);
	}
}
// change order of appearance to match our layout
// add banner to main_content
add_action( 'woocommerce_before_main_content', 'add_perfect_brand_banner' );
function add_perfect_brand_banner() {
	$instance = \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::this();
	
	add_action( 'woocommerce_before_main_content', array( $instance, 'archive_page_banner'), 20 );
}
// remove banner from shop_loop
add_action( 'woocommerce_before_shop_loop', 'remove_perfect_brand_banner', 0 );
function remove_perfect_brand_banner() {
	$instance = \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::this();
	
	remove_action( 'woocommerce_before_shop_loop', array( $instance, 'archive_page_banner' ), 9 );
}

add_action( 'woocommerce_page_title', 'pwb_page_title' );
function pwb_page_title( $title ) {
	if( \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::is_brand_archive_page() ) {
		return $title . "";
	}
}
