/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    /* header sticky */

    function sticky(navbar) {

        let c, currentScrollTop = 0;
        let b = navbar.outerHeight();
        $(window).scroll(function () {
            let a = $(window).scrollTop();


            currentScrollTop = a;

            if (c < currentScrollTop && a > b + b) {
                navbar.addClass("scrollUp");
            } else if (c > currentScrollTop && !(a <= b)) {
                navbar.removeClass("scrollUp");
            }
            c = currentScrollTop;
        });


        $(window).scroll(function () {

            let panel = $('.panel.header').outerHeight();
            if ($(window).scrollTop() >= 46) {
                navbar.addClass('sticky');
                $('body').css('paddingTop', panel);
            }
            else {
                navbar.removeClass('sticky');
                $('body').css('paddingTop', 0);
            }
        });
    }

    sticky($('.header-wrapper'));

    $('.header-right-pane > .header.links').clone().appendTo('#store\\.links');

});
