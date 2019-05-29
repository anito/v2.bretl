<?php
require_once( __DIR__ . '/includes/product_category_handler.php');
require_once( __DIR__ . '/includes/duplicate_content.php');
require_once( __DIR__ . '/includes/sender_email.php');


function stm_enqueue_parent_styles() {

	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('stm-theme-style') );

}
add_action( 'wp_enqueue_scripts', 'stm_enqueue_parent_styles' );

function child_theme_slug_setup() {
    
    load_child_theme_textdomain( 'motors-child', get_stylesheet_directory() . '/languages' );
    
}
add_action( 'after_setup_theme', 'child_theme_slug_setup' );