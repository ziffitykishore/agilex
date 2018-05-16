<?php
/**
 * Template Name: Page What We Do
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
<div class="main-banner" style="background: url('<?php echo $featured_img_url; ?>') no-repeat center center; background-size: cover;">
        <img src="<?php echo $featured_img_url; ?>" class="hidden" alt=""/>
        <div class="page-header-content">
       <div class="container">
         <h1><?php echo the_Title(); ?></h1>
         <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
        </div>     
      </div>
 </div>

<div class="what-we-do-wrap " id="what-we-do">
  <div class="container">
    <div class="page-desc margin-top--70 wow fadeInUp">
    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
    </div>   
  </div>
  <div class="categories-wrap">
    <div class="container">
      <div class="categories-inner-wrap">
        <?php $testi_args = array(
        'post_type' => 'what_we_do',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'order_by' => 'date',
        'order' => 'ASC'
        );
        $agilex_test_query = new WP_Query($testi_args);        
        while ($agilex_test_query->have_posts()) {
        $agilex_test_query->the_post();
        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
    
          <div class="categories-blk clearfix wow fadeInUp">
          <?php if ( has_post_thumbnail() ) {
                                    the_post_thumbnail('full');
                                } else { ?>
                                    <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1170X500.png" alt="<?php the_title(); ?>" />
                                <?php } ?>
            <div class="categories-content  wow fadeInUp">
              <h2><a href="<?php echo get_permalink() ?>"><?php echo the_Title(); ?></a></h2>
              <div class="short-desc">
              <?php echo wp_trim_words( get_the_content(), 35, '' ); ?>
              </div>
              <?php if (get_field('learn_more_text')){ ?>
                <a href="<?php echo get_permalink() ?>" class="btn btn-more btn-blue btn-ripple"><?php the_field('learn_more_text'); ?></a>
              <?php } else { ?>
                <a href="<?php echo get_permalink() ?>" class="btn btn-more btn-blue btn-ripple">Learn More</a>
              <?php } ?>
              
            </div>       
          </div>
        <?php }?>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>                                                                        