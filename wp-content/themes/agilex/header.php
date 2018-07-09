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

    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KJCSL9B');</script>
<!-- End Google Tag Manager -->

    

   
  </head>
  <body <?php body_class(); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KJCSL9B"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<div class="loader scale">
    <div class="circular circular-animate"></div>
    <div class="loader-inner">
        <?php if (function_exists('the_custom_logo')) :
            the_custom_logo();
        endif; ?>
    </div>
</div>

<div class="temp-wrapper">
    <header class="main-header">
        <div class="header-container">
            <div class="logo-wrap pull-left">
                <?php if (function_exists('the_custom_logo')) :
                the_custom_logo();
                endif; ?>

                <?php if ( get_theme_mod( 'm1_logo' ) ) : ?>
                    <a  class="white-logo"  href="<?php echo esc_url( home_url( '/' ) ); ?>" id="site-logo" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                        <img src="<?php echo get_theme_mod( 'm1_logo' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
                    </a>
                <?php endif; ?>
            </div>
            <div class="right-block pull-right">
                <div class="search-wrap ">
                    <a class="search-trigger"><i class="fa fa-search"></i></a>
                    <div class="search-form">
                        <a  class="search-close "></a>
                        <form role="search" autocomplete="off" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">

                            <label class="screen-reader-text" for="s"><?php _x( 'Search for:', 'label' ); ?></label>
                            <input autofocus type="text" placeholder="Search..." value="" name="s" id="search__input" />
                            <!-- <input type="submit" id="searchsubmit" value="<?php echo esc_attr_x( 'Search', 'submit button' ); ?>" /> -->
                            <button type="submit" id="searchsubmit"><span class="sr-only">Search</span></button>
                            <span class="search__info">Hit enter to search or ESC to close</span>
                        </form>
                    </div>
                </div>

                <div class="contact-wrap">
                    <a href="/lets-talk-fragrance/" class="btn btn-sm border-btn txt-upper btn-ripple btn-door">#Let's Talk Fragrance</a>
                </div>

                <div class="hamburger hamburger--spring js-hamburger cd-nav-trigger">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </div>
        </div>
        

        <div id="cd-nav" class="cd-nav">
            <div class="overlaybgDark"></div>
        <div class="cd-navigation-wrapper" style="background-image: url('<?php echo get_theme_mod( 'menu_bg' ); ?>')">
            <div class="hamburger hamburger--spring js-hamburger cd-nav-trigger">
                <div class="hamburger-box">
                    <div class="hamburger-inner"></div>
                </div>
            </div>
            <div class="menu-wrap">
                <nav id="site-navigation" class="navigation main-navigation cd-primary-nav" role="navigation">
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
    </div>
    </header>
    <div class="main-content">