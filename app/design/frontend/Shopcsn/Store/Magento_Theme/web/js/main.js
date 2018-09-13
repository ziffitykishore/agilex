/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery/ui', 
    'jquery/validate', 
    'mage/translate' ,
    'slick',
    'domReady!'
], function($) {
    'use strict';

    jQuery('.overlay').on('click', function() {
        jQuery('html').removeClass('nav-open');
        jQuery('body').removeClass('acc-opened cart-opened');
    });

    jQuery('.showcart').on('click', function() {
        jQuery('html').removeClass('nav-open');
    });

    jQuery('.customer-welcome .customer-name').on('click', function() {
        jQuery('body').toggleClass('acc-opened');
        jQuery('html').removeClass('nav-open');
    });



    jQuery(document).on('click', function() {
        jQuery('.nav-sections').on('click', function(e) {
            e.stopPropagation();
        });

        jQuery('.header').on('click', function(e) {
            jQuery('html').removeClass('nav-open');
            jQuery('body').removeClass('acc-opened cart-opened');
        });
    });

    jQuery('.form-group input.form-control, textarea.form-control')
        .on("focus blur change", function() {
        if (jQuery(this).val() == "") {
            jQuery(this).parents(".form-group").toggleClass("focused");
        }
    });

    jQuery('select').parents('.field').addClass('select-group');

    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > 50) {
            jQuery('.header-wrapper').addClass("fix-header");
        } else {
            jQuery('.header-wrapper').removeClass("fix-header");
        }
    });

    // Hide Header on on scroll down
    var didScroll;
    var lastScrollTop = 0;
    var delta = 5;
    var navbarHeight = jQuery('header').outerHeight();

    jQuery(window).scroll(function(event) {
        didScroll = true;
        console.log('asd');
    });

    setInterval(function() {
        if (didScroll) {
            hasScrolled();
            didScroll = false;
        }
    }, 250);

    function hasScrolled() {
        var st = jQuery(window).scrollTop();

        // Make sure they scroll more than delta
        if (Math.abs(lastScrollTop - st) <= delta)
            return;

        // If they scrolled down and are past the navbar, add class .nav-up.
        // This is necessary so you never see what is "behind" the navbar.
        if (st > lastScrollTop && st > navbarHeight) {
            // Scroll Down
            jQuery('header').removeClass('nav-down').addClass('nav-up');
        } else {
            // Scroll Up
            if (st + jQuery(window).height() < jQuery(document).height()) {
                jQuery('header').removeClass('nav-up').addClass('nav-down');
            }
        }

        lastScrollTop = st;
    }

    /* footer starts */

    jQuery('#sticky-social .trigger').on('click', function() {
        jQuery(this).parent().toggleClass('active');
    });

    $("input").attr("autocomplete", "off");

    /* Accordion */

    function toggleChevron(e) {
        jQuery(e.target)
            .prev('.panel-heading')
            .find("i")
            .toggleClass('fa fa-minus fa fa-plus');
    }
    jQuery('#accordion').on('hidden.bs.collapse', toggleChevron);
    jQuery('#accordion').on('shown.bs.collapse', toggleChevron);



    /* home page slider */

    $('.blog-content-wrap').slick({
        dots: false,
        arrows: false,
        infinite: false,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive: [
          {
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



      /* responsive View */

  var responsiveflag = false;
  function responsiveResize() {
    if ($(window).width() < 768 && responsiveflag == false) {
        jQuery('.block-search').appendTo('.header-wrapper');
      responsiveflag = true;
    } else if ($(window).width() > 767) {
        jQuery('.block-search').insertAfter('.menu-wrap');
      responsiveflag = false;
    }
  }

  responsiveResize();
  $(window).resize(responsiveResize);


});