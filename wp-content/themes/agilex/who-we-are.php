<?php
/**
 * Template Name: Who We Are
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<div class="main-banner" style="background: url(/wp-content/uploads/2018/05/sub_banner_who_we_are.jpg) no-repeat center center; background-size: cover;">
        <img src="" alt=""/>
        <div class="page-header-content">
       <div class="container">
         <h1><?php echo the_Title(); ?></h1>
         <h2><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></h2>
        </div>     
      </div>
</div>
<div class="main-who-we margin-30 wow fadeInUp">
<div class="container">

<?php 
if ( have_posts() ) : while ( have_posts() ) : the_post();
  the_content();
endwhile;
else: ?>
  <p><?php _e('Sorry, no posts matched your criteria.'); ?></p><?php 
endif;?>
</div>
</div>

  <div class="executive-wrap margin-30 wow fadeInUp">
    <div class="container">
      <div class="heading" >
        <div class="heading-title">Executive Leadership</div>
        <div class="sub-heading">Nesciunt tofu stumptown aliqua retro synth master cleanse</div>
      </div>
      <div class="executive-inner">
        <img src="/wp-content/uploads/2018/05/executive_group_photo.jpg" alt=""/>
        <div class="executive-carousel slider-for">
        <?php $testi_args = array(
        'post_type' => 'member',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'order_by' => 'date',
        'order' => 'ASC'
        );
        $agilex_test_query = new WP_Query($testi_args); ?>
        <?php 
        while ($agilex_test_query->have_posts()) {
          $agilex_test_query->the_post();
          $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
            <div class="slider-sec">
              <div class="col-sm-4 member-img">
               <?php the_post_thumbnail('full'); ?>
              </div>
              <div class="col-sm-8 mem-info">
                <div class="exe-personal page-header">
                  <div class="exe-name"><?php echo the_Title(); ?></div>
                  <div class="exe-pos">
                    <?php if (get_field('executive_position')){ ?>
                     <?php the_field('executive_position'); ?>
                    <?php } else { echo 'Lorem Ipsum' ?>
                    <?php } ?></div>
                  </div>
                <div class="exe-desc">
                  <?php echo the_Content(); ?>
                </div>
              </div>
            </div>
        <?php }?>
      </div>
      <div class="slider-nav"> 
        <?php  while ($agilex_test_query->have_posts()) {
          $agilex_test_query->the_post();
          $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>                                                                                                                                                                                                                                                                                                                                                                                                                                                              ">
          <div class="exe-thumb-inner"><div class="exe-thumb"><?php the_post_thumbnail('full'); ?></div></div>
        <?php }?>
      </div>
    </div>
    </div>
  </div>







<?php 
$history_args = array(
'name' => 'our-history',
'post_status'     => 'publish'
); 
$history_arg_query = new WP_Query($history_args);
while ($history_arg_query->have_posts()) {
$history_arg_query->the_post(); 
$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>    
  <div class="our-history pos-rel text-center margin-30 wow fadeInUp" style="background: url('<?php echo $featured_img_url; ?>') no-repeat center center; background-attachment: fixed; background-size: cover; padding: 8% 0;">
    <div class="our-history-outer">
      <img src="/wp-content/uploads/2018/05/our_history_parallex.jpg" style="display: none;"/> 
      <div class="container">
        <div class="heading heading-white" >
          <div class="heading-title"><?php echo the_Title(); ?></div>
          <div class="sub-heading"><?php echo get_the_excerpt();?> </div>
        </div>
        <div class="history-content-inner col-sm-8 col-sm-offset-2">
          <?php echo the_Content(); ?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>



<div class="affiliations-wrap  margin-30 wow fadeInUp">
    <div class="container">
      <div class="heading">
      <div class="heading-title">Our Affiliations</div>
        <div class="sub-heading">Nesciunt tofu stumptown aliqua retro synth master cleanse</div>
      </div>
      <div class="affiliations-outer" id="affiliate-slider">
      <?php $testi_args = array(
        'post_type' => 'affiliation',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'order_by' => 'date',
        'order' => 'ASC'
        );
        $agilex_test_query = new WP_Query($testi_args); ?>
        <?php 
        while ($agilex_test_query->have_posts()) {
          $agilex_test_query->the_post();
          $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>

          <div class="affiliate-thumb">
          <?php if ( has_post_thumbnail() ) {
                  the_post_thumbnail('full');
              } else { ?>
                  <div class="placeholder"><i class="fa fa-image"></i></div>
           <?php } ?>
          </div>
          
          
          
          
          
          
          
          
          <?php }?>
      </div>
    </div>
</div>



<?php get_footer(); ?>                                                                        