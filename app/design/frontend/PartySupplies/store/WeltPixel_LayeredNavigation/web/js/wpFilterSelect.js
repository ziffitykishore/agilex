define([
    "jquery",
    "nice-select"
], function ($) {
    "use strict";

    window.wpFilterSelect = {
        markSelected: function() {
            $.each($('#wp_ln_shopby_items li'), function() {
                var id = $(this).data('attr-id');
                if(id) {
                    var filterElem = $('#wp_ln_attr_' + id);
                    var filterSwatchElem = $('#wp_ln_swatch_attr_' + id);

                    if(filterElem.length) {
                        filterElem.addClass('wp-ln-selected');
                        $('#sorter').niceSelect();
                        $('.filter-url').attr('href', $('.category-url').data('category-url'));
                    }

                    if(filterSwatchElem.length) {
                        filterSwatchElem.addClass('wp-ln-selected');
                        $('#sorter').niceSelect();
                    }
                }
            });

            var responsiveflag = false;
            responsiveResize();
            $(window).resize(responsiveResize);

            function responsiveResize() {

                if (($(window).width()) <= 768 && responsiveflag == false)
                {
                    responsiveflag = true;
                    $('.breadcrumbs-inner .toolbar').prependTo('#layer-product-list');
                }
                else if (($(window).width()) >= 769)
                {
                    responsiveflag = false;
                    $('#layer-product-list .toolbar:first-child').appendTo('.breadcrumbs-inner');
                }

            }
        }
    }
});