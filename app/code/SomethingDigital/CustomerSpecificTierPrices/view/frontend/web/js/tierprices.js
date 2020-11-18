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

            var self = this;
            this.getPrices(sku).done(function (response, textStatus) {

                if (response.data[sku] === undefined) {
                    return;
                }

                var prices = response.data[sku];

                prices = self.removeIfZero(prices);
                var basePrice = $('.price-field').first().data('price');
                if ($('.prices-tier.items').length) {
                    // update magento tier prices if customer special price is lower
                    $('.price-field').each(function() {
                        if ($(this).data('price') > prices['unitPrice']) {
                            $('span.price', $(this)).text(prices['currencySymbol'] + prices['unitPrice']);
                            var saving = self.getSaveBreak(basePrice, prices['unitPrice']);
                            var index = $(this).data('index');
                            $('.col-' + index).text(saving);
                        }
                    });
                }

                if (!prices['QtyPrice1'] && !prices['QtyPrice2'] && !prices['QtyPrice3']) {
                  $('.as-low-as').hide();
                  return;
                }

                $('.product-info-main > table.prices-tier').hide();

                prices['saveBreak1'] = '';
                prices['saveBreak2'] = '';
                prices['saveBreak3'] = '';

                let tierPricesArray = [prices['QtyPrice1'], prices['QtyPrice2'], prices['QtyPrice3']];
                let lowestPrice = Math.min.apply(null, tierPricesArray.filter(Boolean));
                $('.as-low-as .price-wrapper ').text(prices['currencySymbol'] + lowestPrice);

                prices = self.calculateSavings(prices);

                if (prices['QtyPrice1'] && prices['QtyBreak1'] > 1) {
                    prices['UpFirstBreak'] = '1 - ' + (prices['QtyBreak1'] - 1);
                    if (prices['pricePer100']) {
                        prices['RegularPrice'] = '$' + prices['unitPrice'].toFixed(2);
                    } else {
                        prices['RegularPrice'] = prices['price'];
                    }
                }

                if (!prices['QtyPrice2'] && !prices['QtyPrice3']) {
                    prices['QtyBreak1'] = prices['QtyBreak1'] + '+';
                }

                if (prices['QtyBreak2'] && !prices['QtyPrice3']) {
                    prices['QtyBreak1'] = prices['QtyBreak1'] + ' - ' + (prices['QtyBreak2'] - 1);
                    prices['QtyBreak2'] = prices['QtyBreak2'] + '+';
                }

                if (prices['QtyPrice3']) {
                    prices['QtyBreak1'] = prices['QtyBreak1'] + ' - ' + (prices['QtyBreak2'] - 1);
                    prices['QtyBreak2'] = prices['QtyBreak2'] + ' - ' + (prices['QtyBreak3'] - 1);
                    prices['QtyBreak3'] = prices['QtyBreak3'] + '+';
                }

                tierPrices.push(prices);

            }).fail(function (jqXHR, textStatus, errorThrown) {
                // In this case we don't want to update the prices
            });
        },
        removeIfZero: function(prices) {
            for (let i = 1; i <= 3; i++) {
                if (prices['QtyPrice'+i] == 0) {
                    prices['QtyPrice'+i] = '';
                }
            }
            return prices;
        },
        calculateSavings: function(prices) {
            if (prices['msrp'] > prices['unitPrice']) {
                prices['saveMsrp'] = this.getSaveBreak(prices['msrp'], prices['unitPrice']);
            } else {
                prices['saveMsrp'] = '';
            }
            var maxPrice = Math.max(prices['unitPrice'], prices['msrp']);
            for (let i = 1; i <= 3; i++) {
                if (prices['QtyPrice'+i]) {
                    prices['saveBreak'+i] = this.getSaveBreak(maxPrice, prices['QtyPrice'+i]);
                    prices['QtyPrice'+i] = prices['currencySymbol'] + prices['QtyPrice'+i];
                }
            }
            return prices;
        },
        getSaveBreak: function(maxPrice, qtyPrice) {
            return (Math.round(100 - ((100 / maxPrice) * qtyPrice))).toFixed()+'%';
        },
        getPrices: function(sku) {
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