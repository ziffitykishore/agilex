<?php
/**
 * Template Name: Page Blog
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
<div class="main-banner">
        <?php if ($featured_img_url){ ?>
        <img src="<?php echo $featured_img_url; ?>" class="" alt=""/>
        <?php } else  { ?>
          <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X450.png" class="" alt=""/>
        <?php }?>
        <div class="page-header-content">
       <div class="container">
         <h1><?php echo the_Title(); ?></h1>
         <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
        </div>     
      </div>
 </div>

    <div class="blog-grid-wrap">
        <div class="container">
        <ul class="grid effect-8" id="grid">

            <li class="blog-item">
                 <div class="blog-inner">
            <div class="blog-image">
                <img src="/wp-content/uploads/2018/05/natural_Ingredients_02.jpg"/>
            </div>
            <div class="blog-detail-wrap">
                <div class="blog-tile">What is a Creative Fragrance Company?</div>
                <div class="blog-detail text-transform">
                    <div class="blog-author">By Chiristie O'brian</div>
                    <div class="blog-year">23 April 2018</div>
                </div> 
                <div class="blog-extras">
                    <a href="#"><i class="fa fa-heart-o"></i> <span class="count">06</span></a>
                    <a href="#"><i class="fa fa-comment-o"></i> <span class="comments-count"> 28</span></a>
                    <a href="#"><i class="fa fa-share"></i></a>
                </div>
            </div> 
        </div> 
    </li>
    <li class="blog-item">
        <div class="blog-inner">
            <div class="blog-image">
                <img src="/wp-content/uploads/2018/05/candle.png"/>
            </div>
            <div class="blog-detail-wrap">
                <div class="blog-tile">What is a Creative Fragrance Company?</div>
                <div class="blog-detail text-transform">
                    <div class="blog-author">By Chiristie O'brian</div>
                    <div class="blog-year">23 April 2018</div>
                </div> 
                <div class="blog-extras">
                    <a href="#"><i class="fa fa-heart-o"></i> <span class="count">06</span></a>
                    <a href="#"><i class="fa fa-comment-o"></i> <span class="comments-count"> 28</span></a>
                    <a href="#"><i class="fa fa-share"></i></a>
                </div>
            </div> 
        </div> 
    </li>
    <li class="blog-item">
        <div class="blog-inner">
            <div class="blog-image">
                <img src="/wp-content/uploads/2018/05/natural_Ingredients_02.jpg"/>
            </div>
            <div class="blog-detail-wrap">
                <div class="blog-tile">What is a Creative Fragrance Company?</div>
                <div class="blog-detail text-transform">
                    <div class="blog-author">By Chiristie O'brian</div>
                    <div class="blog-year">23 April 2018</div>
                </div> 
                <div class="blog-extras">
                    <a href="#"><i class="fa fa-heart-o"></i> <span class="count">06</span></a>
                    <a href="#"><i class="fa fa-comment-o"></i> <span class="comments-count"> 28</span></a>
                    <a href="#"><i class="fa fa-share"></i></a>
                </div>
            </div> 
        </div> 
    </li>
    <li class="blog-item">
        <div class="blog-inner">
            <div class="blog-image">
                <img src="/wp-content/uploads/2018/05/natural_Ingredients_02.jpg"/>
            </div>
            <div class="blog-detail-wrap">
                <div class="blog-tile">What is a Creative Fragrance Company?</div>
                <div class="blog-detail text-transform">
                    <div class="blog-author">By Chiristie O'brian</div>
                    <div class="blog-year">23 April 2018</div>
                </div> 
                <div class="blog-extras">
                    <a href="#"><i class="fa fa-heart-o"></i> <span class="count">06</span></a>
                    <a href="#"><i class="fa fa-comment-o"></i> <span class="comments-count"> 28</span></a>
                    <a href="#"><i class="fa fa-share"></i></a>
                </div>
            </div> 
        </div> 
    </li>
    <li class="blog-item">
        <div class="blog-inner">
            <div class="blog-image">
                <img src="/wp-content/uploads/2018/05/natural_Ingredients_02.jpg"/>
            </div>
            <div class="blog-detail-wrap">
                <div class="blog-tile">What is a Creative Fragrance Company?</div>
                <div class="blog-detail text-transform">
                    <div class="blog-author">By Chiristie O'brian</div>
                    <div class="blog-year">23 April 2018</div>
                </div> 
                <div class="blog-extras">
                    <a href="#"><i class="fa fa-heart-o"></i> <span class="count">06</span></a>
                    <a href="#"><i class="fa fa-comment-o"></i> <span class="comments-count"> 28</span></a>
                    <a href="#"><i class="fa fa-share"></i></a>
                </div>
            </div> 
        </div> 
    </li>
    <li class="blog-item">
        <div class="blog-inner">
            <div class="blog-image">
                <img src="/wp-content/uploads/2018/05/natural_Ingredients_02.jpg"/>
            </div>
            <div class="blog-detail-wrap">
                <div class="blog-tile">What is a Creative Fragrance Company?</div>
                <div class="blog-detail text-transform">
                    <div class="blog-author">By Chiristie O'brian</div>
                    <div class="blog-year">23 April 2018</div>
                </div> 
                <div class="blog-extras">
                    <a href="#"><i class="fa fa-heart-o"></i> <span class="count">06</span></a>
                    <a href="#"><i class="fa fa-comment-o"></i> <span class="comments-count"> 28</span></a>
                    <a href="#"><i class="fa fa-share"></i></a>
                </div>
            </div> 
        </div> 
    </li>
    <li class="blog-item">
        <div class="blog-inner">
            <div class="blog-image">
                <img src="/wp-content/uploads/2018/05/natural_Ingredients_02.jpg"/>
            </div>
            <div class="blog-detail-wrap">
                <div class="blog-tile">What is a Creative Fragrance Company?</div>
                <div class="blog-detail text-transform">
                    <div class="blog-author">By Chiristie O'brian</div>
                    <div class="blog-year">23 April 2018</div>
                </div> 
                <div class="blog-extras">
                    <a href="#"><i class="fa fa-heart-o"></i> <span class="count">06</span></a>
                    <a href="#"><i class="fa fa-comment-o"></i> <span class="comments-count"> 28</span></a>
                    <a href="#"><i class="fa fa-share"></i></a>
                </div>
            </div> 
        </div> 
    </li>
    <li class="blog-item">
        <div class="blog-inner">
            <div class="blog-image">
                <img src="/wp-content/uploads/2018/05/natural_Ingredients_02.jpg"/>
            </div>
            <div class="blog-detail-wrap">
                <div class="blog-tile">What is a Creative Fragrance Company?</div>
                <div class="blog-detail text-transform">
                    <div class="blog-author">By Chiristie O'brian</div>
                    <div class="blog-year">23 April 2018</div>
                </div> 
                <div class="blog-extras">
                    <a href="#"><i class="fa fa-heart-o"></i> <span class="count">06</span></a>
                    <a href="#"><i class="fa fa-comment-o"></i> <span class="comments-count"> 28</span></a>
                    <a href="#"><i class="fa fa-share"></i></a>
                </div>
            </div> 
        </div> 
    </li>

        </ul>
            
        </div>
    </div>



<?php get_sidebar(); ?>


    <style>

   
.grid {
    
    list-style: none;
    margin: 30px auto;
    padding: 0;
    color: #fff;
}

.grid li.shown,
.no-js .grid li,
.no-cssanimations .grid li {
	opacity: 1;
}

.grid li {
    display: block;
    float: left;
    padding: 15px;
    width: 33%;
    opacity: 0;
}



/* Effect 8:  */
.grid.effect-8 {
	-webkit-perspective: 1300px;
	perspective: 1300px;
}

