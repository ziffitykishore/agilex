require(['jquery', 'slick'], function ($) {
    $(document).ready(function ($) {




        /*  =================
            Footer Links
            ================= */

        //global variables
        var responsiveflag = false;

        responsiveResize();

        $(window).resize(responsiveResize);

        function responsiveResize() {

            if (($(window).width()) <= 768 && responsiveflag == false)
            {
                accordionFooter('enable');
                responsiveflag = true;
            }
            else if (($(window).width()) >= 769)
            {
                accordionFooter('disable');
                responsiveflag = false;
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
        function stickyBar(elm) {
            var elment = $(elm);
            if (elment.length) {
                var stickyOffset = elment.offset().top + 100;
                $(window).scroll(function() {
                    var sticky = elment,
                        scroll = $(window).scrollTop();
                    if (scroll >= stickyOffset) sticky.addClass("fixed");
                    else sticky.removeClass("fixed");
                });
            }
        }
        stickyBar("header.page-header");


        /*  =================
            Home Page Why Us Slider
            ================= */


        slick_on_mobile( $('.why-row'));

        function slick_on_mobile(slider, settings){
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