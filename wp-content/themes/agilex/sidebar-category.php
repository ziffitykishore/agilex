<?php
/**
 * A Simple Category Template
 *
 * @package Agilex
 * @since Agilex 1.0
 */
?>
<?php /* * Search Form */ $cat_id = get_queried_object_id(); ?>
    <?= customSearchForm(null,
        'Search',
        'post',
        $cat_id); ?>
<?php /* * Recent Posts* */ ?>
    <?php
    $catquery = new WP_Query([
        'cat' => $cat_id,
        'posts_per_page' => 5]);
    ?>
    <h3>Recent Posts</h3>
    <ul>
        <?php while ($catquery->have_posts()) : $catquery->the_post(); ?>
         <li>
        <?php /* if (has_post_thumbnail( $post->ID ) ): ?>
          <a href="<?php the_permalink() ?>" rel="<?php the_title() ?>">
          <?php the_post_thumbnail('thumbnail'); ?></a>
          <?php endif; */ ?>
            <h3><a href="<?php the_permalink() ?>" rel="<?php the_title() ?>"><?php the_Title(); ?></a></h3>
            <!-- <span>Date: <?php the_date('d M, Y'); ?></span> -->
        </li>
    <?php endwhile; ?>
    </ul>
    <?php wp_reset_postdata(); ?>

<?php /* * Popular Posts* */
    $catPopular = new WP_Query([
        'meta_key' => '_li_love_count',
        'orderby' => 'meta_value',
        'order' => 'DESC']); ?>
    <h3>Popular Posts</h3>
    <ul>
    <?php while ($catPopular->have_posts()) : $catPopular->the_post(); ?>
        <li>
            <?php /* if (has_post_thumbnail( $post->ID ) ): ?>
              <a href="<?php the_permalink() ?>" rel="<?php the_title() ?>">
              <?php the_post_thumbnail('thumbnail'); ?></a>
              <?php endif; */ ?>
                <h3><a href="<?php the_permalink() ?>" rel="<?php the_title() ?>"><?php the_Title(); ?></a></h3>
                <!-- <span>Date: <?php the_date('d M, Y'); ?></span> -->
            </li>

    <?php endwhile; ?>
    </ul>
    <?php wp_reset_postdata(); ?>

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
