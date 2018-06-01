<?php
/**
 * A Simple Category Template
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<div class="main-banner-wrap">
    <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
    <div class="main-banner">
        <?php if ($featured_img_url){ ?>
            <img src="<?php echo $featured_img_url; ?>" class="" alt=""/>
        <?php } else  { ?>
            <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X450.png" class="" alt=""/>
        <?php }?>
    </div>
    <div class="page-header-content">
        <div class="container">
            <h1><?php echo the_Title(); ?></h1>
            <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
        </div>     
    </div>
</div>


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
                                            <div class="blog-detail text-transform">
                                            <div class="blog-author">By <?php the_author_posts_link() ?></div>
                                            <div class="blog-year"><?php the_time('j F Y') ?></div>
                                        </div> 
                                        <div class="blog-extras">
                                            <a href="#"><i class="fa fa-heart-o"></i> <span class="count">06</span></a>
                                            <a href="#"><i class="fa fa-comment-o"></i> <span class="comments-count"> <?php
                                            comments_popup_link( '0', '1', '%', 'comments-link', 'Comments closed');
                                            ?></span></a>
                                            <a href="#"><i class="fa fa-share"></i></a>
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
        <?php get_sidebar(); ?>
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


    <?php get_footer(); ?>