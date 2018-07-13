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
                <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
            <div class="slider-blk <?php if(!$featured_img_url){ echo 'slider-overlay'; }?>">
                
               <?php if($featured_img_url){     
                   $thumbnail_ID = get_post_thumbnail_id( get_the_ID() );
                   $alt_text = get_post_meta( $thumbnail_ID, '_wp_attachment_image_alt', true );  ?>
                    <img src="<?php echo $featured_img_url; ?>" alt="<?php echo $alt_text; ?>"/>
                <?php } else {?>
                    <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X930.png" alt="Agilex Fragrances"/>
                <?php }?>
                <?php //the_post_thumbnail('full'); ?>
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
    <section class="tab-section uniques wow fadeInUp parallax-img-container" id="unique-section" >
    <?php $args = array(
'name' => 'what-makes-agilex-unique',
'post_type' => 'page',
'post_status'     => 'publish'
); 
$unique_arg_query = new WP_Query($args);
while ($unique_arg_query->have_posts()) {
$unique_arg_query->the_post();  ?>
        <?php 
    $parallax_image_1 = get_field('parallax_image_1');
    $parallax_image_1_x = get_field('parallax_image_1_x');
    $parallax_image_1_y = get_field('parallax_image_1_y');
    $parallax_image_2 = get_field('parallax_image_2');
    $parallax_image_2_x = get_field('parallax_image_2_x');
    $parallax_image_2_y = get_field('parallax_image_2_y');
 ?>

  <?php if($parallax_image_1) {?>
<img src="<?php echo $parallax_image_1['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="<?php echo $parallax_image_1_y; ?>" data-ps-horizontal-position="<?php echo $parallax_image_1_x; ?>"/>
  <?php } ?>
  <?php if($parallax_image_2) {?>
<img src="<?php echo $parallax_image_2['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="<?php echo $parallax_image_2_y; ?>" data-ps-horizontal-position="<?php echo $parallax_image_2_x; ?>"/>
<?php } ?>
        <div class="container">

        <div class="heading">
        <h2 class="heading-title"><?php echo the_Title(); ?></h2>
                <div class="sub-heading"><?php echo wp_trim_words( get_the_content(), 100, '' ); ?></div>
            </div>

            <div class="uniques-inner">
            <?php
            $args = array(
                'post_type' => 'makes_agilex_unique',
                'posts_per_page' => -1,
                'post_status'     => 'publish',
                'order_by' => 'date',
                'order' => 'ASC'
            );
            $agilex_unique_query = new WP_Query($args); $i = 1; ?>
            <ul class="nav nav-tabs responsive" id="myTabs" role="tablist">
                <?php while ($agilex_unique_query->have_posts()) {
                $agilex_unique_query->the_post(); ?>
                <li role="presentation" class="<?php if($i == 1) { echo "active"; } ?> ">
                    <a href="#tab-<?php echo $i; ?>" class="text-uppercase btn-ripple" id="tab-link_<?php echo $i; ?>" role="tab" rel="<?php echo the_field('tab_color'); ?>" data-toggle="tab" aria-controls="<?php echo strtolower(str_replace('', '-', get_the_title())); ?>" aria-expanded="true">
                    <h3 class="tab-title"><?php echo the_Title(); $i++; ?></h3>
                    </a>
                
                </li>
                <?php } ?>
            </ul>
            <div class="tab-content" id="myTabContent">
                <?php $i = 1; while ($agilex_unique_query->have_posts()) {
                        $agilex_unique_query->the_post(); ?>
                <div class="tab-pane fade <?php if($i == 1) { echo "active in"; } ?> tab-pane_<?php echo $i; ?>" role="tabpanel" id="tab-<?php echo $i; ?>" aria-labelledby="<?php echo strtolower(str_replace('', '-', get_the_title())); ?>-tab">
                    <div class="row">
                        <div class="col-sm-6 wow slideInLeft">
                            <div class="project">
                            <div class="img-sec project__card">
                                <?php //the_post_thumbnail('thumbnail'); ?>
                                <figure class="feature-image">
                                <?php if (get_field('primary_image', get_the_ID())){ ?>
                                    <?php $image = get_field('primary_image'); ?>
                                    <img class="primary-image" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
                                <?php }  ?>
                                </figure>
                                
                            </div>
                                </div>
                        </div>
                        <div class="col-sm-6 content-wrap wow slideInRight">
                            

                            <?php if (get_field('short_description', get_the_ID())){  ?>
                                <div class="short-desc">
                    <?php the_field('short_description'); ?>
                            </div> <?php }?>
                            
                            <?php if (get_field('learn_more_text')){ ?>
                <a href="<?php echo get_permalink() ?>" class=" btn btn-md btn-blue btn-ripple btn-door margin-top-40 text-uppercase btn-tab_<?php echo $i; ?>" rel="<?php echo the_field('tab_color'); ?>"><?php the_field('learn_more_text'); ?></a>
              <?php } ?>
                        </div>
                    </div>
                </div>
            <?php  $i++; } ?>
            </div></div>
    <?php /* Restore original Post Data */
        wp_reset_postdata();?>
        </div>




      <?php } ?>
    </section>

    <?php
    /** Makes Agilex Unique Section -- End * */

    /** Who We Are Section -- Start **/ ?>
    <?php $who_args = array(
        'name' => 'who-we-are',
        'post_type' => 'page',
        'post_status'     => 'publish'
    );
    $who_we_are_query = new WP_Query($who_args);
    while ($who_we_are_query->have_posts()) {
        $who_we_are_query->the_post(); ?>
    <section class="who-we-are wow fadeInUp parallax-img-container">

            <?php 
    $parallax_image_1 = get_field('parallax_image_1');
    $parallax_image_1_x = get_field('parallax_image_1_x');
    $parallax_image_1_y = get_field('parallax_image_1_y');
    $parallax_image_2 = get_field('parallax_image_2');
    $parallax_image_2_x = get_field('parallax_image_2_x');
    $parallax_image_2_y = get_field('parallax_image_2_y');
 ?>

  <?php if($parallax_image_1) {?>
<img src="<?php echo $parallax_image_1['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="<?php echo $parallax_image_1_y; ?>" data-ps-horizontal-position="<?php echo $parallax_image_1_x; ?>"/>
  <?php } ?>
  <?php if($parallax_image_2) {?>
<img src="<?php echo $parallax_image_2['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="<?php echo $parallax_image_2_y; ?>" data-ps-horizontal-position="<?php echo $parallax_image_2_x; ?>"/>
<?php } ?>


    <div class="container">
    <div class="heading">
        <a href="<?php echo get_permalink() ?>">
            <h2 class="heading-title"><?php echo the_Title(); ?></h2>
        </a>
        <?php if(get_the_excerpt()) {?>
        <div class="sub-heading"><?php echo get_the_excerpt(); ?></div>
    <?php } ?>
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
                    style="background: url('<?php echo $image['url'];?> ') no-repeat center center; background-size: cover;">
                    <span  class="btn-fancy" > 
                        <span class="play-icon-block">
                            <span class="fa fa-play"></span>
                        </span>
                    </span> 
                    <?php if (get_field('play_video')){ ?>
                      <span class="text-content lined"><?php the_field('play_video'); ?></span>
                    <?php } else { ?>
                      <span class="text-content lined">Play Video</span>
                    <?php } ?> 
                </a>
                <div class=" content-desc">
                <?php if (get_field('short_description', get_the_ID())){ 
                    the_field('short_description');  }  ?>

                    <?php if (get_field('learn_more_text')){ ?>
                        <a href="<?php echo get_permalink() ?>" class="btn-more btn btn-blue btn-door btn-md"><?php the_field('learn_more_text'); ?></a>
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

  <?php if($top_left_image) {?>
<img src="<?php echo $top_left_image['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="360" data-ps-horizontal-position="0"/>
  <?php } ?>
  <?php if($bottom_left_image) {?>
<img src="<?php echo $bottom_left_image['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="0" data-ps-horizontal-position="0"/>
<?php } ?>
<?php if($top_right_image) {?>
<img src="<?php echo $bottom_right_image['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="50" data-ps-horizontal-position="75%"/>
<?php } ?>
<?php if($bottom_right_image) {?>
<img src="<?php echo $top_right_image['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="380" data-ps-horizontal-position="85%"/>
<?php } ?>
    
    </section>
<?php  }
    /* Restore original Post Data */
    wp_reset_postdata();
    /** Who We Are Section -- End * */ ?>
    <section class="what-we-do  wow fadeIn parallax-img-container">
    <?php $args = array(
            'name' => 'what-we-do',
            'post_type' => 'page',
            'post_status'     => 'publish'
            ); 
            $unique_arg_query = new WP_Query($args);
            while ($unique_arg_query->have_posts()) {
            $unique_arg_query->the_post();  ?>

      <?php 
    $parallax_image_1 = get_field('parallax_image_1');
    $parallax_image_1_x = get_field('parallax_image_1_x');
    $parallax_image_1_y = get_field('parallax_image_1_y');
    $parallax_image_2 = get_field('parallax_image_2');
    $parallax_image_2_x = get_field('parallax_image_2_x');
    $parallax_image_2_y = get_field('parallax_image_2_y');
 ?>

  <?php if($parallax_image_1) {?>
<img src="<?php echo $parallax_image_1['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="<?php echo $parallax_image_1_y; ?>" data-ps-horizontal-position="<?php echo $parallax_image_1_x; ?>"/>
  <?php } ?>
  <?php if($parallax_image_2) {?>
<img src="<?php echo $parallax_image_2['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="<?php echo $parallax_image_2_y; ?>" data-ps-horizontal-position="<?php echo $parallax_image_2_x; ?>"/>
<?php } ?>

        <div class="container">
       
                    <div class="heading text-center">
                            <a href="<?php echo get_permalink() ?>"><h2 class="heading-title"><?php echo the_Title(); ?></h2></a>
                            <div class="sub-heading"><?php echo wp_trim_words( get_the_content(), 100, '' ); ?></div>
                        </div>
          
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
                $delay = 0;
                while ($agilex_whatwe_query->have_posts()) { 
                    $agilex_whatwe_query->the_post();
                    $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
                    ?>
                    <div class="col-xs-6 col-sm-6 col-md-4  wow fadeInUp" data-wow-delay="<?php echo $delay; ?>s" >
                        <div class="thumbnail">
                            <a href="<?php echo get_permalink() ?>">
                                <div class="img-sec">
                                    <?php if (get_field('thumb_image', get_the_ID())){ ?>
                                        <?php $image = get_field('thumb_image'); ?>
                                        <img class="thumb-image" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
                                    <?php } else { ?>
                                        <img  class="thumb-image" src="<?php bloginfo('template_directory'); ?>/images/placeholder_370X250.png" alt="<?php the_title(); ?>" />
                                        <?php } ?>
                                </div>
                                <div class="caption">
                                    <h3 class="title"><?php echo the_Title(); ?></h3>
                                    <div class="content-desc">
                                        <?php if (get_field('short_description', get_the_ID())){ 
                                        echo wp_trim_words( get_field('short_description'), 15, '...' );
                                        } else {
                                        echo wp_trim_words( get_the_content(), 15, '...' );
                                        } ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php $delay+=0.2;}
                /* Restore original Post Data */
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php } ?>
                </section>





<?php  /** What We Do Section -- End * */ ?>

 <?php $args = array(
            'name' => 'testimonials',
            'post_status'     => 'publish'
            ); 
            $unique_arg_query = new WP_Query($args);
            while ($unique_arg_query->have_posts()) {
            $unique_arg_query->the_post();  ?>
    <section class="testimonials wow fadeIn parallax-img-container">

    

             <?php 
    $parallax_image_1 = get_field('parallax_image_1');
    $parallax_image_1_x = get_field('parallax_image_1_x');
    $parallax_image_1_y = get_field('parallax_image_1_y');
    $parallax_image_2 = get_field('parallax_image_2');
    $parallax_image_2_x = get_field('parallax_image_2_x');
    $parallax_image_2_y = get_field('parallax_image_2_y');
 ?>

  <?php if($parallax_image_1) {?>
<img src="<?php echo $parallax_image_1['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="<?php echo $parallax_image_1_y; ?>" data-ps-horizontal-position="<?php echo $parallax_image_1_x; ?>"/>
  <?php } ?>
  <?php if($parallax_image_2) {?>
<img src="<?php echo $parallax_image_2['url']; ?>" alt="" class="parallax-move" data-ps-z-index="200" data-ps-vertical-position="<?php echo $parallax_image_2_y; ?>" data-ps-horizontal-position="<?php echo $parallax_image_2_x; ?>"/>
<?php } ?>


        <div class="container">
       
                    <div class="heading text-center">
                            <h2 class="heading-title"><?php echo the_Title(); ?></h2>
                            <div class="sub-heading"><?php echo get_the_excerpt();?></div>
                        </div>
      
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
                                        <span class="quotes"></span>
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
    <?php } ?>

    
    

  
    <?php  /** Testimonial Section -- End * */ ?>
<?php } get_footer(); ?>