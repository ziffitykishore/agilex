require(['jquery', 'slick'], function ($) {
    $(document).ready(function ($) {

        $('.showcart').on('click', function(){
            $("body").removeClass("search-opened");
        });

        var   openCtrl = $(".block-search .block-title"),
            closeCtrl = $(".search-close"),
            searchContainer = $(".block-search"),
            inputSearch = searchContainer.find("#search");

        initEvents();
        searchContainer.on("click", function(e) {
            e.stopPropagation()
            $("body").removeClass("cart-opened");
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



        //global variables
        var responsiveflag = false;

        responsiveResize();

        $(window).resize(responsiveResize);

        function responsiveResize() {

            if (($(window).width()) <= 768 && responsiveflag == false)
            {
                accordionFooter('enable');
                responsiveflag = true;
                $('header.page-header').addClass("fixed");
            }
            else if (($(window).width()) >= 769)
            {
                accordionFooter('disable');
                responsiveflag = false;
                stickyBar();
            }

        }


        function accordionFooter(status) {
            if(status == 'enable')
            {
                $('.mb-foot h5').on('click', function(e){
                    $(this).toggleClass('active').parent().find('ul').stop().slideToggle('medium');
                    e.preventDefault();
                })
                $('.mb-foot').addClass('accordion').find('ul').slideUp('fast');
            }
            else
            {
                $('.mb-foot h5').removeClass('active').off().parent().find('ul').removeAttr('style').slideDown('fast');
                $('.mb-foot').removeClass('accordion');
            }
        }

        /*  =================
            Sticky Navbar
            ================= */

        /* function call stick  */
        function stickyBar() {
            var elment = $('.page-main');
            if (elment.length) {
                var stickyOffset = elment.offset().top + 100;
                $(window).scroll(function() {
                    var sticky = elment,
                        scroll = $(window).scrollTop();
                    if (scroll >= stickyOffset) $('header.page-header').addClass("fixed");
                    else $('header.page-header').removeClass("fixed");
                });
            }
        }


        /*  =================
            Home Page Why Us Slider
            ================= */


        slick_on_mobile( $('.why-row'));

        function slick_on_mobile(slider){
            $(window).on('load resize', function() {
                if ($(window).width() > 767) {
                    if (slider.hasClass('slick-initialized')) {
                        slider.slick('unslick');
                    }
                    return
                }
                if (!slider.hasClass('slick-initialized')) {
                    return slider.slick({
                        dots: false,
                        arrows: false,
                        autoplay:true,
                        autoplaySpeed:2000,
                        centerMode: true,
                        centerPadding:"40"
                    });
                }
            });
        };

        $(document).on('change','.up', function(){
            var names = [];
            var length = $(this).get(0).files.length;
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names.push($(this).get(0).files[i].name);
            }
            // $("input[name=file]").val(names);
            if(length>2){
                var fileName = names.join(', ');
                $(this).closest('.field').find('.input-text').attr("value",length+" files selected");
            }
            else{
                $(this).closest('.field').find('.input-text').attr("value",names);
            }
        });



        /*  =================
            MM-Menu
            ================= */

        var ua = window.navigator.userAgent;
        var isIE = /MSIE|Trident/.test(ua);

        if ( !isIE ) {
            new Mmenu( document.querySelector( '#menu' ) );
        } else {
            $('body').addClass('ie')
        }

    });
});