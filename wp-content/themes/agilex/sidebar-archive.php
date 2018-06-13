<?php
/**
 * A Simple Category Template
 *
 * @package Agilex
 * @since Agilex 1.0
 */
?>

<div class="blog-search box-shadow margin-bottom-30">
<?php get_search_form(); ?>
</div>

<div class="categories-wrap box-shadow margin-bottom-30">
<?php /** Categories */
    $argsCat = array(

        'order_by' => 'date',
        'order' => 'ASC',
  
        'hierarchical' => true,
        'show_post_count' => true
    );
    $categories = get_categories($argsCat);
    $output = '';
    if (!empty($categories)) {?>
        <h2 class="heading-title"><?php echo __("Categories"); ?></h2>
    <?php
        foreach ($categories as $category) {
            $output .= '<li><a href="' . esc_url(get_category_link($category->term_id)) . '" alt="' . esc_attr(sprintf(__('View all posts in %s', 'textdomain'),
                    $category->name)) . '">' . esc_html($category->name) . '</a> <span>('.$category->count.')</li>' ;
    }
    echo "<ul class='stack-list'>".trim($output)."</ul>";
}
?>
</div>

<div class="archieve-wrap box-shadow margin-bottom-30">

<?php /**Archives */
    $args = array(
        'type' => 'monthly',
        'format' => 'html',
        'show_post_count' => false,
        'echo' => 1,
        'order' => 'DESC',
        'post_type' => 'post'
    );
    if (customArchievesLink($cat_id, $args)) { ?>
        <h2 class="heading-title"><?php echo __("Archive"); ?></h2><ul class="stack-list">
        <?php echo customArchievesLink($cat_id, $args) ?>
        </ul>
    <?php }    ?>
    </div>
    <div class="instagram-wrap box-shadow margin-bottom-30">
    <h2 class="heading-title"><?php echo __("Instagram"); ?></h2>
<?php /**Instagram Feed */
echo do_shortcode('[instagram-feed showfollow=false showbio=false showheader=false]'); ?>

</div>