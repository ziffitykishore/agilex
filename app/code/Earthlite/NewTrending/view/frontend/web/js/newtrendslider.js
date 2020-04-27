define(['jquery', 'slick'], function ($) {
    "use strict";
    $(function () {
        let $catSlider = $('.new-trend-slider');

        $catSlider.slick({
            infinite: false,
            speed: 300,
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
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        arrows: false,
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }

            ]
        });

    });
});
    