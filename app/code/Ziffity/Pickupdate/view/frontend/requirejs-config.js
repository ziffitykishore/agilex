var config = {
    "map": {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default" : "Ziffity_Pickupdate/js/shipping-save-processor/default-override"
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Ziffity_Pickupdate/js/checkout/shipping-mixin': true
            }
        }
    }
};