<?php
/**
 * A Simple Category Template
 *
 * @package Agilex
 * @since Agilex 1.0
 */
?>



<div class="blog-search">
<?php /* * Search Form */ $cat_id = get_queried_object_id(); ?>
    <?= customSearchForm(null,
        'Search',
        'post',
        $cat_id); ?>
        </div>
<?php /* * Recent Posts* */ ?>


<div class="sidebar-post-sec">
    <?php
    $catquery = new WP_Query([
        'cat' => $cat_id,
        'posts_per_page' => 5]);
    ?>
    <ul class="nav nav-tabs">
                <li class="active" role="presentation"><a href="#recent" role="tab" data-toggle="tab">Recent Posts</a></li>
                <li role="presentation"><a href="#popular" role="tab" data-toggle="tab">Popular Posts</a></li>
        </ul>
        <div class="tab-content">
                <div class="tab-pane fade in active" id="recent">
                <ul class="sidebar-post">
                    <?php while ($catquery->have_posts()) : $catquery->the_post(); ?>
                    <li class="">
                        <a class="media" href="<?php the_permalink() ?>" rel="<?php the_title() ?>">
                            <div class="media-left post-thumb">
                            <?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "thumbnail" ); ?>
                                    <img class="" src="<?php echo $thumbnail[0]; ?>" alt="<?php the_title() ?>"/>
                            </div>
                            <div class="media-body">
                                <span class="media-heading"><?php the_Title(); ?></span>
                                <span class="post-date">Date: <?php the_date('d F, Y'); ?></span>
                            </div>
                        </a>
                    </li>
                <?php endwhile; ?>
                </ul>
                <?php wp_reset_postdata(); ?>
                </div>
                <div class="tab-pane fade in" id="popular">
                <?php /* * Popular Posts* */
                    $catPopular = new WP_Query([
                        'meta_key' => '_li_love_count',
                        'orderby' => 'meta_value',
                        'order' => 'DESC']); ?>
                    
                    <ul class="sidebar-post">
                    <?php while ($catPopular->have_posts()) : $catPopular->the_post(); ?>
                        <li>
                            <a class="media" href="<?php the_permalink() ?>" rel="<?php the_title() ?>">
                                <div class="media-left post-thumb">
                                <?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ); ?>
                                    <img src="<?php echo $thumbnail[0]; ?>" alt="<?php the_title() ?>"/>
                                </div>
                                <div class="media-body">
                                    <span class="media-heading"><?php the_Title(); ?></span>
                                    <span class="post-date">Date: <?php the_date('d F, Y'); ?></span>
                                </div>
                            </a>
                        </li>



                    <?php endwhile; ?>
                    </ul>
                    <?php wp_reset_postdata(); ?>
                </div>
    </div>

</div>


<?php /** Categories */ ?>
    <?php
    $argsCat = array(
        'parent' => $cat_id,
        'order_by' => 'date',
        'order' => 'ASC',
        'exclude' => $cat_id,
        'hierarchical' => true
    );
    $categories = get_categories($argsCat);
    $output = '';
    if (!empty($categories)) {
        echo __("Categories"); ?>
    <ul> <?php
        foreach ($categories as $category) {
            $output .= '<li><a href="' . esc_url(get_category_link($category->term_id)) . '" alt="' . esc_attr(sprintf(__('View all posts in %s', 'textdomain'),
                    $category->name)) . '">' . esc_html($category->name) . '</a></li>' ;
    } ?>
    </ul><?php
    echo trim($output);
}
?>
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
    );
    echo __("Archive");
    wp_get_archives($args);
    ?>

<?php /**Instagram Feed */
echo do_shortcode('[instagram-feed showfollow=false showbio=false showheader=false]'); ?>