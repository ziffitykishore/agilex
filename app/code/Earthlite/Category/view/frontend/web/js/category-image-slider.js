define(['jquery', 'slick'], function ($) {
    "use strict";
    $(function () {
        $(".clp-banner").slick({
            dots: false,
            infinite: true,
            speed: 300,
            lazyLoad: true
        });
    });
});
