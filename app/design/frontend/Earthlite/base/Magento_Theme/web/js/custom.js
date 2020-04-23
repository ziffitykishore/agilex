/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery-ui-modules/progressbar',
], function ($) {
    'use strict';

    /* Loader */
    var progressbar = $("#progressbar"),
        progressLabel = $(".progress-label");

    progressbar.progressbar({
        value: 0,
        change: function () {
            /* progressLabel.text(progressbar.progressbar("value") + "%"); */
        },
        complete: function () {
            progressLabel.text( "Loaded!" );
            $('.loader').fadeOut(1000);
            $('body').addClass('page-loaded');
        }
    });

    function progress() {
        var val = progressbar.progressbar("value") || 0;
        progressbar.progressbar("value", val + 1);
        if (val < 100) {
            setTimeout(progress, 0);
        }
    }

    progressbar.progressbar("value", 0);
    $(document).ready(function () {
        progress();
    });
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
            } else {
                navbar.removeClass('sticky');
                $('body').css('paddingTop', 0);
            }
        });
    }

    sticky($('.header-wrapper'));

    $('.header-right-pane > .header.links').clone().appendTo('#store\\.links');
    $('.links-wrap .block-title').on('click', function () {
        $(this).parent().toggleClass('active');
        $(this).parents('.links-wrap .block').siblings().find('.block').removeClass('active');
    });
    $('.alert .close').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.alert').slideUp();
    });

    // home brand1 sec modal
    $('.home-brand1 a').on('click', function (e) {
        e.preventDefault();
        $('body').addClass('brand-active');
    });
    $('.brand-overlay, .home-brand-more .icon-x').on('click', function (e) {
        e.preventDefault();
        $('body').removeClass('brand-active');
    });
    
    // plp filter
    $('.btn-filter').on('click', function(){
        $('body').addClass('filter-active');
    });
    
    function veritcalScroll() {
        if ($('.related .product-items li').length > 3) {
            var items = $('.related .product-items li').outerHeight();
            $('.related .product-items').css('max-height', 3 * items);
        }
    }

    // veritcalScroll();

    $(window).on('load resize', function () {
        veritcalScroll();
    });

    // toggle text
    function moreToggler(s) {
        var showChar = 150;
        var ellipsestext = "";
        var moretext = "Read more";
        var lesstext = "Read less";
        var selector = s;
        $(selector).each(function () {
            var content = $(this).html();

            if (content.length > showChar) {
                var c = content.substr(0, showChar);
                var h = content.substr(showChar, content.length - showChar);
                var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink read-more">' + moretext + '</a></span>';
                $(this).html(html);
            }
        });

        $(".morelink").click(function () {
            if ($(this).hasClass("less")) {
                $(this).removeClass("less");
                $(this).html(moretext);
            } else {
                $(this).addClass("less");
                $(this).html(lesstext);
            }
            $(this).parent().prev().toggle();
            $(this).prev().toggle();
            return false;
        });
    }
    if ($(window).width() < 767) {
        moreToggler('.category-description p');
    }

});
