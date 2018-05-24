<?php
/**
 * Template Name: Page What We Do
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
<div class="main-banner" style="background: url('<?php echo $featured_img_url; ?>') no-repeat center center; background-size: cover;">
        <img src="<?php echo $featured_img_url; ?>" class="hidden" alt=""/>
        <div class="page-header-content">
       <div class="container">
         <h1><?php echo the_Title(); ?></h1>
         <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
        </div>     
      </div>
 </div>

<div class="what-we-do-wrap " id="what-we-do">
  <div class="container">
    <div class="page-desc margin-top--70 wow fadeInUp">
    <?php
    // TO SHOW THE PAGE CONTENTS
    while ( have_posts() ) : the_post(); ?>
    <?php echo get_the_content(); ?>
    <?php
    endwhile; //resetting the page loop
    wp_reset_query(); //resetting the page query
    ?>
    </div>   
  </div>
  </div> 

<?php if (get_field('tab_content', get_the_ID())): ?>
      <?php the_field('tab_content', get_the_ID()); ?>
  <?php endif; ?>

<div class="related-categories-wrap wow fadeInUp">
  <div class="container">
  <div class="related-categories-innner flex-sec">
<?php $exclude_post = $post->ID;
$query = new WP_Query( array( 'post_type' => 'what_we_do', 'order_by' => 'date', 'order' => 'ASC', 'post__not_in' => array( $exclude_post ) ) ); 
$query->have_posts();
while ( $query->have_posts() ) { $query->the_post(); ?>
  <div class="category-blk flex-xs-100 flex-sm-30 flex-md-30 flex-20">
  <a href="<?php echo get_permalink() ?>" class="img-sec">
    <figure>
  <?php if (get_field('thumb_image', get_the_ID())){ ?>
      <?php $image = get_field('thumb_image'); ?>
      <img class="thumb-image" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
  <?php } else { ?>
      <img  class="thumb-image" src="<?php bloginfo('template_directory'); ?>/images/placeholder_370X480.png" alt="<?php the_title(); ?>" />
      <?php } ?>
      <figcaption><div class="category-title"><?php echo  get_the_title(); ?></div></figcaption>
  </figure>
  </a>
  </div>
<?php } ?>
  </div>
  </div>
</div>
	  
                                                                                                                                                                                                                            
<?php get_footer(); ?>                                                                        