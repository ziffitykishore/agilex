<?php
/**
 * Template Name: Page Custom
 *
 * @package Agilex
 * @since Agilex 1.0
 */

  get_header(); ?>
  
  <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
    <div class="main-banner" style="background: url('<?php echo $featured_img_url; ?>') no-repeat center center; background-size: cover;">
        <img src="<?php echo $featured_img_url; ?>" class="hidden" alt="" />
        <div class="page-header-content">
            <div class="container">
                <h1>
                    <?php echo the_Title(); ?>
                </h1>
                <p>
                    <?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?>
                </p>
            </div>
        </div>
    </div>

     
     <?php 
      // TO SHOW THE PAGE CONTENTS
    while ( have_posts() ) : the_post(); ?>
    <?php echo the_Content(); ?>
    <?php
     endwhile; //resetting the page loop
     wp_reset_query(); //resetting the page query
      ?>
      
	<?php get_footer(); ?>