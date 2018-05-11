<?php
/**
 * Template for displaying all single posts
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<div class="main-banner">
        <img src="/wp-content/uploads/2018/05/sub_banner_who_we_are.jpg" alt=""/>
       <div class="container">
         <h1><?php echo the_Title(); ?></h1>
         <h2><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></h2>
        </div>     
</div>
<div class="main-who-we">
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

<div class="executive-wrap">
  <div class="container">
    <div class="heading" >
      <div class="heading-title">Executive Leadership</div>
      <div class="sub-heading">Nesciunt tofu stumptown aliqua retro synth master cleanse</div>
    </div>
    <div class="executive-inner">
      <img src="/wp-content/uploads/2018/05/executive_group_photo.jpg" alt=""/>
      <div class="executive-carousel slider-for">
        <div class="slider-sec row">
          <div class="col-sm-4 member">
              <img src="/wp-content/uploads/2018/05/joy_atkinson.jpg" alt="">
          </div>
          <div class="col-sm-8 mem-info">
            <div class="exe-personal page-header"><div class="exe-name">Joy Atkinson</div><div class="exe-pos">President</div></div>
            <div class="exe-desc">
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p> 
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
            </div>
          </div>
        </div>
        <div class="slider-sec row">
          <div class="col-sm-4 member">
              <img src="/wp-content/uploads/2018/05/photo_2.jpg" alt="">
          </div>
          <div class="col-sm-8 mem-info">
            <div class="exe-personal page-header"><div class="exe-name">Joy Atkinson</div><div class="exe-pos">President</div></div>
            <div class="exe-desc">
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p> 
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
            </div>
          </div>
        </div>
        <div class="slider-sec row">
          <div class="col-sm-4 member">
              <img src="/wp-content/uploads/2018/05/photo_3.jpg" alt="">
          </div>
          <div class="col-sm-8 mem-info">
            <div class="exe-personal page-header"><div class="exe-name">Joy Atkinson</div><div class="exe-pos">President</div></div>
            <div class="exe-desc">
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p> 
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
            </div>
          </div>
        </div>
        <div class="slider-sec row">
          <div class="col-sm-4 member">
              <img src="/wp-content/uploads/2018/05/photo_4.jpg" alt="">
          </div>
          <div class="col-sm-8 mem-info">
            <div class="exe-personal page-header"><div class="exe-name">Joy Atkinson</div><div class="exe-pos">President</div></div>
            <div class="exe-desc">
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p> 
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
            </div>
          </div>
        </div>
        <div class="slider-sec row">
          <div class="col-sm-4 member">
              <img src="/wp-content/uploads/2018/05/photo_3.jpg" alt="">
          </div>
          <div class="col-sm-8 mem-info">
            <div class="exe-personal page-header"><div class="exe-name">Joy Atkinson</div><div class="exe-pos">President</div></div>
            <div class="exe-desc">
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p> 
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
            </div>
          </div>
        </div>
        <div class="slider-sec row">
          <div class="col-sm-4 member">
              <img src="/wp-content/uploads/2018/05/photo_2.jpg" alt="">
          </div>
          <div class="col-sm-8 mem-info">
            <div class="exe-personal page-header"><div class="exe-name">Joy Atkinson</div><div class="exe-pos">President</div></div>
            <div class="exe-desc">
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p> 
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
            </div>
          </div>
        </div>
        <div class="slider-sec row">
          <div class="col-sm-4 member">
              <img src="/wp-content/uploads/2018/05/photo_4.jpg" alt="">
          </div>
          <div class="col-sm-8 mem-info">
            <div class="exe-personal page-header"><div class="exe-name">Joy Atkinson</div><div class="exe-pos">President</div></div>
            <div class="exe-desc">
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p> 
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
            </div>
          </div>
        </div>
        
      </div>
      <div class="slider-nav col-sm-6 col-sm-offset-3                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             ">
          <div class="exe-thumb-inner"><div class="exe-thumb"><img src="/wp-content/uploads/2018/05/joy_atkinson.jpg" alt=""></div></div>
          <div class="exe-thumb-inner"><div class="exe-thumb"><img src="/wp-content/uploads/2018/05/photo_2.jpg" alt=""></div></div>
          <div class="exe-thumb-inner"><div class="exe-thumb"><img src="/wp-content/uploads/2018/05/photo_3.jpg" alt=""></div></div>
          <div class="exe-thumb-inner"><div class="exe-thumb"><img src="/wp-content/uploads/2018/05/photo_4.jpg" alt=""></div></div>
          <div class="exe-thumb-inner"><div class="exe-thumb"><img src="/wp-content/uploads/2018/05/joy_atkinson.jpg" alt=""></div></div>
          <div class="exe-thumb-inner"><div class="exe-thumb"><img src="/wp-content/uploads/2018/05/photo_2.jpg" alt=""></div></div>
          <div class="exe-thumb-inner"><div class="exe-thumb"><img src="/wp-content/uploads/2018/05/photo_3.jpg" alt=""></div></div>
         
      </div>
    </div>
  </div>
</div>


<div class="our-history pos-rel text-center" style="background: url(/wp-content/uploads/2018/05/our_history_parallex.jpg) no-repeat center center; background-attachment: fixed; background-size: cover; padding: 8% 0;">
  <div class="our-history-outer">
    <img src="/wp-content/uploads/2018/05/our_history_parallex.jpg" style="display: none;"/> 
    <div class="container">
      <div class="heading heading-white" >
        <div class="heading-title">Our History</div>
        <div class="sub-heading">Nesciunt tofu stumptown aliqua retro synth master cleanse</div>
      </div>
      <div class="history-content-inner col-sm-8 col-sm-offset-2">
        <div class="history-content">
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p> 
        <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
</div>
        <a href="#" class="text-uppercase btn btn-lg btn-ripple btn-txt-white border-white-2x ">Do you want to know more about us?</a>
      </div>
    </div>
  </div>
</div>


<div class="affiliations-wrap">
    <div class="container">
      <div class="heading">
      <div class="heading-title">Our Affiliations</div>
        <div class="sub-heading">Nesciunt tofu stumptown aliqua retro synth master cleanse</div>
      </div>
      <div class="affiliations-outer" id="affiliate-slider">

          <div class="affiliate-thumb">
              <img src="/wp-content/uploads/2018/05/logo_oregon_tilth.png" alt=""/>
          </div>
          <div class="affiliate-thumb">
              <img src="/wp-content/uploads/2018/05/logo_good_manfacturing_practice.png" alt=""/>
          </div>
          <div class="affiliate-thumb">
              <img src="/wp-content/uploads/2018/05/logo_fair_for_life.png" alt=""/>
          </div>
          <div class="affiliate-thumb">
              <img src="/wp-content/uploads/2018/05/logo_bbb.png" alt=""/>
          </div>
          <div class="affiliate-thumb">
              <img src="/wp-content/uploads/2018/05/logo_crc.png" alt=""/>
          </div>
          <div class="affiliate-thumb">
              <img src="/wp-content/uploads/2018/05/logo_oregon_tilth.png" alt=""/>
          </div>
          <div class="affiliate-thumb">
              <img src="/wp-content/uploads/2018/05/logo_good_manfacturing_practice.png" alt=""/>
          </div>
          <div class="affiliate-thumb">
              <img src="/wp-content/uploads/2018/05/logo_fair_for_life.png" alt=""/>
          </div>
          <div class="affiliate-thumb">
              <img src="/wp-content/uploads/2018/05/logo_bbb.png" alt=""/>
          </div>
      </div>
    </div>
</div>



<?php get_footer(); ?>                                                                        
