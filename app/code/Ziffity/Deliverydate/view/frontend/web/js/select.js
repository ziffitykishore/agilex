define([
        'ko',
        'jquery',
        'Magento_Ui/js/form/element/select',
        'Magento_Theme/js/jstz.min'
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
            onUpdate: function (val) {
                if(val){
                    localStorage.setItem("selectedDeliveryTime",val);
                }
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
                var currentObj = this;
                $.ajax({
                     showLoader: true,
                     url: window.location.origin+'/delivery/delivery/timeinterval',
                     type: 'POST',
                     data : {
                        'date' : window.deliveryDate,
                        'timeZone' : currentObj.getClientLocalTimezone()
                     },
                     dataType: 'json'

                 }).done(function (data) {
                    currentObj.options(data);
                    if(data[0].value){
                        localStorage.setItem('deliverySlots', JSON.stringify(data));
                    }
                    if(localStorage.getItem('saveDeliveryFormData') === 'true'){
                        localStorage.setItem("saveDeliveryFormData",false);
                        setTimeout(function(){
                            $("select[name=amdeliverydate_time]").val(localStorage.getItem('selectedDeliveryTime')).trigger('change');
                            $("textarea[name=amdeliverydate_comment]").val(localStorage.getItem('selectedDeliveryComment')).trigger('change');
                        }, 1000);
                    }
                 });
            },
            getClientLocalTimezone: function () {
                var localTimezone = null;
                if (typeof Intl !== 'undefined') {
                    // use Intl approach
                    localTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                }
                if (!localTimezone) {
                    // use jstz approach in IE browser
                    localTimezone = jstz.determine().name();
                }
                return localTimezone;
            }

        });
    }
);