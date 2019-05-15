<?php
require_once( __DIR__ . '/includes/product_category_handler.php');
require_once( __DIR__ . '/includes/duplicate_content.php');
require_once( __DIR__ . '/includes/sender_email.php');

add_shortcode('my_sales', 'shortcode_handler_my_sales');
add_action('init', 'fix_sales_handler_from_post', 998);

function ret_false() {
    return false;
}

/**
 *
 * CART
 *
 * */
if (!function_exists('woocommerce_cart')) {

    /**
     * Output the Mini-cart - used by cart widget.
     *
     */
    function woocommerce_cart($args = array()) {

        $defaults = array(
            'list_class' => ''
        );

        $args = wp_parse_args($args, $defaults);

        get_sidebar('cart');
    }

}

//add_action( 'woocommerce_before_account_navigation', 'action_woocommerce_before_account_navigation', 10, 0 );
function action_woocommerce_before_account_navigation() {
    echo '<div class="entry-post wrapper">';
}

//add_action( 'woocommerce_after_account_navigation', 'action_woocommerce_after_account_navigation', 10, 0 );
function action_woocommerce_after_account_navigation() {
    echo '</div>';
}

function new_products() {
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_visibility',
                'value' => array('catalog', 'visible'),
                'compare' => 'IN'
            )
        )
    );

    $loop = new WP_Query($args);

    while ($loop->have_posts()) {
        $loop->the_post();
        global $product;
        if (!$product->is_visible())
            continue;
        wc_get_template_part('content', 'product');
    }
}

function get_recent_product_ids() {
    // Load from cache
    $recent_product_ids = get_transient('recent_products');

    // Valid cache found
    if (false !== $recent_product_ids) {
        return $recent_product_ids;
    }

    $recent_products = get_recent_products();
    $recent_product_ids = wp_parse_id_list(
            array_merge(wp_list_pluck($recent_products, 'id'), array_diff(wp_list_pluck($recent_products, 'parent_id'), array(0)))
    );

    return $recent_product_ids;
}

function get_recent_products() {
    global $wpdb;

    $date = date('Y-m-d', strtotime('-30 days'));

    $ret = $wpdb->get_results("
		SELECT post.ID as id, post.post_parent as parent_id FROM `$wpdb->posts` AS post
		LEFT JOIN `$wpdb->postmeta` AS meta ON post.ID = meta.post_id
		LEFT JOIN `$wpdb->postmeta` AS meta2 ON post.ID = meta2.post_id
		WHERE post.post_type IN ( 'product', 'product_variation' )
			AND post.post_status = 'publish'
			AND post.post_date > '{$date}'
		GROUP BY post.ID;
	");
}

function product_query($q) {

    $product_category = get_terms('product_cat', $args)[0];
    $recent_product_ids = get_recent_product_ids();
    $product_ids_on_sale = wc_get_product_ids_on_sale();

    echo("recent:<br>");
    var_dump((array) $recent_product_ids);
    echo("<br>");

    $q->set('post__in', (array) $recent_product_ids);
}

/** check for sales attribute and if true add SALES Category to it */
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

/** check for featured product attribute and if true add FEATURED Category to it */
add_action("woocommerce_before_product_object_save", "before_product_object_save", 99, 2);

function before_product_object_save($product, $data_store) {
    $is_featured = $product->is_featured();
    set_product_cats($product, FEATURED_CAT_ID, $is_featured);
}

add_action('init', 'disable_wp_emojicons');

function disable_wp_emojicons() {

    // all actions related to emojis
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');

    // filter to remove TinyMCE emojis
    add_filter('tiny_mce_plugins', 'disable_emojicons_tinymce');
}

add_action('woocommerce_before_main_content', 'remove_vp_category_image', 0);

function remove_vp_category_image() {
    remove_action('woocommerce_before_main_content', 'vp_category_image', 21);
    add_action('woocommerce_before_main_content', 'my_category_image', 21);
}

function my_category_image() {
    if (is_product_category()) {
        global $wp_query;
        $cat = $wp_query->get_queried_object();
        $thumbnail_id = get_woocommerce_term_meta($cat->term_id, 'thumbnail_id', true);
        if ($thumbnail_id) {
            $thumbnail_post = get_post($thumbnail_id);
            $image = wp_get_attachment_url($thumbnail_id);
            if ($image) {
                ?>
                <div class="cat-thumb">
                <?php if (!empty($thumbnail_post->post_title) || !empty($thumbnail_post->post_excerpt)) : ?>
                        <div class="cat-thumb-overlay">
                    <?php echo (!empty($thumbnail_post->post_title) ? '<h3>' . $thumbnail_post->post_title . '</h3>' : '' ); ?>
                    <?php echo (!empty($thumbnail_post->post_excerpt) ? '<i>' . $thumbnail_post->post_excerpt . '</i>' : '' ); ?>
                        </div>
                <?php endif; ?>
                    <img src="<?php echo $image ?>" alt="<?php echo get_post_meta($thumbnail_post->ID, '_wp_attachment_image_alt', true); ?>" />
                </div>
                <?php
            }
        }
    }
}

function disable_emojicons_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}

