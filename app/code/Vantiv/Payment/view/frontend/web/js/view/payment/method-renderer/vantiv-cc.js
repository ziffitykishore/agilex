/**
 * Copyright © 2018 Vantiv, LLC. All rights reserved.
 * See LICENSE.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'mage/translate',
        'Magento_Vault/js/view/payment/vault-enabler',
        'Magento_Ui/js/modal/alert'
    ],
    function ($, Component, Quote, $t, VaultEnabler, alert) {
        'use strict';

        /**
         * eProtect response data.
         */
        var responseData = false;

        /**
         * eProtect service response successful code.
         */
        var eprotectSuccessCode = "870";

        return Component.extend({
            defaults: {
                template: 'Vantiv_Payment/payment/vantiv-cc',
                timeoutMessage: $t('Sorry, but something went wrong. Please contact the seller.'),
                "additional_data": {}
            },

            initialize: function() {
                this._super();

                this.vaultEnabler = new VaultEnabler();
                this.vaultEnabler.setPaymentCode(this.getVaultCode());

                return this;
            },

            /**
             * Get eProtect configuration.
             */
            getConfig: function () {
                var config = window.checkoutConfig.payment[this.getCode()];
                return config.eprotect;
            },

            getData: function () {
                var data = {
                    "method": this.item.method
                };
                if (this.responseData) {
                    data.additional_data = {
                        "paypage_registration_id": this.responseData.paypageRegistrationId,
                        "type": this.responseData.type,
                        "last_four": this.responseData.lastFour,
                        "exp_month": this.responseData.expMonth,
                        "exp_year": this.responseData.expYear,
                    };

                    this.vaultEnabler.visitAdditionalData(data);
                }
                return data;
            },

            /**
             * Check if vault is enabled.
             *
             * @returns {Bool}
             */
            isVaultEnabled: function () {
                return this.vaultEnabler.isVaultEnabled();
            },

            /**
             * Get vault code.
             *
             * @returns {String}
             */
            getVaultCode: function () {
                return window.checkoutConfig.payment[this.getCode()].vault_code;
            },

            /**
             * Initialize eProtect Client.
             */
            initEprotect: function () {
                var scriptUrl = window.checkoutConfig.payment[this.getCode()].script_url;

                require([scriptUrl], this.initClient.bind(this));
            },

            /**
             * Init payframe client.
             */
            initClient: function() {
                var config = this.getConfig();
                config.callback = this.eprotectResponseHandler.bind(this);
                this.client = new LitlePayframeClient(config);
            },

            /**
             * Submit CC data from IFrame.
             *
             * @returns {boolean}
             */
            submitEprotect: function () {
                var startTime = new Date().getTime();
                this.client.getPaypageRegistrationId({
                    "id": Math.floor(Math.random() * 999999),
                    "orderId": ""
                });

                return false;
            },

            /**
             * eProtect Response Handler callback.
             *
             * @param responseData
             */
            eprotectResponseHandler: function (responseData) {
                if (responseData.response == eprotectSuccessCode) {
                    this.responseData = responseData;
                    this.placeOrder();
                } else {
                    alert({
                        title: $t('Credit and debit card payment method.'),
                        content: $t('An error occurred') + ': ' + $t(responseData.message)
                    });
                }
            }
        });
    }
);
