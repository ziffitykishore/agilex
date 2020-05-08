define([
    'jquery',
    'Mageplaza_AjaxLayer/js/action/submit-filter',
    'Magento_Catalog/js/price-utils',
    'jquery/ui',
    'accordion',
    'productListToolbarForm'
], function ($, submitFilterAction, ultil) {
    "use strict";

    $.widget('custom.layer', $.mage.accordion,{
        options: {                        
            isAjax: true,            
            checkboxEl: 'input[type=checkbox], input[type=radio]',
            sliderElementPrefix: '#custom_slider_',
            sliderTextElementPrefix: '#custom_price_text'
        },

        _create: function () {                        
            this.initProductListUrl();
            this.initObserve();
            this.initSlider();
            this.initActiveItems();            
        },

        initActiveItems: function () {
            var layerActives = this.options.active,
                actives = [];

            if (typeof window.layerActiveTabs !== 'undefined') {
                layerActives = window.layerActiveTabs;
            }
            if (layerActives.length) {
                this.element.find('.filter-options-item').each(function (index) {
                    if (~$.inArray($(this).attr('attribute'), layerActives)) {
                        actives.push(index);
                    }
                });
            }

            this.options.active = actives;

            return this;
        },

        initProductListUrl: function () {
            var isProcessToolbar = false,
                isAjax = this.options.isAjax;
            $.mage.productListToolbarForm.prototype.changeUrl = function (paramName, paramValue, defaultValue) {
                if (isProcessToolbar) {
                    return;
                }
                if (isAjax) {
                    isProcessToolbar = true;
                }

                var urlPaths = this.options.url.split('?'),
                    baseUrl = urlPaths[0],
                    urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                    paramData = {},
                    parameters;
                for (var i = 0; i < urlParams.length; i++) {
                    parameters = urlParams[i].split('=');
                    paramData[parameters[0]] = parameters[1] !== undefined
                        ? window.decodeURIComponent(parameters[1].replace(/\+/g, '%20'))
                        : '';
                }
                paramData[paramName] = paramValue;
                if (paramValue === defaultValue) {
                    delete paramData[paramName];
                }
                paramData = $.param(paramData);
                if (isAjax) {
                    submitFilterAction(baseUrl + (paramData.length ? '?' + paramData : ''));
                } else location.href = baseUrl + (paramData.length ? '?' + paramData : '');
            }
        },

        initObserve: function () {
            var self = this;
            var isAjax = this.options.isAjax;

            // fix browser back, forward button
            if (typeof window.history.replaceState === "function") {
                window.history.replaceState({url: document.URL}, document.title);

                setTimeout(function () {
                    window.onpopstate = function (e) {
                        if (e.state) {
                            submitFilterAction(e.state.url, 1);
                        }
                    };
                }, 0)
            }

            var pageElements = $('#layer-product-list').find('.pages a');
            pageElements.each(function () {
                var el = $(this),
                    link = self.checkUrl(el.prop('href'));
                if (!link) {
                    return;
                }

                el.bind('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    if (isAjax) {
                        submitFilterAction(link);
                    } else location.href = link;
                })
            });

            var currentElements = this.element.find('.filter-current a, .filter-actions a, .filter-title a');
            currentElements.each(function (index) {
                var el = $(this),
                    link = self.checkUrl(el.prop('href'));
                if (!link) {
                    return;
                }

                el.bind('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    if (isAjax) {
                        submitFilterAction(link);
                    } else {
                        location.href = link;
                    }
                });
            });

            var optionElements = this.element.find('.filter-options a');
            optionElements.each(function (index) {
                var el = $(this),
                    link = self.checkUrl(el.prop('href'));
                if (!link) {
                    return;
                }

                el.bind('click', function (e) {
                    if (el.hasClass('swatch-option-link-layered')) {
                        self.selectSwatchOption(el);
                    } else {
                        var checkboxEl = el.siblings(self.options.checkboxEl);
                        checkboxEl.prop('checked', !checkboxEl.prop('checked'));
                    }

                    e.stopPropagation();
                    e.preventDefault();
                    self.ajaxSubmit(link);
                });

                var checkbox = el.siblings(self.options.checkboxEl);
                checkbox.bind('click', function (e) {
                    e.stopPropagation();
                    self.ajaxSubmit(link);
                });
            });

            var swatchElements = this.element.find('.swatch-attribute');
            swatchElements.each(function (index) {
                var el = $(this);
                var attCode = el.attr('attribute-code');
                if (attCode) {
                    if (self.options.params.hasOwnProperty(attCode)) {
                        var attValues = self.options.params[attCode].split(",");
                        var swatchOptions = el.find('.swatch-option');
                        swatchOptions.each(function (option) {
                            var elOption = $(this);
                            if ($.inArray(elOption.attr('option-id'), attValues) !== -1) {
                                elOption.addClass('selected');
                            }
                        });
                    }
                }
            });
        },

        selectSwatchOption: function (el) {
            var childEl = el.find('.swatch-option');
            if (childEl.hasClass('selected')) {
                childEl.removeClass('selected');
            } else {
                childEl.addClass('selected');
            }
        },

        initSlider: function () {
            var self = this,
                slider = this.options.slider;

            for (var code in slider) {
                if (slider.hasOwnProperty(code)) {
                    var sliderConfig = slider[code],
                        sliderElement = self.element.find(this.options.sliderElementPrefix + code),
                        priceFormat = sliderConfig.hasOwnProperty('priceFormat') ? JSON.parse(sliderConfig.priceFormat) : null;

                    if (sliderElement.length) {
                        sliderElement.slider({
                            range: true,
                            min: sliderConfig.minValue,
                            max: sliderConfig.maxValue,
                            values: [sliderConfig.selectedFrom, sliderConfig.selectedTo],
                            slide: function (event, ui) {
                                self.displaySliderText(code, ui.values[0], ui.values[1], priceFormat);
                            },
                            change: function (event, ui) {
                                self.ajaxSubmit(self.getSliderUrl(sliderConfig.ajaxUrl, ui.values[0], ui.values[1]));
                            }
                        });
                    }
                    self.displaySliderText(code, sliderConfig.selectedFrom, sliderConfig.selectedTo, priceFormat);
                }
            }
        },

        displaySliderText: function (code, from, to, format) {
            var textElement = this.element.find(this.options.sliderTextElementPrefix);
            if (textElement.length) {
                if (format !== null) {
                    from = this.formatPrice(from, format);
                    to = this.formatPrice(to, format);
                }

                textElement.html('<div class="min-price">'+ from +'</div><div class="max-price">'+ to + '</div>');
            }
        },

        getSliderUrl: function (url, from, to) {
            return url.replace('from-to', from + '-' + to);
        },

        formatPrice: function (value, format) {
            return ultil.formatPrice(value, format);
        },

        ajaxSubmit: function (submitUrl) {
            var isAjax = this.options.isAjax;
            this.element.find(this.options.mobileShopbyElement).trigger('click');

            if (isAjax) {
                return submitFilterAction(submitUrl);
            }
            location.href = submitUrl;
        },

        checkUrl: function (url) {
            var regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;

            return regex.test(url) ? url : null;
        },
        
    });

    return $.custom.layer;
});