add_action('init', 'store_handle_query_vars', 999);

function store_handle_query_vars() {

    $do_interrupt = false;

    if (isset($_REQUEST['store_action'])) {
        switch ($_REQUEST['store_action']) {
            case 'add_to_cart_success':
                echo json_encode(array('store_action' => 'success', 'message' => __('Added to cart', 'wptouch-pro')));
                $do_interrupt = true;
                break;
            case 'refresh_cart':
                woocommerce_cart();
                $do_interrupt = true;
                break;
            case 'add_to_cart':
                break;
            default:
                print_r(wc_get_notices());
        }

        if ($do_interrupt) {
            wc_clear_notices();
            die();
        }
    } else {
        store_handle_ajax_add_to_cart_failure();
    }
}

function store_handle_ajax_add_to_cart_failure() {
    if (function_exists('wc_get_notices')) {
        $notices = wc_get_notices();
        if (array_key_exists('error', $notices) && isset($_REQUEST['add-to-cart'])) {
            $notices = wc_get_notices();
            $error = preg_replace('#<a.*?>([^>]*)</a> (.*)#i', '$2', $notices['error'][0]);
            echo json_encode(array('store_action' => 'error', 'message' => $error));
            wc_clear_notices();
            die();
        }
    }
}

/*
 *
 * Cart behavior from wptouch
 * Intercepting $REQUEST
 * Add to Cart functionality for variations
 */
add_filter('woocommerce_add_to_cart_redirect', 'return_cart_redirect');

function return_cart_redirect($redirect_url) {
    if (isset($_REQUEST['ajax_cart']) && (!is_cart() )) {
        return '?store_action=add_to_cart_success';
    } else {
        return $redirect_url;
    }
}

add_filter('wc_add_to_cart_message', 'clear_add_to_cart_message');

function clear_add_to_cart_message() {
    return false;
}

add_action('wp_enqueue_scripts', 'add_styles', PHP_INT_MAX);

function add_styles() {
    wp_enqueue_style('child-style', get_stylesheet_uri(), array('vendipro'));
}

// Function to add customer scripts to the site
add_action('wp_enqueue_scripts', 'add_scripts');

