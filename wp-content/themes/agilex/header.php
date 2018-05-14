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
    <header>
        <div class="header-container">
            <div class="logo-wrap pull-left">
                <?php if (function_exists('the_custom_logo')) :
                      the_custom_logo();
                  endif; ?>
                  <a  class="white-logo" href="<?php echo esc_url(home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>">
					<img src="/wp-content/uploads/2018/05/logo_agilex_fragrances_white.png" alt="<?php bloginfo( 'name' ); ?>">
				</a>
            </div>
            <div class="right-block pull-right">
                <div class="search-wrap ">
                    <a class="search-trigger"><i class="fa fa-search"></i></a>
                    <div class="search-form">
                        <a  class="search-close "></a>
                        <form role="search" autocomplete="off" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">

                            <label class="screen-reader-text" for="s"><?php _x( 'Search for:', 'label' ); ?></label>
                            <input autofocus type="text" placeholder="Search here...." value="<?php echo get_search_query(); ?>" name="s" id="search__input" />
                            <!-- <input type="submit" id="searchsubmit" value="<?php echo esc_attr_x( 'Search', 'submit button' ); ?>" /> -->
                            <button type="submit" id="searchsubmit"><span class="sr-only">Search</span></button>
                            <span class="search__info">Hit enter to search or ESC to close</span>
                        </form>
                    </div>
                </div>

                <div class="contact-wrap">
                    <a class="btn btn-sm border-btn txt-upper btn-ripple">Contact Us</a>
                </div>
                
                <a href="#cd-nav" class="cd-nav-trigger">Menu
                    <span class="cd-nav-icon"></span>
                    <svg x="0px" y="0px" width="54px" height="54px" viewBox="0 0 54 54">
                        <circle fill="transparent" stroke="#ffffff" stroke-width="1" cx="27" cy="27" r="25" stroke-dasharray="157 157" stroke-dashoffset="157"></circle>
                    </svg>
                </a>
            </div>

            
                <div id="cd-nav" class="cd-nav">
                    <div class="cd-navigation-wrapper">
                        <div class="col-xs-12 col-sm-6 left-side">
                            <h2>Navigation</h2>
                                
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
                    <div class="col-xs-12 col-sm-6 right-side">
                            
                                <div class="cd-contact-info">
                                    <?php if (is_active_sidebar('footer-contact-us')) : ?>
                                        <?php dynamic_sidebar('footer-contact-us'); ?>
                                    <?php endif; ?>
                                </div>

                                <div class="social-links  ">
                                    <h2>Follows us on</h2>
                                    <?php if (is_active_sidebar('footer-social-links')) : ?>
                                        <?php dynamic_sidebar('footer-social-links'); ?>
                                    <?php endif; ?>
                                </div>
                            
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