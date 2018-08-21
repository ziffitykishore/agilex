<?php
/**
 * Template for displaying 404 pages (Not Found)
 *
 * @package Bootstrap Canvas WP
 * @since Bootstrap Canvas WP 1.0
 */

  get_header(); ?>
  
  <?php
    $pageargs = array(

            'post_type' => 'page',
            'name' => 'no-page-found'
        );
        $postValues = get_posts($pageargs);
    ?>
    <div class="main-banner-wrap">
        <?php foreach($postValues as $postValue): ?>
        <?php $featured_img_url = get_the_post_thumbnail_url($postValue->ID,'full'); ?>
        <div class="main-banner">
            <?php if ($featured_img_url){ ?>
                <img src="<?php echo $featured_img_url; ?>" class="" alt="<?php $featured_img_url['alt']; ?>"/>
            <?php } else  { ?>
                <img src="<?php bloginfo('template_directory'); ?>/images/404.jpg" class="" alt=""/>
            <?php }?>

        </div>
        <div class="page-header-content">
            <div class="container">
                
            </div>
        </div><?php endforeach;?>
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