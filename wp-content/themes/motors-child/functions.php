<?php
require_once( __DIR__ . '/includes/product_category_handler.php');
require_once( __DIR__ . '/includes/duplicate_content.php');
require_once( __DIR__ . '/includes/sender_email.php');


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
    wp_enqueue_script('fancybox', get_stylesheet_directory_uri() . '/assets/js/fancybox/jquery.fancybox.js', false, false, true);
    wp_enqueue_script('fancybox-helper', get_stylesheet_directory_uri() . '/assets/js/fancybox-helper.js', false, false, true);
    wp_enqueue_style('fancybox', get_stylesheet_directory_uri() . '/assets/css/fancybox/jquery.fancybox.css');
    wp_enqueue_style('fancy-metaslider', get_stylesheet_directory_uri() . '/assets/css/fancy-metaslider.css');
    wp_enqueue_style('dashicons');

}
add_action( 'wp_enqueue_scripts', 'stm_enqueue_parent_styles', 11 );

function child_theme_slug_setup() {
    
    load_child_theme_textdomain( 'motors-child', get_stylesheet_directory() . '/languages' );
    
}
add_action( 'after_setup_theme', 'child_theme_slug_setup' );