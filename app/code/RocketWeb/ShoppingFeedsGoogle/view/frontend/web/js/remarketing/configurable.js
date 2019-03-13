(function (factory) {
    if (typeof define === "function" && define.amd) {
        define([
            "jquery"
        ], factory);
    } else {
        factory(jQuery);
    }
}(function ($) {

    function getQueryParameters() {
        return document.location.search.replace(/(^\?)/,'').split("&").map(function(n){return n=n.split("="),this[n[0]]=n[1],this;}.bind({}))[0];
    }

    var GSF_Configurable = function(productConfig, associatedProducts) {
        this.selected = {};
        this.prices = {};
        this.productConfig = productConfig;
        this.associatedProducts = associatedProducts || {};
        this.currentAttributesValues = {};

        var self = this;
        var n = 0;

        $('.swatch-attribute').each(function (index, element) {
            n++;
            window.setTimeout(self.autoUpdate(element), 100 * n);
        });
    };

    GSF_Configurable.prototype = {

        autoUpdate: function(element) {
            this.registerEvents($(element));
            this.selectOptions($(element));
        },

        selectOptions: function(attributeElement) {
            var params = getQueryParameters();
            var attributeId = attributeElement.attr('attribute-id');
            var attributeOptions = attributeElement.find($('.swatch-attribute .swatch-option'));

            attributeOptions.each(function (index, element) {
                var temp = $(element);
                if (temp.attr('option-id') == params[attributeId]) {
                    temp.click();
                }
            });
        },

        registerEvents: function (attributeElement) {
            var self = this;
            var attributeOptions = attributeElement.find($('.swatch-attribute .swatch-option'));
            var attributeId = attributeElement.attr('attribute-id');

            attributeOptions.each(function (index, element) {
                $(element).on('click', function () {
                    //NEGATION here because the class 'selected' is added after the click even
                    var isSelected = !$(this).hasClass('selected');
                    var optionId = isSelected ? $(this).attr('option-id') : undefined;
                    self.update(attributeId, optionId);
                });
            });
        },

        update: function(attributeId, optionId) {
            var selectedProductId;

            // this means a product option (attribute) has been unchecked by the customer
            if (optionId === undefined) {
                delete this.currentAttributesValues[attributeId];
                return ;
            }

            // a certain attribute option has been selected by the customer
            this.currentAttributesValues[attributeId] = optionId;
            var countSelectedAttributes = Object.keys(this.currentAttributesValues).length;
            var totalProductAttributes = Object.keys(this.productConfig.attributes).length;

            // check if all the product attributes have been configured
            if (countSelectedAttributes != totalProductAttributes) {
                return ;
            }

            // search for a product matching the optionIds selected
            for (var productId in this.productConfig.index) {
                if (this.productConfig.index.hasOwnProperty(productId)) {
                    selectedProductId = productId;
                    for (var productAttributeId in this.currentAttributesValues) {
                        if (this.currentAttributesValues.hasOwnProperty(productAttributeId)) {
                            if (this.productConfig.index[productId][productAttributeId] != this.currentAttributesValues[productAttributeId]) {
                                selectedProductId = undefined;
                                break ;
                            }
                        }
                    }

                    // we found a productId that matches all the selected attribute options, break form the entire loop
                    if (selectedProductId !== undefined) {
                        break ;
                    }
                }
            }

            // no product found, just return
            if (selectedProductId === undefined) {
                return ;
            }

            // Update google remarketing tag
            if (!('google_tag_params' in window)) {
                google_tag_params = {};
            }

            var oldProdId = google_tag_params.ecomm_prodid;
            var optionPrices = this.productConfig.optionPrices[selectedProductId];
            google_tag_params.ecomm_totalvalue = parseFloat(optionPrices.finalPrice.amount).toFixed(2);

            if (selectedProductId in this.associatedProducts) {
                // use SKU
                google_tag_params.ecomm_prodid = this.associatedProducts[selectedProductId];
            } else {
                // use SQL product Id
                google_tag_params.ecomm_prodid = selectedProductId;
            }

            google_tag_params.ecomm_pagetype = 'product';
            google_custom_params = google_tag_params;
            var div = $('#gsf_associated_products');

            // call Google doubleclick service
            if (oldProdId != google_tag_params.ecomm_prodid && typeof google_conversion_id != 'undefined' && div.length) {
                div = div[0];
                var elem = document.createElement("img");
                elem.setAttribute("src", "//googleads.g.doubleclick.net/pagead/viewthroughconversion/"+google_conversion_id+"/?value=0&amp;guid=ON&amp;script=0");
                elem.setAttribute("height", "1"); elem.setAttribute("width", "1"); elem.setAttribute("style", "border-style:none;");
                div.appendChild(elem);
            }
        }
    };

    return GSF_Configurable;
}));
