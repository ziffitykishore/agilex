<?php
/**
 * Template Name: Page Contact Us
 *
 * @package Agilex
 * @since Agilex 1.0
 */

  get_header(); ?>
  
<div class="main-banner-wrap contact-page">
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

      <?php $contact_sidebar = get_field('contact_sidebar'); ?>
<div class="contact-form-sec">
  <div class="container">
    <div class="form-sec-inner flex-sec margin-top--150 white-bg">
      <div class="flex-sm-100 flex-md-60  pad-25 contact-form <?php if($contact_sidebar){ echo 'flex-70'; } else { echo 'flex-100'; } ?>">
         
         <?php echo get_field('contact_form_7');  ?>
      </div>

      
           <?php  if($contact_sidebar){ ?>
      <div class="flex-sm-100 flex-md-40 flex-30 pad-25 address-section">       
              <?php echo $contact_sidebar ; ?>
      </div>
    <?php }?>
    </div>
  </div>
</div>
      
	<?php get_footer(); ?>