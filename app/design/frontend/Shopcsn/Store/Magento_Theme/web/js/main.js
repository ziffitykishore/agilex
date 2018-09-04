/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'underscore',
    'sidebar',
    'mage/translate',
    'mage/dropdown',
    'domReady!'
], function (Component, ko, _) {
    'use strict';

    jQuery('.overlay').on('click', function () {
        jQuery('html').removeClass('nav-open');
        jQuery('body').removeClass('acc-opened cart-opened');
    });

    jQuery('.showcart').on('click', function () {
        jQuery('html').removeClass('nav-open');
    });

    jQuery('.customer-welcome .customer-name').on('click', function(){
        jQuery('body').addClass('acc-opened');
    });



    jQuery(document).on('click', function () {
        jQuery('.nav-sections').on('click', function (e) {
            e.stopPropagation();
        });
        
        jQuery('.header').on('click', function (e) {
            jQuery('html').removeClass('nav-open');
            jQuery('body').removeClass('acc-opened cart-opened');
        });
    });
    jQuery(".form-group input.form-control").on("focus blur", function () {
        if (jQuery(this).val() == "") {
            jQuery(this)
                .parents(".form-group")
                .toggleClass("focused");
        }
    });
        jQuery(window).scroll(function () {
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

jQuery(window).scroll(function(event){
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
    if(Math.abs(lastScrollTop - st) <= delta)
        return;
    
    // If they scrolled down and are past the navbar, add class .nav-up.
    // This is necessary so you never see what is "behind" the navbar.
    if (st > lastScrollTop && st > navbarHeight){
        // Scroll Down
        jQuery('header').removeClass('nav-down').addClass('nav-up');
    } else {
        // Scroll Up
        if(st + jQuery(window).height() < jQuery(document).height()) {
            jQuery('header').removeClass('nav-up').addClass('nav-down');
        }
    }
    
    lastScrollTop = st;
}

     /* footer starts */

     jQuery('#sticky-social .trigger').on('click', function () {
        jQuery(this).parent().toggleClass('active');
    });





});