define(
    [
    'jquery',
    'jquery/ui',
    'productListToolbarForm'
    ], function ($) {
        "use strict";

        $.widget(
            'custom.layer', {

                options: {            
                    navigationSelector: '#layered-filter-block'
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
                            if(!link) { return; }
                            el.off('click').on(
                                'click', function (e) {
                                    if (el.hasClass('swatch-option-link-layered')) {
                                        var childEl = el.find('.swatch-option');
                                        childEl.addClass('selected');
                                    } else {
                                        var checkboxEl = el.find('input[type=checkbox]');
                                        checkboxEl.prop('checked', !checkboxEl.prop('checked'));
                                    }

                                    self.ajaxSubmit(link);
                                    e.stopPropagation();
                                    e.preventDefault();
                                }
                            );
                            var checkbox = el.find('input[type=checkbox]');
                            checkbox.off('click').on(
                                'click', function (e) {
                                    self.ajaxSubmit(link);
                                    e.stopPropagation();
                                }
                            );
                        }
                    );

                    $(".filter-current a").off('click').on(
                        'click', function (e) {
                            var link = self.checkUrl($(this).prop('href'));
                            if(!link) { return; }

                            self.ajaxSubmit(link);
                            e.stopPropagation();
                            e.preventDefault();
                        }
                    );

                    $(".filter-actions a").off('click').on(
                        'click', function (e) {
                            var link = self.checkUrl($(this).prop('href'));
                            if(!link) { return; }

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
                            data: {isAjax: 1},
                            type: 'post',
                            dataType: 'json',
                            beforeSend: function () {                                
                                if (typeof window.history.pushState === 'function') {
                                    window.history.pushState({url: submitUrl}, '', submitUrl);
                                }
                            },
                            success: function (res) {
                                if (res.backUrl) {
                                    window.location = res.backUrl;
                                    return;
                                }
                                if (res.navigation) {
                                    $(self.options.navigationSelector).replaceWith(res.navigation);
                                    $(self.options.navigationSelector).trigger('navigationUpdated');
                                }
                                if (res.products) {
                                    $(self.options.productsListSelector).replaceWith(res.products);
                                    $(self.options.productsListSelector).trigger('contentUpdated');
                                }                                
                            },
                            error: function () {
                                window.location.reload();
                            }
                        }
                    );
                }
            }
        );

        return $.custom.layer;
    }
);
