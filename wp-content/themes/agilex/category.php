<?php
/**
 * Template for displaying Category Archive pages
 *
 * @package Bootstrap Canvas WP
 * @since Bootstrap Canvas WP 1.0
 */

  get_header(); ?>
<?php  $slugs = explode('/', get_query_var('category_name'));
        $currentCategory = get_category_by_slug('/'.end($slugs));
	$category = &get_category($currentCategory->category_parent);
    if($category->slug == 'blog' ) { ?>
        <?php
            $args = array(

                'post_type' => 'page',
                'name' => 'blog'
            );
            $blog_query = new WP_Query($args);
            $blog_query->have_posts();
            $blog_query->the_post();
    } ?>
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
    <?php
    if($category->slug == 'blog' ) {
        get_template_part( 'loop_category', 'category' );
        get_sidebar('category');
    } else {
        get_template_part( 'loop', 'category' );
    ?>

    <!-- /.blog-main -->
    <?php get_sidebar('category'); } ?>


	<?php get_footer(); ?>