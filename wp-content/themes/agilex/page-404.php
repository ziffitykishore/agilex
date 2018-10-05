<?php
/**
 * Template Name: Page Not Found                                                                
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>

<div class="main-banner-wrap">
  <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
    <div class="main-banner">
      <?php if ($featured_img_url){ 
        $thumbnail_ID = get_post_thumbnail_id( get_the_ID() );
        $alt_text = get_post_meta( $thumbnail_ID, '_wp_attachment_image_alt', true ); ?>
        <img src="<?php echo $featured_img_url; ?>" class="" alt="<?php echo $alt_text; ?>"/>
      <?php } else  { ?>
        <img src="<?php bloginfo('template_directory'); ?>/images/blog-header.jpg" class="" alt="<?php echo the_Title(); ?>"/>
      <?php }?>
    </div>
    <div class="page-header-content">
      <div class="container">
        <h1><?php echo the_Title(); ?></h1>
        <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
      </div>     
    </div>
</div>

<?php include(TEMPLATEPATH . "/404.php"); ?>
                                                                      