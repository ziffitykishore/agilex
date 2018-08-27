/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'domReady!'
], function ($, jQuery) {
    'use strict';

    
    //     `use strict`;
        console.log("I'm Ready!.......");
    //require(["jquery" , "jquery/jquery-ui"], function($){ 
   
        $('.overlay').on('click', function(){
            $('html').removeClass('nav-open');
            $('body').removeClass('acc-opened cart-opened');
        });

        $('.showcart').on('click', function(){
            $('html').removeClass('nav-open');
        });

        $('#btn-close').on('click', function(){
            $('body').removeClass('cart-opened');
        });
        
        $(document).on('click', function(){
            $('.nav-sections').on('click', function(e){
                e.stopPropagation();
            }); 
            $('.header').on('click', function(e){
                $('html').removeClass('nav-open');
            });      
        });      
});


$(".form-group input.form-control").on("focus blur", function() {
  if ($(this).val() == "") {
      $(this)
          .parents(".form-group")
          .toggleClass("focused");
  }
});





