<?php
/**
 * Template Name: Page How We Do                                                                          
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>

<div class="main-banner-wrap">
  <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>      
    <div class="main-banner">
      <?php if ($featured_img_url){ 
        $thumbnail_ID = get_post_thumbnail_id( get_the_ID() );
        $alt_text = get_post_meta( $thumbnail_ID, '_wp_attachment_image_alt', true ); ?>
        <img src="<?php echo $featured_img_url; ?>" class="" alt="<?php echo $alt_text; ?>"/>
      <?php } else  { ?>
        <img src="<?php bloginfo('template_directory'); ?>/images/blog-header.jpg" class="" alt="<?php echo the_Title(); ?>"/>
      <?php }?>
    </div>
    <div class="page-header-content">
      <div class="container">
        <h1><?php echo the_Title(); ?></h1>
        <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>       
      </div>     
    </div>
</div>

<div class="how-we-work " id="how-we-work">
  <div class="container">
    <div class="page-desc margin-top--70  white-bg">
      <?php
      // TO SHOW THE PAGE CONTENTS
        while ( have_posts() ) : the_post(); ?>
          <?php echo get_the_content(); ?>
        <?php endwhile; //resetting the page loop
        wp_reset_query(); //resetting the page query
      ?>
    </div>   

  <?php $processes = get_post_meta( get_the_ID(), 'how-we-are', true );
  if($processes) { ?>
    <ul class="cd-icons-filling js-cd-icons-filling pad-70">
      
      <?php $i = 1;   foreach( $processes as $process){ ?>
        <li class="cd-service cd-service--<?php echo $i; ?> js-cd-service row">
          <div class="img-blk col-sm-3">
          <?php if($process["process-icon"]) { ?>
          <img src="<?php echo wp_get_attachment_url($process["process-icon"]); ?>" alt="<?php echo $process["process-title"]; ?>"/>
          <?php }?>
          </div>
          <div class="content-detail col-sm-9">
          <div class="heading">
          <h2 class="heading-title"><?php echo $process["process-title"]; ?></h2>
          </div>
          <div class="content-desc"><?php echo $process["process-content"]; ?></div>
          </div>
        </li>
      <?php  $i++; } ?> 
      <div class="before-bg">
        <div class="inner-elment top-bg"></div>
        <div class="inner-elment bottom-bg"></div>
      </div>
    </ul>

  <?php } ?>
  </div>
</div>

<script>

  jQuery(function($){
  function iconFilling(){
    
    var offset = $(".cd-service .img-blk img").offset().left + 1;
  var width = $(".cd-service .img-blk img").width() - 2;
  jQuery('.before-bg').css('left', offset);
  console.log(offset);
  jQuery('.before-bg').css('width', width);
    
  
  $(window).scroll(function() {
  /* $(".bottom-bg").height($(".cd-icons-filling").height() + $(".cd-icons-filling").offset().top - (($(window).height() / 2) + $(window).scrollTop()))  */
  });
  }
  iconFilling();
  $(window).load(function(){
    setTimeout(() => {
      iconFilling();    
    }, 200);
    
  });
  $(window).resize(iconFilling);
  
  });
  
	
	
  jQuery.fn.aPosition = function() {
    thisLeft = this.offset().left;
    thisTop = this.offset().top;
    thisParent = this.parent();

    parentLeft = thisParent.offset().left;
    parentTop = thisParent.offset().top;

    return {
        left: thisLeft-parentLeft,
        top: thisTop-parentTop
    };
};
  


/* icon filling effects */

function IconsFilling( element ) {
    this.element = element;
    this.blocks = this.element.getElementsByClassName("js-cd-service");
    this.update();
}

IconsFilling.prototype.update = function() {
    if ( !"classList" in document.documentElement ) {
        return;
    }
    this.selectBlock();
    this.changeBg();
};

IconsFilling.prototype.selectBlock = function() {
    for(var i = 0; i < this.blocks.length; i++) {
        ( this.blocks[i].getBoundingClientRect().top < window.innerHeight/2 ) ? this.blocks[i].classList.add("cd-service--focus") : this.blocks[i].classList.remove("cd-service--focus");
    }
};

IconsFilling.prototype.changeBg = function() {
    removeClassPrefix(this.element, 'cd-icons-filling--new-color-');
    this.element.classList.add('cd-icons-filling--new-color-' + (Number(this.element.getElementsByClassName("cd-service--focus").length) - 1));
};

var iconsFillingContainer = document.getElementsByClassName("js-cd-icons-filling"),
    iconsFillingArray = [],
    scrolling = false;
if( iconsFillingContainer.length > 0 ) {
    for( var i = 0; i < iconsFillingContainer.length; i++) {
        (function(i){
            iconsFillingArray.push(new IconsFilling(iconsFillingContainer[i]));
        })(i);
    }

    /* update active block on scrolling */
    window.addEventListener("scroll", function(event) {
        if( !scrolling ) {
            scrolling = true;
            (!window.requestAnimationFrame) ? setTimeout(checkIconsFilling, 250) : window.requestAnimationFrame(checkIconsFilling);
        }
    });
}

function checkIconsFilling() {
    iconsFillingArray.forEach(function(iconsFilling){
        iconsFilling.update();
    });
    scrolling = false;
}

function removeClassPrefix(el, prefix) {
    /* remove all classes starting with 'prefix' */
    var classes = el.className.split(" ").filter(function(c) {
        return c.indexOf(prefix) < 0;
    });
    el.className = classes.join(" ");
}


/* icon filling effects */
  </script>

<?php get_footer(); ?>                                                                        