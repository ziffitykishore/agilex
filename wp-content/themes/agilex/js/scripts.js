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

    $('body').addClass('loading');
    jQuery(window).on('load', function(){
			
      jQuery('.loader').removeClass('scale');
      
      $('body').removeClass('loading');
    });

    $('.fancybox').fancybox({
      padding: 0,
      aspectRatio : true
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

    $(".search-wrap").each(function() {
      $(this)
        .find(".search-trigger")
        .on("click", function(e) {
          e.stopPropagation();
          $(this)
            .parent()
            .toggleClass("search-open");
            $('body').toggleClass('js');
            $('.main-content').toggleClass('main-wrap--move');
            $('.searchform input').focus();
        });

      $(this)
        .find(".search-close")
        .on("click", function(e) {
          e.stopPropagation();
          $(this)
            .closest(".search-wrap")
            .removeClass("search-open");
            $('body').removeClass('js');
            $('.main-content').removeClass('main-wrap--move');
        });
    });
  });

  /* reset the image tag */

  jQuery("img")
    .removeAttr("width")
    .removeAttr("height");

  

  $(window).on('load', function(){

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

  function footerFixed() {
    if ($(window).width() > 768) {
      var footer = $(".footer-wrap"),
        footerHeight = footer.outerHeight();
      $(".main-content").css("margin-bottom", footerHeight);
    } else {
      $(".main-content").css("margin-bottom", 0);
    }
  }
  footerFixed();
  $(window).on("load resize", footerFixed);

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
    event.preventDefault();
    if (!isLateralNavAnimating) {
      if ($(this).parents(".csstransitions").length > 0)
        isLateralNavAnimating = true;
      $("body").toggleClass("navigation-is-open");
      $(".cd-navigation-wrapper").one(
        "webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend",
        function() {
          isLateralNavAnimating = false;
        }
      );
    }
  });


  /* initialize the wow script */
  var wow = new WOW(
    {
      boxClass:     'wow',      // animated element css class (default is wow)
      animateClass: 'animated', // animation css class (default is animated)
      offset:       0,          // distance to the element when triggering the animation (default is 0)
      mobile:       true,       // trigger animations on mobile devices (default is true)
      live:         true,       // act on asynchronously loaded content (default is true)
      callback:     function(box) {
        // the callback is fired every time an animation is started
        // the argument that is passed in is the DOM node being animated
      },
      scrollContainer: null // optional scroll container selector, otherwise use window
    }
  );
  wow.init();

  var parallaxSettings = {
    initialOpacity: 1, //from 0 to 1, e.g. 0.34 is a valid value. 0 = transparent, 1 = Opaque
    opacitySpeed: 0.1, //values from 0.01 to 1 -> 0.01: slowly appears on screen; 1: appears as soon as the user scrolls 1px
    pageLoader: false
  };

  parallaxImgScroll(parallaxSettings);


})(jQuery);

(function($) {
  //      fakewaffle.responsiveTabs(['xs', 'sm']);
  fakewaffle.responsiveTabs(["xs"]);
})(jQuery);






