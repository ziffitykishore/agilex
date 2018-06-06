<?php
/**
 * The loop that displays posts
 */
?>

<div class="blog-wrap">
    <div class="container">
        <div class="blog-inner-section row">
            <div class="col-sm-9 blog-grid-sec">
            <?php if ( have_posts() ) : ?>
                    <ul class="grid-sec effect-8 row" id="grid">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <li class="blog-item col-sm-6 col-md-4">
                                <div class="blog-inner">
                                    <div class="blog-image">
                                        <img src="<?php echo the_cfc_field('thumbnail', 'post-thumbnail');?>" alt=""/>
                                    </div>
                                    <div class="blog-detail-wrap">
                                        <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><div class="blog-tile"><?php the_title(); ?></div></a>
                                            <div class="blog-detail text-uppercase">
                                            <div class="blog-author">By <?php  the_author_meta( 'display_name', $postData[0]->post_author ) ?></div>
                                            <div class="blog-year"><?php the_time('j F Y') ?></div>
                                        </div>
                                        <div class="blog-extras">
                                            <?php echo do_shortcode('[wishlist-feed]'); ?>
                                            <span class="comment-sec extras-link"><i class="fa fa-comment-o"></i> <span class="comments-count"> <?php
                                            echo get_comments_number();
                                            ?></span></span>
                                            <span  class="social_sharing extras-link"><i class="fa fa-share"></i>
                                                <div class="social_sharing-content" ><?php echo do_shortcode('[wp_social_sharing]'); ?></div>
                                            </span>
                                            
                                        </div>
                                    </div>
                                </div>
                            </li>
                    <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>Sorry, no posts matched your criteria.</p>
                <?php endif; ?>
            </div>
            <div class="col-sm-3 sidebar-blog">
            <?php get_sidebar('category'); ?>
                </div>


        </div>
    </div>
</div>


<script src="<?php echo get_template_directory_uri(); ?>/js/masonry.pkgd.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/imagesloaded.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/classie.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/AnimOnScroll.js"></script>
<script>
   new AnimOnScroll( document.getElementById( 'grid' ), {
				minDuration : 0.4,
				maxDuration : 0.6,
				viewportFactor : 0.2
			} );
    </script>

