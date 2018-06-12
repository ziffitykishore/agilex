<?php
/**
 * Template for displaying 404 pages (Not Found)
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
    </div>
</div>

<div class="err-page-wrap pad-30">
      <div class="container">

        <div class="col-sm-12 blog-main">

          <h2 class="center"><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'bootstrapcanvaswp' ); ?></h2>
          <p class="center">
          <?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'bootstrapcanvaswp' ); ?></p>
		      <div class="blog-search box-shadow margin-bottom-30 margin-top-40"><?php get_search_form(); ?></div>

        </div><!-- /.blog-main -->

        <?php //get_sidebar(); ?>
      </div>
      </div>
      
	<?php get_footer(); ?>