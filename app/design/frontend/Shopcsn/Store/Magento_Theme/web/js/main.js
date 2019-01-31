define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'jquery/ui',
    'slick',
    'fancybox',
    'bminjs',
    'domReady!'
], function($, modal) {
    var _body = $('body'),
        _html = $('html');

    /*home page banner*/
    if ($('.home-banner').length > 0) {
        $('.home-banner').slick({
            autoplay: true,
            autoplaySpeed: 3000,
            dots: true,
            infinite: true,
            speed: 500,
            fade: true,
            arrows: false,
            cssEase: 'linear',
            responsive: [{
                breakpoint: 767,
                settings: {
                    dots: false
                }
            }],
            lazyLoad: 'ondemand'
        });
    }

    /* Header Section */
    $('<div class="overlay"></div>').appendTo('.page-wrapper');
    $('.nav-toggle').on('click', function() {
        _body.removeClass('acc-opened');
    });
    $('.overlay').on('click', function() {
        _body.removeClass('acc-opened menu-opened');
        _html.removeClass('nav-open nav-before-open');
    });
    $('.customer-welcome .customer-name').on('click', function() {
        _body.toggleClass('acc-opened').removeClass('menu-opened');
    });
    $('#login-popup, .menu-heading .close, .showcart').on('click', function() {
        _html.removeClass('nav-open nav-before-open');
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
    $(document).on("focus autocomplete change", '.onestepcheckout-index-index .input-text, .field input.input-text, textarea.form-control', function(e) {
        e.stopPropagation();
        var clstField = $(this).closest('.field');
        if ($(this).attr('name') == 'street[0]') {
            $(this).closest('fieldset').find('legend').remove();
            clstField.find('label').text('Street Address');
        }
        clstField.addClass('focused');
    });
    $(document).on('focus', '.onestepcheckout-index-index select', function() {
        $(this).closest('.field').addClass('focused select-group');
    });
    $(document).on("blur", '.onestepcheckout-index-index .input-text, .field input.input-text, textarea.form-control', function(e) {
        e.stopPropagation();
        var clstField = $(this).closest('.field');
        if ($(this).val() == '') {
            clstField.removeClass('focused');
        }
    });

    $('select').closest('.field').addClass('select-group');

    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
            $('.header-wrapper').addClass("fix-header").removeClass('no-sticky');
        } else {
            $('.header-wrapper').removeClass("fix-header nav-down").addClass('no-sticky');
        }
        didScroll = true;
    });

    // Hide Header on on scroll down
    var didScroll;
    var lastScrollTop = 0;
    var delta = 5;
    var navbarHeight = $('.header-wrapper').outerHeight();

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

    /* Fancy box */
    $("a[data-fancybox]").fancybox({
        padding: 0,
        aspectRatio: true,
        allowfullscreen: "true"
    });

    /* footer starts */
    $('#sticky-social .trigger').on('click', function() {
        $(this).parent().toggleClass('active');
    });
    $('.has-promo-text').on('click', function() {
        $(this).toggleClass('active');
        $('.promotion-text').toggleClass('active');
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
        $(trigger).on('click', function() {
            $(this).closest(container).find(contentSec).toggleClass('active');
            $(this).closest(container).toggleClass('active');
        });
    }

    $('.btn-comment').on('click', function() {
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
                    slidesToScroll: 1,
                    dots: true
                }
            }
        ]
    });

    if ($('.testi-slider').length > 0) {
        $('.testi-slider').slick({
            dots: true,
            autoplay: true,
            autoplaySpeed: 3000,
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
        var header = $('.header-wrapper'),
            headerHeight = header.innerHeight();
        header.next('.page-main').css('padding-top', headerHeight);
        header.next('.breadcrumbs').css('padding-top', headerHeight);
        if ($(window).width() < 769 && responsiveflag == false) {
            $('.test-image-sec').css('height', 'auto');
            responsiveflag = true;
        } else if ($(window).width() > 768) {
            testimonials();
            responsiveflag = false;
        }
    }

    responsiveResize();
    $(window).resize(function() {
        responsiveResize();
    });

    $('#blog-subscribe').on('click', function() {
        $('#newsletter').focus().closest('form').submit();
    });

    $('.product-info-main .product-social-links').appendTo('.box-tocart');
    toggleContent('.blog-sidebar .block', '.block-title', '.block-content');

    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        modalClass: 'call-price',
        buttons: false,
        title: $('.contact-title').text()
    };
    var callPopup = modal(options, $('#callforquote-popup'));
    $(document).on("click", '.btn-callforprice', function(e) {
        $('#callforquote-popup').modal('openModal');
        e.preventDefault();
    });
    /*to add ifram content from ajax*/
    $(window).bind("load", function() {
        $.ajax({
            async: true,
            type: "GET",
            url: BASE_URL+"zcore/index/iframe",
            crossDomain: false,
            success: function (res) {
                $('.ticker.jFrame .container').html(res.html);
            },
            error: function (){
                console.log('Iframe content not loading');
            }
        });
    });

});