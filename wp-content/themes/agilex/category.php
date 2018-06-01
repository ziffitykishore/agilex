<?php
/**
 * Template for displaying Category Archive pages
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

      <div class="row">

        <div class="col-sm-8 blog-main">

          <h1><?php printf( __( 'Category Archives: %s', 'bootstrapcanvaswp' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?></h1>
		  <hr />
		  <?php get_template_part( 'loop', 'category' ); ?>

        </div><!-- /.blog-main -->

        <?php get_sidebar(); ?>

      </div><!-- /.row -->
      
	<?php get_footer(); ?>