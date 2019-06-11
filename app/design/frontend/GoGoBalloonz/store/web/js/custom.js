define(['jquery', 'slick', 'scroller'], function ($) {
    $(document).ready(function ($) {

        var c, currentScrollTop = 0,
            navbar = $('.header-wrapper');

        $(window).scroll(function () {
            var a = $(window).scrollTop();
            var b = navbar.height();

            currentScrollTop = a;

            if (c < currentScrollTop && a > b + b) {
                navbar.addClass("scrollUp");
            } else if (c > currentScrollTop && !(a <= b)) {
                navbar.removeClass("scrollUp");
            }
            c = currentScrollTop;
        });


        $(window).scroll(function(){
            if ($(window).scrollTop() >= 1) {
                $('.header-wrapper').addClass('fixed-header');
            }
            else {
                $('.header-wrapper').removeClass('fixed-header');
            }
        });

        // Home slider
        $('.home-slider').on('init', function (e, slick) {
            var $firstAnimatingElements = $('.banner-item:first-child').find('[data-animation]');
            doAnimations($firstAnimatingElements);
        });
        $('.home-slider').on('beforeChange', function (e, slick, currentSlide, nextSlide) {
            var $animatingElements = $('.banner-item[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
            doAnimations($animatingElements);
        });
        $('.home-slider').slick({
            dots: true,
            infinite: true,
            autoplaySpeed: 6000,
            speed: 3000,
            fade: true,
            cssEase: 'linear',
            arrows: false,
            autoplay: true
        });

        function doAnimations(elements) {
            var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
            elements.each(function () {
                var $this = $(this);
                var $animationDelay = $this.data('delay');
                var $animationType = 'animated ' + $this.data('animation');
                $this.css({
                    'animation-delay': $animationDelay,
                    '-webkit-animation-delay': $animationDelay
                });
                $this.addClass($animationType).one(animationEndEvents, function () {
                    $this.removeClass($animationType);
                });
            });
        }

        // Home our products

        body_sizer();
        $(window).resize(body_sizer);

        function body_sizer() {
            var bodyheight = $(window).height();
            $(".home-slider .banner-item").height(bodyheight);
        }



        new Mmenu( document.querySelector( '#menu' ) );



        $('.featured-product-inner > .product-item').prependTo('.featured-product-inner .product-items');

        $('.best-seller .product-items').slick({
            dots: false,
            infinite: true,
            arrows: true,
            autoplay: true
        });


        function toggle(container, item, bodyClass) {
            $(container).find(item).on('click', function(){
                $(container).toggleClass('_active');

                $('body').toggleClass(bodyClass);

            });

            $(container).on("click", function(e) {
                e.stopPropagation()
            });
        }


        //toggle('.block-search', '.block-title', 'search-opened');

        var   openCtrl = $(".block-search .block-title"),
              closeCtrl = $(".search-close"),
              searchContainer = $(".block-search"),
              inputSearch = searchContainer.find("#search");

            initEvents();
            searchContainer.on("click", function(e) {
                e.stopPropagation()
            });

        function initEvents() {
            openCtrl.on("click", openSearch);
            closeCtrl.on("click", closeSearch);
            $(document).on("keyup", function(ev) {
                if (ev.keyCode == 27) {
                    closeSearch()
                }
            });
        }

        function openSearch(e) {
            $("body").toggleClass("search-opened");
            setTimeout(function() {
                inputSearch.focus()
            }, 600);
        }
        function closeSearch() {
            $("body").removeClass("search-opened");
            inputSearch.blur();
            inputSearch.value = ""
        }

        $(document).click(function() {
            $("body").removeClass("search-opened");
            inputSearch.blur();
            inputSearch.value = ""
        });

        $(".scroll-down").on("click", function(e) {
            e.preventDefault();
            $("html, body").animate({
                scrollTop: $($(this).attr("href")).offset().top
            }, 500, "linear")
        });








        //global variables
        var responsiveflag = false;

        responsiveResize();

        $(window).resize(responsiveResize);

        if (navigator.userAgent.match(/Android/i)) {
            var viewport = document.querySelector('meta[name="viewport"]');
            viewport.setAttribute('content', 'initial-scale=1.0,maximum-scale=1.0,user-scalable=0,width=device-width,height=device-height');
            window.scrollTo(0, 1);
        }




        $('.navigation .parent > a').on('click', function (){
            $(this).closest('.parent').toggleClass('active-slide');
        });

        $('.nav-toggle').on('click', function(){
            var parentLi = $('.navigation .parent');
            if (parentLi.hasClass('active-slide')) {
                parentLi.removeClass('active-slide');
            }
        });




        function responsiveResize() {

            if (($(window).width()) <= 768 && responsiveflag == false)
            {
                accordionFooter('enable');

                $('.location-track').appendTo('.nav-sections');



                $('.navigation .parent').each(function(){

                    //var icon = '<span class="icon icon-angle-right"></span>';
                    $(this).find('> a').addClass('level-top')
                    $(this).find('> a').click(function(e){
                        e.preventDefault();
                    });

                    if(!$(this).find('.icon').length){
                        //$(icon).appendTo($(this));
                    }

                });



                responsiveflag = true;
            }
            else if (($(window).width()) >= 769)
            {
                accordionFooter('disable');
                $('.location-track').insertBefore('.account-links');
                $('.navigation .parent .icon').remove();
                responsiveflag = false;
            }

        }


        function accordionFooter(status)
        {
            if(status == 'enable')
            {
                $('.accordion-menu .block-title').on('click', function(e){
                    $(this).toggleClass('active').parent().find('.block-content').stop().slideToggle('medium');
                    e.preventDefault();
                })
                $('.accordion-menu').addClass('accordion').find('.block-content').slideUp('fast');
            }
            else
            {
                $('.accordion-menu .block-title').removeClass('active').off().parent().find('.block-content').removeAttr('style').slideDown('fast');
                $('.accordion-menu').removeClass('accordion');
            }
        }

    });



    function bgSource(imgcontainer) {
        $(imgcontainer).each(function () {
            var img = $(this).find("img");

            if(img.length) {
                var height = img.height();
                var img_src = img.attr("src");

                $(this).css({
                    "background-image": "url(" + img_src + ")",
                    "background-size": "cover",
                    "background-repeat": "no-repeat",
                    "background-position": "center"
                });
            }

            img.hide();
        });
    }

    bgSource(".home-slider .banner-item");

    bgSource(".category-info");

    $('.product-social-links').appendTo('.box-tocart .fieldset');

});


