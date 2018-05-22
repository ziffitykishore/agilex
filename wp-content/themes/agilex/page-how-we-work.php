<?php
/**
 * Template Name: Page What We Do
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>

<style>
.cd-icons-filling {
  width: 90%;
  max-width: 1170px;
  margin: 0 auto;
  /* hide ::after pseudo element - fix for Edge 15 and below */
  overflow: hidden;
  position: relative;
}

.cd-icons-filling .before-bg{
  position: absolute;
  top: 0;
  bottom: 0;
}

.cd-icons-filling .before-bg:before, .cd-icons-filling .before-bg:after {
  /* the 2 underneath colored sections */
  /* fix flickering on Edge 15 and below */
  content: '/';
  color: transparent;
  position: fixed;
  /* trick to remove flickering on resize */
  width: calc(90% - 2px);
  max-width: 1170px;
  left: 50%;
  right: auto;
  -webkit-transform: translateX(-50%);
      -ms-transform: translateX(-50%);
          transform: translateX(-50%);
  height: 50vh;
  z-index: -1;
}

.cd-icons-filling .before-bg:before {
  /* fix bug - ::before element visible before starting scrolling */
  top: -1px;
  background-color: #f4bd89;
  -webkit-transition: all 0.8s;
  transition: all 0.8s;
}

.cd-icons-filling .before-bg:after {
  top: 50%;
  background-color: #71495b;
}

@media only screen and (min-width: 1170px) {
  .cd-icons-filling.cd-icons-filling--new-color-1::before {
    background-color: #c06c69;
  }
  .cd-icons-filling.cd-icons-filling--new-color-2::before {
    background-color: #bf69c0;
  }
  .cd-icons-filling.cd-icons-filling--new-color-3::before {
    background-color: #699ec0;
  }
}

.cd-service {
  position: relative;
  z-index: 2;
  min-height: 50px;
  margin-left: 56px;
  background-color: #3e253c;
  padding: 1em 1em 4em;
}

.cd-service::before, .cd-service::after {
  content: '';
  position: absolute;
  width: 56px;
  right: 100%;
  z-index: 2;
}

.cd-service::before {
  top: 0;
  height: 50px;
  background-repeat: no-repeat;
}

.cd-service::after {
  top: 50px;
  bottom: 0;
  background-image: url("https://codyhouse.co/demo/icons-filling-effect/img/cd-pattern-small.svg");
  background-repeat: repeat-y;
}

.cd-service.cd-service--divider::after {
  top: 0;
}

.cd-service.cd-service--divider:last-child {
  display: none;
}

.cd-service.cd-service--1::before {
  background-image: url("https://codyhouse.co/demo/icons-filling-effect/img/cd-icon-1-small.svg");
}

.cd-service.cd-service--2::before {
  background-image: url("https://codyhouse.co/demo/icons-filling-effect/img/cd-icon-2-small.svg");
}

.cd-service.cd-service--3::before {
  background-image: url("https://codyhouse.co/demo/icons-filling-effect/img/cd-icon-3-small.svg");
}

.cd-service.cd-service--4::before {
  background-image: url("https://codyhouse.co/demo/icons-filling-effect/img/cd-icon-4-small.svg");
}

.cd-service h2 {
  text-transform: uppercase;
  color: white;
  margin-bottom: 1em;
  font-family: "Merriweather Sans", sans-serif;
}

.cd-service p {
  font-size: 1.4rem;
  line-height: 1.4;
  color: rgba(255, 255, 255, 0.5);
}

@media only screen and (min-width: 1170px) {
  .cd-service {
    min-height: 525px;
    margin-left: 420px;
    padding: 6em 2em;
  }
  .cd-service::before, .cd-service::after {
    width: 420px;
  }
  .cd-service::before {
    height: 325px;
  }
  .cd-service::after {
    top: 325px;
    background-image: url("https://codyhouse.co/demo/icons-filling-effect/img/cd-pattern-large.svg");
  }
  .cd-service.cd-service--divider:first-child, .cd-service.cd-service--divider:last-child {
    min-height: 50px;
    padding: 0;
  }
  .cd-service.cd-service--divider:last-child {
    display: block;
  }
  .cd-service.cd-service--1::before {
    background-image: url("https://codyhouse.co/demo/icons-filling-effect/img/cd-icon-1-large.svg");
  }
  .cd-service.cd-service--2::before {
    background-image: url("https://codyhouse.co/demo/icons-filling-effect/img/cd-icon-2-large.svg");
  }
  .cd-service.cd-service--3::before {
    background-image: url("https://codyhouse.co/demo/icons-filling-effect/img/cd-icon-3-large.svg");
  }
  .cd-service.cd-service--4::before {
    background-image: url("https://codyhouse.co/demo/icons-filling-effect/img/cd-icon-4-large.svg");
  }
  .cd-service h2, .cd-service p {
    color: #71495b;
    -webkit-transition: color 0.5s;
    transition: color 0.5s;
  }
  .cd-service h2 {
    font-size: 3rem;
  }
  .cd-service p {
    font-size: 1.8rem;
    line-height: 1.6;
  }
  .cd-service.cd-service--focus h2 {
    color: white;
  }
  .cd-service.cd-service--focus p {
    color: rgba(255, 255, 255, 0.5);
  }
}
</style>
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

<ul class="cd-icons-filling js-cd-icons-filling">

  <div class="before-bg"></div>
		<li class="cd-service cd-service--divider"></li>

		<li class="cd-service cd-service--1 js-cd-service">
			<h2>Web Design</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perferendis pariatur tenetur quod veritatis nulla aspernatur architecto! Fugit, reprehenderit amet deserunt molestiae ut libero facere quasi velit perferendis ullam quis necessitatibus!</p>
		</li> <!-- cd-service -->

		<li class="cd-service cd-service--2 js-cd-service">
			<h2>Responsive Approach</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perferendis pariatur tenetur quod veritatis nulla aspernatur architecto! Fugit, reprehenderit amet deserunt molestiae ut libero facere quasi velit perferendis ullam quis necessitatibus!</p>
		</li> <!-- cd-service -->

		<li class="cd-service cd-service--3 js-cd-service">
			<h2>E-commerce</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perferendis pariatur tenetur quod veritatis nulla aspernatur architecto! Fugit, reprehenderit amet deserunt molestiae ut libero facere quasi velit perferendis ullam quis necessitatibus!</p>
		</li> <!-- cd-service -->

		<li class="cd-service cd-service--4 js-cd-service">
			<h2>CMS Integration</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perferendis pariatur tenetur quod veritatis nulla aspernatur architecto! Fugit, reprehenderit amet deserunt molestiae ut libero facere quasi velit perferendis ullam quis necessitatibus!</p>
		</li> <!-- cd-service -->

		<li class="cd-service cd-service--divider"></li>
	</ul> <!-- cd-services -->

<script>
  (function(){
	function IconsFilling( element ) {
		this.element = element;
		this.blocks = this.element.getElementsByClassName("js-cd-service");
		this.update();
	};

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

		//update active block on scrolling
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
	};

	function removeClassPrefix(el, prefix) {
		//remove all classes starting with 'prefix'
        var classes = el.className.split(" ").filter(function(c) {
            return c.indexOf(prefix) < 0;
        });
        el.className = classes.join(" ");
	};
})();
  </script>
<?php get_footer(); ?>                                                                        