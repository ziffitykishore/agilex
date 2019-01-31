<?php
/**
 * Template Name: Page Our History
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<div class="main-banner-wrap">
<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
$thumbnail_ID = get_post_thumbnail_id( get_the_ID() );
$alt_text = get_post_meta( $thumbnail_ID, '_wp_attachment_image_alt', true ); ?>      
    <div class="main-banner bg-image" data-src="<?php echo $featured_img_url; ?>">
        <?php if($featured_img_url){?>
        <img src="<?php bloginfo('template_directory'); ?>/images/blog-header.jpg" data-src="<?php echo $featured_img_url; ?>" class="lazy" alt="<?php echo $alt_text; ?>"/>
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



    <div class="timeline_inner margin-top--70 white-bg pad-tb-50 pad-30">
    <?php $args = array(
                'post_type' => 'our_history',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'order_by' => 'date',
                'order' => 'ASC'
                );
    $agilex_unique_query = new WP_Query($args); ?>
    <ul class="nav nav-tabs history-tab" role="tablist">
        <?php 
        $firstLoop = true;
        $counter = 1;
        while ($agilex_unique_query->have_posts()) {
        $agilex_unique_query->the_post(); ?>
        <li role="presentation" 
        <?php 
        
        if($firstLoop == true){
            echo 'class="current_page_item active"';
        }
        ?>
        >
            
        <?php if (get_field('year', get_the_ID())){ ?>
                                <a href="#history<?php echo $counter; ?>" role="tab" data-toggle="tab" class="timeline-year <?php if (get_field('from_year', get_the_ID())) { echo 'timeline-year-from'; } if (get_field('beyond', get_the_ID())) { echo 'timeline-year-beyond'; } ?> "><span class="current-year"><?php echo get_field('year'); ?></span> <?php if (get_field('from_year', get_the_ID())){ ?> - <span class="additional"><?php echo get_field('from_year');?></span>  <?php }?> <?php if (get_field('beyond', get_the_ID())){?> & <span class="additional"><?php echo get_field('beyond');?></span> <?php }?></a>
                            <?php } else { ?>
                                <div class="timeline-year">0000</div>
                            <?php } ?>
                <div class="hist-title"><?php echo the_Title(); ?></div>
            
        </li>
        <?php $counter++; ?>
        <?php $firstLoop = false; } ?>
    </ul>
    <div class="tab-content">
        <?php  $counter = 1; 
        $firstLoop2 = true;
        while ($agilex_unique_query->have_posts()) {
                $agilex_unique_query->the_post();
                $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
                $thumbnail_ID = get_post_thumbnail_id( get_the_ID() );
                   $alt_text = get_post_meta( $thumbnail_ID, '_wp_attachment_image_alt', true ); ?>
        <div id="history<?php echo $counter; ?>" role="tabpanel" class="timeline-blk tab-pane 
        <?php 
        
        if($firstLoop2 == true){
            echo ' active"';
        }
        ?>
        ">
        
        <div class="timeline-item flex-sec ">
        
                    <div class="timeline-image flex-xs-100 flex-50 ">
                        <div class="img-sec">
                            
                            <figure class="feature-image">
                            <?php if ( has_post_thumbnail() ) { ?>
                                    <img data-src="<?php echo $featured_img_url;?>" alt="<?php echo $alt_text;?>" class="lazy">
                                <?php } else { ?>
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
                                <?php $counter++; ?>
    <?php $firstLoop2 = false;  } ?>
    </div></div>
<?php /* Restore original Post Data */
wp_reset_postdata();?>
</div>





</section>



    <?php get_footer(); ?>

    <style>

#magic-line2 {     
    position: absolute;
    top: 40px;
    left: -14px;
    width: 100px;
    height: 0;
     background: transparent; 
     transform: translateX(0);
      transform-origin: left; 
      transition: transform 0.4s; 
      }
      .hist-title {
    margin-top: 50px;
    color: #174a79;
    transition-duration: 0.5s;
    opacity: 0;
    white-space: normal;
}
.nav-tabs {
    border-bottom: 0;
}
.nav-tabs li a:before {
content: '';
    background: url(/wp-content/themes/agilex/images/scale.jpg);
    width: 170px;
    height: 28px;
    position: absolute;
    left: -8px;
    top: 43px;
    background-size: cover;
    background-repeat: no-repeat;
}



#magic-line2:before {
    content: '';
    background: url(/wp-content/themes/agilex/images/scale-ho.jpg);
    width: 180px;
    height: 28px;
    position: absolute;
    left: 7px;
    top: 4px;
    background-size: cover;
    background-repeat: no-repeat;
}
.scrtabs-tabs-fixed-container {
    min-height: 125px;
}
.tab-content {
margin-top: 100px;
display: inline-block;
width: 100%;
}
.timeline-item.flex-sec {
    float: left;
}

.nav.nav-tabs>li { position: unset; margin-right: 2px;}
.nav > li > a:hover, .nav > li > a:focus{
    background: transparent;
}
.nav-tabs { margin: 0 auto; list-style: none; position: relative; display: flex; justify-content: center; cursor: pointer;}
.nav-tabs li { display: inline-block; min-width: 170px; max-width: 170px; text-align: center;}
.nav-tabs li a { color: #bbb; font-size: 14px; display: block; padding: 6px 10px 4px 10px; text-decoration: none; text-transform: uppercase; border-color: white;}
.nav-tabs li a:hover { color: white; }
.nav-tabs li:hover .hist-title { opacity: 1 }
.nav.nav-tabs>li.active>a, .nav.nav-tabs>li.active>a:hover, .nav.nav-tabs>li.active>a:focus {color: #174a79; font-weight: 600; border-color: transparent;}
.nav.nav-tabs>li.active .hist-title { opacity: 1 }

.timeline-section .scrtabs-tabs-fixed-container{
    margin: 0 auto;
    float: none;
    
}

.touch .timeline-section .scrtabs-tabs-fixed-container{
    overflow-x:auto;
}

.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-right,
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-left {
    border-width: 0;
    position: absolute;
}
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-right:hover,
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-left:hover {
    background: transparent;
}
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-right:hover:after,
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-left:hover:after {
    border-color: #174a79;
}
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-right:after,
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-left:after {
    content: '';
    border: 2px solid #ddd;
    width: 15px;
    height: 15px;
    border-left-width: 0;
    border-top-width: 0;
    position: absolute;
}
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-right:after{
    transform: rotate(-45deg);
}
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-left:after {
    transform: rotate(135deg);
}
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-right{
    right: 15px;
    top: 20px;
}
.scrtabs-tab-scroll-arrow.scrtabs-tab-scroll-arrow-left{
    left: 15px;
    top: 20px;
}
.scrtabs-tab-scroll-arrow .glyphicon {
    display: none;
}


.touch .scrtabs-tabs-fixed-container {

}

@media screen and (min-width: 1300px) {
.nav-tabs li.active:first-child ~ #magic-line2{
    transform: translateX(20px) !important;
}
}

@media screen and (max-width: 1024px) {
    .hist-title{
        font-size: 12px;
    }
}


</style>
