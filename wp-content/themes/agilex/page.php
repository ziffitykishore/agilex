<?php
/**
 * Template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Bootstrap Canvas WP
 * @since Bootstrap Canvas WP 1.0
 */

  get_header(); ?>
  
<div class="main-banner-wrap">
  <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
  <div class="main-banner">
    <?php if ($featured_img_url){ ?>
      <img src="<?php echo $featured_img_url; ?>" class="" alt=""/>
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

        <div class="main-content-wrapper"> 
          <div class="container">
          <?php get_template_part( 'loop', 'page' ); ?>
      </div>
        

      </div><!-- /.row -->
      
	<?php get_footer(); ?>