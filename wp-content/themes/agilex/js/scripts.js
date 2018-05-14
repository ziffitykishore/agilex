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

      $(
          '<span class="caret"></span>'
      ).insertBefore("#primary-menu .menu-item-has-children > .sub-menu");
      $("#primary-menu a").addClass("ripple");

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
      var mainContainer = $('.main-content'),
          openCtrl = $('.search-trigger'),
          closeCtrl = $('.search-close'),
          searchContainer = $('.search-wrap'),
          inputSearch = searchContainer.find('#search__input');


      initEvents();


      function initEvents() {
          openCtrl.on('click', openSearch);
          closeCtrl.on('click', closeSearch);
          $(document).on('keyup', function(ev) {
              // escape key.
              if (ev.keyCode == 27) {
                  closeSearch();
              }
          });
      }

      function openSearch() {
          $('body').toggleClass('js-search ');
          mainContainer.toggleClass('main-wrap--move');
          searchContainer.toggleClass('search-open');
          setTimeout(function() {
              inputSearch.focus();
          }, 600);
      }

      function closeSearch() {
          $('body').removeClass('js-search ');
          mainContainer.removeClass('main-wrap--move');
          searchContainer.removeClass('search-open');
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

  $("#hero-slider .slider-blk").each(function() {
      var img = $(this).find("img");
      var height = img.height();
      var img_src = img.attr("src");

      $(this).css({
          "background-image": "url(" + img_src + ")",
          "background-repeat": "no-repeat",
          "background-size": "cover",
          "background-position": "center"
      });

      img.hide();
  });




  $(".btn-ripple").click(function(e) {
      // Remove any old one
      $(".ripple").remove();

      // Setup
      var posX = $(this).offset().left,
          posY = $(this).offset().top,
          buttonWidth = $(this).width(),
          buttonHeight = $(this).height();

      // Add the element
      $(this).prepend("<span class='ripple'></span>");

      // Make it round!
      if (buttonWidth >= buttonHeight) {
          buttonHeight = buttonWidth;
      } else {
          buttonWidth = buttonHeight;
      }

      // Get the center of the element
      var x = e.pageX - posX - buttonWidth / 2;
      var y = e.pageY - posY - buttonHeight / 2;

      // Add the ripples CSS and start the animation
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

  // declare variable
  var scrollTop = $(".scroll-top");

  $(window).scroll(function() {
      // declare variable
      var topPos = $(this).scrollTop();

      // if user scrolls down - show scroll to top button
      if (topPos > 100) {
          $(scrollTop).css("opacity", "1");

      } else {
          $(scrollTop).css("opacity", "0");
      }

  }); // scroll END

  //Click event to scroll to top
  $(scrollTop).click(function() {
      $('html, body').animate({
          scrollTop: 0
      }, 800);
      return false;

  }); // click() scroll top EMD

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


  var isLateralNavAnimating = false;
  $(".cd-nav-trigger").on("click", function(event) {
      $("body").toggleClass("navigation-is-open");
      event.preventDefault();

  });


  /* initialize the wow script */
  var wow = new WOW({
      boxClass: 'wow', // animated element css class (default is wow)
      animateClass: 'animated', // animation css class (default is animated)
      offset: 150, // distance to the element when triggering the animation (default is 0)
      mobile: true, // trigger animations on mobile devices (default is true)
      live: true, // act on asynchronously loaded content (default is true)
      callback: function(box) {
          // the callback is fired every time an animation is started
          // the argument that is passed in is the DOM node being animated
      },
      scrollContainer: null // optional scroll container selector, otherwise use window
  });
  wow.init();





  var parallaxSettings = {
      initialOpacity: 1, //from 0 to 1, e.g. 0.34 is a valid value. 0 = transparent, 1 = Opaque
      opacitySpeed: 0.1, //values from 0.01 to 1 -> 0.01: slowly appears on screen; 1: appears as soon as the user scrolls 1px
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
      prevArrow: '<button class="slick-prev btn btn-ripple" aria-label="Previous" type="button">Previous</button>',
      nextArrow: '<button class="slick-next btn btn-ripple" aria-label="Next" type="button">Next</button>',
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


  if (jQuery().niceScroll) {
      $("html").niceScroll({

          mousescrollstep: 38,
          cursorwidth: 5,
          cursorborder: 0,
          cursorcolor: '#174a79',
          cursorborderradius: 0,
          autohidemode: false,
          horizrailenabled: false
      });
  }


  // fakewaffle.responsiveTabs(['xs', 'sm']);
  fakewaffle.responsiveTabs(["xs"]);

  /* Parallax effects */

  function parallaxIt(container, target, movement) {
      $(container).mousemove(function(e) {
          var $this = $(container),
              targetElm = $this.find(target);

          var relX = e.pageX - $this.offset().left;
          var relY = e.pageY - $this.offset().top;

          TweenMax.to(targetElm, 1, {
              x: (relX - $this.width() / 2) / $this.width() * movement,
              y: (relY - $this.height() / 2) / $this.height() * movement
          });
      }).mouseleave(function(e) {
          var $this = $(container),
              targetElm = $this.find(target);
          targetElm.removeAttr('style');
      });
  }



  function paparallaxImgEffects() {
      parallaxIt('.no-touch .img-sec', '.feature-image', -100);
      parallaxIt('.no-touch .img-sec', '.seconday-image', -30);
  }





  /* Responsive View */


  function ResponsiveView() {
      if ($(window).width() > 768) {
          var footer = $(".footer-wrap"),
              footerHeight = footer.outerHeight(); /* get the height from footer */
          $(".main-content").css("margin-bottom", footerHeight);

          paparallaxImgEffects();

      } else {
          $(".main-content").css("margin-bottom", 0);
      }

  }

  ResponsiveView();

  $(window).on("load resize", ResponsiveView);



})(jQuery);