function add_scripts() {

    if (IS_PRODUCTION) {
        $current_user = wp_get_current_user();
        $user_id =  (0 == $current_user->ID) ? '' : $current_user->ID;
        // Register analyticstracking.js file (Google Analytics)
        wp_register_script('google-analytics', get_stylesheet_directory_uri() . '/js/analyticstracking.js', false, '1.0', true);
        // Enqueue the registered script file
        wp_enqueue_script('google-analytics');
        // Register gtag.js file (Google Analytics)
//        wp_register_script('google-analytics', get_stylesheet_directory_uri() . '/js/gtag.js', false, '1.0', true);
        // hand over the userID to the analytics script
        wp_localize_script('google-analytics', 'atts', array('user_id' => $user_id, 'ga_id' => GA_ID ));
    } else {
        wp_register_script('gsap-dev-tools', get_stylesheet_directory_uri() . '/js/gsap/dev/GSDevTools.min.js', false, '0.9.1', true);
        wp_enqueue_script('gsap-dev-tools');

    }

    /*
     * Icons & Fonts
     */
    wp_enqueue_style( 'child-style-fonts', get_stylesheet_directory_uri() . '/css/fonts.css' );
	wp_enqueue_style( 'child-style-forms', get_stylesheet_directory_uri() . '/css/forms.css' );
	wp_enqueue_style( 'child-style-animations', get_stylesheet_directory_uri() . '/css/animations.css' );
	wp_enqueue_style( 'child-style-payments', get_stylesheet_directory_uri() . '/css/payments.css' );
    wp_enqueue_style('linear-icons', get_stylesheet_directory_uri() . '/css/linearicons.css');
    wp_enqueue_style('custom-gsap', get_stylesheet_directory_uri() . '/css/gsap/style.css');
    
    wp_register_script('spin.jquery', get_stylesheet_directory_uri() . '/js/spinjs/spin-jquery' . (!IS_DEV_MODE ? '.min' : '') . '.js', false, '0.1', true);
    wp_enqueue_script('spin.jquery');

    wp_register_script('spin', get_stylesheet_directory_uri() . '/js/spinjs/spin.min.js', false, '0.1', true);
    wp_enqueue_script('spin');

    wp_register_script('readmore', get_stylesheet_directory_uri() . '/js/node_modules/readmore-js/readmore.js', array('jquery'), '1.0', true);
    wp_enqueue_script('readmore');

    wp_register_script('fb', get_stylesheet_directory_uri() . '/js/fb.js', array('jquery'), '1.0', true);
    wp_enqueue_script('fb');

    wp_register_script('jquery-utils', get_stylesheet_directory_uri() . '/js/utils.js', array('jquery'), '1.0', true);
    wp_enqueue_script('jquery-utils');
    
    /*
     * GSAP Libraries
     */
    wp_register_script('gsap-tween-max', get_stylesheet_directory_uri() . '/js/gsap/minified/TweenMax.min.js', false, '1.20.4', true);
    wp_enqueue_script('gsap-tween-max');
    wp_register_script('gsap-tween-lite', get_stylesheet_directory_uri() . '/js/gsap/minified/TweenLite.min.js', false, '1.20.4', true);
//    wp_enqueue_script('gsap-tween-lite');
    wp_register_script('gsap-timeline-max', get_stylesheet_directory_uri() . '/js/gsap/minified/TimelineMax.min.js', false, '1.20.4', true);
     wp_enqueue_script('gsap-timeline-max');
    wp_register_script('gsap-timeline-lite', get_stylesheet_directory_uri() . '/js/gsap/minified/TimelineLite.min.js', false, '1.20.4', true);
//    wp_enqueue_script('gsap-timeline-lite');
    wp_register_script('gsap-morph-svg', get_stylesheet_directory_uri() . '/js/gsap/minified/plugins/MorphSVGPlugin.min.js', false, '0.9.1', true);
//    wp_enqueue_script('gsap-morph-svg');
    wp_register_script('gsap-scramble-text', get_stylesheet_directory_uri() . '/js/gsap/minified/plugins/ScrambleTextPlugin.min.js', false, '0.9.1', true);
//    wp_enqueue_script('gsap-scramble-text');
    wp_register_script('gsap-throw-props', get_stylesheet_directory_uri() . '/js/gsap/minified/plugins/ThrowPropsPlugin.min.js', false, '0.9.1', true);
//    wp_enqueue_script('gsap-throw-props');
    wp_register_script('gsap-draw-svg', get_stylesheet_directory_uri() . '/js/gsap/minified/plugins/DrawSVGPlugin.min.js', false, '0.9.1', true);
//    wp_enqueue_script('gsap-draw-svg');
    wp_register_script('gsap-split-text', get_stylesheet_directory_uri() . '/js/gsap/minified/utils/SplitText.min.js', false, '0.9.1', true);
//    wp_enqueue_script('gsap-split-text');
    
    /*
     * ScrollMagic
     */
    wp_register_script('scroll-magic', get_stylesheet_directory_uri() . '/js/ScrollMagic/ScrollMagic.js', array('main'), '0.10', true);
//    wp_enqueue_script('scroll-magic');
    /*
     * GSAP Main
     */
//    wp_register_script('gsap-main', get_stylesheet_directory_uri() . '/js/gsap/custom/main.splitText.js', array('main'), '0.10', true);
//    wp_register_script('gsap-main', get_stylesheet_directory_uri() . '/js/gsap/custom/main.drawSVG.js', array('main'), '0.10', true);
//    wp_register_script('gsap-main', get_stylesheet_directory_uri() . '/js/gsap/custom/main.morphSVG.js', array('main'), '0.10', true);
//    wp_enqueue_script('gsap-main');
    
    /*
     * Main JS
     */
    wp_register_script('main', get_stylesheet_directory_uri() . '/js/main.js', array('jquery-utils', 'gsap-tween-max'), '0.10', true);
    wp_enqueue_script('main');
    
    /*
     * Sidebar Cart
     */
    wp_register_script('cart', get_stylesheet_directory_uri() . '/js/sidebar.cart.js', array('jquery'), '1.0', true);
    wp_enqueue_script('cart');

    /*
     * Other Libraries
     */
    wp_enqueue_script('fancybox', get_stylesheet_directory_uri() . '/js/fancybox/jquery.fancybox.js', false, false, true);
    wp_enqueue_script('fancybox-helper', get_stylesheet_directory_uri() . '/js/fancybox-helper.js', false, false, true);
    wp_enqueue_style('fancybox', get_stylesheet_directory_uri() . '/css/fancybox/jquery.fancybox.css');
    wp_enqueue_style('fancy-metaslider', get_stylesheet_directory_uri() . '/css/fancy-metaslider.css');
    wp_enqueue_style('dashicons');

    /*
     * Twitter Bootstrap
     */
    wp_register_script('bootstrap', get_stylesheet_directory_uri() . '/node_modules/bootstrap/dist/js/bootstrap.js', array('jquery'), false, true);
    wp_enqueue_script('bootstrap');
    
    /*
     * My Customized Bootstrap Vars && Bootstrap Styles Libraries
     */
    /*
     * Complete Bootstrap Library( All Libraries )
     */
    //    wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/node_modules/bootstrap/dist/css/bootstrap.css');
    wp_enqueue_style('my-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap/index.css');
}

