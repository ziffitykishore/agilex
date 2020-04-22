define(
    [
        'jquery',
        'jquery/ui',
        'productListToolbarForm'
    ], function ($) {
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
                console.log('initObserve initialized');
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

                        self.ajaxSubmit(link);
                        e.stopPropagation();
                        e.preventDefault();
                    }
                );

            },

            checkUrl: function (url) {
                var regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;

                return regex.test(url) ? url : null;
            },


            ajaxSubmit: function (submitUrl) {
                var self = this;

                $.ajax(
                    {
                        url: submitUrl,
                        type: 'post',
                        dataType: 'json',
                        beforeSend: function () {
                            if (typeof window.history.pushState === 'function') {
                                window.history.pushState({ url: submitUrl }, '', submitUrl);
                            }
                        },
                        success: function (res) {
                            if (res.backUrl) {
                                window.location = res.backUrl;
                                return;
                            }
                            if (res.navigation) {
                                $(self.options.layerContainer).html(res.navigation);
                            }
                            if (res.products) {
                                $(self.options.productContainer).html(res.products);
                            }
                        },
                        error: function () {
                            console.log('error');
                            window.location.reload();
                        }
                    }
                );
            }
        }
        );

        return $.custom.sorter;
    }
);
