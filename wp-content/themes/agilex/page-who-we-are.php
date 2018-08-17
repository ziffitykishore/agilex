<?php
/**
 * Template Name: Who We Are
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>

<div class="main-banner-wrap">
<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
<div class="main-banner">
<?php if($featured_img_url){     
                   $thumbnail_ID = get_post_thumbnail_id( get_the_ID() );
                   $alt_text = get_post_meta( $thumbnail_ID, '_wp_attachment_image_alt', true );  ?>
                    <img src="<?php echo $featured_img_url; ?>" alt="<?php echo $alt_text; ?>"/>
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




<?php 
$whoweare_args = array(
  'post_type' => 'page',
'name' => 'who-we-are',
'post_status'     => 'publish'
); 
$whoweare_arg_query = new WP_Query($whoweare_args);
while ($whoweare_arg_query->have_posts()) {
$whoweare_arg_query->the_post(); 
$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?> 
<div class="main-who-we margin-30  ">
<div class="container">

<div class="inner-content video-content-inner white-bg">
<?php if (get_field('video_image', get_the_ID())){ ?>
<?php $image = get_field('video_image'); ?>
<?php } ?>
<a data-fancybox tabindex="0" href="<?php if (get_field('video_link', get_the_ID())): ?><?php the_field('video_link', get_the_ID()); ?><?php endif; ?> " data-fancybox-type="iframe" class="video-content" style="background: url('<?php echo $image['url'];?> ') no-repeat center center; background-size: cover;">
<span  class="btn-fancy" > 
<span class="play-icon-block">
<span class="fa fa-play"></span>
</span>
</span>
<?php if (get_field('play_video')){ ?>
<span class="text-content lined"><?php the_field('play_video'); ?></span>
<?php } else { ?>
<span class="text-content lined">Play Video</span>
<?php } ?> 
</a>
<div class=" content-desc">
<?php if (get_field('short_description', get_the_ID())){ 
the_field('short_description'); }
else {
echo '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>';
}?>
<?php if (get_field('learn_more_text')){ ?>
<a href="<?php if (get_field('learn_more_link')){ echo the_field('learn_more_link'); } else { echo '#'; } ?>" class="btn-more btn-blue btn-door btn-md btn"><?php the_field('learn_more_text'); ?></a>
<?php }  ?>
</div>
</div>
</div>
</div>
<?php }?>

<div class="executive-wrap margin-30 wow fadeIn">
<?php $query_args = array(

'post_type' => 'page',
'name' => 'executive-leadership',
'post_status'     => 'publish'
);  
$exe_arg_query = new WP_Query($query_args);
while ($exe_arg_query->have_posts()) {
$exe_arg_query->the_post();  ?>
<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
<div class="container">
<div class="heading text-center" >
<h2 class="heading-title"><?php echo the_Title(); ?></h2>
<div class="sub-heading"><?php echo get_the_excerpt();?></div>
</div>
<div class="executive-inner">
<?php if ( has_post_thumbnail() ) {
the_post_thumbnail('full');
} else { ?>
<img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1170X550.png" alt="<?php the_title(); ?>" />
<?php } ?> 
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
<div class="exe-slider-inner flex-sec">
<div class="col-sm-4 col-xs-12 member-img">
<?php if ( has_post_thumbnail() ) {
the_post_thumbnail('full');
} else { ?>
<div class="user-icon-wrap"><div class="user-icon"><i class="fa fa-user fa-4x"></i></div></div>
<?php } ?>
</div>
<div class="col-sm-8 col-xs-12 mem-info">
<div class="exe-personal page-header">
<h3 class="exe-name"><?php echo the_Title(); ?></h3>
<?php if (get_field('linkedin_link')){ ?>
<a href="<?php the_field('linkedin_link'); ?>" class="btn btn-ripple linkedin"><i class="fa fa-linkedin"></i></a>
<?php } ?>

<?php if (get_field('executive_position')){ ?>
  <div class="exe-pos"><?php the_field('executive_position'); ?></div>
<?php } ?>

</div>
<div class="exe-desc">
<?php echo the_Content(); ?>
</div>
</div>
</div>
</div>
<?php }?>
</div>
<div class="slider-nav"> 
<?php  while ($agilex_test_query->have_posts()) {
$agilex_test_query->the_post();
$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'thumbnail'); ?>                                                                                                                                                                                                                                                                                                                                                                                                                                                              ">
<div class="exe-thumb-inner">
  <div class="exe-thumb">
    <?php if($featured_img_url) {?>
    <img src="<?php echo $featured_img_url ?>" alt="<?php echo the_Title(); ?>"/>
    <?php } else { ?>
      <div class="user-icon-wrap"><div class="user-icon"><i class="fa fa-user fa-4x"></i></div></div>
    <?php }?>
  </div>
</div>
<?php }?>
</div>
</div>
</div>
<?php } ?>
</div>




<!-- our history section -->


<?php 
$history_args = array(
  'post_type' => 'page',
'name' => 'our-history',
'post_status'     => 'publish'
); 
$history_arg_query = new WP_Query($history_args);
while ($history_arg_query->have_posts()) {
$history_arg_query->the_post(); 
$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>    
<div class="our-history pos-rel text-center margin-30 wow fadeIn" style="background-image: url('<?php echo $featured_img_url; ?>'); padding: 50px 0;">
<div class="our-history-outer">
<img src="/wp-content/uploads/2018/05/our_history_parallex.jpg" style="display: none;"/> 
<div class="container">
<div class="heading heading-white" >
<h2 class="heading-title"><?php echo the_Title(); ?></h2>
<div class="sub-heading"><?php echo get_the_excerpt();?> </div>
</div>
<div class="history-content-inner col-sm-8 col-sm-offset-2">

<?php if (get_field('short_description')){ ?>
  <div class="history-content">
<?php the_field('short_description') ?>
</div>
<?php }  ?>
<?php if (get_field('learn_more_text')){ ?>
<a href="<?php echo get_permalink() ?>" class="text-uppercase btn btn-lg btn-more btn-ripple btn-door"><?php the_field('learn_more_text'); ?></a>
<?php }  ?>
</div>
</div>
</div>
</div>
<?php } ?>


<!-- affiliations -->


<div class="affiliations-wrap  margin-30 wow fadeInUp">
  <div class="container">
    <?php $args = array(
    'name' => 'our-affiliations',
    'post_type' => 'page',
    'post_status'     => 'publish'
    ); 
    $unique_arg_query = new WP_Query($args);
    while ($unique_arg_query->have_posts()) {
      $unique_arg_query->the_post();  ?>
      <div class="heading text-center">
        <h2 class="heading-title"><?php echo the_Title(); ?></h2>
        <div class="sub-heading"><?php  echo get_the_excerpt();?></div>
      </div>
    <?php } ?>
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
        <div class="affiliate-thumb hover_ani">
          <?php if ( has_post_thumbnail() ) {
            the_post_thumbnail('full');
          } else { ?>
            <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_130X130.png" alt="<?php the_title(); ?>" />
          <?php } ?>
        </div>
    <?php }?>
    </div>
  </div>
</div>


<script>
  $(function(){
      function memberImage(){
        var memImg = $('.member-img').find('img'),
            memimgHeight = memImg.height();
            $('.member-img .user-icon-wrap').css('height', memimgHeight);

      }
      memberImage();
      $(window).on('resize', memberImage);
  });
</script>
<?php get_footer(); ?>                                                                        