define(['jquery', 'slick', 'scroller'], function ($) {
    $(document).ready(function ($) {

        // Hide Header on on scroll down
        var didScroll;
        var lastScrollTop = 0;
        var delta = 5;
        var navbarHeight = $('.page-header > .header').outerHeight();

        setInterval(function() {
            if (didScroll) {
                hasScrolled();
                didScroll = false;
            }
        }, 250);

        function hasScrolled() {
            var st = $(window).scrollTop();

            // Make sure they scroll more than delta
            if (Math.abs(lastScrollTop - st) <= delta)
                return;

            // If they scrolled down and are past the navbar, add class .nav-up.
            // This is necessary so you never see what is "behind" the navbar.
            if (st > lastScrollTop && st > navbarHeight) {
                // Scroll Down
                $('.page-header > .header').removeClass('nav-down').addClass('nav-up');
                $('.mob-sticky').removeClass('scroll-down').addClass('scroll-up');
            } else {
                // Scroll Up
                if (st + $(window).height() < $(document).height()) {
                    $('.page-header > .header').removeClass('nav-up').addClass('nav-down');
                    $('.mob-sticky').removeClass('scroll-up').addClass('scroll-down');
                }
            }

            if (st == 0) {
                $('.page-header > .header').removeClass('nav-down');
            }

            lastScrollTop = st;
        }

        // Home slider
        $('.home-slider').on('init', function (e, slick) {
            var $firstAnimatingElements = $('.banner-item:first-child').find('[data-animation]');
            doAnimations($firstAnimatingElements);
        });
        $('.home-slider').on('beforeChange', function (e, slick, currentSlide, nextSlide) {
            var $animatingElements = $('.banner-item[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
            doAnimations($animatingElements);
        });
        $('.home-slider').slick({
            dots: true,
            infinite: true,
            autoplaySpeed: 6000,
            speed: 3000,
            fade: true,
            cssEase: 'linear',
            arrows: false,
            autoplay: true
        });

        function doAnimations(elements) {
            var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
            elements.each(function () {
                var $this = $(this);
                var $animationDelay = $this.data('delay');
                var $animationType = 'animated ' + $this.data('animation');
                $this.css({
                    'animation-delay': $animationDelay,
                    '-webkit-animation-delay': $animationDelay
                });
                $this.addClass($animationType).one(animationEndEvents, function () {
                    $this.removeClass($animationType);
                });
            });
        }

        // Home our products

        $('.featured-product-inner > .product-item').prependTo('.featured-product-inner .product-items');

        $('.best-seller .product-items').slick({
            dots: false,
            infinite: true,
            arrows: true,
            autoplay: true
        });


        /* function call stick  */
        function stickyBar(elm) {
            var elment = $(elm);
            if (elment.length) {
                var stickyOffset = elment.offset().top;
                $(window).scroll(function() {
                    var sticky = elment,
                        scroll = $(window).scrollTop();
                    if (scroll >= stickyOffset) sticky.addClass("fixed");
                    else sticky.removeClass("fixed");
                });
            }
        }
        stickyBar(".page-header > .header");


    });
});


