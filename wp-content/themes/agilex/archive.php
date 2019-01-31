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



<div class="blog-wrap">
      <div class="container">

       <!--    <h1>
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
		  </h1> -->
        
		  <?php get_template_part( 'loop_archive', 'archive' ); ?>

				
      </div><!-- /.row -->

	<?php get_footer(); ?>