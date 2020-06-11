define([
    'jquery',
    'mage/utils/wrapper',
    'Earthlite_OrderComments/js/model/ObserveData'
], function ($, wrapper, ObserveData) {
    'use strict';
        
    return function (payloadExtender) {        
        
        return wrapper.wrap(payloadExtender, function (originalAction, payload) {
            
            payload = originalAction(payload);
            var orderComments = $('[name="custom_attributes[order_comments]"]').val();
            
            ObserveData.setComment(orderComments);

            payload.addressInformation.extension_attributes.order_comments = orderComments;
            return payload;
        });
    };
});
