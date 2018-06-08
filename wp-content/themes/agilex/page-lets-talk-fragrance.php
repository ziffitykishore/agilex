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

     
<div class="contact-form-sec">
  <div class="container">
    <div class="form-sec-inner flex-sec margin-top--150 white-bg">
      <div class="flex-sm-100 flex-md-60 flex-70 pad-25 contact-form">
        <?php echo do_shortcode('[contact-form-7 id="300" title="Contact Us"]') ?>
      </div>
      <div class="flex-sm-100 flex-md-40 flex-30 pad-25 address-section">
        <div class="address-wrap">
          <h2>Creative Center</h2>
          <address>
            Agilex Fragrances
            140 Centennial Avenue
            Piscataway, NJ 08854
          </address>
        </div>
        <div class="manufacture-wrap border-sec">
          <h2>Manufacturing Center</h2>
          <div class="text-uppercase location-point">Somerset, <strong>New Jersey</strong></div>
          <div class="text-uppercase location-point">Zhaoqing, <strong>China</strong></div>
        </div>
        <div class="phone-wrap">
          <a href="#" class=""><span class="icon icon-ph "><img src="/wp-content/uploads/2018/05/icon_telephone_white.png" alt="" /></span> 800.542.7662</a><a href="#" class=""><span class="icon icon-ph "><img src="/wp-content/uploads/2018/05/icon_fax_white.png" alt="" /></span> 732.393.7378</a>
        </div>
        <div class="social-links">
        <span class="text-uppercase">Follows us:</span> <a class="social-links" title="Pinterest" href="#"><span class="sr-only">Pinterest</span> <i class="fa fa-pinterest-p"></i></a><a class="social-links" title="Instagram" href="#"><span class="sr-only">Instagram</span> <i class="fa fa-instagram"></i></a><a class="social-links" title="Linked In" href="#"><span class="sr-only">Linked In</span> <i class="fa fa-linkedin"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>
      
	<?php get_footer(); ?>