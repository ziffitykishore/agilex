<?php
/**
 * Template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Bootstrap Canvas WP
 * @since Bootstrap Canvas WP 1.0
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

      <div class="row">

        <div class="col-sm-8 blog-main">

          <?php get_template_part( 'loop', 'page' ); ?>

        </div><!-- /.blog-main -->

        <?php get_sidebar(); ?>

      </div><!-- /.row -->
      
	<?php get_footer(); ?>