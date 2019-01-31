<?php
/**
 * Template for displaying all single posts
 *
 * @package Bootstrap Canvas WP
 * @since Bootstrap Canvas WP 1.0
 */

  //get_header(); ?>
<?php $postDataValue = get_post(); ?>
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
        <?php $title = the_Title(); 
        if (str_word_count($title) >= 10) {
          $keys = array_keys(str_word_count($title, 2));
          $title = substr($title, 0, $keys[$len]); ?>
          <h1 class="length">asdsadasd</h1>  
          <?php } else { ?>    
         
        <?php }?>
        <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>
      </div>

    </div>
    <div class="blog-detail-wrap">
          <div class="blog-detail text-uppercase">
              <div class="blog-author">By <?php  the_author_meta( 'display_name', $postDataValue->post_author ) ?></div>
            <div class="blog-year"><?php the_time('j M Y') ?></div>
          </div>
          <div class="blog-extras">
              <?php //echo do_shortcode('[wishlist-feed]'); ?>
              <span class="comment-sec extras-link"><i class="fa fa-comment-o"></i> <span class="comments-count"> <?php
              echo get_comments_number();
              ?></span></span>
              <span  class="social_sharing extras-link"><i class="fa fa-share-alt"></i>
                  <div class="social_sharing-content" ><?php echo do_shortcode('[wp_social_sharing]'); ?></div>
              </span>
          </div>
      </div>
</div>

<div class="single-page-wrap">
  <div class="container">
    <div class="single-content-outer col-sm-12 col-md-8 col-md-offset-2 margin-top--70 white-bg">
      <div class="single-content-inner pad-70 row">
        <?php get_template_part( 'loop', 'single' ); ?>
      </div>
    </div>
  </div>
</div>
<div class="author-details-wrap">
  <div class="container">
    <div class="author-details-outer">
      <?php $linkedin = get_the_author_meta( 'linkedin', $post->post_author );?> 
      <div class="user-avatar">
        <a href="<?php echo $linkedin; ?>"><?php echo get_avatar(get_the_author_meta('ID'), ''); ?></a>
      </div>
      <div class="author-details-inner">
        <div class="text-uppercase author-text">Author</div>
        <div class="author-name text-uppercase">
          <a href="<?php echo $linkedin; ?>"><?php  the_author_meta( 'display_name', $postDataValue->post_author ) ?></a>
        </div>
        <?php
        // Author bio.
        if ( is_single() && get_the_author_meta( 'description' ) ) :
        get_template_part( 'author-bio' );
        endif;
        ?>
        </div>
    </div>
  </div>
</div>

<div class="pagination-wrap">
  <div class="container">
    <?php //previous_post_link(); ?>    <?php //next_post_link(); ?>
    <div class="pagination-inner">
    <?php
$post_id = $post->ID; // Get current post ID
$cat = get_the_category();
$current_cat_id = $cat[0]->cat_ID; // Get current Category ID 
$args = array('category'=>$current_cat_id,'orderby'=>'post_date','order'=> 'DESC');
$posts = get_posts($args);
// Get IDs of posts retrieved by get_posts function
$ids = array();
foreach ($posts as $page) {
$posts[] += $page->ID;
}

$current = array_search(get_the_ID(), $posts);
$prevID = $posts[$current-1];
$nextID = $posts[$current+1];
?>
    <?php if (!empty($prevID)){ ?> <a  rel="prev" href="<?php echo get_permalink($prevID); ?>" title="<?php echo get_the_title($prevID); ?>" class="post-link btn btn-ripple btn-prev"><i class="fa fa fa-chevron-left"></i></a> <?php } ?>
    <a class="cat-link" href="<?php echo get_category_link($cat[0]->cat_ID); ?>" title=""><i class="fa fa-th"></i></a>

    <?php if (!empty($nextID)){ ?> <a  rel="next"  href="<?php echo get_permalink($nextID); ?>" title="<?php echo get_the_title($nextID); ?>" class="post-link btn btn-ripple btn-next"><i class="fa fa fa-chevron-right"></i></a> <?php } ?>
  </div>

  </div>
</div>

<?php //get_sidebar(); ?>


	<?php get_footer(); ?>