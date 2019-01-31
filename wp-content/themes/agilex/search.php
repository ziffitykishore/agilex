<?php
/**
  * Template Name: Page Search Results
 *
 * @package Bootstrap Canvas WP
 * @since Bootstrap Canvas WP 1.0
 */

  get_header(); ?>
    <?php
    $pageargs = array(

            'post_type' => 'page',
            'name' => 'search'
        );
        $postValues = get_posts($pageargs);
    ?>
    <div class="main-banner-wrap">
        <?php foreach($postValues as $postValue): ?>
        <?php $featured_img_url = get_the_post_thumbnail_url($postValue->ID,'full');
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
                <h1><h1><?php printf( __( 'Search Results for: %s', 'bootstrapcanvaswp' ), '<span class="text-capitalize">' . get_search_query() . '</span>' ); ?></h1></h1>
                <p><?php echo $postValue->post_excerpt ?></p>
            </div>
        </div><?php endforeach;?>
    </div>



       <div class="blog-wrap search-results-wrap pad-tb-50"> 
        <div class="container">
        <div class="search-results-inner">
        <div class="blog-search box-shadow margin-bottom-30 margin-top-40">
        <?php get_template_part( 'loop', 'search' ); ?>
            </div>
            </div>
        </div>
      </div>
      
	<?php get_footer(); ?>