define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Ui/js/modal/modal'
], function ($, ko, Component, modal) {
    'use strict';

    return Component.extend({
        modalWindow: null,
        productType: null,
        popupSelector: null,
        popupOpenerSelector: null,
        swatchSelector: null,
        swatchWidgetName: 'mageSwatchRenderer',
        stockData: null, // full data
        stockDataObservable: ko.observable([]), // data to use in template
        stockDataIsEmptyObservable: ko.observable(false),
        selectedConfigurableSimpleProductId: null,
        initialize: function () {
            this._super();
            this.updateStockDataObservable();
            this.bindConfigurableOptions();
        },
        updateStockDataObservable: function (simpleProductId) {
            var data = [];
            $.each(this.stockData, function(productId, stockItems) {
                if (simpleProductId && simpleProductId != productId) {
                    return true;
                }
                $.each(stockItems, function(key, stockItem) {
                    data.push(stockItem);
                });
            });
            if (data.length) {
                this.stockDataIsEmptyObservable(false);
            } else {
                this.stockDataIsEmptyObservable(true);
            }
            this.stockDataObservable(data);
        },
        createModalPopup: function () {
            if (this.modalWindow == null) {
                var options = {
                    'type': 'popup',
                    'modalClass': 'popup-stock-info',
                    'responsive': true,
                    'innerScroll': true,
                    'buttons': []
                };
                this.modalWindow = $(this.popupSelector);
                modal(options, this.modalWindow);

                var self = this;
                $(this.popupOpenerSelector).click(function () {
                    self.modalWindow.modal('openModal');
                });
            }
        },
        bindConfigurableOptions: function () {
            var self = this;
            if (this.productType === 'configurable') {
                $('.price-box').on('updatePrice', function() {
                    var swatchWidget = $(self.swatchSelector).data(self.swatchWidgetName);
                    if (!swatchWidget) {
                        return;
                    }
                    var productId = swatchWidget.getProduct();
                    if (self.selectedConfigurableSimpleProductId === productId) {
                        return;
                    }
                    // if productId is null, then show all data
                    self.updateStockDataObservable(productId);
                    self.selectedConfigurableSimpleProductId = productId;
                });
            }
        }
    });
});
