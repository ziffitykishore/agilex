/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'slick',
    'left-sticky',
    'jquery/ui',
    'domReady!'
], function($) {   
    /* Header Section */
    $('<div class="overlay"></div>').appendTo('.page-wrapper');
    $('.nav-toggle').on('click', function(e) {
        $('body').toggleClass('menu-opened').removeClass('acc-opened cart-opened');

    });
    $('.overlay').on('click', function() {
        $('body').removeClass('acc-opened cart-opened menu-opened');
    });

    $('.showcart').on('click', function() {
        $('body').removeClass('menu-opened acc-opened').addClass('cart-opened');
    });

    $(document).on('click', '#btn-minicart-close', function (){
        $('body').removeClass('cart-opened');
    });

    $('.customer-welcome .customer-name').on('click', function() {
        $('body').toggleClass('acc-opened').removeClass('menu-opened cart-opened');
    });

    $('.menu-wrap li.level0').each(function() {
        if ($(this).find('.level2').length) {
            $(this).addClass('mega-menu');
        } else {
            $(this).addClass('simple-menu');
        }
    });

    /* autocomplete off */
    $('input').attr('autocomplete', 'off');
    $(document).on("focus autocomplete", '.onestepcheckout-index-index .input-text, .field input.input-text, textarea.form-control', function() {            
        var clstField = $(this).closest('.field');
        if($(this).attr('name') == 'street[0]'){
            $(this).closest('fieldset').find('legend').remove();
            clstField.find('label').text('Street Address');
        }
        clstField.addClass('focused');
    });
    $(document).on('focus', '.onestepcheckout-index-index select', function(){
        $(this).closest('.field').addClass('focused select-group');
    });
    $(document).on("blur", '.onestepcheckout-index-index .input-text, .field input.input-text, textarea.form-control', function() {
        var clstField = $(this).closest('.field');
        if($(this).val() == ''){
            clstField.removeClass('focused');
        }
    });

    $('select').parents('.field').addClass('select-group');

    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
            $('.header-wrapper').addClass("fix-header").removeClass('no-sticky');
        } else {
            $('.header-wrapper').removeClass("fix-header nav-down").addClass('no-sticky');
        }
    });

    // Hide Header on on scroll down
    var didScroll;
    var lastScrollTop = 0;
    var delta = 5;
    var navbarHeight = $('.header-wrapper').outerHeight();
    
    $(window).scroll(function(event) {
        didScroll = true;
    });

    setInterval(function() {
        if (didScroll) {
            hasScrolled();
            didScroll = false;
        }
    }, 250);

    function hasScrolled() {
        var st = $(window).scrollTop();

        // Make sure they scroll more than delta
        if (Math.abs(lastScrollTop - st) <= delta)
            return;

        // If they scrolled down and are past the navbar, add class .nav-up.
        // This is necessary so you never see what is "behind" the navbar.
        if (st > lastScrollTop && st > navbarHeight) {
            // Scroll Down
            $('.header-wrapper').removeClass('nav-down').addClass('nav-up');
            $('.mob-sticky').removeClass('scroll-down').addClass('scroll-up');
        } else {
            // Scroll Up
            if (st + $(window).height() < $(document).height()) {
                $('.header-wrapper').removeClass('nav-up').addClass('nav-down');
                $('.mob-sticky').removeClass('scroll-up').addClass('scroll-down');
            }
        }

        if (st == 0) {
            $('.header-wrapper').removeClass('nav-down');
        }

        lastScrollTop = st;
    }
   
    /* footer starts */
    $('#sticky-social .trigger').on('click', function() {
        $(this).parent().toggleClass('active');
    });

    function bgSource(imgcontainer) {
        $(imgcontainer).each(function() {
            var img = $(this).find("img");
            var height = img.height();
            var img_src = img.attr("src");
            $(this).css({
                "background-image": "url(" + img_src + ")"
            });
            img.hide();
        });
    }

    //bgSource('.category_image');

    function toggleContent(container, trigger, contentSec) {
        $(trigger).on('click', function (){
            $(this).closest(container).find(contentSec).toggleClass('active');
            $(this).closest(container).toggleClass('active');
        });
    }

    $('.btn-comment').on('click', function (){
        $('#post-comments .c-reply').toggle();
    });

    /* Accordion */
    function toggleChevron(e) {
        $(e.target)
            .prev('.panel-heading')
            .find("i")
            .toggleClass('fa fa-minus fa fa-plus');
    }
    $('#accordion').on('hidden.bs.collapse', toggleChevron);
    $('#accordion').on('shown.bs.collapse', toggleChevron);

    /* home page slider */
    $('.blog-slider').slick({
        dots: false,
        arrows: false,
        infinite: false,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive: [{
                breakpoint: 1023,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    infinite: true
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });


    if($('.testi-slider').length){
        $('.testi-slider').slick({
            dots: true,
            autoplay: true,
            arrows: false,
            infinite: true,
            speed: 500,
            fade: true,
            cssEase: 'linear'
        });
    }

    function testimonials() {
        var reviewWrap = $('.testi-slider-wrap').height();
        $('.test-image-sec').height(reviewWrap);
    }

    /* ticker sticky */
    /* function call stick  */
    function stickyBar(elm) {
        var elment = $(elm);
        if (elment.length) {
            var stickyOffset = elment.offset().top;
            $(window).scroll(function() {
                var sticky = elment,
                    scroll = $(window).scrollTop();
                if (scroll >= stickyOffset) sticky.addClass("fixed");
                else sticky.removeClass("fixed");
            });
        }
    }
    stickyBar(".ticker");

    /* to find if mobile */
    /* var win_w = $(window).width();
    var is_mobile = (win_w < 769) ? true : false;
    var is_tab = (win_w < 993) ? true : false; */

    /* responsive View */

    var responsiveflag = false;

    function responsiveResize() {
        if ($(window).width() < 768 && responsiveflag == false) {
            $('.block-search').appendTo('.header-wrapper');
            $('.test-image-sec').css('height', 'auto');
            toggleContent('.blog-sidebar .block', '.block-title', '.block-content');
            responsiveflag = true;

        } else if ($(window).width() > 768) {
            $('.block-search').insertAfter('.menu-wrap');
            testimonials();
            $('.blog-sidebar .block-content').removeAttr('style');
            responsiveflag = false;
        }
    }

    responsiveResize();
    $(window).resize(function() {
        responsiveResize();
    });
    
    $('#blog-subscribe').on('click', function(){
        $('#newsletter').focus();
        $('#newsletter').closest('form').submit();
    });

    $(".page-with-filter .sidebar").stick_in_parent();
});
