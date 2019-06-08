define([
        'ko',
        'jquery',
        'Magento_Ui/js/form/element/select'
    ], function (
    ko,
    $,
    AbstractField
    ) {
        'use strict';

        return AbstractField.extend({
            options: ko.observableArray(),
            defaults: {
                deliverydateConfig: window.checkoutConfig.amasty.deliverydate,
                elementTmpl: 'Amasty_Deliverydate/form/element/select'
            },
            initConfig: function (config) {
                this._super();

                this.abridgeIntervalsSet = config.options.abridge == undefined ? config.options[1] : config.options.abridge;
                this.fullIntervalsSet = config.options.full == undefined ? config.options[0] : config.options.full;
                this.displayed = this.fullIntervalsSet;
            },
            onUpdate: function () {
                this.bubble('update', this.hasChanged());
            },

            initObservable: function () {
                this._super();
                var newOptions = [];
                
                this.options(newOptions);
                this.updateTimeInterval();
                return this;
            },
            updateTimeInterval: function(){

                self = this;
                $.ajax({
                     showLoader: true,
                     url: window.location.origin+'/delivery/delivery/timeinterval',
                     type: 'POST',
                     data : {
                        'date' : window.deliveryDate,
                        'timeZone' : Intl.DateTimeFormat().resolvedOptions().timeZone
                     },
                     dataType: 'json'

                 }).done(function (data) {
                    self.options(data);
                    localStorage.setItem('deliverySlots', JSON.stringify(data));
                 });
            }

        });
    }
);