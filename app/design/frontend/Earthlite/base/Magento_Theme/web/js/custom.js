/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

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
