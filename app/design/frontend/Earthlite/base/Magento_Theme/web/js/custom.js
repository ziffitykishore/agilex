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

            let panel = $('.header-wrapper').outerHeight();
            if ($(window).scrollTop() >= 130) {
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
    $('.links-wrap .block-title').on('click', function(){
        $(this).parent().toggleClass('active');
        $(this).parents('.links-wrap .block').siblings().find('.block').removeClass('active');
    });
    $('.alert .close').on('click',function(e){
        e.preventDefault();
        $(this).closest('.alert').slideUp();
    });
    
    // home brand1 sec modal
    $('.home-brand1 a').on('click',function(e){
        e.preventDefault();
        $('body').addClass('brand-active');
    });
    $('.brand-overlay, .home-brand-more .icon-x').on('click',function(e){
        e.preventDefault();
        $('body').removeClass('brand-active');
    });
    function veritcalScroll() {
        if($('.related .product-items li').length > 3){
            var items = $('.related .product-items li').outerHeight();
            $('.related .product-items').css('max-height', 3 * items);
        }
    }

   // veritcalScroll();

    $(window).on('load resize', function(){
        veritcalScroll();
    });
    
});
