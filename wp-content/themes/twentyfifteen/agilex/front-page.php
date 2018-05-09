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
        <div class="heading-title"><?php echo the_Title(); ?></div>
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


<div class="what-we-do">
        <div class="container">
            <div class="heading">
                <div class="heading-title">Who We Are</div>
                <div class="sub-heading">Nesciunt tofu stumptown aliqua retro synth master cleanse</div>
            </div>
            <div class="grid-content">
                <div class="col-xs-18 col-sm-6 col-md-4">
                    <div class="thumbnail">
                        <img src="http://placehold.it/500x300" alt="">
                        <div class="caption">
                            <h4>Thumbnail label</h4>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere, soluta, eligendi doloribus sunt minus amet sit debitis repellat. Consectetur, culpa itaque odio similique suscipit</p>

                        </div>
                    </div>
                </div>
                <div class="col-xs-18 col-sm-6 col-md-4">
                    <div class="thumbnail">
                        <img src="http://placehold.it/500x300" alt="">
                        <div class="caption">
                            <h4>Thumbnail label</h4>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere, soluta, eligendi doloribus sunt minus amet sit debitis repellat. Consectetur, culpa itaque odio similique suscipit</p>

                        </div>
                    </div>
                </div>
                <div class="col-xs-18 col-sm-6 col-md-4">
                    <div class="thumbnail">
                        <img src="http://placehold.it/500x300" alt="">
                        <div class="caption">
                            <h4>Thumbnail label</h4>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere, soluta, eligendi doloribus sunt minus amet sit debitis repellat. Consectetur, culpa itaque odio similique suscipit</p>

                        </div>
                    </div>
                </div>
                <div class="col-xs-18 col-sm-6 col-md-4">
                    <div class="thumbnail">
                        <img src="http://placehold.it/500x300" alt="">
                        <div class="caption">
                            <h4>Thumbnail label</h4>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere, soluta, eligendi doloribus sunt minus amet sit debitis repellat. Consectetur, culpa itaque odio similique suscipit</p>

                        </div>
                    </div>
                </div>
                <div class="col-xs-18 col-sm-6 col-md-4">
                    <div class="thumbnail">
                        <img src="http://placehold.it/500x300" alt="">
                        <div class="caption">
                            <h4>Thumbnail label</h4>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere, soluta, eligendi doloribus sunt minus amet sit debitis repellat. Consectetur, culpa itaque odio similique suscipit</p>

                        </div>
                    </div>
                </div>
                <div class="col-xs-18 col-sm-6 col-md-4">
                    <div class="thumbnail">
                        <img src="http://placehold.it/500x300" alt="">
                        <div class="caption">
                            <h4>Thumbnail label</h4>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere, soluta, eligendi doloribus sunt minus amet sit debitis repellat. Consectetur, culpa itaque odio similique suscipit</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                <div class="testimonials-content text-center col-sm-8 col-sm-offset-2">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>

                    <div class="review-details">
                        <div class="author">
                            <strong>Lorem Ipsum</strong>
                        </div>
                        <div class="author-place">
                            Syndey
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



<?php get_footer(); ?>