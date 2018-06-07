<?php
/**
 * A Simple Category Template
 *
 * @package Agilex
 * @since Agilex 1.0
 */
?>



<div class="blog-search box-shadow margin-bottom-30">
<?php /* * Search Form */ $cat_id = get_queried_object_id(); ?>
    <?= customSearchForm(null,
        'Search',
        'post',
        $cat_id); ?>
        </div>
<?php /* * Recent Posts* */ ?>


<div class="sidebar-post-sec box-shadow margin-bottom-30">
    <?php
    $catquery = new WP_Query([
        'cat' => $cat_id,
        'posts_per_page' => 5]);
    ?>
    <ul class="nav nav-tabs">
                <li  class="active" role="presentation"><a href="#popular" role="tab" data-toggle="tab">Popular</a></li>
                <li role="presentation"><a href="#recent" role="tab" data-toggle="tab">Recent</a></li>
                
        </ul>
        <div class="tab-content">
                <div class="tab-pane fade in " id="recent">
                <ul class="sidebar-post">
                    <?php while ($catquery->have_posts()) : $catquery->the_post(); ?>
                    <li class="">
                        <div class="media">
                            <a class="media-left post-thumb" href="<?php the_permalink() ?>" rel="<?php the_title() ?>">
                            <?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "thumbnail" ); ?>
                                    <img class="" src="<?php echo $thumbnail[0]; ?>" alt="<?php the_title() ?>"/>
                            </a>
                            <div class="media-body">
                                <a href="<?php the_permalink() ?>" rel="<?php the_title() ?>"><span class="media-heading"><?php the_Title(); ?></span></a>
                                <span class="post-date">Date: <?php the_date('d F, Y'); ?></span>
                            </div>
                        </div>
                    </li>
                <?php endwhile; ?>
                </ul>
                <?php wp_reset_postdata(); ?>
                </div>
                <div class="tab-pane fade in active" id="popular">
                <?php /* * Popular Posts* */
                    $catPopular = new WP_Query([
                        'meta_key' => '_li_love_count',
                        'orderby' => 'meta_value',
                        'order' => 'DESC']); ?>
                    
                    <ul class="sidebar-post">
                    <?php while ($catPopular->have_posts()) : $catPopular->the_post(); ?>
                    <li class="">
                        <div class="media">
                            <a class="media-left post-thumb" href="<?php the_permalink() ?>" rel="<?php the_title() ?>">
                            <?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "thumbnail" ); ?>
                                    <img class="" src="<?php echo $thumbnail[0]; ?>" alt="<?php the_title() ?>"/>
                            </a>
                            <div class="media-body">
                                <a href="<?php the_permalink() ?>" rel="<?php the_title() ?>"><span class="media-heading"><?php the_Title(); ?></span></a>
                                <span class="post-date">Date: <?php the_date('d F, Y'); ?></span>
                            </div>
                        </div>
                    </li>



                    <?php endwhile; ?>
                    </ul>
                    <?php wp_reset_postdata(); ?>
                </div>
    </div>

</div>

<div class="categories-wrap box-shadow margin-bottom-30">
<h2 class="heading-title"><?php echo __("Categories"); ?></h2>
<?php
                $args = array(
  'orderby' => 'name',
  'parent' => $cat_id,
  'taxonomy' => 'category',
  'hide_empty' => 1,
  'hierarchical' => true

  );
$categories = get_categories( $args );
$content=''; ?>
<ul class="stack-list">

<?php foreach ( $categories as $category ) { ?>
    <li><a href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->name ?></a></li>
    
<?php } ?>
</ul>
</div>

<div class="archieve-wrap box-shadow margin-bottom-30">
<h2 class="heading-title"><?php echo __("Archive"); ?></h2>
<?php /**Archives */
    $args = array(
        'cat' => $cat_id,
        'type' => 'monthly',
        'limit' => '',
        'format' => 'html',
        'before' => '',
        'after' => '',
        'show_post_count' => false,
        'echo' => 1,
        'order' => 'DESC',
        'post_type' => 'post'
    ); ?> 
    <ul class="stack-list">
    <?php wp_get_archives($args); ?>
</ul>
    </div>

    <div class="instagram-wrap box-shadow margin-bottom-30">
    <h2 class="heading-title"><?php echo __("Instgram"); ?></h2>
<?php /**Instagram Feed */
echo do_shortcode('[instagram-feed showfollow=false showbio=false showheader=false]'); ?>

</div>