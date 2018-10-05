<?php
/**
 * Template Name: Page Our History
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

<!-- <div class="timeline-sec" id="our-history">
    <div class="container">
        <div class="margin-top--70  white-bg timeline-outer">
            <div class="timeline-inner pad-70">
                <?php $args = array(
                'post_type' => 'our_history',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'order_by' => 'date',
                'order' => 'ASC'
                );
                $agilex_history_query = new WP_Query($args); ?>
                <?php 
                while ($agilex_history_query->have_posts()) {
                $agilex_history_query->the_post();
                $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
                <div class="timeline-item flex-sec ">
                    <div class="timeline-image flex-xs-100 flex-50 wow fadeInUp">
                        <div class="img-sec">
                            
                            <figure class="feature-image">
                            <?php if ( has_post_thumbnail() ) {
                                    the_post_thumbnail('full');
                                } else { ?>
                                    <img  class="" src="<?php bloginfo('template_directory'); ?>/images/placeholder_500X350.png" alt="Placeholder" />
                            <?php } ?>
                                
                            </figure>
                            <figure class="seconday-image">
                                <img  class="" src="<?php bloginfo('template_directory'); ?>/images/placeholder_500X350.png" alt="<?php the_title(); ?>" />
                            </figure>
                        </div>    
                    </div>
                    <div class=" timeline-content flex-xs-100 flex-50 wow fadeInUp">
                        <div class="timeline-heading">
                            <h2 class="timeline-title"><?php echo the_Title(); ?></h2>
                            <?php if (get_field('year', get_the_ID())){ ?>
                                <div class="timeline-year <?php if (get_field('from_year', get_the_ID())) { echo 'timeline-year-from'; } if (get_field('beyond', get_the_ID())) { echo 'timeline-year-beyond'; } ?> "><span class="current-year"><?php echo get_field('year'); ?></span> <?php if (get_field('from_year', get_the_ID())){ ?> <span class="additional"><?php echo get_field('from_year');?></span>  <?php }?> <?php if (get_field('beyond', get_the_ID())){?> <span class="additional"><?php echo get_field('beyond');?></span> <?php }?></div>
                            <?php } else { ?>
                                <div class="timeline-year">0000</div>
                            <?php } ?>
                        </div>
                        <div class="timeline-desc">
                        <?php echo the_Content(); ?>
                        </div>
                    </div>
                </div>   
                <?php } ?>   
            </div>
        </div>
    </div>
</div> -->




<section class="timeline-section" >


<div class="container">



    <div class="timeline-inner margin-top--70 white-bg pad-tb-50 pad-30">
    <?php $args = array(
                'post_type' => 'our_history',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'order_by' => 'date',
                'order' => 'ASC'
                );
    $agilex_unique_query = new WP_Query($args); ?>
    <ul class="nav ord-tab list slider-nav" role="tablist">
        <?php while ($agilex_unique_query->have_posts()) {
        $agilex_unique_query->the_post(); ?>
        <li role="presentation">
            
        <?php if (get_field('year', get_the_ID())){ ?>
                                <div class="timeline-year <?php if (get_field('from_year', get_the_ID())) { echo 'timeline-year-from'; } if (get_field('beyond', get_the_ID())) { echo 'timeline-year-beyond'; } ?> "><span class="current-year"><?php echo get_field('year'); ?></span> <?php if (get_field('from_year', get_the_ID())){ ?> - <span class="additional"><?php echo get_field('from_year');?></span>  <?php }?> <?php if (get_field('beyond', get_the_ID())){?> & <span class="additional"><?php echo get_field('beyond');?></span> <?php }?></div>
                            <?php } else { ?>
                                <div class="timeline-year">0000</div>
                            <?php } ?>
                <div class="hist-title"><?php echo the_Title(); ?></div>
            
        </li>
        <?php } ?>
    </ul>
    <div class="tab-content slider-for">
        <?php  while ($agilex_unique_query->have_posts()) {
                $agilex_unique_query->the_post();?>
        <div class="timeline-blk">
        
        <div class="timeline-item flex-sec ">
        
                    <div class="timeline-image flex-xs-100 flex-50 ">
                        <div class="img-sec">
                            
                            <figure class="feature-image">
                            <?php if ( has_post_thumbnail() ) {
                                    the_post_thumbnail('full');
                                } else { ?>
                                    <img  class="" src="<?php bloginfo('template_directory'); ?>/images/placeholder_500X350.png" alt="Placeholder" />
                            <?php } ?>
                                
                            </figure>
                            <figure class="secondary-image">
                                <?php if (get_field('secondary_image', get_the_ID())){ ?>
                                    <?php $image = get_field('secondary_image'); ?>
                                    <img class="secondary_image" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
                                <?php }  else {?>
                                    <img  class="" src="<?php bloginfo('template_directory'); ?>/images/placeholder_500X350.png" alt="<?php the_title(); ?>" />
                                <?php } ?>
                            </figure>
                        </div>    
                    </div>
                    <div class="timeline-content flex-xs-100 flex-50">
                        <div class="timeline-heading">
                            <h2 class="timeline-title"><?php echo the_Title(); ?></h2>
                            
                        </div>
                        <div class="timeline-desc">
                        <?php echo the_Content(); ?>
                        </div>
                    </div>
                </div> 
            
                                </div>
    <?php   } ?>
    </div></div>
<?php /* Restore original Post Data */
wp_reset_postdata();?>
</div>





</section>



    <?php get_footer(); ?>