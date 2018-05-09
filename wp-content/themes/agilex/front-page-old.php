<?php
/**
 * The template for the front page.
 *
 * @package Ziffity
 * @subpackage Agilex
 * @since Agilex 1.0
 */
get_header();
?>

<?php
if (is_front_page()) { ?>
    <div class="slider-homepage">
        <div id="hero-slider">
            <?php
            $slider_args = array(
                'post_type' => 'banner_slider',
                'posts_per_page' => -1,
                'post_status'     => 'publish'
            );
            $banner_slider_query = new WP_Query($slider_args); ?>
             <?php while ($banner_slider_query->have_posts()) {
                $banner_slider_query->the_post(); ?>
            <div class="slider-blk">
                <?php the_post_thumbnail('full'); ?>
                <div class="slider-content">
                    <div class="container">
                        <?php echo the_Content();  ?>
                    </div>
                </div>

            </div>
            <?php } ?>
        </div>
        <a class="scroll-down" href="#">&darr;</a>
    </div>
    <?php /** Makes Agilex Unique Section -- Start **/ ?>
    <div class="tab-section uniques">
        <div class="container">
            <?php
            $args = array(
                'post_type' => 'makes_agilex_unique',
                'posts_per_page' => -1,
                'post_status'     => 'publish'
            );
            $agilex_unique_query = new WP_Query($args); $i = 0; ?>
            <ul class="nav nav-tabs" id="myTabs" role="tablist">
                <?php while ($agilex_unique_query->have_posts()) {
                $agilex_unique_query->the_post(); ?>
                <li role="presentation" class="<?php if($i == 0) { echo "active"; } ?> ">
                    <a href="#<?php echo the_Title(); ?>" class="text-uppercase" id="<?php echo the_Title(); ?>-tab" role="tab" data-toggle="tab" aria-controls="<?php echo the_Title(); ?>" aria-expanded="true">
                        <?php echo the_Title(); $i++; ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
            <div class="tab-content" id="myTabContent">
                <?php $i = 0; while ($agilex_unique_query->have_posts()) {
                        $agilex_unique_query->the_post(); ?>
                <div class="tab-pane fade <?php if($i == 0) { echo "active in"; } ?> " role="tabpanel" id="<?php echo the_Title(); ?>" aria-labelledby="<?php echo the_Title(); ?>-tab">
                    <div class="row">
                        <div class="col-sm-4">
                            <?php the_post_thumbnail('thumbnail'); ?>
                        </div>
                        <div class="col-sm-8">
                            <?php echo the_Content(); $i++; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </div>
    <?php /* Restore original Post Data */
        wp_reset_postdata();?>
        </div>
    </div>

    <?php
    /** Makes Agilex Unique Section -- End * */
    /** Who We Are Section -- Start * */
    $who_args = array(
        'name' => 'who-we-are',
        'post_status'     => 'publish'
    );
    $who_we_are_query = new WP_Query($who_args);
    while ($who_we_are_query->have_posts()) {
        $who_we_are_query->the_post();
        //$featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
        /* link thumbnail to full size image for use with lightbox */
        //the_post_thumbnail('thumbnail');
        echo the_Title();
        echo wp_strip_all_tags( get_the_excerpt(), true );
        echo the_Content();
    }
    /* Restore original Post Data */
    wp_reset_postdata();
    /** Who We Are Section -- End * */
    /** What We Do Section -- Start * */
    $args = array(
        'post_type' => 'what_we_do',
        'posts_per_page' => -1,
        'post_status'     => 'publish'
    );
    $agilex_unique_query = new WP_Query($args);
    while ($agilex_unique_query->have_posts()) {
        $agilex_unique_query->the_post();
        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
        /* link thumbnail to full size image for use with lightbox */
        the_post_thumbnail('thumbnail');
        echo the_Title();
        echo the_Content();
    }
    /* Restore original Post Data */
    wp_reset_postdata();
    /** What We Do Section -- End * */
    /** Testimonial Section -- Start * */
    $args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => -1,
        'post_status'     => 'publish'
    );
    $agilex_unique_query = new WP_Query($args);
    while ($agilex_unique_query->have_posts()) {
        $agilex_unique_query->the_post();
        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),
            'full');
        /* link thumbnail to full size image for use with lightbox */
        the_post_thumbnail('thumbnail');
        echo the_Title();
        echo the_Content();
    }
    /* Restore original Post Data */
    wp_reset_postdata();
    /** Testimonial Section -- End * */
}
?>

<?php get_footer(); ?>