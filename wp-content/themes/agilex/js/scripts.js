/**
 * Functionality specific to Bootstrap Canvas WP.
 *
 * Provides helper functions to enhance the theme experience.
 */


/* preloader */







function myFunction() {
  var x = document.getElementById("primary-menu");
  if (x.className === "nav-menu") {
      x.className += " responsive";
  } else {
      x.className = "nav-menu";
  }
}
(function($) {
  $(document).ready(function() {

    $('select').niceSelect();

      /* Scroll down based on Hash tag */
      var header = jQuery('.header-container ').outerHeight(); /* Get the Height from .header-container */
      jQuery('.scroll-down').on('click', function(e) {
          e.preventDefault();
          jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr('href')).offset().top - header }, 500, 'linear');
      });

      $('body').addClass('loading');
      jQuery(window).on('load', function() {

          jQuery('.loader').removeClass('scale');

          $('body').removeClass('loading');
      });



      $("#searchsubmit, #commentform #submit").addClass("btn btn-default");
      $(
          'button, html input[type="button"], input[type="reset"], input[type="submit"]'
      ).addClass("btn btn-default");
      $(
          'input:not(button, html input[type="button"], input[type="reset"], input[type="submit"]), input[type="file"], select, textarea'
      ).addClass("form-control");
      if (
          $("label")
          .parent()
          .not("div")
      ) {
          $("label:not(#searchform label,#commentform label)").wrap("<div></div>");
      }
      $("table").addClass("table table-bordered");
      $(".attachment-thumbnail").addClass("thumbnail");
      $("embed-responsive-item,iframe,embed,object,video")
          .parent()
          .addClass("embed-responsive embed-responsive-16by9");
      $(".navbar-nav").addClass("blog-nav");
      $(
              ".dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:hover, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:focus"
          )
          .closest(".navbar-nav")
          .removeClass("blog-nav");
      $(".navbar-nav li").each(function() {
          /* $(this)
            .find(".sub-menu")
            .addClass("dropdown-menu"); */
          /* if (!$(this).hasClass("dropdown")) {
            $(this).addClass("dropdown");
          } */
      });

      $(".overlaybgDark").on("click", function(event) {
        $("body").removeClass("navigation-is-open");
        event.preventDefault();
    });

    var isLateralNavAnimating = false;
    $(".cd-nav-trigger").on("click", function(event) {
        $("body").toggleClass("navigation-is-open").removeClass('search-open');
        event.preventDefault();
    });

      $(
          '<span class="caret"></span>'
      ).insertBefore("#primary-menu .menu-item-has-children > .sub-menu");
      $("#primary-menu a").addClass("ripple-link");


      $(".cd-primary-nav .navbar-nav li").each(function() {
            var menuLink = $(this).find('a'),
                menuText = menuLink.text();
            

        $('<span class="clip-wrap"><span>'+ menuText +'</span></span>').appendTo(menuLink);

      });

      
      jQuery("#primary-menu .caret").on("click", function(e) {
          if (
              jQuery(this)
              .parent()
              .has("ul")
          ) {
              e.preventDefault();
          }
          $(this)
              .next("ul")
              .slideToggle();
          $(this)
              .parent()
              .toggleClass('menu-open');
          $(this)
              .parent()
              .siblings("li")
              .find("ul")
              .slideUp();
          $(this)
              .parent()
              .siblings("li")
              .removeClass('menu-open');
      });




      /* search container */

      $(document).click(function() {
        $('body').removeClass('search-open');
    });
      var mainContainer = $('.main-content'),
          openCtrl = $('.search-trigger'),
          closeCtrl = $('.search-close'),
          searchContainer = $('.search-wrap'),
          inputSearch = searchContainer.find('#search__input');


      initEvents();

        searchContainer.on('click', function(e){
            e.stopPropagation();
        });

      function initEvents() {
          openCtrl.on('click', openSearch);
          closeCtrl.on('click', closeSearch);
          $(document).on('keyup', function(ev) {
              /*  escape key. */
              if (ev.keyCode == 27) {
                  closeSearch();
              }
          });
      }

      function openSearch(e) {

          $('body').toggleClass('search-open').removeClass('navigation-is-open');
          mainContainer.toggleClass('main-wrap--move');
          
          
          
          setTimeout(function() {
              inputSearch.focus();
          }, 600);

          
      }

      function closeSearch() {
          $('body').removeClass('search-open');
          mainContainer.removeClass('main-wrap--move');
          
          inputSearch.blur();
          inputSearch.value = '';
      }






  });





  /* reset the image tag */

  jQuery("img")
      .removeAttr("width")
      .removeAttr("height");

  /* Home page slider settings */

  $('body').toggleClass('');

  $(window).on('load', function() {

      $("#hero-slider").slick({
          dots: true,
          infinite: true,
          speed: 2500,
          fade: true,
          cssEase: 'linear',
          arrows: false,
          autoplay: true,
          autoplaySpeed: 8000
      });





  });


  jQuery('#hero-slider').on('init', function(e, slick) {
      var $firstAnimatingElements = jQuery('#hero-slider .slick-slide:first-child').find('[data-animation]');
      doAnimations($firstAnimatingElements);
  });
  jQuery('#hero-slider').on('beforeChange', function(e, slick, currentSlide, nextSlide) {
      var $animatingElements = jQuery('#hero-slider .slick-slide[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
      doAnimations($animatingElements);
  });

  function doAnimations(elements) {
      var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
      elements.each(function() {
          var $this = jQuery(this);
          var $animationDelay = $this.data('delay');
          var $animationType = 'animated ' + $this.data('animation');
          $this.css({
              'animation-delay': $animationDelay,
              '-webkit-animation-delay': $animationDelay
          });
          $this.addClass($animationType).one(animationEndEvents, function() {
              $this.removeClass($animationType);
          });
      });
  }

  function bgSource(imgcontainer){
  $(imgcontainer).each(function() {
      var img = $(this).find("img");
      var height = img.height();
      var img_src = img.attr("src");

      $(this).css({
          "background-image": "url(" + img_src + ")",
          "background-size": "cover",
          "background-repeat": "no-repeat",
          "background-position": "center"
      });

      img.hide();
  });
}

bgSource('#hero-slider .slider-blk');

bgSource('#what-we-do .categories-blk');

bgSource('.news-sec-blk .image-sec');

bgSource('.news-sec-blk .image-sec');

bgSource('.no-touch .sub-category .image-sec');



  $(".btn-ripple").click(function(e) {
     /* Remove any old one */
      $(".ripple").remove();

      /*  Setup */
      var posX = $(this).offset().left,
          posY = $(this).offset().top,
          buttonWidth = $(this).width(),
          buttonHeight = $(this).height();

      /* Add the element */
      $(this).prepend("<span class='ripple'></span>");

      /*  Make it round! */
      if (buttonWidth >= buttonHeight) {
          buttonHeight = buttonWidth;
      } else {
          buttonWidth = buttonHeight;
      }

      /* Get the center of the element */
      var x = e.pageX - posX - buttonWidth / 2;
      var y = e.pageY - posY - buttonHeight / 2;

      /*  Add the ripples CSS and start the animation */
      $(".ripple")
          .css({
              width: buttonWidth,
              height: buttonHeight,
              top: y + "px",
              left: x + "px"
          })
          .addClass("rippleEffect");
  });

  /* Magic line floating effects */

  var $el,
      leftPos,
      newWidth,
      $mainNav = $("#myTabs");
  if ($mainNav.length) {
      $mainNav.append("<span id='magic-line'></span>");
      var $magicLine = $("#magic-line");

      $magicLine
          .width($(".active").width())
          .css("left", $(".active a").position().left)
          .data("origLeft", $magicLine.position().left)
          .data("origWidth", $magicLine.width());

      $("#myTabs li a").hover(
          function() {
              $el = $(this);
              leftPos = $el.position().left;
              newWidth = $el.parent().width();
              $magicLine.stop().animate({
                  left: leftPos,
                  width: newWidth
              });
          }
          /* , function() {
                      $magicLine.stop().animate({
                          left: $magicLine.data("origLeft"),
                          width: $magicLine.data("origWidth")
                      });
                  } */
      );
  }

  $(".reviews-inner").slick({
      autoplay: true,
      autoplaySpeed: 3000,
      dots: true,
      fade: true,
      arrows: false
  });

  //TO TOP BUTTON ---------------------------------------------------------------------/

  /******************************
        BOTTOM SCROLL TOP BUTTON
     ******************************/

  /*  declare variable */
  var scrollTop = $(".scroll-top");

 /* $(window).scroll(function() {
      // declare variable
      var topPos = $(this).scrollTop();

      // if user scrolls down - show scroll to top button
      if (topPos > 100) {
          $(scrollTop).css("opacity", "1");

      } else {
          $(scrollTop).css("opacity", "0");
      }

  }); */ 

  //Click event to scroll to top
  $(scrollTop).click(function() {
      
      $('html, body').animate({
          scrollTop: 0
      }, 800);
      
      
  }); /*  click() scroll top End */

  //HEADER SHADOW ---------------------------------------------------------------------/
  var animatedHeader = false,
      header = $(".header-container"),
      headerOffset = header.offset().top;

  $(window).scroll(function() {
      if ($(window).scrollTop() > headerOffset) {
          header.addClass("header-sticky");
      } else {
          header.removeClass("header-sticky");
          header.removeClass("header-top");
      }
  });


function sliderHover(element){
  $(element).mouseover(function(){
    $(element).removeClass("js_active").addClass("no_active");
    $(this).removeClass( "no_active").addClass("js_active");
}).mouseout(function(){
    $(element).removeClass( "no_active").removeClass("js_active");
});
}


sliderHover('.affiliate-thumb');



 




  /* Fancybox load */

  $('.fancybox').fancybox({
      padding: 0,
      aspectRatio: true,
      'allowfullscreen': 'true'
  });

  /*----- Add active class for opended panel */
  $(".panel-group .panel-collapse.in")
      .prev()
      .addClass("active");
  $(".panel").on("show.bs.collapse hide.bs.collapse", function(e) {
      if (e.type == "show") {
          $(this).addClass("active");
      } else {
          $(this).removeClass("active");
      }
  });



  

  /* initialize the wow script */
  var wow = new WOW({
      boxClass: 'wow', 
      animateClass: 'animated', 
      offset: 150, 
      mobile: true,
      live: true, 
     
      scrollContainer: null 
  });
  wow.init();

  



  var parallaxSettings = {
      initialOpacity: 1, /* from 0 to 1, e.g. 0.34 is a valid value. 0 = transparent, 1 = Opaque */
      opacitySpeed: 0.1, /* values from 0.01 to 1 -> 0.01: slowly appears on screen; 1: appears as soon as the user scrolls 1px */
      pageLoader: false
  };

  parallaxImgScroll(parallaxSettings);


  /* executive slider */

  $('.slider-for').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: false,
      fade: true,
      asNavFor: '.slider-nav'
  });
  $('.slider-nav').slick({
      slidesToShow: 4,
      slidesToScroll: 1,
      asNavFor: '.slider-for',
      arrows: true,
      prevArrow: '<a href="#" class="slick-prev btn btn-ripple ripple-link" aria-label="Previous">Previous</a>',
      nextArrow: '<a href="#" class="slick-next btn btn-ripple ripple-link" aria-label="Next">Next</a>',
      focusOnSelect: true,
      responsive: [{
          breakpoint: 768,
          settings: {

              slidesToShow: 3
          }
      }, {
          breakpoint: 628,
          settings: {
              slidesToShow: 2
          }
      }]
  });


  $('#affiliate-slider').slick({
      slidesToShow: 5,
      slidesToScroll: 1,
      arrows: true,
      prevArrow: '<button class="slick-prev btn btn-ripple ripple-link" aria-label="Previous" type="button">Previous</button>',
      nextArrow: '<button class="slick-next btn btn-ripple ripple-link" aria-label="Next" type="button">Next</button>',
      dots: false,
      responsive: [{
          breakpoint: 1024,
          settings: {

              slidesToShow: 4
          }
      }, {
          breakpoint: 768,
          settings: {
              slidesToShow: 3
          }
      }, {
          breakpoint: 567,
          settings: {
              slidesToShow: 2
          }
      }, {
          breakpoint: 360,
          settings: {
              slidesToShow: 1
          }
      }]

  });


 


