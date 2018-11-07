<?php
/*
Plugin Name: Spine App
Plugin URI: https://webpremiere.de
Description: Extend Wordpress by SpineJS
Version: 1.0.5
Author: Webpremiere
Author URI: https://webpremiere.de
Text Domain: spine-app
Domain Path: /languages
*/
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Define constants
 **/
if ( ! defined( 'SPINEAPP_PLUGIN_URL' ) ) {
	define( 'SPINEAPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( is_admin() ) {
    //	do nothing
} else {
	require_once dirname( __FILE__ ) . '/public/class-spineapp-public.php';
	$SpineApp_Public = new SpineApp_Public();
	$SpineApp_Public -> init();
}