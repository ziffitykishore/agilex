<?php
/**
 * Template Name: Page Careers
 *
 * @package Agilex
 * @since Agilex 1.0
 */

  get_header(); ?>

    <div class="main-banner-wrap">
        <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
        <div class="main-banner">
            <?php if ($featured_img_url){ ?>
            <img src="<?php echo $featured_img_url; ?>" class="" alt="" />
            <?php } else  { ?>
            <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X450.png" class="" alt="" />
            <?php }?>
        </div>
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

<div class="careers-wrap">
  <div class="container">
    <div class="white-bg margin-top--70 pad-70 inner-section clearfix">
      <?php 
      // TO SHOW THE PAGE CONTENTS
      while ( have_posts() ) : the_post(); ?>
      <?php echo the_Content(); ?>
      <?php
      endwhile; //resetting the page loop
      wp_reset_query(); //resetting the page query
      ?>
      <div class="col-sm-6  col-md-5 careers-form-wrap ">
        <div class="careers-form pad-30">
          <div class="heading text-uppercase text-center">
            <div class="heading-title">Apply Today!</div>
          </div>
          <?php echo get_field('contact_form_7');  ?> ?>
        </div>
      </div>
      <div class="opening-wrap pad-tb-50 clear-both">
        <div class="container">
          <div class="opening-inner">
              <div class="heading text-center">
                <div class="heading-title text-uppercase">Current Openings</div>
                <div class="sub-heading">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</div>
              </div>
          </div>
          <div class="opening-list-sec">
            <div class="opening-list flex-sec ">
                <div class="flex-30 flex-sm-100 post-name-wrap">
                    <div class="post-name">Account Excutive</div>
                    <a href="#" class="btn btn-md btn-door btn-blue">Apply Now</a>
                </div>
                <div class="flex-70 flex-sm-100 job-desc">
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
                    </p>  
                </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>





      <div class="image-gallery row pad-tb-50">
        <div class="container">
      <?php 
    $image_1 = get_field('image_1');
    $image_2 = get_field('image_2');
    $image_3 = get_field('image_3');
    $image_4 = get_field('image_4');
    $image_5 = get_field('image_5');
 ?>
          <div class="col-sm-6 no-pad">
          <div class="col-sm-6 img-sec wow fadeIn ">
          
              <div class="img-diagonal alignnone"><img src="<?php echo $image_1['url']; ?>" alt="" class=" size-full wp-image-363" /></div>
          
          </div>
          <div class="col-sm-6 img-sec wow fadeIn ">
         <?php if($image_2) {?>
              <div class="img-diagonal alignnone"><img src="<?php echo $image_2['url']; ?>" alt="" class=" size-full wp-image-363" /></div>
              <?php } ?>
          </div>
          </div>
          <div class="col-sm-6 img-sec  no-padd wow fadeIn">
          <?php if($image_3) {?>
              <div class="img-diagonal alignnone"><img src="<?php echo $image_3['url']; ?>" alt="" class=" size-full wp-image-363" /></div>
              <?php } ?>
          </div>
          <div class="col-sm-8 img-sec wow fadeIn">
          <?php if($image_4) {?>
              <div class="img-diagonal alignnone"><img src="<?php echo $image_4['url']; ?>" alt="" class=" size-full wp-image-363" /></div>
              <?php } ?>
          </div>
          <div class="col-sm-4 img-sec wow fadeIn">
          <?php if($image_5) {?>
              <div class="img-diagonal alignnone"><img src="<?php echo $image_5['url']; ?>" alt="" class=" size-full wp-image-363" /></div>
              <?php } ?>
          </div>
          </div>
          </div>


 



    <?php get_footer(); ?>