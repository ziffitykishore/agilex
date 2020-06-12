/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/view/summary',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar',
    'Earthlite_OrderComments/js/model/ObserveData'
], function ($, ko, Component, selectShippingAddressAction, quote, formPopUpState, checkoutData, customerData, stepNavigator, sidebarModel, ObserveData) {
    'use strict';

    var configValues = window.checkoutConfig;               


    return Component.extend({

        defaults: {
            template: 'Earthlite_OrderComments/checkout/sidebar/shipping-option-information'
        },


         isVisible: function () {
            return stepNavigator.isProcessed('shipping');
        },

        isVisibleSidebar: function (){
            return stepNavigator.getActiveItemIndex();
        },

        initObservable: function () {
            this._super();
            return this;
        },

        orderUpdatedComments: function () {        	
                        
            return ObserveData.getComment();
            
        },

        backToShippingMethod: function () {
            sidebarModel.hide();
            stepNavigator.navigateTo('shipping', 'opc-shipping_method');
        }        

    });
});