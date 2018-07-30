<?php
/**
 * Template Name: Page Sitemap
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>

<div class="main-banner-wrap">
<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
<div class="main-banner">
<?php if($featured_img_url){     
                   $thumbnail_ID = get_post_thumbnail_id( get_the_ID() );
                   $alt_text = get_post_meta( $thumbnail_ID, '_wp_attachment_image_alt', true );  ?>
                    <img src="<?php echo $featured_img_url; ?>" alt="<?php echo $alt_text; ?>"/>
        <?php } else  { ?>
          <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X450.png" class="" alt=""/>
        <?php }?>
        </div>
        <div class="page-header-content">
       <div class="container">
         <h1><?php echo the_Title(); ?></h1>
         <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
        </div>     
      
 </div>
        </div>


<div class="sitemap-wrap">
  <div class="container">
<ul class="nav navbar-stack">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'top-menu',
                            'menu_class' => 'primary-menu',
                            'container' => 'false',
                            'items_wrap' => '%3$s',
                            'fallback_cb' => 'bootstrap_canvas_wp_menu_fallback',
                        ));
                        
                            wp_nav_menu(array(
                                'menu' => 'Footer menu',
                                'menu_class' => 'footer-menu',
                                'container' => 'false',
                                'items_wrap' => '%3$s',
                            ));
                        
                        ?>
                    </ul>
                      </div>
</div>



<?php get_footer(); ?>                                                                        