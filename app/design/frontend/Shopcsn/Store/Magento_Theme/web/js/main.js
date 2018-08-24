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
        console.log("ready...!");
    //require(["jquery" , "jquery/jquery-ui"], function($){ 


    //HEADER SHADOW ---------------------------------------------------------------------/
  function headerSticky() {
    var header = $(".header-wrapper"),
      headerOffset = header.offset().top;
    headerHeight = header.outerHeight();

    if ($("body").hasClass("home") != 1) {
      $(".main-content").css("margin-top", headerHeight);
    }

    $(window).scroll(function() {});
  }

  headerSticky();
  $(window).on("resize", headerSticky);
  // Hide Header on on scroll down
  var didScroll;
  var lastScrollTop = 0;
  var delta = 0;
  var navbarHeight = $(".header-wrapper").outerHeight();
  $(".header-wrapper").addClass("header-ani");
  function headerStick() {
    $(window).scroll(function(event) {
      didScroll = true;
      if ($(this).scrollTop() === 0) {
        $(".header-wrapper").removeClass("slideUp slideDown");
      }
    });
  }

  //reset the scroll to 0 (top of page)
  $(window).on("beforeunload", function() {
    setInterval(function() {
      $(this).scrollTop(0);
      $("body").fadeOut("slow");
    }, 500);
    $("body").addClass("loader");
    if ($(this).scrollTop() === 0) {
      $(".header-wrapper").removeClass("slideUp slideDown");
    }
  });

  headerStick();
  $(window).on("resize", headerStick);
  setInterval(function() {
    if (didScroll) {
      hasScrolled();
      didScroll = false;
    }
  }, 500);

  function hasScrolled() {
    var st = $(this).scrollTop();

    // Make sure they scroll more than delta
    if (Math.abs(lastScrollTop - st) <= delta) return;

    // If they scrolled down and are past the navbar, add class .nav-up.
    // This is necessary so you never see what is "behind" the navbar.
    if (st === 0) {
      $(".header-wrapper").removeClass("header-sticky");
    } else {
      if (st > lastScrollTop && st > navbarHeight - 20) {
        // Scroll Down
        $(".header-wrapper")
          .removeClass("header-sticky slideDown")
          .addClass("slideUp");
      } else {
        // Scroll Up
        if (st + navbarHeight + $(window).height() < $(document).height()) {
          $(".header-wrapper")
            .removeClass("slideUp")
            .addClass("header-sticky slideDown");
        }
      }
    }

    lastScrollTop = st;
  }



        
          
        if($('.customer-welcome').hasClass('active')){
            console.log('asd');
        }
         
    
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