.grid.effect-8 li.animate {
	-webkit-transform-style: preserve-3d;
	transform-style: preserve-3d;
	-webkit-transform: scale(0.4);
	transform: scale(0.4);
	-webkit-animation: popUp .8s ease-in forwards;
	animation: popUp .8s ease-in forwards;
}

@-webkit-keyframes popUp {
	0% { }
	70% { -webkit-transform: scale(1.1); opacity: .8; -webkit-animation-timing-function: ease-out; }
	100% { -webkit-transform: scale(1); opacity: 1; }
}

@keyframes popUp {
	0% { }
	70% { -webkit-transform: scale(1.1); transform: scale(1.1); opacity: .8; -webkit-animation-timing-function: ease-out; animation-timing-function: ease-out; }
	100% { -webkit-transform: scale(1); transform: scale(1); opacity: 1; }
}

@media screen and (max-width: 900px) {
	.grid li {
		width: 50%;
	}
}

@media screen and (max-width: 400px) {
	.grid li {
		width: 100%;
	}
}

.blog-inner{
    position: relative;
}

        .blog-inner:before {
    background: -moz-linear-gradient(to bottom, rgba(0, 0, 0, 0) 0, rgba(0,0,0,0.8) 100%);
    background: -webkit-linear-gradient(to bottom, rgba(0, 0, 0, 0) 0, rgba(0,0,0,0.8) 100%);
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0, rgba(0,0,0,0.8) 100%);
    background: -o-linear-gradient(0deg, rgba(0, 0, 0, 0) 0, rgba(0,0,0,0.8) 100%);
    /* Opera 11.10+ */
    background: -ms-linear-gradient(0deg, rgba(0, 0, 0, 0) 0, rgba(0,0,0,0.8) 100%);
    /* IE10+ */
    filter: progid: DXImageTransform.Microsoft.gradient(startColorstr="#00000000", endColorstr="#194b7deb", GradientType=0);
    bottom: 0;
    content: "";
    display: block;
    height: 50%;
    left: 0;
    position: absolute;
    right: 0;
}

        .blog-detail-wrap{
            position: absolute;
            bottom: 0;
            padding: 0 30px;
        }
        .blog-tile{
            font-size: 20px;
            line-height: 25px;
            
        }
        .blog-detail{
            padding: 15px 0;
            border-bottom: 2px solid #fff;
        }
        .blog-extras{
            padding: 10px 0;
         
        }

        .blog-extras a{
            color: #fff;
        }
        </style>



<?php 

    $args = array(
	'post_type' => 'post',
	'post_status' => 'publish',
	'category_name' => 'blog',
	'posts_per_page' => 5,
);
$arr_posts = new WP_Query( $args );

if ( $arr_posts->have_posts() ) : ?>
    <ul>
	<?php while ( $arr_posts->have_posts() ) :
		$arr_posts->the_post();
		?>
		<li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail(); 
            }else{ ?>

<img src="<?php echo the_cfc_field('thumbnail', 'post-thumbnail');?>" alt=""/>
       <?php      } ?>
		
			<header class="entry-header">
				<h1 class="entry-title"><a href="<?php echo get_permalink() ?>"><?php the_title(); ?></a> <p>
  This post currently has
  <?php echo get_comments_number(get_the_ID()); ?>.
</p></h1>
			</header>
			<div class="entry-content">
				<?php the_excerpt(); ?>
			</div>
        </li>
        <?php 	endwhile; ?>
        </ul>
        <?php  endif; ?>


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