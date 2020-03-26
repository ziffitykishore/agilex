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

        $(window).scroll(function () {
            let a = $(window).scrollTop();
            let b = navbar.height();

            currentScrollTop = a;

            if (c < currentScrollTop && a > b + b) {
                navbar.addClass("scrollUp");
            } else if (c > currentScrollTop && !(a <= b)) {
                navbar.removeClass("scrollUp");
            }
            c = currentScrollTop;
        });


        $(window).scroll(function () {
            if ($(window).scrollTop() >= 46) {
                navbar.addClass('sticky');
            }
            else {
                navbar.removeClass('sticky');
            }
        });
    }

    sticky($('.header-wrapper'));

    $('.header-right-pane > .header.links').clone().appendTo('#store\\.links');

    // Sticky header and filter
    var scroll = $(window).scrollTop();
    var $window = $(window);
    var head_height = $('.header.content').outerHeight();

    $window.scroll(function () {
        scroll = $window.scrollTop();

        if (scroll > 220) {
            $('body').addClass('fix-header');
            $('body').css('paddingTop', head_height);
        } else {
            $('body').removeClass('fix-header');
            $('body').css('paddingTop', 0);
        }
    });

});
