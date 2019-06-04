define(
    [],
    function () {
        'use strict';

        return function (Shipping) {
            return Shipping.extend({
                validateShippingInformation: function () {
                    var validationResult = this._super();
                    var additionalFields = this.getChild('shippingAdditional');
                    if (additionalFields && additionalFields.hasChild('ziffity-pickup-date')) {
                        return validationResult && additionalFields.getChild('ziffity-pickup-date').validate()
                    }
                    return validationResult;
                }
            });
        }
    }
);
