<?php
/**
 * The template for the front page.
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header();
?>

<?php
if (is_front_page()) { ?>
    <section class="slider-homepage">
        <div id="hero-slider" class="dots-bar">
            <?php
            $slider_args = array(
                'post_type' => 'banner_slider',
                'posts_per_page' => -1,
                'post_status'     => 'publish',
                'order_by' => 'date',
                'order' => 'ASC'
            );
            $banner_slider_query = new WP_Query($slider_args); ?>
             <?php while ($banner_slider_query->have_posts()) {
                $banner_slider_query->the_post(); ?>
            <div class="slider-blk">
                <?php the_post_thumbnail('full'); ?>
                <div class="slider-content">
                    <div class="container">
                        <?php echo the_Content();  ?>
                    </div>
                </div>

            </div>
            <?php } ?>
        </div>
        <a class="scroll-down" href="#unique-section">&darr;</a>
        <div class="social-links-sec">
            <?php if (is_active_sidebar('footer-social-links')) : ?>
                <?php dynamic_sidebar('footer-social-links'); ?>
            <?php endif; ?>
        </div>
             </section>
    <?php /** Makes Agilex Unique Section -- Start **/ ?>
    <section class="tab-section uniques wow fadeInUp" id="unique-section" >
        <div class="container">
        <?php $args = array(
'name' => 'agilex-unique',
'post_status'     => 'publish'
); 
$unique_arg_query = new WP_Query($args);
while ($unique_arg_query->have_posts()) {
$unique_arg_query->the_post();  ?>
        <div class="heading">
                <div class="heading-title"><?php echo the_Title(); ?></div>
                <div class="sub-heading"><?php echo get_the_excerpt();?></div>
            </div>
<?php } ?>
            <div class="uniques-inner">
            <?php
            $args = array(
                'post_type' => 'makes_agilex_unique',
                'posts_per_page' => -1,
                'post_status'     => 'publish',
                'order_by' => 'date',
                'order' => 'ASC'
            );
            $agilex_unique_query = new WP_Query($args); $i = 0; ?>
            <ul class="nav nav-tabs responsive" id="myTabs" role="tablist">
                <?php while ($agilex_unique_query->have_posts()) {
                $agilex_unique_query->the_post(); ?>
                <li role="presentation" class="<?php if($i == 0) { echo "active"; } ?> ">
                    <a href="#<?php echo strtolower(str_replace(' ', '-', get_the_title())); ?>" class="text-uppercase btn-ripple" id="<?php echo strtolower(str_replace(' ', '-', get_the_title())); ?>-tab" role="tab" data-toggle="tab" aria-controls="<?php echo strtolower(str_replace(' ', '-', get_the_title())); ?>" aria-expanded="true">
                        <?php echo the_Title(); $i++; ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
            <div class="tab-content" id="myTabContent">
                <?php $i = 0; while ($agilex_unique_query->have_posts()) {
                        $agilex_unique_query->the_post(); ?>
                <div class="tab-pane fade <?php if($i == 0) { echo "active in"; } ?> " role="tabpanel" id="<?php echo strtolower(str_replace(' ', '-', get_the_title())); ?>" aria-labelledby="<?php echo strtolower(str_replace(' ', '-', get_the_title())); ?>-tab">
                    <div class="row">
                        <div class="col-sm-4 wow slideInLeft">
                            <div class="project">
                            <div class="img-sec project__card">
                                <?php //the_post_thumbnail('thumbnail'); ?>
                                <figure class="feature-image">
                                <?php if ( has_post_thumbnail() ) {
                                    the_post_thumbnail('full');
                                } else { ?>
                                    <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_370X480.png" alt="<?php the_title(); ?>" />
                                <?php } ?>
                                </figure>
                                <?php if (get_field('secondary_image', get_the_ID())){ ?>
                                    <?php $image = get_field('secondary_image'); ?>
                                    <img class="seconday-image" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
                                <?php } else { ?>
                                    <img  class="seconday-image" src="<?php bloginfo('template_directory'); ?>/images/placeholder_370X480.png" alt="<?php the_title(); ?>" />
                                    <?php } ?>
                            </div>
                                </div>
                        </div>
                        <div class="col-sm-8 content-wrap wow slideInRight">
                            <?php echo the_Content(); $i++; ?>
                            <?php if (get_field('learn_more_text')){ ?>
                <a href="<?php echo get_permalink() ?>" class=" btn btn-md btn-blue btn-ripple btn-door margin-top-40"><?php the_field('learn_more_text'); ?></a>
              <?php } else { ?>
                <a href="<?php echo get_permalink() ?>" class=" btn btn-md btn-blue btn-ripple btn-door margin-top-40">Learn More</a>
              <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </div></div>
    <?php /* Restore original Post Data */
        wp_reset_postdata();?>
        </div>
    </section>

    <?php
    /** Makes Agilex Unique Section -- End * */

    /** Who We Are Section -- Start **/ ?>
    <?php $who_args = array(
        'name' => 'who-we-are',
        'post_status'     => 'publish'
    );
    $who_we_are_query = new WP_Query($who_args);
    while ($who_we_are_query->have_posts()) {
        $who_we_are_query->the_post(); ?>
    <section class="who-we-are wow fadeInUp parallax-img-container">
    <div class="container">
    <div class="heading">
        <a href="<?php echo get_permalink() ?>">
            <div class="heading-title"><?php echo the_Title(); ?></div>
        </a>
        <div class="sub-heading"><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></div>
    </div>

    <div class="content-outer-wrap">
        <div class="inner-content-wrap">
            <div class="inner-content video-content-inner">
                <?php if (get_field('video_image', get_the_ID())){ ?>
                    <?php $image = get_field('video_image'); ?>
                <?php } ?>
                <a 
                    data-fancybox tabindex="0" 
                    href="
                    <?php if (get_field('video_link', get_the_ID())): ?>
                    <?php the_field('video_link', get_the_ID()); ?>
                    <?php endif; ?> " 
                    data-fancybox-type="iframe" 
                    class="video-content" 
                    style="background: url('<?php echo $image['url'];?> ') no-repeat center center; background-size: 100%;">
                    <span  class="btn-fancy" > 
                        <span class="play-icon-block">
                            <span class="fa fa-play"></span>
                        </span>
                    </span> 
                    <span class="text-content lined">Play Video</span>
                </a>
                <div class=" content-desc">
                <?php if (get_field('short_description', get_the_ID())){ 
                    the_field('short_description'); }
                    else {
                        echo '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>';
                    }?>

                    <?php if (get_field('learn_more_text')){ ?>
                        <a href="#" class="btn-more"><?php the_field('learn_more_text'); ?></a>
                    <?php } else { ?>
                        <a href="#" class="btn-more">Learn More</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?php 
    $top_left_image = get_field('top_left_image');
    $top_right_image = get_field('top_right_image');
    $bottom_left_image = get_field('bottom_left_image');
    $bottom_right_image = get_field('bottom_right_image');
 ?>
<img src="<?php echo $top_left_image['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="400" data-ps-horizontal-position="-50"/>
<img src="<?php echo $bottom_left_image['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="0" data-ps-horizontal-position="-80"/>
<img src="<?php echo $bottom_right_image['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="-20" data-ps-horizontal-position="75%"/>
<img src="<?php echo $top_right_image['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="380" data-ps-horizontal-position="85%"/>
      
    
    </section>
<?php  }
    /* Restore original Post Data */
    wp_reset_postdata();
    /** Who We Are Section -- End * */ ?>
    <section class="what-we-do">
        <div class="container">
        <?php $args = array(
            'name' => 'what-we-do',
            'post_status'     => 'publish'
            ); 
            $unique_arg_query = new WP_Query($args);
            while ($unique_arg_query->have_posts()) {
            $unique_arg_query->the_post();  ?>
                    <div class="heading text-center">
                            <div class="heading-title"><?php echo the_Title(); ?></div>
                            <div class="sub-heading"><?php echo get_the_excerpt();?></div>
                        </div>
            <?php } ?>
            <div class="grid-content row">
                <?php
                /** What We Do Section -- Start * */
                $whatArgs = array(
                    'post_type' => 'what_we_do',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'order_by' => 'date',
                    'order' => 'ASC'
                );
                $agilex_whatwe_query = new WP_Query($whatArgs);
                while ($agilex_whatwe_query->have_posts()) {
                    $agilex_whatwe_query->the_post();
                    $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
                    ?>
                    <div class="col-xs-6 col-sm-6 col-md-4  wow fadeInUp">
                        <div class="thumbnail">
                            <a href="<?php echo get_permalink() ?>" class="img-sec">
                            
                                <?php if (get_field('thumb_image', get_the_ID())){ ?>
                                    <?php $image = get_field('thumb_image'); ?>
                                    <img class="thumb-image" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
                                <?php } else { ?>
                                    <img  class="thumb-image" src="<?php bloginfo('template_directory'); ?>/images/placeholder_370X480.png" alt="<?php the_title(); ?>" />
                                    <?php } ?>
                                </a>
                            <div class="caption">
                                <a href="<?php echo get_permalink() ?>">
                                    <h4><?php echo the_Title(); ?></h4></a>
                                    <div class="content-desc">
                                     <?php if (get_field('short_description', get_the_ID())){ 
                                        
                                        echo wp_trim_words( get_field('short_description'), 15, ' ' );
                                     } else {

                                        echo wp_trim_words( get_the_content(), 15, ' ' );
                                     } ?>
                                         
                                        
                                    </div>
                                
                             
                                
                            </div>
                        </div>
                    </div>
                <?php }
                /* Restore original Post Data */
                wp_reset_postdata();
                ?>
            </div>
        </div>
                </section>
<?php  /** What We Do Section -- End * */ ?>
    <section class="testimonials wow fadeInUp">
        <div class="container">
        <?php $args = array(
            'name' => 'testimonials',
            'post_status'     => 'publish'
            ); 
            $unique_arg_query = new WP_Query($args);
            while ($unique_arg_query->have_posts()) {
            $unique_arg_query->the_post();  ?>
                    <div class="heading text-center">
                            <div class="heading-title"><?php echo the_Title(); ?></div>
                            <div class="sub-heading"><?php echo get_the_excerpt();?></div>
                        </div>
            <?php } ?>
            <div class="testimonials-outer">
                <div class="reviews-inner dots-bar">
                    
                        <?php
                        /** Testimonial Section -- Start * */
                        $testi_args = array(
                            'post_type' => 'testimonial',
                            'posts_per_page' => -1,
                            'post_status' => 'publish'
                        );
                        $agilex_test_query = new WP_Query($testi_args);
                        while ($agilex_test_query->have_posts()) {
                            $agilex_test_query->the_post();
                            $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
                            /* link thumbnail to full size image for use with lightbox */
                            ?>
                            
                            <div class="reviews-blk">
                                <div class="review-details text-center col-sm-10 col-sm-offset-1">
                                    
                                    <div class="review-content ">
                                        <?php echo the_Content(); ?>
                                    </div>
                                    <div class="author-details">
                                        <div class="author-name">
                                            <strong>
                                                <?php if (get_field('name_testimonial', get_the_ID())): ?>
                                                <?php the_field('name_testimonial', get_the_ID()); ?>
                                                <?php endif; ?>
                                            </strong>
                                        </div>
                                        <div class="author-pos">
                                            <?php if (get_field('position', get_the_ID())): ?>
                                                <?php the_field('position', get_the_ID()); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                </div>
                        <?php }
                   
                        /* Restore original Post Data */
                        wp_reset_postdata(); ?>
                    
                </div>
            </div>
            
        </div>
    </section>


    
    

  
    <?php  /** Testimonial Section -- End * */ ?>
<?php } get_footer(); ?>