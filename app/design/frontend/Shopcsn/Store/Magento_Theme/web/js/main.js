/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'domReady!'
], function($, jQuery) {
    'use strict';

    $('.overlay').on('click', function () {
        $('html').removeClass('nav-open');
        $('body').removeClass('acc-opened cart-opened');
    });

    $('.showcart').on('click', function () {
        $('html').removeClass('nav-open');
    });

    $('.customer-welcome').on('click', function(){
        $('body').addClass('acc-opened');
    });



    $(document).on('click', function () {
        $('.nav-sections').on('click', function (e) {
            e.stopPropagation();
        });
        
        $('.header').on('click', function (e) {
            $('html').removeClass('nav-open');
            $('body').removeClass('acc-opened cart-opened');
        });
    });
    $(".form-group input.form-control").on("focus blur", function () {
        if ($(this).val() == "") {
            $(this)
                .parents(".form-group")
                .toggleClass("focused");
        }
    });
        $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
          $('.header-wrapper').addClass("fix-header");
        } else {
         $('.header-wrapper').removeClass("fix-header");
        }
     });

     /* footer starts */

     $('#sticky-social .trigger').on('click', function () {
        $(this).parent().toggleClass('active');
    });
});