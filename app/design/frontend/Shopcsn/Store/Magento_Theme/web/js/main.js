/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'domReady!'
], function($, jQuery) {
    'use strict';


    //     `use strict`;
    console.log("Price Slider !.......");
    //require(["jquery" , "jquery/jquery-ui"], function($){ 

    $('.overlay').on('click', function() {
        $('html').removeClass('nav-open');
        $('body').removeClass('acc-opened cart-opened');
    });

    $('.showcart').on('click', function() {
        $('html').removeClass('nav-open');
    });

    $('.nav-toggle').on('click', function() {
        if ($('.minicart-wrapper').hasClass('active')) {
            $('.showcart').trigger('click');
        }
    });

    $(document).on('click', function() {
        $('.nav-sections').on('click', function(e) {
            e.stopPropagation();
        });
        $('.header').on('click', function(e) {
            $('html').removeClass('nav-open');
        });
    });

    $(".form-group input.form-control").on("focus blur", function() {
        if ($(this).val() == "") {
            $(this)
                .parents(".form-group")
                .toggleClass("focused");
        }
    });



});