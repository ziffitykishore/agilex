define([
    'uiComponent',
    'jquery',
    'ko'
], function(Component,$,ko) {
    'use strict';

    var tierPrices= ko.observableArray([]);
 
    return Component.extend({
        productType: null,
        initialize: function () {
            this._super();
            if (this.productType != 'simple') {
                return;
            }
            var sku = $('[itemprop=sku]').text();

            this.getPrices(sku).done(function (response, textStatus) {

                var prices = response.data[sku];

                prices['saveBreak1'] = '';
                prices['saveBreak2'] = '';
                prices['saveBreak3'] = '';

                if (prices['QtyPrice1'] != 0 || prices['QtyPrice2'] != 0 || prices['QtyPrice3'] != 0) {
                    let tierPricesArray = [prices['QtyPrice1'], prices['QtyPrice2'], prices['QtyPrice3']];
                    let lowestPrice = Math.min.apply(null, tierPricesArray.filter(Boolean));
                    $('.as-low-as .price-wrapper ').text(prices['currencySymbol'] + lowestPrice);
                }

                if (prices['QtyPrice1']) {
                    prices['saveBreak1'] =(Math.round(100 - ((100 / prices['unitPrice']) * prices['QtyPrice1']))).toFixed()+'%';
                    prices['QtyPrice1'] = prices['currencySymbol'] + prices['QtyPrice1'];
                }
                if (prices['QtyPrice2']) {
                    prices['saveBreak2'] = (Math.round(100 - ((100 / prices['unitPrice']) * prices['QtyPrice2']))).toFixed()+'%';
                    prices['QtyPrice2'] = prices['currencySymbol'] + prices['QtyPrice2'];
                }
                if (prices['QtyPrice3']) {
                    prices['saveBreak3'] = (Math.round(100 - ((100 / prices['unitPrice']) * prices['QtyPrice3']))).toFixed()+'%';
                    prices['QtyPrice3'] = prices['currencySymbol'] + prices['QtyPrice3'];
                }
                if (!prices['QtyPrice2'] && !prices['QtyPrice3']) {
                    prices['QtyBreak1'] = prices['QtyBreak1'] + '+';
                }
                if (prices['QtyBreak2'] && !prices['QtyPrice3']) {
                    prices['QtyBreak2'] = prices['QtyBreak2'] + '+';
                }

                if (!prices['QtyPrice1'] && !prices['QtyPrice2'] && !prices['QtyPrice3']) {
                    return;
                } else {
                    $('.product-info-main > table.prices-tier').hide();
                }

                tierPrices.push(prices);

            }).fail(function (jqXHR, textStatus, errorThrown) {
                // In this case we don't want to update the prices
                console.log("Request failed: " + textStatus + ' : ' + errorThrown);
            });
        },
        getPrices: function(sku){
            var settings = {
                method: 'POST',
                dataType: 'json',
                url: '/csp/prices',
                data: {
                    products: {0:sku},
                }
            }
            return $.ajax(settings);
        },
        getTierPrices: function () {
            return tierPrices;
        }
    });
});