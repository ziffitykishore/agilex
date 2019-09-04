 define([
     'jquery',
     'mage/utils/wrapper'
 ], function ($, wrapper) {
     'use strict';

     return function (selectShippingMethodAction) {

         return wrapper.wrap(selectShippingMethodAction, function (originalAction) {
            var settings = {
                method: 'POST',
                dataType: 'json',
                url: '/deliverydates/deliveryinfo/index'
            };
            var deliveryInfo = $.ajax(settings);

            deliveryInfo.done(function (response, textStatus) {
                for (var sku in response.data.deliverydates) {
                    $('.product-item-details[data-sku="'+sku+'"] .deliveryInfo').html(response.data.deliverydates[sku]);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log("Request failed: " + textStatus + ' : ' + errorThrown);
            });

            return originalAction();
         });
     };
 });