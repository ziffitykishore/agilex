define([
    'Magento_Customer/js/model/address-list'
],
function (
    addressList
) {
    'use strict';    

    var addressOptions = addressList().filter(function (address) {
        return address.getType() === 'customer-address';
    });

    return function (BillingAddress) {
        return BillingAddress.extend({
            addressOptions: addressOptions,
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