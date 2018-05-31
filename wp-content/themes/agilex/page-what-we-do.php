<?php
/**
 * Template Name: Page What We Do
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
<div class="main-banner">
        <?php if ($featured_img_url){ ?>
        <img src="<?php echo $featured_img_url; ?>" class="" alt=""/>
        <?php } else  { ?>
          <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X450.png" class="" alt=""/>
        <?php }?>
        <div class="page-header-content">
       <div class="container">
         <h1><?php echo the_Title(); ?></h1>
         <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
        </div>     
      </div>
 </div>

<div class="sub-service-wrap" id="what-we-do">
  <div class="container">
    <div class="page-desc margin-top--70 wow fadeInUp">
    <?php
    // TO SHOW THE PAGE CONTENTS
    while ( have_posts() ) : the_post(); ?>
   <?php echo get_the_content(); ?>
   <?php
    endwhile; //resetting the page loop
    wp_reset_query(); //resetting the page query
    ?>
    </div>   
  </div>
  <div class="categories-wrap">
    <div class="container">
      <div class="categories-inner-wrap">
        <?php $testi_args = array(
        'post_type' => 'what_we_do',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'order_by' => 'date',
        'order' => 'ASC'
        );
        $agilex_test_query = new WP_Query($testi_args);        
        while ($agilex_test_query->have_posts()) {
        $agilex_test_query->the_post();
        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
    
          <div class="categories-blk clearfix wow fadeInUp border-efx ">
            <div class="border-ani"></div>
          <?php if ( has_post_thumbnail() ) {
                                    the_post_thumbnail('full');
                                } else { ?>
                                    <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1170X500.png" alt="<?php the_title(); ?>" />
                                <?php } ?>
            <div class="categories-content  wow fadeInUp">
            <a href="<?php echo get_permalink() ?>"><h2><?php echo the_Title(); ?></h2></a>
              <div class="short-desc">
              <?php echo wp_trim_words( get_the_content(), 35, '...' ); ?>
              </div>
              <?php if (get_field('learn_more_text')){ ?>
                <a href="<?php echo get_permalink() ?>" class="btn btn-more btn-blue btn-ripple btn-door"><?php the_field('learn_more_text'); ?></a>
              <?php } else { ?>
                <a href="<?php echo get_permalink() ?>" class="btn btn-more btn-blue btn-ripple btn-door">Learn More</a>
              <?php } ?>
              
            </div>       
          </div>
        <?php }?>
      </div>
    </div>
  </div>
</div>
                                                                                                                                                                                                                            
<?php get_footer(); ?>                                                                        