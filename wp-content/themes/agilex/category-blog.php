<?php
/**
 * A Simple Category Template
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>

<?php 
$pageargs = array(
       
        'post_type' => 'page',
        'name' => 'blog'
    );
    $postValues = get_posts($pageargs);  
?>
<div class="main-banner-wrap">
    <?php foreach($postValues as $postValue): ?>
    <?php $featured_img_url = get_the_post_thumbnail_url($postValue->ID,'full'); ?>      
    <div class="main-banner">
        <?php if ($featured_img_url){ ?>
            <img src="<?php echo $featured_img_url; ?>" class="" alt=""/>
        <?php } else  { ?>
            <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X450.png" class="" alt=""/>
        <?php }?>
    </div>
    <div class="page-header-content">
        <div class="container">
            <h1><?php echo $postValue->post_title ?></h1>
            <p><?php echo $postValue->post_excerpt ?></p>       
        </div>     
    </div><?php endforeach;?>
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
                                            <div class="blog-detail text-uppercase">
                                            <div class="blog-author">By <?php  the_author_meta( 'display_name', $postData[0]->post_author ) ?></div>
                                            <div class="blog-year"><?php the_time('j F Y') ?></div>
                                        </div> 
                                        <div class="blog-extras">
                                            <a href="#"><i class="fa fa-heart-o"></i> <span class="count">06</span></a>
                                            <a href="#"><i class="fa fa-comment-o"></i> <span class="comments-count"> <?php
                                            echo get_comments_number();
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
            <div class="col-sm-3">
            <?php /**Search Form */
            $cat_id = $category->cat_ID; ?>
        <?= customSearchForm(null, 'Search', 'post', $cat_id ); ?>
            <?php /**Recent Posts**/?>

           <?php $catquery = new WP_Query([
               'cat' => $category->cat_ID,
               'posts_per_page' => 5 ]); ?>
            <?php /**Popular Posts**/?>
                 <?php $catPopular = new WP_Query([
               'cat' => $category->cat_ID,
               'posts_per_page' => 5 ]);  ?>
            
            <ul class="nav nav-tabs">
                 <li class="active" role="presentation"><a href="#recent" role="tab" data-toggle="tab">Recent Posts</a></li>
                 <li role="presentation"><a href="#popular" role="tab" data-toggle="tab">Popular Posts</a></li>
           </ul>

            <div class="tab-content">
                <div class="tab-pane fade in active" id="recent">
                <?php if ( have_posts() ) : ?>
                    <ul>
                        <?php while($catquery->have_posts()) : $catquery->the_post(); ?>
                        <li>
                            <?php  if (has_post_thumbnail( $post->ID ) ): ?>
                                <a href="<?php the_permalink() ?>" rel="<?php the_title() ?>">
                                    <?php the_post_thumbnail('thumbnail'); ?></a>
                            <?php endif;  ?>
                            <h3><a href="<?php the_permalink() ?>" rel="<?php the_title() ?>"><?php the_Title(); ?></a></h3>
                            <!-- <span>Date: <?php //the_date('d M, Y'); ?></span> -->
                        </li>
                        <?php endwhile; ?>
                    </ul>
                    <?php else: ?>
                    <p>Sorry, no posts matched your criteria.</p>
                    <?php endif; ?>
                </div>
                <div class="tab-pane fade in" id="popular">
                <?php if ( have_posts() ) : ?>
                    <ul>
                    <?php while($catPopular->have_posts()) : $catPopular->the_post();
                        if (get_post_meta( $post->ID, '_li_love_count', true) > 0) { ?>
                        <li>
                            <?php if (has_post_thumbnail( $post->ID ) ): ?>
                                <a href="<?php the_permalink() ?>" rel="<?php the_title() ?>">
                                    <?php the_post_thumbnail('featured-small'); ?></a>
                            <?php endif;  ?>
                            <h3><a href="<?php the_permalink() ?>" rel="<?php the_title() ?>"><?php the_Title(); ?></a></h3>
                            <!-- <span>Date: <?php the_date('d M, Y'); ?></span> -->
                        </li>

                        <?php } endwhile; ?>
                    </ul>
                    <?php else: ?>
                    <p>Sorry, no posts matched your criteria.</p>
                    <?php endif; ?>
                   
                </div>
           </div>
           
                
   
         
            <h3>Popular Posts</h3>
            <ul>
                
            </ul>
        <?php wp_reset_postdata(); ?>


<div class="categories">
    <h2>categories</h2>
    <ul>
    <?php
                $args = array(
  'orderby' => 'name',
  'parent' => '5',
  'taxonomy' => 'category',
  'hide_empty' => 1 ,
  'number' => '5'
  );
$categories = get_categories( $args );
$content='';
foreach ( $categories as $category ) { ?>
    <li><a href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->name; ?></a></li>
    
<?php } ?>
                </ul>

                </div>

        <?php //get_sidebar(); ?>
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