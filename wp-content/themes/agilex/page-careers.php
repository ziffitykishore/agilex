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
         <?php

        $postArg = array('post_type' => 'jobpost');
        $jobPosts = get_posts( $postArg );
	$countPosts = count($jobPosts);
	if ($countPosts > 0) { ?>
    <select id="job-post-list" class="job-list">
        <?php foreach ( $jobPosts as $jobPost ) {
                //setup_postdata($jobPost);
                echo '<option class="post-id" value='.$jobPost->ID. '>'.$jobPost->post_title.'</option>';
            }
        } ?>
    </select>
<div id="update"><?php
    $general_post_args = array('post_type' => 'jobpost', 'name' => 'general-post');
    $general_post = get_posts( $general_post_args );
    foreach ( $general_post as $post ):
        setup_postdata($post);
        echo  sjb_job_listing_application_form($post);
    endforeach; ?>
 </div>
      <div class="col-sm-6  col-md-5 careers-form-wrap ">
        <div class="careers-form pad-30">
          <div class="heading text-uppercase text-center">
            <div class="heading-title">Apply Today!</div>
          </div>

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
            <?php
                $post_args = array('post_type' => 'jobpost',
                    'post_status'  => 'publish',
                    'orderby'  => 'date',
                    'order'   => 'DESC');
                $job_lists = get_posts( $post_args );
                $count_posts = count($job_lists);
                if($count_posts>0) {
                foreach ( $job_lists as $job_list ):
                    setup_postdata($job_list);
                if ($job_list->post_name != 'general-post') { ?>

          <div class="opening-list-sec">
            <div class="opening-list flex-sec ">
                <div class="flex-30 flex-sm-100 post-name-wrap">
                    <div class="post-name"><?php echo $job_list->post_title; ?></div>
                    <a href="#" class="btn btn-md btn-door btn-blue app-button"><?php echo __('Apply Now'); ?></a>
                </div>
                <div class="flex-70 flex-sm-100 job-desc">
                    <p><?php echo $job_list->post_content; ?></p>
                </div>
            </div>

                </div><?php } endforeach; }  ?>
        </div>
      </div>
    </div>
  </div>
</div>





      <!-- <div class="image-gallery row pad-tb-50">
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
          </div> -->





<?php $tabdetails = get_post_meta( get_the_ID(), 'img-gallery', true ); ?>

  <div class="image-gallery  pad-tb-50">
        <div class="container">
            <div class="gallery-inner flex-sec">
      <?php $i = 1;   foreach( $tabdetails  as $tabdetail){ ?>
    
       
         
            
           
              <div class="image-sec">
              <?php if($tabdetail["img-show"]) { ?>
              <img src="<?php echo wp_get_attachment_url($tabdetail["img-show"]); ?>" alt=""/>
              <?php }?>
              </div>
           
            
         
       
      
        <?php  $i++; } ?> 
              </div>
   
              </div></div>





    <?php get_footer(); ?>