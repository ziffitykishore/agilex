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
    'mage/dropdown'
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

     /* footer starts */

     jQuery('#sticky-social .trigger').on('click', function () {
        jQuery(this).parent().toggleClass('active');
    });
});