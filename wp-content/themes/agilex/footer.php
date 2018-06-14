<?php
/**
 * Template for displaying the footer
 *
 * Contains the closing of the id=main div and all content
 * after. Calls sidebar-footer.php for bottom widgets.
 *
 * @package Agilex
 * @since Agilex WP 1.0
 */
?>
    </div><!-- /.container -->

    <footer class="footer-wrap">
        <div class="footer-content">
            <div class="container">
                <?php if (is_active_sidebar('footer-kick-area')) : ?>
                    <?php dynamic_sidebar('footer-kick-area'); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer-contact">
            <div class="container">
                <div class="row">
                    <div class="footer-contact-us col-sm-6">
                        <?php if (is_active_sidebar('footer-contact-us')) : ?>
                            <?php dynamic_sidebar('footer-contact-us'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="social-icon-footer col-sm-6">
                        <div class="footer_menu">
                            <?php if (is_active_sidebar('footer-social-links')) : ?>
                                <?php dynamic_sidebar('footer-social-links'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-copy black-bg">
            <div class="container">
                <div class="row">
                <div class="col-md-7 col-md-push-5 footer_menu ">
                        <ul class=" navbar-nav navbar-right">
                        <?php
                            wp_nav_menu(array(
                                'menu' => 'Footer menu',
                                'menu_class' => 'footer-menu',
                                'container' => 'false',
                                'items_wrap' => '%3$s',
                            ));
                        ?>
                        </ul>
                    </div>
                    <div class=" col-md-5 col-md-pull-7 copyright-content">
                        <?php
                        $copyright_text = get_theme_mod( 'copyrighttext', '' );
                        $year = (int) preg_replace('/[^0-9]/', '', $copyright_text);
                        if ($year != date("Y")) {
                            $cur_year = date("Y");
                            $copyright_text = str_replace($year,$cur_year,$copyright_text);
                        } ?>
                        <?php if ( $copyright_text !== '' ) : ?>
                            <p class="copyright"><?php echo $copyright_text; ?></p>
                        <?php else: ?>
                            <p class="copyright">&copy; <?php echo date(Y) ." ". get_bloginfo( 'name' ); ?>. All Rights Reserved.</p>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
            <div class="scroll-top ">
            <span class="scroll-up btn-ripple"><span class="sr-only"><?php _e( 'Back to top', 'bootstrapcanvaswp' ); ?></span> <i class="fa fa-chevron-up"></i></span>
            </div>
        </div>
          
                    </footer>

    <?php
	  /*
	   * Always have wp_footer() just before the closing </body>
	   * tag of your theme, or you will break many plugins, which
	   * generally use this hook to reference JavaScript files.
	   */
	  wp_footer();
    ?>
    </div>
   

    <script type="text/javascript" id="cookieinfo"
        src="<?php echo get_template_directory_uri(); ?>/js/cookieinfo.min.js"
        data-bg="#174a79a6"
        data-fg="#FFFFFF"
        data-link="#fff"
        data-message = "This website intends to use cookies to improve the site and your experience. By continuing to browse the site you are agreeing to accept our use of cookies. If you require further information and/or do not wish to have cookies placed when using the site, visit "
        data-linkmsg = "Cookies page."
        data-moreinfo: "http://wikipedia.org/wiki/HTTP_cookie"
        data-cookie="CookieInfoScript"
        data-text-align="left"
        data-fontSize = "10px"
        data-fontFamily = "Popins, arial, sans-serif"
        data-divlinkbg = "transparent"
        data-divlink = "#fff"
        data-zindex = "8888"
        data-close-text="Got it!">
</script>

  </body>
</html>
