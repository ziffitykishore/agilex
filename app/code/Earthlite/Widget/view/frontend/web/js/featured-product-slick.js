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
                    breakpoint: 1300,
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
                    breakpoint: 640,
                    settings: {
                        arrows: false,
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }

            ]
        });

        $('.featured-products .product-items').on('afterChange', function (event, slick, currentSlide) {
            $('.slick-list').addClass('pad');
            if (currentSlide === 0) {
                $('.slick-list').removeClass('pad');
                $('.slick-list').addClass('first').removeClass('last');
            }

            if (currentSlide !== 0) {
                $('.slick-list').removeClass('first');
            }

            if (slick.slideCount === currentSlide + 1) {
                $('.slick-list').addClass('last').removeClass('first');
            }

            if (slick.slideCount !== currentSlide + 1) {
                $('.slick-list').removeClass('last');
            }
        });

        $('.featured-products .product-items').on('beforeChange', function (event, slick, currentSlide) {
            $('.slick-active').last().addClass('fade')
        });
    });
});
