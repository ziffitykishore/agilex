<?php
/**
 * The template for the front page.
 *
 * @package Agilex
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
                    <a href="#<?php echo the_Title(); ?>" class="text-uppercase btn-ripple" id="<?php echo the_Title(); ?>-tab" role="tab" data-toggle="tab" aria-controls="<?php echo the_Title(); ?>" aria-expanded="true">
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

    /** Who We Are Section -- Start **/ ?>
    <?php $who_args = array(
        'name' => 'who-we-are',
        'post_status'     => 'publish'
    );
    $who_we_are_query = new WP_Query($who_args);
    while ($who_we_are_query->have_posts()) {
        $who_we_are_query->the_post(); ?>
    <div class="who-we-are">
    <div class="heading">
        <a href="<?php echo get_permalink() ?>">
            <div class="heading-title"><?php echo the_Title(); ?></div>
        </a>
        <div class="sub-heading"><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></div>
    </div>

    <div class="inner-content-outer">
            <div class="container">
                <div class="inner-content">
                    <?php echo the_Content(); ?>
                </div>
            </div>
    </div>
</div>
<?php  }
    /* Restore original Post Data */
    wp_reset_postdata();
    /** Who We Are Section -- End * */ ?>
    <div class="what-we-do">
        <div class="container">
            <div class="heading">
                <div class="heading-title">What We Do</div>
                <div class="sub-heading">Nesciunt tofu stumptown aliqua retro synth master cleanse</div>
            </div>
            <div class="grid-content">
                <?php
                /** What We Do Section -- Start * */
                $whatArgs = array(
                    'post_type' => 'what_we_do',
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                );
                $agilex_whatwe_query = new WP_Query($whatArgs);
                while ($agilex_whatwe_query->have_posts()) {
                    $agilex_whatwe_query->the_post();
                    $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
                    ?>
                    <div class="col-xs-18 col-sm-6 col-md-4">
                        <div class="thumbnail">
                            <?php the_post_thumbnail('thumbnail'); ?>
                            <div class="caption">
                                <a href="<?php echo get_permalink() ?>">
                                    <h4><?php echo the_Title(); ?></h4></a>
                                <p><?php echo the_Content(); ?></p>

                            </div>
                        </div>
                    </div>
                <?php }
                /* Restore original Post Data */
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </div>
<?php  /** What We Do Section -- End * */ ?>
    <section class="testimonials">
        <div class="container">
            <div class="heading text-center">
                <div class="heading-title">
                    Here's what our customers say about us
                </div>
                <div class="sub-heading">
                    Lorem ipsum dolor sit amet, consectetur
                </div>
            </div>
            <div class="testimonials-outer">
                <?php
                /** Testimonial Section -- Start * */
                $testi_args = array(
                    'post_type' => 'testimonial',
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                );
                $agilex_test_query = new WP_Query($testi_args);
                while ($agilex_test_query->have_posts()) {
                    $agilex_test_query->the_post();
                    $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
                    /* link thumbnail to full size image for use with lightbox */
                    ?>
                    <div class="testimonials-content text-center col-sm-8 col-sm-offset-2">
                        <p><?php echo the_Content(); ?></p>
                        <div class="review-details">
                            <div class="author">
                                <strong>
                                    <?php if (get_field('name_testimonial', get_the_ID())): ?>
                                    <h2><?php the_field('name_testimonial', get_the_ID()); ?></h2>
                                    <?php endif; ?>
                                </strong>
                            </div>
                            <div class="author-place">
                                <?php if (get_field('position', get_the_ID())): ?>
                                <h2><?php the_field('position', get_the_ID()); ?></h2>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php }
                /* Restore original Post Data */
                wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php  /** Testimonial Section -- End * */ ?>
<?php } get_footer(); ?>