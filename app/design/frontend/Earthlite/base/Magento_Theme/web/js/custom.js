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
            $('.preloader').fadeOut(1000);
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

    $('.showsearch').on('click', function(){
        $(this).parent('.block-search').find('.input-text').val('');
        $('.autocomplete-suggestions').hide();
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

    $('.btn-sort').on('click', function(){
        $('body').toggleClass('sorter-active');
    });

    $('.sorter-close').on('click', function(){
        $('body').removeClass('sorter-active');
    });
    
    function veritcalScroll() {
        if ($('.related .product-items li, .crosssell .product-items li').length > 3) {
            var items = $('.related .product-items li, .crosssell .product-items li').outerHeight();
            $('.related .product-items, .crosssell .product-items').css('max-height', 3 * items);
        }
    }

    // veritcalScroll();

    $(window).on('load resize', function () {
        veritcalScroll();
    });

    $('.field.password').each(function(){
        $(this).find('.control').append('<span class="icon-password icon-eye"/>');

        $('.icon-password').on('click', function(){
            $(this).toggleClass("icon-eye icon-eye-off");
            var input = $(this).parent().find(".input-text");
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    })

    function bgSource(imgcontainer) {
        $(imgcontainer).each(function () {
            var img = $(this).find("img");

            if(img.length) {
                var height = img.height();
                var img_src = img.attr("src");

                $(this).css({
                    "background-image": "url(" + img_src + ")",
                    "background-size": "cover"
                });
            }

            img.hide();
        });
    }

    bgSource(".category-image-wrapper");

    // toggle text
    function moreToggler(s,c) {
        var showChar = c;
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
    moreToggler('.category-description p', 150);
    if ($(window).width() > 767) {
        moreToggler('.category-description p', 350);
    }

});
