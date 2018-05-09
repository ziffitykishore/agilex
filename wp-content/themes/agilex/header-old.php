<?php
/**
 * Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">.
 *
 * @package Agilex
 * @since Agilex 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>

	<?php
	  /*
	   * We add some JavaScript to pages with the comment form
	   * to support sites with threaded comments (when in use).
	   */
	  if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	  /*
	   * Always have wp_head() just before the closing </head>
	   * tag of your theme, or you will break many plugins, which
	   * generally use this hook to add elements to <head> such
	   * as styles, scripts, and meta tags.
	   */
	  wp_head();
    ?>
  </head>
  <body <?php body_class(); ?>>
    <header>
        <div class="header-container">
            <div class="logo-wrap pull-left">
                <?php if (function_exists('the_custom_logo')) :
                      the_custom_logo();
                  endif; ?>
            </div>
            <div class="right-block pull-right">
                <div class="search-wrap ">
                    <i class="fa fa-search"></i>
                    <div class="search-form">
                        <form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">

                            <label class="screen-reader-text" for="s"><?php _x( 'Search for:', 'label' ); ?></label>
                            <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" />
                            <input type="submit" id="searchsubmit" value="<?php echo esc_attr_x( 'Search', 'submit button' ); ?>" />
                        </form>
                    </div>
                </div>

                <div class="contact-wrap">
                    <a href="<?php echo home_url( '/' )."/contact"; ?>" class="border-btn txt-upper">Contact Us</a>
                </div>
                <div class="hambur-wrap">
                    <div class="hambur-inner">
                        <span class="bar bar-1"></span>
                        <span class="bar bar-2"></span>
                        <span class="bar bar-3"></span>
                    </div>
                </div>
            </div>
            <div class="sidebar-push">
                <nav id="site-navigation menu-wrap" class="navigation main-navigation" role="navigation">
                    <ul id="primary-menu" class="nav navbar-nav">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'top-menu',
                            'menu_class' => 'primary-menu',
                            'container' => 'false',
                            'items_wrap' => '%3$s',
                            'fallback_cb' => 'bootstrap_canvas_wp_menu_fallback',
                        ));
                        ?>
                    </ul>
                </nav><!-- #site-navigation -->

            </div>
        </div>
    </header>
    <div class="container">