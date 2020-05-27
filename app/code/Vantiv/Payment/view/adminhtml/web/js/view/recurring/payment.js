/**
 * Copyright © 2018 Vantiv, LLC. All rights reserved.
 * See LICENSE.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'mage/translate',
        'Magento_Ui/js/modal/alert'
    ],
    function ($, $t, alert) {
        'use strict';

        $.widget('mage.eprotect', {
            form: null,
            paymentMethod: null,
            paymentFrame: null,
            vantivRegistrationID: null,
            vantivCCType: null,

            /**
             * Validation creation.
             *
             * @protected
             */
            _create: function () {
                this._initializeElements();

                this.form.on('beforeSubmit', this.submitEprotect.bind(this));

                this.options.submitFlag = false;

                var scriptUrl = this.options.config.scriptUrl;
                delete this.options.config.scriptUrl;

                require([scriptUrl], this.initEprotect.bind(this));

                this.paymentMethodChange();

                this.paymentMethod.change(this.paymentMethodChange.bind(this));
            },

            _initializeElements: function() {
                this.form = $("#edit_form");
                this.paymentMethod = $("select[name='vantiv_subscription_payment']");
                this.paymentFrame = $("#payframe");
                this.vantivRegistrationID = $("#vantiv-paypage-registration-id");
                this.vantivCCType = $("#vantiv-cc-type");
            },

            /**
             * Payment Method change callback
             *
             * @param event
             */
            paymentMethodChange: function() {
                if(this.isNewCard()) {
                    this.paymentFrame.show();
                } else {
                    this.paymentFrame.hide();
                }
            },

            /**
             * Init payframe client.
             */
            initEprotect: function () {
                this.options.config.callback = this.eprotectCallback.bind(this);
                this.payframeClient = new LitlePayframeClient(this.options.config);
            },

            /**
             * Submit eProtect iFrame.
             */
            submitEprotect: function () {
                if(!this.form.validation('isValid')) {
                    return false;
                }

                this.form.trigger("processStart");

                if(!this.isNewCard()) {
                    return true;
                }
                if (!this.options.submitFlag) {
                    this.payframeClient.getPaypageRegistrationId({
                        "id": Math.floor(Math.random() * 999999),
                        "orderId": ""
                    });

                    return false;
                } else {
                    return true;
                }
            },

            /**
             * eProtect submit callback.
             *
             * @param responseData
             */
            eprotectCallback: function (responseData) {
                if (responseData.response == "870") {
                    this.options.submitFlag = true;

                    this.vantivRegistrationID.val(responseData.paypageRegistrationId);
                    this.vantivCCType.val(responseData.type);
                    this.form.submit();
                } else {
                    this.form.trigger("processStop");

                    alert({
                        title: $t('Credit Card Form'),
                        content: $t('An error occurred: ') + $t(responseData.message)
                    });
                }
            },

            /**
             * Determines if user is entering new card data
             *
             * @returns {boolean}
             */
            isNewCard: function() {
                return (this.paymentMethod.val() === "-2");
            }
        });

        return $.mage.eprotect;
    }
);