// we need SpineApp to be loaded after wpadminbar has been rendered (to access it)
// which seems only be possible using wp_footer hook
//add_action('wp_footer', 'add_spine_app', 1001); // hosted in includes folder

/*
 * add woocommerce styles
 */
add_filter('woocommerce_enqueue_styles', 'woocommerce_styles');

function woocommerce_styles() {
    return array(
        'woocommerce-layout' => array(
            'src' => plugins_url('woocommerce/assets/css/woocommerce-layout.css'),
//            'src' => get_stylesheet_directory_uri() . '/css/woocommerce-layout.css',
            'deps' => '',
            'version' => WC_VERSION,
            'media' => 'all',
            'has_rtl' => true,
        ),
        'woocommerce' => array(
            'src' => plugins_url('woocommerce/assets/css/woocommerce.css'),
            'deps' => '',
            'version' => WC_VERSION,
            'media' => 'all',
            'has_rtl' => true,
        ),
        'woocommerce-smallscreen' => array(
            'src' => plugins_url('woocommerce/assets/css/woocommerce-smallscreen.css'),
            'deps' => 'woocommerce-layout',
            'version' => WC_VERSION,
            'media' => 'only screen and (max-width: ' . apply_filters('woocommerce_style_smallscreen_breakpoint', $breakpoint = '600px') . ')',
            'has_rtl' => true,
        ),
    );
}

// Display number of Products per page
add_filter('loop_shop_per_page', create_function('$cols', 'return 36;'), 20);

// Add specific body CSS class by filter.
add_filter('body_class', 'body_classes');

