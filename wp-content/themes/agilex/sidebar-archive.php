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
        'order' => 'DESC',
        'orderby' => 'date',
        'posts_per_page' => 3]);
    ?>
    <ul class="nav nav-tabs">
                <li  class="active" role="presentation"><a href="#popular" class="btn-ripple btn-blue-ripple" role="tab" data-toggle="tab">Popular</a></li>
                <li role="presentation"><a href="#recent" role="tab" class="btn-ripple btn-blue-ripple" data-toggle="tab">Recent</a></li>

        </ul>
        <div class="tab-content">
                <div class="tab-pane fade in " id="recent">
                <ul class="sidebar-post">
                    <?php if(have_posts()) {?>

                    <?php while ($catquery->have_posts()) : $catquery->the_post(); ?>
                    <li class="">
                        <div class="media">
                            <a class="media-left post-thumb" href="<?php the_permalink() ?>" rel="<?php the_title() ?>">
                            <?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "thumbnail" ); ?>
                            <?php if($thumbnail) {?>
                                    <img class="" src="<?php echo $thumbnail[0]; ?>" alt="<?php the_title() ?>"/>
                                <?php } else { ?>
                                    <img src="<?php echo get_template_directory_uri()?>/images/blog-small.jpg" alt="<?php the_Title(); ?>"/>                    
                                <?php }?>
                            </a>
                            <div class="media-body">
                                <a href="<?php the_permalink() ?>" rel="<?php the_title() ?>"><span class="media-heading"><?php wp_trim_words(the_Title(), 8, '...'); ?></span></a>
                                <span class="post-date">Date: <?php echo get_the_date('d M, Y', $post->ID); ?></span>
                            </div>
                        </div>
                    </li>
                <?php endwhile; ?>
                                <?php } else { ?>
                                    <li>No Recent post </li>
                                <?php } ?>
                </ul>
                <?php wp_reset_postdata(); ?>
                </div>
                <div class="tab-pane fade in active" id="popular">
                    <?php /* * Popular Posts* */
                    $popularpost = new WP_Query( array( 'posts_per_page' => 3,
                    'meta_key' => 'wpb_post_views_count',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC'
                    ) );

                    ?>

                    <ul class="sidebar-post">
                        <?php if($popularpost->have_posts()){ ?>
                            <?php while ( $popularpost->have_posts() ) : $popularpost->the_post(); ?>
                                <li class="">
                                    <div class="media">
                                        <a class="media-left post-thumb" href="<?php echo get_permalink( $popularpost->ID ); ?>" rel="<?php the_title() ?>">
                                            <?php  $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $popularpost->ID ), "thumbnail" ); ?>
                                            <?php if($thumbnail) {?>
                                                <img class="" src="<?php echo $thumbnail[0]; ?>" alt="<?php the_title() ?>"/>
                                            <?php } else {?>

                                                <img src="<?php echo get_template_directory_uri()?>/images/blog-small.jpg" alt="<?php the_title() ?>"/>
                                            <?php } ?>
                                        </a>

                                        <div class="media-body">
                                            <a href="<?php echo get_permalink( $popularpost->ID ); ?>" rel="<?php the_title() ?>"><span class="media-heading"><?php echo wp_trim_words(the_title(), 8, '...') ?></span></a>
                                            <span class="post-date">Date: <?php echo get_the_date('d M, Y', $popularpost->ID); ?></span>
                                        </div>
                                    </div>
                                </li>
                            <?php endwhile; }else { ?>
                            <li>No Popular post here</li>
                        <?php } ?>
                    </ul>
                    <?php wp_reset_postdata(); ?>
                </div>
    </div>

</div>

<div class="categories-wrap box-shadow margin-bottom-30">
<?php /** Categories */
    $argsCat = array(
        'parent' =>  get_cat_ID('blog'),
        'order_by' => 'date',
        'order' => 'ASC',
        'exclude' => get_cat_ID('blog'),
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
                    <a href="<?php bloginfo('url') ?>/<?php echo $month->year; ?>/<?php echo date("m", mktime(0, 0, 0, $month->month, 1, $month->year)) ?>/"><span class="archive-month"><?php echo date_i18n("F", mktime(0, 0, 0, $month->month, 1, $month->year)) ?></span></a>
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
echo do_shortcode('[wdi_feed id="1"]');  ?>

</div>
