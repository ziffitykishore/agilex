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
                pickupdateConfig: window.checkoutConfig.ziffity.pickupdate,
                elementTmpl: 'Ziffity_Pickupdate/form/element/select'
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
                     url: window.location.origin+'/pickupOR/pickup/timeinterval',
                     type: 'POST',
                     data : {
                        'date' : window.pickupDate,
                        'timeZone' : Intl.DateTimeFormat().resolvedOptions().timeZone
                     },
                     dataType: 'json'

                 }).done(function (data) {
                    self.options(data);

                 });
            }

        });
    }
);