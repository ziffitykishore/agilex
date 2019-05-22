var config = {
    "map": {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default" : "Amasty_Deliverydate/js/shipping-save-processor/default-override"
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Amasty_Deliverydate/js/checkout/shipping-mixin': true
            }
        }
    }
};