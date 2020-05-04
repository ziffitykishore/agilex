define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (payloadExtender) {
        return wrapper.wrap(payloadExtender, function (originalAction, payload) {
            payload = originalAction(payload);
            var orderComments = $('[name="custom_attributes[order_comments]"]').val();
            payload.addressInformation.extension_attributes.order_comments = orderComments;
            return payload;
        });
    };
});
