define([], function () {
    'use strict';    

    return function (BillingAddress) {
        return BillingAddress.extend({
            initialize: function () {
                this._super();

                if (this.addressOptions.length > 1) {
                    this.addressOptions = this.addressOptions.filter(function (address) {
                        return address.customerAddressId !== null;
                    });
                }

                return this;
            }
        });
    }
});