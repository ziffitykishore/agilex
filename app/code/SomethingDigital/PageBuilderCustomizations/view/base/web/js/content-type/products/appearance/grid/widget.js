define([
    'jquery',
    'slick',
    'SomethingDigital_PageBuilderCustomizations/js/breakpoints'
], function ($, slick, breakpoints) {
    'use strict';

    return function (config, element) {
        var $element = $(element);
        var $products = $(element).find('.product-items');

        if($element.data('isSlider')) {
            if ($products.hasClass('slick-initialized')) {
                $products.slick('unslick');
            }

            $products.slick({
                autoplay: $element.data('productsAutoplay'),
                autoplaySpeed: $element.data('productsAutoplaySpeed') || 0,
                slidesToShow: $element.data('slidesToShow'),
                fade: $element.data('productsFade'),
                infinite: $element.data('productsInfiniteLoop'),
                arrows: $element.data('productsShowArrows'),
                dots: $element.data('productsShowDots'),
                responsive: [
                    {
                        breakpoint: breakpoints.screen__m,
                        settings: {
                            slidesToShow: $element.data('slidesToShowTablet'),
                        }
                    },
                    {
                        breakpoint: breakpoints.screen__xs,
                        settings: {
                            slidesToShow: $element.data('slidesToShowMobile'),
                        }
                    }
                ]
            });
        }
    };
});
