<?php
/**
 * Template for displaying all single posts
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<div class="main-who-we">
<?php echo the_Title(); ?>
<?php echo "test"; ?>
<?php 
if ( have_posts() ) : while ( have_posts() ) : the_post();
  the_content();
endwhile;
else: ?>
  <p><?php _e('Sorry, no posts matched your criteria.'); ?></p><?php 
endif;?>
</div>

<?php get_footer(); ?>
