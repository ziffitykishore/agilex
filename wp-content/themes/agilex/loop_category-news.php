<?php
/**
 * The loop that displays posts
 */
?>

<div class="blog-wrap">
    <div class="container">
        <div class="blog-inner-section">
            <div class="blog-grid-sec">
            <?php if ( have_posts() ) : ?>
                    <ul class="grid-sec effect-8 row" id="grid">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <li class="blog-item col-xss-12 col-xs-6 col-md-4 col-lg-3 news-item">
                                <div class="blog-inner">
                                    <div class="blog-image">
                                    <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">


                                            <?php
                                            global $post;
                                            $image_object = get_cfc_field('thumbnail', 'post-thumbnail', $post->ID );
                                            ?>
                                            <?php $featured_img_url = get_the_post_thumbnail_url($postValue->ID,'thumbnail'); ?>
                                            <?php if($image_object){ ?>
                                            <img class="lazy" src="<?php echo get_template_directory_uri()?>/images/blog_thumb-sm.jpg" data-src="<?php echo $image_object['url']; ?>" alt="<?php the_title(); ?>" title="<?php the_title(); ?>"/>
                                            <?php } elseif($featured_img_url) {
                                                $thumbnail_ID = get_post_thumbnail_id( get_the_ID() );
                                                $alt_text = get_post_meta( $thumbnail_ID, '_wp_attachment_image_alt', true );  ?>
                                                <img class="lazy" src="<?php echo get_template_directory_uri()?>/images/blog_thumb-sm.jpg" data-src="<?php echo $featured_img_url; ?>" alt="<?php echo $alt_text; ?>"/>
                                            <?php } else { ?>
                                                <img class="lazy" src="<?php echo get_template_directory_uri()?>/images/blog_thumb-sm.jpg" data-src="<?php echo get_template_directory_uri()?>/images/blog_thumb-sm.jpg" alt="Agilex Fragrances"/>                    
                                            <?php  }?>

                                            </a>
                                    </div>
                                    <div class="blog-detail-wrap">
                                        <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><div class="blog-tile"><?php echo wp_trim_words(get_the_title(),7, '...'); ?></div></a>
                                            <div class="blog-detail text-uppercase">
                                                <div class="blog-author">By <?php the_author_meta( 'display_name', $postValue[0]->post_author ) ?></div>
                                                <div class="blog-year"><?php the_time('j M Y') ?></div>
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
            


        </div>
    </div>
</div>



