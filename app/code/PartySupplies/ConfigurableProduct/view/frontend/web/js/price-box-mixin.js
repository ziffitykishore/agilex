define([
    'jquery',
    'mage/template',
    'Magento_Catalog/js/price-utils',
    'mage/utils/wrapper'
], function ($, mageTemplate, utils, wrapper) {
    'use strict';
 
    return function(targetModule){
 
        var reloadPrice = targetModule.prototype.reloadPrice;
        var reloadPriceWrapper = wrapper.wrap(reloadPrice, function(original){

            var priceFormat = (this.options.priceConfig && this.options.priceConfig.priceFormat) || {},
                priceTemplate = mageTemplate(this.options.priceTemplate);

            _.each(this.cache.displayPrices, function (price, priceCode) {
                price.final = _.reduce(price.adjustments, function (memo, amount) {
                    return memo + amount;
                }, price.amount);

                price.formatted = utils.formatPrice(price.final, priceFormat);

                $('[data-price-type="' + priceCode + '"]', this.element).html(priceTemplate({
                    data: price
                }));

                price.formatted = utils.formatPrice(price.final * this.options.priceConfig.casePack, priceFormat);

                $('[data-role="case_price"]').html(priceTemplate({
                    data: price
                }));
            }, this);
        });
        targetModule.prototype.reloadPrice = reloadPriceWrapper;
        return targetModule;
    };
});
