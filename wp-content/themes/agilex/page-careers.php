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
          <?php

$postArg = array('post_type' => 'jobpost');
$jobPosts = get_posts( $postArg );
$countPosts = count($jobPosts);
if ($countPosts > 0) { ?>
<div class="select-box">
<select id="job-post-list" class="job-list" name="job-post-list">
    <option value>Apply For</option>
<?php foreach ( $jobPosts as $jobPost ) {
        //setup_postdata($jobPost);
        echo '<option class="post-id" value='.$jobPost->ID. '>'.$jobPost->post_title.'</option>';
    }
} ?>
</select>
</div>
<div id="update"><?php
$general_post_args = array('post_type' => 'jobpost', 'name' => 'general-post');
$general_post = get_posts( $general_post_args );
foreach ( $general_post as $post ):
setup_postdata($post);
echo  sjb_job_listing_application_form($post);
endforeach; ?>
</div>
        </div>
      </div>
      <div class="opening-wrap pad-tb-50 clear-both">
        <div class="container">
          <div class="opening-inner">
              <div class="heading text-center">
                <div class="heading-title text-uppercase">Current Openings</div>
                
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
                    //var_dump($job_list);
                if ($job_list->post_name != 'general-post') { ?>

          <div class="opening-list-sec">
            <div class="opening-list flex-sec ">
                <div class="flex-30 flex-sm-100 post-name-wrap">
                    <div class="post-name"><?php echo $job_list->post_title; ?></div>

                    <a href="#" class="btn btn-md btn-door btn-blue btn-job app-button" data-val="<?php echo $job_list->ID; ?>"><?php echo __('Apply Now'); ?></a>
                    <input type="hidden" name="job-id" value="<?php echo $job_list->ID; ?>">

                </div>
                <div class="flex-70 flex-sm-100 job-desc">
                    <p><?php echo $job_list->post_content; ?></p>
                </div>
            </div>

                </div><?php } endforeach; }  ?>
            <?php wp_reset_postdata(); ?>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- <div class="image-gallery  pad-tb-50">
    <div class="container">
        <div class="gallery-inner flex-sec">
            <?php $tabdetails_carrers = get_post_meta( get_the_ID(), 'img-show', true ); ?>
            <?php foreach( $tabdetails_carrers  as $tabdetails_carrer){ ?>
                <div class="image-sec">
                    <img src="<?php echo wp_get_attachment_url($tabdetails_carrer["image-thumbnail"]); ?>" />
                </div>
            <?php } ?>

        </div>
    </div>
</div> -->

<script>
    jQuery(function($){
        var currentValue = $('.nice-select .current').text();

        $('.jobpost-form').submit(function(){
            if($('#job-post-list').val() == ''){
                $('<span class="not-valid">Please select job post</span>').appendTo('.select-box');
            }    
        });

        $(".jobpost-form .btn").on('click', function () {
        if ($('#job-post-list').val() == '') {
            $('select').niceSelect('update');
            //alert("Please select the job");
            $('<span class="not-valid">Please select job post</span>').appendTo('.select-box');
        }
    });

        $(window).on('load', function(){
       /*  $('.nice-select .list').prepend('<li data-value class="label-content">Apply for</li>');

            var noOption = $('.label-content').text();
            if( currentValue !== noOption){
            $('.nice-select .current').text(noOption);
            } */
        });




        function scrollPostion(){
        var  careerForm = $('.careers-form').offset().top;
        header = $('.header-container').height();
            $('html, body').animate({
                scrollTop: careerForm - header
            }, 800);

    }
        $('.btn-job').click(function() {
            scrollPostion();
            $('#job-post-list').val($(this).data('val')).trigger('change');
            $('select').niceSelect('update');
        });
    });
</script>
    <?php get_footer(); ?>