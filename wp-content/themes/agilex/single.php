<?php
/**
 * Template for displaying all single posts
 *
 * @package Bootstrap Canvas WP
 * @since Bootstrap Canvas WP 1.0
 */

  get_header(); ?>
  
<div class="main-banner-wrap">
  <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
    <div class="main-banner">
      <?php if ($featured_img_url){ ?>
        <img src="<?php echo $featured_img_url; ?>" class="" alt=""/>
      <?php } else  { ?>
        <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X450.png" class="" alt=""/>
      <?php }?>
    </div>
    <div class="page-header-content">
      <div class="container">
        <h1><?php echo the_Title(); ?></h1>
        <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
      </div>     
        <div class="blog-detail-wrap">
          <div class="blog-detail text-uppercase">
            <div class="blog-author">By <?php  the_author_meta( 'display_name', $postData[0]->post_author ) ?></div>
            <div class="blog-year"><?php the_time('j F Y') ?></div>
          </div>
          <div class="blog-extras">
              <?php echo do_shortcode('[wishlist-feed]'); ?>
              <span class="comment-sec extras-link"><i class="fa fa-comment-o"></i> <span class="comments-count"> <?php
              echo get_comments_number();
              ?></span></span>
              <span  class="social_sharing extras-link"><i class="fa fa-share-alt"></i>
                  <div class="social_sharing-content" ><?php echo do_shortcode('[wp_social_sharing]'); ?></div>
              </span>
          </div>
      </div>
    </div>
</div>



      
      <? echo get_avatar( get_the_author_meta('user_email'), $size = '50'); ?>
<div class="single-page-wrap">
  <div class="container">
    <div class="single-content-outer col-sm-12 col-md-8 col-md-offset-2 margin-top--70 white-bg">
      <div class="single-content-inner pad-70 ">
        <?php get_template_part( 'loop-single', 'single' ); ?>
      </div>
      <?php previous_post_link('&laquo; &laquo; %', 'Previous', 'no'); ?> | <?php next_post_link('% &raquo; &raquo; ', 'Next', 'no'); ?>

    </div>
  </div>
</div>

<?php //get_sidebar(); ?>

      
	<?php get_footer(); ?>