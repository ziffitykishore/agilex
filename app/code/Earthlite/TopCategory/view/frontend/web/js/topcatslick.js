define(['jquery', 'slick'], function ($) {
    "use strict";
    $(function () {
        let $catSlider = $('.category-slider .product-items');
        var $progressBar = $('.progress-handle');

        $catSlider.slick({
            infinite: false,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 3,
            responsive: [
                {
                    breakpoint: 1300,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 3
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                }

            ]
        });

        $catSlider.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
            var act = $('.category-slider').find('.slick-active').length;
            var calc = ((nextSlide) / (slick.slideCount - act)) * 100;
            var ts = slick.slideCount - 1;
            var n = 100 / (ts - nextSlide);



            //$progressBar.css('left',  + calc+ '%');
            if (!(calc == 100)) {
                $progressBar.css('left', 'calc(' + calc + '% - 0px)');
            } else {
                $progressBar.css('left', 'calc(' + calc + '% - 100px)');
            }
            if (timeout) {
                clearTimeout(timeout);
            } // Set Timeout
            var timeout = setTimeout(function () {
                return $('.category-slider').removeClass('is-pressed');
            }, 350);
            $('.category-slider').addClass('is-pressed');
        });

        $catSlider.on('afterChange', function (event, slick, currentSlide) {
            $('.category-slider .slick-list').addClass('pad');
            if (currentSlide === 0) {
                $('.category-slider .slick-list').removeClass('pad');
            }

            if (currentSlide !== 0) {
                $('.category-slider .slick-list').removeClass('first');
            }

            if (slick.slideCount === currentSlide + 1) {
                $('.category-slider .slick-list').addClass('last').removeClass('first');
            }

            if (slick.slideCount !== currentSlide + 1) {
                $('.category-slider .slick-list').removeClass('last');
            }
        });

        /* $catSlider.on('wheel', (function(e) {
            e.preventDefault();
            if (e.originalEvent.deltaY < 0) {
              $(this).slick('slickNext');
            } else {
              $(this).slick('slickPrev');
            }
        })); */
    });
});
    