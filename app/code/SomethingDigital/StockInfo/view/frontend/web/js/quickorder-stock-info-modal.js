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
        isOAN: null,
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
                 // Stop Tab key Action On empty Product Popup
                $(".fieldset").on('keydown', 'input[type=text]', function(e) {          
                    var keyCode = e.keyCode || e.which;
                    var values = $(this).val();
                    if (keyCode == 9 && values.length > 9 ) {
                        $(this).next('.qty').find('input[type=number]').focus(); 
                    }
                });
                $('.block-addbysku').on('click',this.popupOpenerSelector, function () {
                    var data = [];
                    var stockData = $(this).parent().data('stock');

                    $.each(stockData, function(productId, stockItems) {
                        $.each(stockItems, function(key, stockItem) {
                            if(stockItem.label == "Duncan, SC"){
                                data[0] = stockItem;
                            }
                            else if(stockItem.label == "Queens, NY"){
                                data[1] = stockItem;
                            }
                            else{
                                data[2] = stockItem;
                            }
                        });
                    });
                    self.stockDataObservable(data);
                    self.modalWindow.modal('openModal');
                });
            }
        }
    });
});