/* fakewaffle.responsiveTabs(['xs', 'sm']); */
  fakewaffle.responsiveTabs(["xs"]);

  /* Parallax effects */

  function parallaxIt(container, target, movement) {
      $(this).mousemove(function(e) {
          var $this = $(container),
              targetElm = $this.find(target);

          var relX = e.pageX - $this.offset().left;
          var relY = e.pageY - $this.offset().top;

          TweenMax.to(targetElm, 1, {
              x: (relX - $this.width() / 2) / $this.width() * movement,
              y: (relY - $this.height() / 2) / $this.height() * movement
          });
      });
  }



  $.fn.parallax = function ( resistance, mouse ) 
{
	$el = $( this );
	TweenLite.to( $el, 0.2, 
	{
		x : -(( mouse.clientX - (window.innerWidth/2) ) / resistance ),
		y : -(( mouse.clientY - (window.innerHeight/2) ) / resistance )
	});

};


$('.no-touch .uniques .img-sec').each(function(){
$(this).mousemove( function( e ) {
    $( '.feature-image' ).parallax( -30, e );
    $( '.seconday-image' )	  .parallax( 10	, e );
   
});

})




  function paparallaxImgEffects() {
   //   parallaxIt('.no-touch #unique-section .img-sec', '.feature-image', -100);
    //  parallaxIt('.no-touch #unique-section .img-sec', '.seconday-image', -30);
  }


  /* text wrap */
