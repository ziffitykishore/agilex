define(
    [
        'jquery',
        'Mageplaza_AjaxLayer/js/action/submit-filter',
        'jquery/ui',
        'productListToolbarForm'
    ], function ($, submitFilterAction) {
        "use strict";

        $.widget(
            'custom.sorter', {

            options: {
                productContainer: '#layer-product-list',
                layerContainer: '.layered-filter-block-container'
            },

            _create: function () {
                this.initObserve();
            },

            initObserve: function () {
                var self = this;
                var aElements = this.element.find('a');
                aElements.each(
                    function (index) {
                        var el = $(this);
                        var link = self.checkUrl(el.prop('href'));
                        if (!link) { return; }
                    }
                );

                $(".sorter-select a").off('click').on(
                    'click', function (e) {
                        var link = self.checkUrl($(this).prop('href'));
                        if (!link) { return; }

                        submitFilterAction(link);
                        e.stopPropagation();
                        e.preventDefault();
                        $('body').removeClass('sorter-active');
                        $('body').on('click','.sorter-close',function(){
                            $('body').removeClass('sorter-active');
                        });
                    }
                );

            },

            checkUrl: function (url) {
                var regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;

                return regex.test(url) ? url : null;
            }
        }
        );

        return $.custom.sorter;
    }
);
