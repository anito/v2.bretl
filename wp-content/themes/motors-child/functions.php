<?php
require_once( __DIR__ . '/includes/product_category_handler.php');
require_once( __DIR__ . '/includes/duplicate_content.php');
require_once( __DIR__ . '/includes/sender_email.php');
require_once( __DIR__ . '/includes/mesmerize_helper.php');


function stm_enqueue_parent_styles() {
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('stm-theme-style') );
	wp_enqueue_style( 'child-extra', get_stylesheet_directory_uri() . '/assets/css/extra.css', array('stm-theme-style') );
	wp_enqueue_style( 'card-sidebar', get_stylesheet_directory_uri() . '/assets/css/card_sidebar.css', false );

	/*
	* Main JS
	*/
    wp_register_script('main', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), '0.10', true);
    wp_enqueue_script('main');
	/*
	* Sidebar Cart
	*/
    wp_register_script('cart', get_stylesheet_directory_uri() . '/assets/js/sidebar.cart.js', array('jquery'), '1.0', true);
    wp_enqueue_script('cart');

	/*
	* Other Libraries
	*/
    wp_enqueue_script('fancybox', get_stylesheet_directory_uri() . '/vendors/fancybox/dist/jquery.fancybox.js', false, false, true);
	wp_enqueue_style('fancybox', get_stylesheet_directory_uri() . '/vendors/fancybox/dist/jquery.fancybox.css');
	
    wp_enqueue_script('fancybox-helper', get_stylesheet_directory_uri() . '/assets/js/fancybox-helper.js', false, false, true);
    wp_enqueue_style('fancy-metaslider', get_stylesheet_directory_uri() . '/assets/css/fancy-metaslider.css');
    wp_enqueue_style('dashicons');

}
add_action( 'wp_enqueue_scripts', 'stm_enqueue_parent_styles', 11 );

function child_theme_slug_setup() {
    
    load_child_theme_textdomain( 'motors-child', get_stylesheet_directory() . '/languages' );
    
}
add_action( 'after_setup_theme', 'child_theme_slug_setup' );

/** Handle Sales Category */
add_action("save_post", "on_save_post", 99, 3);

function on_save_post($post_id, $post, $is_update) {
    global $woocommerce;

    $product_id = $post_id;
    $product = wc_get_product($product_id);

    if (!$product)
        return 0;

    if ($product->is_type('variation')) {
        $variation = new WC_Product_Variation($product);
        $product_id = $variation->get_parent_id();
        $product = wc_get_product($product_id);
    }
    fix_cat($product_id, SALES_CAT_ID);
}

/** Handle Featured Products */
add_action("woocommerce_before_product_object_save", "before_product_object_save", 99, 2);

function before_product_object_save($product, $data_store) {
    $is_featured = $product->is_featured();
    set_product_cats($product, FEATURED_CAT_ID, $is_featured);
}

/** Google Fonts */
function mesmerize_get_general_google_fonts()
{
    return array(
        array(
            'family'  => 'Open Sans',
            "weights" => array("300", "400", "600", "700"),
        ),
        
        array(
            'family'  => 'Muli',
            "weights" => array("300", "300italic", "400", "400italic", "600", "600italic", "700", "700italic", "900", "900italic"),
        ),
        array(
            'family'  => 'Playfair Display',
            "weights" => array("400", "400italic", "700", "700italic"),
        ),
    );
}

function mesmerize_do_enqueue_google_fonts()
{
    $fontsURL = array();
    if (mesmerize_can_show_cached_value('mesmerize_google_fonts')) {
        
        $fontsURL = mesmerize_get_cached_value('mesmerize_google_fonts');
    } else {
        $gFonts = mesmerize_get_general_google_fonts();
        
        $fonts = array();
        
        foreach ($gFonts as $font) {
            $fonts[$font['family']] = $font;
        }
        
        $gFonts = apply_filters("mesmerize_google_fonts", $fonts);
        
        $fontQuery = array();
        foreach ($gFonts as $family => $font) {
            $fontQuery[] = $family . ":" . implode(',', $font['weights']);
        }
        
        $query_args = array(
            'family' => urlencode(implode('|', $fontQuery)),
            'subset' => urlencode('latin,latin-ext'),
        );
        
        $fontsURL = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
        
        mesmerize_cache_value('mesmerize_google_fonts', $fontsURL);
    }
    
    wp_enqueue_style('mesmerize-fonts', $fontsURL, array(), null);
}

add_action('wp_enqueue_scripts', 'mesmerize_do_enqueue_google_fonts');

/**
 * Fix Post Template Slug for body_class
 */
function filter_post_classes( $classes ) {
	$classes[] = 'page-template-' . LAYOUT_TEMPLATE;
	return $classes;
}

add_filter( 'body_class', 'filter_post_classes' );