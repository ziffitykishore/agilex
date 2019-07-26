define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'ko',
        'uiRegistry',
        'Magento_Checkout/js/model/shipping-service'
    ],
    function($, Component, quote, ko, registry, shippingService) {
        'use strict';

        function visibilityFields(currentMethod, configMethods, field) {
            if(field) {
                if ($.inArray(currentMethod, configMethods) != -1) {
                    field.visible(true);
                } else {
                    field.visible(false);
                }
            }
        }
        
        return Component.extend({
            defaults: {
                formSelector: '#checkout-step-shipping_method button',
                template: 'Ziffity_Pickupdate/pickupdate',
                pickupdateConfig: window.checkoutConfig.ziffity.pickupdate,
                modules: {
                    pickupdateDate: '${ $.name }.pickupdate_date',
                    pickupdateTime: '${ $.name }.pickupdate_time',
                    pickupdateComment: '${ $.name }.pickupdate_comment'
                },
                listens: {
                    '${ $.name }.pickupdate_date:value': 'onChangeDate'
                }
            },
            onChangeDate: function (val){
                if (val) {
                    var mageCache = localStorage.getItem('mage-cache-storage');
                    var mageCacheToJson = JSON.parse(mageCache);
                    var shippingData = mageCacheToJson['checkout-data']['shippingAddressFromData'];
                    var billingData = mageCacheToJson['checkout-data']['billingAddressFromData'];
                    if (shippingData !== null) {
                        localStorage.setItem("selectedPickupDate", shippingData['pickupdate_date']);
                    } else {
                        localStorage.setItem("selectedPickupDate", billingData['pickupdate_date']);
                    }
                }
                if (this.pickupdateConfig.moduleEnabled) {
                    quote.ziffityPickupDateDate = val;
                    if (this.pickupdateTime()) {
                        if (val) {
                            this.pickupdateTime().disabled(false);
                        } else {
                            this.pickupdateTime().disabled("disabled");
                        }

                        var options = (this.pickupdateDate().currentDate === val) ?
                            this.pickupdateTime().abridgeIntervalsSet :
                            this.pickupdateTime().fullIntervalsSet;

                        var tintervals = this.pickupdateConfig.restrictTinterval;
                        this.pickupdateTime().value('');
                        var date = new Date(val);
                        var values = [date.getDate(), date.getMonth() + 1];
                        for (var id in values) {
                            values[id] = values[id].toString().replace( /^([0-9])$/, '0$1' );
                        }
                        val = date.getFullYear() + '-' + values[1] + '-' + values[0];
                        $.each(options, function (i, obj) {
                            obj.disabled = tintervals[val] && $.inArray(obj.value, tintervals[val]) != -1;
                        }.bind(this));
                        this.pickupdateTime().displayed = options;
                        this.pickupdateTime().initObservable();
                    }
                }
            },

            initialize: function () {
                _.bindAll(
                    this,
                    'initShipping'
                );

                this._super();
                if(this.pickupdateConfig.moduleEnabled) {
                    quote.shippingMethod.subscribe(function(method) {
                        var currentShippingMethod = '';
                        if (method) {
                            currentShippingMethod = method['carrier_code'] + '_' + method['method_code'];
                        }

                        if(this.pickupdateConfig.dateEnabledCarriers) {
                            visibilityFields(currentShippingMethod,
                                this.pickupdateConfig.dateShippingMethods,
                                this.pickupdateDate()
                            );
                        }

                        if (this.pickupdateConfig.timeEnabledCarriers) {
                            visibilityFields(currentShippingMethod,
                                this.pickupdateConfig.timeShippingMethods,
                                this.pickupdateTime()
                            );
                        }

                        if(this.pickupdateConfig.commentEnabledCarriers) {
                            visibilityFields(currentShippingMethod,
                                this.pickupdateConfig.commentShippingMethods,
                                this.pickupdateComment()
                            );
                        }
                    }.bind(this));

                    var subscription = shippingService.isLoading.subscribe(function (isLoading) {
                        if (isLoading === false && this.pickupdateTime()) {
                            this.pickupdateTime().value(window.checkoutConfig.ziffity.pickupdate.defaultTime);
                            subscription.dispose();
                        }
                    }.bind(this));

                    $.async(this.formSelector, this.initShipping);

                    registry.async(this.name + '.pickupdate_time')(
                        function () {
                            if (this.pickupdateDate()) {
                                this.onChangeDate(this.pickupdateDate().value());
                            }
                        }.bind(this)
                    );
                }

                return this;
            },

            initShipping: function (ctx) {
                $(this.formSelector).on("click", function () {
                    return this.validate();
                }.bind(this));
            },

            validate: function () {
                var allFieldsValid = true,
                    notValidField = null;

                if (this.pickupdateDate() && !this.pickupdateDate().validate().valid) {
                    notValidField = this.pickupdateDate().validate().target;
                    allFieldsValid = false;
                }

                if (this.pickupdateTime() && !this.pickupdateTime().validate().valid) {
                    notValidField = this.pickupdateTime().validate().target;
                    allFieldsValid = false;
                }

                if (this.pickupdateComment() && !this.pickupdateComment().validate().valid) {
                    notValidField = this.pickupdateComment().validate().target;
                    allFieldsValid = false;
                }

                if (!allFieldsValid) {
                    notValidField.focused(true);
                }

                return allFieldsValid;
            },

            showGeneralComment: function() {
                return this.pickupdateConfig.generalComment;
            },

            isModuleEnabled: function() {
                //To enable proceed to checkout button
                $("button[data-role='proceed-to-checkout']").removeAttr("disabled");
                return this.pickupdateConfig.moduleEnabled;
            },

            styleMagentoNotice: function () {
                return this.pickupdateConfig.generalCommentStyle == 'notice';
            },
                      
            setPickup: function () {
                $.cookie("is_pickup", true);
            },
            
            setDelivery: function () {
                $.cookie("is_pickup", false);
            }
        });
    }
);
