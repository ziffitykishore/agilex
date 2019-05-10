define(['jquery', 'slick', 'scroller'], function ($) {
    $(document).ready(function ($) {

        var c, currentScrollTop = 0,
            navbar = $('.header-wrapper');

        $(window).scroll(function () {
            var a = $(window).scrollTop();
            var b = navbar.height();

            currentScrollTop = a;

            if (c < currentScrollTop && a > b + b) {
                navbar.addClass("scrollUp");
            } else if (c > currentScrollTop && !(a <= b)) {
                navbar.removeClass("scrollUp");
            }
            c = currentScrollTop;
        });


        $(window).scroll(function(){
            if ($(window).scrollTop() >= 1) {
                $('.header-wrapper').addClass('fixed-header');
            }
            else {
                $('.header-wrapper').removeClass('fixed-header');
            }
        });

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

        body_sizer();
        $(window).resize(body_sizer);

        function body_sizer() {
            var bodyheight = $(window).height();
            $(".home-slider .banner-item").height(bodyheight);
        }

        function bgSource(imgcontainer) {
            $(imgcontainer).each(function () {
                var img = $(this).find("img");

                if(img.length) {
                    var height = img.height();
                    var img_src = img.attr("src");

                    $(this).css({
                        "background-image": "url(" + img_src + ")",
                        "background-size": "cover",
                        "background-repeat": "no-repeat",
                        "background-position": "center"
                    });
                }

                img.hide();
            });
        }

        bgSource(".home-slider .banner-item");

        bgSource(".category-info");


        $('.featured-product-inner > .product-item').prependTo('.featured-product-inner .product-items');

        $('.best-seller .product-items').slick({
            dots: false,
            infinite: true,
            arrows: true,
            autoplay: true
        });


        function toggle(container, item, bodyClass) {
            $(container).find(item).on('click', function(){
                $(container).toggleClass('_active');

                $('body').toggleClass(bodyClass);

            });

            $(container).on("click", function(e) {
                e.stopPropagation()
            });
        }


        toggle('.block-search', '.block-title', 'search-opened');

        $(document).click(function() {
            $("body").removeClass("search-opened")
        });

        $(".scroll-down").on("click", function(e) {
            e.preventDefault();
            $("html, body").animate({
                scrollTop: $($(this).attr("href")).offset().top
            }, 500, "linear")
        });

    });
});


