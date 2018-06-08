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



      
      <? echo get_avatar( get_the_author_meta('user_email'), $size = '50'); ?>
<div class="single-page-wrap">
  <div class="container">
    <div class="single-content-outer col-sm-12 col-md-8 col-md-offset-2 margin-top--70 white-bg">
      <div class="single-content-inner pad-70 ">
        <?php get_template_part( 'loop', 'single' ); ?>
      </div>
    </div>
  </div>
</div>

<div class="pagination">
    <?php //previous_post_link(); ?>    <?php //next_post_link(); ?>
    <?php
    $post_id = $post->ID; // Get current post ID
    $cat = get_the_category(); 
    $current_cat_id = $cat[0]->cat_ID; // Get current Category ID 
    $args = array('category'=>$current_cat_id,'orderby'=>'post_date','order'=> 'DESC');
    $posts = get_posts($args);
    // Get IDs of posts retrieved by get_posts function
    $ids = array();
    foreach ($posts as $thepost) {
    $ids[] = $thepost->ID;
    }
    // Get and Echo the Previous and Next post link within same Category
    $index = array_search($post->ID, $ids);
    $prev_post = $ids[$index-1];
    $next_post = $ids[$index+1];
    ?>
    <?php if (!empty($prev_post)){ ?> <a class="previous-post" rel="prev" href="<?php echo get_permalink($prev_post) ?>"> <span class="meta-icon"><i class="fa fa-angle-left fa-lg"></i></span> Previous</a> <?php } ?>
    <a href="<?php echo get_category_link($cat[0]->cat_ID); ?>"><?php $cat[0]->cat_name; ?></a>

    <?php if (!empty($next_post)){ ?> <a class="next-post" rel="next" href="<?php echo get_permalink($next_post) ?>">Next <span class="meta-icon"><i class="fa fa-angle-right fa-lg"></i></span> </a> <?php } ?>
</div>

<?php //get_sidebar(); ?>

      
	<?php get_footer(); ?>