<?php
/*
 * Add some JS to the footer
 * @since 2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin public class
 **/
if ( ! class_exists( 'SpineApp_Public' ) ) { // Don't initialise if there's already a class activated
	
	class SpineApp_Public {
	
		public function __construct() {
			//
		}
        
        /*		
		 * Initialize the class and start calling our hooks and filters		
		 * @since 2.0.0		
		 */
		public function init() {
			add_filter( 'body_class', array ( $this, 'body_class' ) );
            add_action ( 'wp_enqueue_scripts', array ( $this, 'enqueue_scripts' ) );
			add_action ( 'wp_footer', array ( $this, 'add_spine_js' ), 1001) ;
		}
        
        /*		
		 * Initialize the class and start calling our hooks and filters		
		 * @since 2.0.0		
		 */
		public function body_class( $classes ) {
			return $classes;
		}
        
        /*
		 * Enqueue styles and scripts
		 * @since 2.0.0
		 */
		public function enqueue_scripts() {
            wp_deregister_script('jquery');
            wp_enqueue_style('spine-app-styles', SPINEAPP_PLUGIN_URL . 'assets/spine/public/application.css');
			wp_enqueue_script ( 'jquery', SPINEAPP_PLUGIN_URL . 'assets/spine/public/application.js', false, '1.0.0', true );
		}
        
        /*		
		 * Initialize the class and start calling our hooks and filters		
		 * @since 2.0.0		
		 */
		public function add_spine_js() {

            echo '<!-- #spine-app -->';
        ?>
            <script id="spine-app" type="text/javascript">

                (function ($, exports) {
                    'use strict';

                    var initApp = function() {
                        var App = require("index");
                        exports.app = new App({el: $("body"), isProduction:<?php echo (IS_PRODUCTION) ? 'true': 'false'; ?>, isAdmin:<?php echo (current_user_can('edit_pages')) ? 'true': 'false'; ?>, smallFontSize: '0.8em', mediumFontSize: '0.9em', largeFontSize: '1.1em', defaultFontSize: 'SMALL' });
                    }

                    initApp();
                })(jQuery, this)

            </script>

        <?php
        }
        
    }
}