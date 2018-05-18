<?php
/**
 * Template Name: Page Our History
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

    <div class="timeline-sec" id="our-history">
        <div class="container">
            <div class="margin-top--70  white-bg timeline-outer">
                <div class="timeline-inner pad-70">
                    <div class="timeline-item flex-sec ">
                    <div class="timeline-image flex-50 wow fadeInUp">
                            <div class="img-sec">
                                <figure class="feature-image">
                                    <img  class="" src="/wp-content/uploads/2018/05/01_history.jpg" alt="<?php the_title(); ?>" />
                                </figure>
                                                               
                                
                                <figure class="seconday-image">
                                    <img  class="" src="<?php bloginfo('template_directory'); ?>/images/placeholder_500X350.png" alt="<?php the_title(); ?>" />
                                </figure>
                            </div>    
                        </div>
                        <div class=" timeline-content flex-50 wow fadeInUp">
                            <div class="timeline-heading">
                                <div class="timeline-title">The Beginning</div>
                                <div class="timeline-year">1950</div>
                            </div>
                            <div class="timeline-desc">
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type</p>
                            </div>
                        </div>
                       

                    </div>   
                    <div class="timeline-item flex-sec ">
                    <div class="timeline-image flex-50 wow fadeInUp">
                            <div class="img-sec">
                                <figure class="feature-image">
                                    <img  class="" src="/wp-content/uploads/2018/05/01_history.jpg" alt="<?php the_title(); ?>" />
                                </figure>
                                                               
                                
                                <figure class="seconday-image">
                                    <img  class="" src="<?php bloginfo('template_directory'); ?>/images/placeholder_500X350.png" alt="<?php the_title(); ?>" />
                                </figure>
                            </div>    
                        </div>
                        <div class=" timeline-content flex-50 wow fadeInUp">
                            <div class="timeline-heading">
                                <div class="timeline-title">The Beginning</div>
                                <div class="timeline-year">1950</div>
                            </div>
                            <div class="timeline-desc">
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type</p>
                            </div>
                        </div>
                       

                    </div>   
                      
                </div>
                
            </div>
        
      </div>
    </div>

    <?php get_footer(); ?>