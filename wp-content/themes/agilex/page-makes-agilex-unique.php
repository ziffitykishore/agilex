<?php
/**
 * Template Name: Page What Makes Agilex Unique
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
          <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X450.png" class="" alt="Agilex Fragrances"/>
        <?php }?>
        </div>
        <div class="page-header-content">
       <div class="container">
         <h1><?php echo the_Title(); ?></h1>
         <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
        </div>     
      
 </div>
        </div>

<div class="sub-service-wrap" id="agilex-unique">
  <div class="container">
    <div class="page-desc margin-top--70 wow fadeIn">
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
        'post_type' => 'makes_agilex_unique',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'order_by' => 'date',
        'order' => 'ASC'
        );
        $agilex_test_query = new WP_Query($testi_args);        
        while ($agilex_test_query->have_posts()) {
        $agilex_test_query->the_post();
        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
    
          <div class="categories-blk clearfix wow fadeIn border-efx ">
            <div class="border-ani"></div>
            <?php if (get_field('full_image', get_the_ID())){ ?>
                    <?php $image = get_field('full_image'); ?>
                      <img class="full-image" src="<?php echo $image['url']; ?>" alt="<?php echo the_Title(); ?>" />
                  <?php } else { ?>
                      <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1170X500.png" alt="<?php the_title(); ?>" />
            <?php } ?>
            <div class="categories-content  wow fadeIn">
              <h2><a href="<?php echo get_permalink() ?>"><?php echo the_Title(); ?></a></h2>
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