function body_classes($classes) {
    $classes = array_merge($classes, array('logo-active', 'vflex'));
    if (is_shop()) {
        $classes = array_merge($classes, array('shop-page'));
    }
    return $classes;
}

;

/**
 * WooCommerce Extra Feature Related Products
 * --------------------------
 *
 * Change number of related products on product page
 * Set own value for 'posts_per_page'
 *
 */
add_filter('woocommerce_output_related_products_args', 'filter_woocommerce_output_related_products_args');

function filter_woocommerce_output_related_products_args($args) {

    $args['posts_per_page'] = 5;
    $args['columns'] = 5; // arranged in 5 columns
    return $args;
}

add_filter('woocommerce_is_attribute_in_product_name', function () {
    return false;
});

add_filter('woocommerce_gzdp_checkout_wrapper_classes', 'active_step_wrapper_classes');

function active_step_wrapper_classes($classes) {

    $ret = array_merge($classes, array('entry-post', 'wrapper'));
    return $ret;
}

add_action('wp_enqueue_scripts', 'remove_styles', 30);

function remove_styles() {
    wp_dequeue_style('pac-styles');
    wp_dequeue_style('pac-layout-styles');
}

add_action('woocommerce_archive_description', 'reorder_description', -1);

function reorder_description() {
    if (\Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::is_brand_archive_page()) {
        remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
        add_action('woocommerce_archive_description', function() {
            wc_format_content(wc_get_template('templ/brand/description_line.php'));
        }, 1);
        remove_action('woocommerce_archive_description', 'woocommerce_result_count', 0);
        add_action('woocommerce_archive_description', 'woocommerce_result_count', 2);
    }
}

// change order of appearance to match our layout
// add banner to main_content
add_action('woocommerce_before_main_content', 'add_perfect_brand_banner');

function add_perfect_brand_banner() {
    $instance = \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::this();

    add_action('woocommerce_before_main_content', array($instance, 'archive_page_banner'), 20);
}

// remove banner from shop_loop
add_action('woocommerce_before_shop_loop', 'remove_perfect_brand_banner', 0);

function remove_perfect_brand_banner() {
    $instance = \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::this();

    remove_action('woocommerce_before_shop_loop', array($instance, 'archive_page_banner'), 9);
}

add_action('woocommerce_page_title', 'pwb_page_title');

function pwb_page_title($title) {
    if (\Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::is_brand_archive_page()) {
        return $title . "";
    }
    return $title;
}

/*
 * wc_remove_related_products (disabled)
 *
 * Clear the query arguments for related products so none show.
 *
 */

//add_filter('woocommerce_related_products_args','wc_remove_related_products', 10);
function wc_remove_related_products($args) {
    return array();
}

if (wp_is_mobile()) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if( is_plugin_active('wptouch-pro') ) {
        add_action('init', 'load_wptouch');
    }
}

function load_wptouch() {
    $localize_params = array(
        'ajaxurl' => get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php',
        'siteurl' => str_replace(array('http://' . $_SERVER['SERVER_NAME'] . '', 'https://' . $_SERVER['SERVER_NAME'] . ''), '', get_bloginfo('url') . '/'),
        'security_nonce' => wp_create_nonce('wptouch-ajax'),
        'current_shortcode_url' => add_query_arg(array('wptouch_shortcode' => '1'), esc_url_raw($_SERVER['REQUEST_URI'])),
        'query_vars' => $query_vars
    );

    wp_enqueue_script('wptouch-front-ajax', WPTOUCH_URL . '/include/js/wptouch.min.js', array('jquery'), md5(WPTOUCH_VERSION), true);
    wp_localize_script('wptouch-front-ajax', 'wptouchMain', apply_filters('wptouch_localize_scripts', $localize_params));

    wptouch_load_framework();
}

add_filter('dg_gallery_template', 'document_gallery_wrapper');

function document_gallery_wrapper($gallery) {
    if (is_product()) {
        return $gallery;
    } else {
        return $gallery;
    }
}

// Hook into the_content
//add_filter('the_content', 'clear_br');
function clear_br($content) {
    return $content;
}

