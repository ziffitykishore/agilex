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
        'parent' => $cat_id,
        'order_by' => 'date',
        'order' => 'ASC',
        'exclude' => $cat_id,
        'hierarchical' => true,
        'show_post_count' => true
    );
    $categories = get_categories($argsCat);
    $output = '';
    if (!empty($categories)) {?>
    
        <h2 class="heading-title"><?php echo __("Categories"); ?></h2>
        <div class="inner-sec stack-list">
    <?php
        foreach ($categories as $category) {
            $output .= '<li class=""><a href="' . esc_url(get_category_link($category->term_id)) . '" alt="' . esc_attr(sprintf(__('View all posts in %s', 'textdomain'),
                    $category->name)) . '">' . esc_html($category->name) . '</a> <span>('.$category->count.')</li>' ;
    }
    echo "<ul>".trim($output)."</ul></div>";
}
?>
</div>


<?php /**Archives */ ?>

<div class="archieve-wrap box-shadow margin-bottom-30">
        <h2 class="heading-title"><?php echo __("Archive"); ?></h2>
        <div class="inner-sec stack-list">
        <?php
            global $wpdb;
            $limit = 0;
            $year_prev = null;
            $months = $wpdb->get_results("SELECT DISTINCT MONTH( post_date ) AS month ,	YEAR( post_date ) AS year, COUNT( id ) as post_count FROM $wpdb->posts WHERE post_status = 'publish' and post_date <= now( ) and post_type = 'post' GROUP BY month , year ORDER BY post_date DESC");
            foreach($months as $month) :
        	$year_current = $month->year;
	            if ($year_current != $year_prev) {
                    if($year_current != date('Y')) {?>
                </ul>
                    <?php }?>
                <h3 class="list-trigger">
                    <a href="<?php bloginfo('url') ?>/<?php echo $month->year; ?>/"><?php echo $month->year; ?></a>
                    <span class="fa fa-plus"></span>
                </h3>	
                <ul class="post-list">			
                <?php } ?>
                <li>
                    <a href="<?php bloginfo('url') ?>/<?php echo $month->year; ?>/<?php echo date("m", mktime(0, 0, 0, $month->month, 1, $month->year)) ?>"><span class="archive-month"><?php echo date_i18n("F", mktime(0, 0, 0, $month->month, 1, $month->year)) ?></span></a>
                    &nbsp;<span>(<?php echo $month->post_count; ?>)</span>
                </li>
                <?php $year_prev = $year_current;
                    endforeach; ?>
                </ul>
                </div> 
    </div>




    <div class="instagram-wrap box-shadow margin-bottom-30">
    <h2 class="heading-title"><?php echo __("Instagram"); ?></h2>
<?php /**Instagram Feed */
echo do_shortcode('[instagram-feed showfollow=false showbio=false showheader=false]'); ?>

</div>