<?php
/**
 * The loop that displays posts
 */
?>

<div class="blog-wrap">
    <div class="container">
        <div class="blog-inner-section row">
            <div class=" col-md-12 col-lg-9 blog-grid-sec">
            <?php if ( have_posts() ) : ?>
                    <ul class="grid-sec effect-8 row" id="grid">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <li class="blog-item col-sm-6 col-md-4">
                                <div class="blog-inner">
                                    <div class="blog-image">
                                    <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">


                                            <?php
                                            global $post;
                                            $image_object = get_cfc_field('thumbnail', 'post-thumbnail', $post->ID );
                                            ?>
                                            <?php $featured_img_url = get_the_post_thumbnail_url($postValue->ID,'thumbnail'); ?>
                                            <?php if($image_object){ ?>
                                            <img src="<?php echo $image_object['url']; ?>" alt="<?php the_title(); ?>" title="<?php the_title(); ?>"/>
                                            <?php } elseif($featured_img_url) {?>
                                                
                                                <img src="<?php echo $featured_img_url ?>" alt="<?php the_title(); ?>"/>
                                            <?php } else { ?>
                                                <img src="<?php echo get_template_directory_uri()?>/images/placeholder_360X510.png"/>                    
                                            <?php  }?>

                                            </a>
                                    </div>
                                    <div class="blog-detail-wrap">
                                        <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><div class="blog-tile"><?php echo wp_trim_words(get_the_title(),5, '...'); ?></div></a>
                                            <div class="blog-detail text-uppercase">
                                            <div class="blog-author">By <?php the_author_meta( 'display_name', $postValue[0]->post_author ) ?></div>
                                            <div class="blog-year"><?php the_time('j F Y') ?></div>
                                        </div>
                                        <div class="blog-extras">
                                            <?php echo do_shortcode('[wishlist-feed]'); ?>
                                            <span class="comment-sec extras-link"><i class="fa fa-comment-o"></i> <span class="comments-count"> <?php
                                            echo get_comments_number();
                                            ?></span></span>
                                            <span  class="social_sharing extras-link"><i class="fa fa-share-alt"></i>
                                                <div class="social_sharing-content" ><?php echo do_shortcode('[wp_social_sharing]'); ?></div>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                    <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-center">Sorry, no posts matched your criteria.</p>
                <?php endif; ?>
            </div>
            <div class="col-md-12 col-lg-3  sidebar-blog">
            <?php get_sidebar('category'); ?>
                </div>


        </div>
    </div>
</div>



