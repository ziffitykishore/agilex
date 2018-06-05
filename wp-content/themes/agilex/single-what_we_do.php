<?php
/**
 * Template Name: Single What We Do
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

<div class="page-content-wrap" id="page-content">
  <div class="container">
    <div class="page-desc margin-top--70 wow fadeInUp">
    <?php
    // TO SHOW THE PAGE CONTENTS
    while ( have_posts() ) : the_post(); ?>
    <?php echo get_the_content(); ?>
    <?php
    endwhile; //resetting the page loop
    wp_reset_query(); //resetting the page query
    ?>
    </div>   
  </div>
  </div> 


<?php $tabdetails = get_post_meta( get_the_ID(), 'tabcontent', true );
if($tabdetails) { ?>
  <div class="tab-section sub-category wow fadeInUp">
    <div class="container">
      <ul id="myTabs" class="nav nav-tabs responsive" role="tablist">
      <?php $i = 1; foreach( $tabdetails  as $tabdetail){ ?>
      <li class="<?php echo ($i == 1) ? "active": '';?>"><a role="tab" class="text-uppercase btn-ripple" href="#tab-<?php echo $i; ?>" data-toggle="tab"><?php echo $tabdetail["tab-title"]; ?></a></li>
      <?php $i++; } ?> 
      </ul>
      <div class="tab-content">
      <?php $i = 1;   foreach( $tabdetails  as $tabdetail){ ?>
        <div id="tab-<?php echo $i; ?>" class="tab-pane fade <?php echo ($i == 1) ? "active": '';?> in">
       
          <div class="tab-inner flex-sec effect-milo border-efx">
            <div class="image-wrap flex-sm-100 flex-50 <?php if(empty($tabdetail["tab-image"])) { echo "hidden" ;} ?>">
            <div class="border-ani"></div>
              <div class="image-sec">
              <?php if($tabdetail["tab-image"]) { ?>
              <img src="<?php echo wp_get_attachment_url($tabdetail["tab-image"]); ?>" alt="<?php echo $tabdetail["tab-title"]; ?>"/>
              <?php } else {?>
                <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_585X500.png" alt="Agilex Fragrances" />
              <?php }?>
              </div>
            </div>
            <div class="content-wrap flex-sm-100 <?php if(empty($tabdetail["tab-image"])) {  echo "flex-100";  } else { echo "flex-50" ;}  ?>">
              <div class="feature-title"><?php echo $tabdetail["tab-title"]; ?></div>
              <div class="feature-desc">
                <?php echo $tabdetail["tab-content"]; ?>
              </div>
            </div>
          </div>
       
        </div>
        <?php  $i++; } ?> 
      </div>
    </div>
  </div>
<?php } ?>
 


<?php $exclude_post = $post->ID;
$query = new WP_Query( array( 'post_type' => 'what_we_do', 'order_by' => 'date', 'order' => 'ASC', 'post__not_in' => array( $exclude_post ) ) ); 
if($query->have_posts()){ ?>

<div class="related-categories-wrap wow fadeInUp">
  <div class="container">
  <div class="related-categories-innner flex-sec">
<?php $delay=0;
while ( $query->have_posts() ) { $query->the_post(); ?>
  <div class="category-blk flex-xs-100 flex-sm-30 flex-md-30 flex-20 wow fadeInUp"  data-wow-delay="<?php echo $delay; ?>s">
  <a href="<?php echo get_permalink() ?>" class="img-sec">
    <figure>

  <?php if (get_field('thumb_image', get_the_ID())){ ?>
      <?php $image = get_field('thumb_image'); ?>
      <img class="thumb-image" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
  <?php } else { ?>
      <img  class="thumb-image" src="<?php bloginfo('template_directory'); ?>/images/placeholder_218X180.png" alt="<?php the_title(); ?>" />
      <?php } ?>
      <figcaption><div class="category-title"><?php echo  get_the_title(); ?></div></figcaption>
  </figure>
  </a>
  </div>
<?php  $delay+=0.2; } ?>
  </div>
  </div>
</div>
  <?php }?>
	  
                                                                                                                                                                                                                            
<?php get_footer(); ?>                                                                        