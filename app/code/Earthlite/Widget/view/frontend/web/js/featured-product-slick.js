define(['jquery', 'slick'], function ($) {
    "use strict";
    $(function () {
        $('.featured-products .product-items').slick({
            infinite: false,
            speed: 300,
            lazyLoad: true,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1320,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }

            ]
        });

        $('.featured-products .product-items').on('afterChange', function (event, slick, currentSlide) {
            $('.featured-products .slick-list').addClass('pad');
            if (currentSlide === 0) {
                $('.featured-products .slick-list').removeClass('pad');
            }

            if (currentSlide !== 0) {
                $('.featured-products .slick-list').removeClass('first');
            }

            if (slick.slideCount === currentSlide + 1) {
                $('.featured-products .slick-list').addClass('last').removeClass('first');
            }

            if (slick.slideCount !== currentSlide + 1) {
                $('.featured-products .slick-list').removeClass('last');
            }
        });
    });
});