// customize Product Descrption tab
//add_filter( 'woocommerce_product_tabs', 'woo_custom_description_tab', 98 );
function woo_custom_description_tab($tabs) {

    $tabs['description']['callback'] = 'woo_custom_description_tab_content'; // Custom description callback

    return $tabs;
}

function woo_custom_description_tab_content() {
    echo '<h2>Custom Description</h2>';
    echo '<p>Here\'s a custom description</p>';
}

// set a title when initializing new products
add_filter('default_title', 'default_product_title');

function default_product_title($title) {
    global $post_type;

    switch ($post_type) {

        case 'product':
            $title = 'Neues Produkt';
            break;
    }

    return $title;
}

// set a description when initializing new products
add_filter('default_content', 'default_product_content');

function default_product_content($content) {
    global $post_type;

    switch ($post_type) {

        case 'product':
            $content = wc_get_template_html('templ/new_product_description.php');
            break;
    }

    return $content;
}

// return attachment from postname ( filename w/o suffix )
function get_attachment_by_post_name($post_name) {
    $args = array(
        'posts_per_page' => 1,
        'post_type' => 'attachment',
        'name' => trim($post_name),
    );
    $get_attachment = new WP_Query($args);

    if ($get_attachment->posts[0])
        return $get_attachment->posts[0];
    else
        return false;
}

// add my own custom theme support (mainly for blurring of background images)
function my_custom_background_cb() {
    // $background is the saved custom image, or the default image.
    $background = set_url_scheme(get_background_image());

    // $color is the saved custom color.
    // A default has to be specified in style.css. It will not be printed here.
    $color = get_background_color();

    if ($color === get_theme_support('custom-background', 'default-color')) {
        $color = false;
    }

    if (!$background && !$color) {
        if (is_customize_preview()) {
            echo '<style type="text/css" id="custom-background-css"></style>';
        }
        return;
    }

    $style = $color ? "background-color: #$color;" : '';

    if ($background) {
        $image = ' background-image: url("' . esc_url_raw($background) . '");';

        // Background Position.
        $position_x = get_theme_mod('background_position_x', get_theme_support('custom-background', 'default-position-x'));
        $position_y = get_theme_mod('background_position_y', get_theme_support('custom-background', 'default-position-y'));

        if (!in_array($position_x, array('left', 'center', 'right'), true)) {
            $position_x = 'left';
        }

        if (!in_array($position_y, array('top', 'center', 'bottom'), true)) {
            $position_y = 'top';
        }

        $position = " background-position: $position_x $position_y;";

        // Background Size.
        $size = get_theme_mod('background_size', get_theme_support('custom-background', 'default-size'));

        if (!in_array($size, array('auto', 'contain', 'cover'), true)) {
            $size = 'auto';
        }

        $size = " background-size: $size;";

        // Background Repeat.
        $repeat = get_theme_mod('background_repeat', get_theme_support('custom-background', 'default-repeat'));

        if (!in_array($repeat, array('repeat-x', 'repeat-y', 'repeat', 'no-repeat'), true)) {
            $repeat = 'repeat';
        }

        $repeat = " background-repeat: $repeat;";

        // Background Scroll.
        $attachment = get_theme_mod('background_attachment', get_theme_support('custom-background', 'default-attachment'));

        if ('fixed' !== $attachment) {
            $attachment = 'scroll';
        }

        $attachment = " background-attachment: $attachment;";

        $style .= $image . $position . $size . $repeat . $attachment;
    }
    ?>
    <style type="text/css" id="custom-background-css">
        body.custom-background:before { <?php echo trim($style); ?> }
    </style>
    <?php
}

function custom_theme_support() {
    add_theme_support('custom-background', array(
        'default-color' => '333333',
        'wp-head-callback' => 'my_custom_background_cb'
    ));
}

add_action('after_setup_theme', 'custom_theme_support');

add_filter( 'upload_mimes', 'allow_svg_upload' );
function allow_svg_upload( $m ) {
    $m['svg'] = 'image/svg+xml';
    $m['svgz'] = 'image/svg+xml';
    return $m;
}
