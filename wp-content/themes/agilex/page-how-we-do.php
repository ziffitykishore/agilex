<?php
/**
 * Template Name: Page How We Do                                                                          
 *
 * @package Agilex
 * @since Agilex 1.0
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

<div class="how-we-work " id="how-we-work">
  <div class="container">
    <div class="page-desc margin-top--70  white-bg">
      <?php
      // TO SHOW THE PAGE CONTENTS
        while ( have_posts() ) : the_post(); ?>
          <?php echo get_the_content(); ?>
        <?php endwhile; //resetting the page loop
        wp_reset_query(); //resetting the page query
      ?>
    </div>   

  <?php $processes = get_post_meta( get_the_ID(), 'how-we-are', true );
  if($processes) { ?>
    <ul class="cd-icons-filling js-cd-icons-filling pad-70">
      <div class="before-bg">
        <div class="inner-elment top-bg"></div>
        <div class="inner-elment bottom-bg"></div>
      </div>
      <?php $i = 1;   foreach( $processes as $process){ ?>
        <li class="cd-service cd-service--<?php echo $i; ?> js-cd-service row">
          <div class="img-blk col-sm-3">
          <?php if($process["process-icon"]) { ?>
          <img src="<?php echo wp_get_attachment_url($process["process-icon"]); ?>" alt="<?php echo $process["process-title"]; ?>"/>
          <?php }?>
          </div>
          <div class="content-detail col-sm-9">
          <div class="heading">
          <h2 class="heading-title"><?php echo $process["process-title"]; ?></h2>
          </div>
          <div class="content-desc"><?php echo $process["process-content"]; ?></div>
          </div>
        </li>
      <?php  $i++; } ?> 
    </ul>
  <?php } ?>
  </div>
</div>


<?php get_footer(); ?>                                                                        