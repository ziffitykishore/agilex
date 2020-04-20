define(['jquery', 'slick'], function ($) {
    "use strict";
    $(function () {
        $(".category-image").slick({
            dots: false,
            infinite: true,
            speed: 300,
            lazyLoad: true
        });
    });
});
