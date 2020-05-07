define([
    'jquery',
    'ko',
    'Magento_Customer/js/model/customer'
], function($, ko, customer) {
    'use strict';

    var mixin = {
        isActive: function () {
            if(customer.isLoggedIn()) {
                $('body').addClass('loggedin');
            } else {
                $('body').removeClass('loggedin');
            }
            return !customer.isLoggedIn();
        }
    };

    return function(target) {
        return target.extend(mixin);
    }
});
