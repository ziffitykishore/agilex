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

            _create: function () {
                this.initObserve();
            },

            initObserve: function () {
                var self = this;
                var aElements = $('#layer-product-list').find('.sorter-select a');
                aElements.each(
                    function () {
                        var el = $(this);
                        var link = self.checkUrl(el.prop('href'));
                        if (!link) { return; }
                        el.bind('click', function (e) {
                            e.stopPropagation();
                            e.preventDefault();
                            submitFilterAction(link);
                            $('body').removeClass('sorter-active');
                            $('body').on('click','.sorter-close',function(){
                                $('body').removeClass('sorter-active');
                            });
                        })
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
