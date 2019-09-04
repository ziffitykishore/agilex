var config = {
    map: {
        '*': {
            'Magento_Checkout/template/summary/item/details.html':
                'SomethingDigital_ShipperHqCustomizations/template/summary/item/details.html'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/select-shipping-method': {
                'SomethingDigital_ShipperHqCustomizations/js/action/select-shipping-method-mixin': true
            }
        } 
    }
};