function textWrap(content){
  $(content).each(function() {
    var html = $(this).text();
    var word = html .substr(0, html.indexOf(" "));
    var rest = html .substr(html.indexOf(" "));
    $(this).html(rest).prepend($("<span class='first'/>").html(word));
 });
}

textWrap('.timeline-title');


$(".no-touch .project").hover3d({
    selector: ".project__card"
});

  

$(window).on('load', function(){
    if (jQuery().niceScroll) {
        $("html").niceScroll({
  
          scrollspeed: 100,
          cursorcolor: "#174a7a",
          cursorborder: "1px solid #174a7a",
            autohidemode: false,
            horizrailenabled: false
        });
    } 
});



function stickyFooter(status) {
    var footer = $(".footer-wrap"),
    footerHeight = footer.outerHeight(); /* get the height from footer */
    
	if(status == 'enable') {
        $(".main-content").css("margin-bottom", footerHeight);
    } else {
        $(".main-content").css("margin-bottom", 0);
    }
}


function checkScrollBar(status) {
    var hContent = $("body").height(); /*  get the height of your content */
    var hWindow = $(window).height();  /* get the height of the visitor's browser window */

    /* if the height of your content is bigger than the height of the
    browser window, we have a scroll bar */
    if(status == 'enable') {
        if(hContent>hWindow) {             
            stickyFooter('enable');
            $('body').toggleClass('sticky-footer');     
        }
    } else {
        stickyFooter('disable');
        $('body').removeClass('sticky-footer');     
    }
    
}


var responsiveflag = false;
function responsiveResize() {
	
	if (($(window).width()) <= 767 && responsiveflag == false)
	{
		
        checkScrollBar('disable');
        stickyFooter('disable');
		responsiveflag = true;
	}
	else if (($(window).width()) >= 768)
	{
		checkScrollBar('enable');
		stickyFooter('enable');
		responsiveflag = false;
		
	}
	
}


    responsiveResize();
	$(window).resize(responsiveResize);

})(jQuery);


