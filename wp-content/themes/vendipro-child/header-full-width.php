<?php
/**
 * The template for displaying header
 *
 * @author Vendidero
 * @version 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php vp_html_tag_schema(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="google-site-verification" content="1E8bMLbzs-rgTtj-UXfkAxBFZq5NoSjBdnoADXyd7eo" />
        <title><?php wp_title('|', true, 'right'); ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
        <link href="/favicon.ico?v=1.1" type="image/x-icon" rel="shortcut icon">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <header id="hmain">
            <div id="hbar">
                <div class="container container-nomargin">
                    <div class="grid grid-3">
                        <div class="column">
                            <p><?php echo do_shortcode(vp_get_option('features_1')); ?></p>
                        </div>
                        <div class="column align-center">
                            <p><?php echo do_shortcode(vp_get_option('features_2')); ?></p>
                        </div>
                        <div class="column align-right">
                            <p><?php echo do_shortcode(vp_get_option('features_3')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-inner">
                <div class="container">
                    <div class="widgets" id="widgets-header">
                        <div class="grid grid-<?php echo vendipro()->widgets->get_widget_count('vp_header') + 2; ?>">
                            <div id="logo" class="widget column">
                                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-text <?php echo (!vp_get_option('show_logo') ? 'logo-active' : '' ); ?>" rel="home" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>">
                                    <span class="pagetitle"><?php echo bloginfo('name'); ?></span>
                                    <span class="subtitle"><?php echo bloginfo('description'); ?></span>
                                </a>
                                <a class="logo-img <?php echo ( vp_get_option('show_logo') ? 'logo-active' : '' ); ?>" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
                                    <span class="logo-helper"></span>
                                    <img src="<?php echo ( is_ssl() ? str_replace('http://', 'https://', vp_get_option('logo')) : vp_get_option('logo') ); ?>" rel="logo" alt="<?php echo vp_get_option('logo_alt'); ?>" />
                                </a>
                            </div>

                            <?php dynamic_sidebar('vp_header'); ?>

                            <?php do_action('vendipro_after_widgets_header'); ?>

                        </div>
                    </div>
                    <div class="container-overlay"></div>
                    <div class="header-view"></div>
                    <div class="font-sizes"></div>
                </div>
                <div class="container container-nomargin" id="nav-wrapper">
                    <nav class="navbar" role="navigation">

                        <div class="menu-left">
                            <?php wp_nav_menu(array('container' => '', 'theme_location' => 'header-main', 'menu_class' => 'nav slimmenu', 'menu_id' => 'menu-header')); ?>
                        </div>

                    </nav>
                </div>
                <div class="container container-nomargin" id="featured-image">
                    <?php
                    if (vp_get_option('blog_featured_header_image') && has_post_thumbnail($post->ID) &&
                            ( $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(HEADER_IMAGE_WIDTH, HEADER_IMAGE_WIDTH)) ) &&
                            $image[1] >= HEADER_IMAGE_WIDTH) :
                        ?>
                        <div class="featured-image full-width"><?php echo get_the_post_thumbnail($post->ID, 'post-thumbnail'); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        <div class="container" id="main-wrapper">