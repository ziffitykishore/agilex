require(['jquery', 'slick'], function ($) {
    $(document).ready(function ($) {

    /*  =================
        Home Page Banner Slider Starts
        ================= */

    $(document).ready(function(){
        $(".home-slider").slick({
            dots:true,
            prevArrow: false,
            nextArrow: false,
            autoplay: false,
            autoplaySpeed: 2000,
        });
    });

    /*  =================
        Footer Links
        ================= */

    $('.mb-foot h5').on('click', function () {
        var _this = $(this);
        $('.mb-foot h5').not(_this).removeClass('active');
        $('.mb-foot h5').not(_this).next('ul').slideUp('slow');
        if (!_this.hasClass('active')) {
            _this.addClass('active');
            _this.next('ul').slideDown('slow');
        } else {
            _this.removeClass('active');
            _this.next('ul').slideUp('slow');
        }
    });

    /*  =================
        Sticky Navbar
        ================= */

        var scroll = $(window).scrollTop();
        var $window = $(window);
        $window.scroll(function () {
            scroll = $(window).scrollTop();
            (scroll > 570) ? $('.page-wrapper').addClass('fix-header') : $('.page-wrapper').removeClass('fix-header');
        });

    /*  =================
        Home Page Why Us Slider
        ================= */

        settings_slider = {
            dots: false,
            arrows: true,
            autoplay:true,
            autoplaySpeed:2000
          }
          slick_on_mobile( $('.why-row'), settings_slider);
        
          function slick_on_mobile(slider, settings){
            $(window).on('load resize', function() {
              if ($(window).width() > 767) {
                if (slider.hasClass('slick-initialized')) {
                  slider.slick('unslick');
                }
                return
              }
              if (!slider.hasClass('slick-initialized')) {
                return slider.slick(settings);
              }
            });
          };

    /*  =================
        MM-Menu
        ================= */

        var ua = window.navigator.userAgent;
        var isIE = /MSIE|Trident/.test(ua);

        if ( !isIE ) {
            new Mmenu( document.querySelector( '#menu' ) );
        } else {
            $('body').addClass('ie')
        }

    });
});