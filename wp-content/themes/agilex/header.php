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

    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700" rel="stylesheet">

     <?php if ( get_theme_mod( 'm1_logo' ) ) : ?>
    <link rel="shortcut icon" href="<?php echo get_theme_mod( 'fav_icon' ); ?>" />
    <?php endif; ?>
    
  </head>
  <body <?php body_class(); ?>>

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
                            <input autofocus type="text" placeholder="Search..." value="<?php echo get_search_query(); ?>" name="s" id="search__input" />
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

            
                <div id="cd-nav" class="cd-nav">
                <!-- <div class="hamburger hamburger--spring js-hamburger cd-nav-trigger">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div> -->
                    <div class="overlaybgDark"></div>
                    <div class="cd-navigation-wrapper">
                    
                    
                                
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
            <!-- <div class="sidebar-push">
                <span class="overlay"></span>
                <div class="menu-wrap">
                    <nav id="site-navigation" class="navigation main-navigation" role="navigation">
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
                    </nav>
                </div>

            </div> -->
        </div>
    </header>
    <div class="main-content">