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

    <div class="footer-wrap">
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
                <div class="col-md-6 col-md-push-6 footer_menu ">
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
                    <div class="col-md-6 col-md-pull-6 copyright-content">
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
                            <p class="copyright">&copy; Copyright <?php echo date(Y); ?> Agilex. All Rights Reserved.</p>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
        </div>
            <div class="scroll-top">
                <a href="#"><?php _e( 'Back to top', 'bootstrapcanvaswp' ); ?></a>
            </div>
    </div>

    <?php
	  /*
	   * Always have wp_footer() just before the closing </body>
	   * tag of your theme, or you will break many plugins, which
	   * generally use this hook to reference JavaScript files.
	   */
	  wp_footer();
	?>
  </body>
</html>