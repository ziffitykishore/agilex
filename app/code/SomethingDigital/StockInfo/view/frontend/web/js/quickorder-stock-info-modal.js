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
                        window.lastId = $(this).attr('id');
                    } else{
                        if(keyCode == 9)
                            return false;
                    }
                });
                $('body').on('DOMSubtreeModified', '.product-block', function(){
                    $('body').find('#'+window.lastId).focus();
                });
                $('.block-addbysku').on('click',this.popupOpenerSelector, function () {
                    var data = [];
                    var stockData = $(this).parent().data('stock');

                    $.each(stockData, function(productId, stockItems) {
                        $.each(stockItems, function(key, stockItem) {
                            data.push(stockItem);
                        });
                    });
                    
                    // sort by label
                    data.sort(function (a, b) {
                        return b.label.localeCompare(a.label);
                    });

                    self.stockDataObservable(data);
                    self.modalWindow.modal('openModal');
                });
            }
        }
    });
});
