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
            },
            hasBilling: function() {
                var hasBilling = addressList().some(function (addrs) {
                    if (addrs.customAttributes !== undefined && addrs.customAttributes.hasOwnProperty('is_billing')) {
                        if (addrs.customAttributes.is_billing.value == 1) {
                            return true;
                        }
                    }
                });
                if (hasBilling) {
                    return true;
                }
                else {
                    return false;
                }
            }
        });
    }
});