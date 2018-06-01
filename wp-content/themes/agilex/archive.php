<?php
/**
 * Template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
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
            <h1><?php if ( is_day() ) : 
		      printf( __( 'Daily Archives: <span>%s</span>', 'bootstrapcanvaswp' ), get_the_date() ); 
		    elseif ( is_month() ) : 
			  printf( __( 'Monthly Archives: <span>%s</span>', 'bootstrapcanvaswp' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'bootstrapcanvaswp' ) ) ); 
			elseif ( is_year() ) : 
			  printf( __( 'Yearly Archives: <span>%s</span>', 'bootstrapcanvaswp' ), get_the_date( _x( 'Y', 'yearly archives date format', 'bootstrapcanvaswp' ) ) );
			else : 
			  _e( 'Blog Archives', 'bootstrapcanvaswp' ); 
			endif; 
		  ?></h1>
            <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
        </div>     
    </div>
</div>

      <div class="row">

        <div class="col-sm-8 blog-main">

          <h1>
		  <?php 
		    if ( is_day() ) : 
		      printf( __( 'Daily Archives: <span>%s</span>', 'bootstrapcanvaswp' ), get_the_date() ); 
		    elseif ( is_month() ) : 
			  printf( __( 'Monthly Archives: <span>%s</span>', 'bootstrapcanvaswp' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'bootstrapcanvaswp' ) ) ); 
			elseif ( is_year() ) : 
			  printf( __( 'Yearly Archives: <span>%s</span>', 'bootstrapcanvaswp' ), get_the_date( _x( 'Y', 'yearly archives date format', 'bootstrapcanvaswp' ) ) );
			else : 
			  _e( 'Blog Archives', 'bootstrapcanvaswp' ); 
			endif; 
		  ?>
		  </h1>
          <hr /> 
		  <?php get_template_part( 'loop', 'archive' ); ?>

        </div><!-- /.blog-main -->

        <?php get_sidebar(); ?>

      </div><!-- /.row -->
      
	<?php get_footer(); ?>