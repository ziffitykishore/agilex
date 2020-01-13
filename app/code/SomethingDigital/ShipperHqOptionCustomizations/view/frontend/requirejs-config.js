var config = {
    map: {
        '*': {
            'ShipperHQ_Option/template/checkout/shipping/shipperhq-option.html':
                'SomethingDigital_ShipperHqOptionCustomizations/template/checkout/shipping/shipperhq-option.html'
        }
    },
    config: {
        mixins: {
            'ShipperHQ_Option/js/view/checkout/shipping/shipperhq-option': {
                'SomethingDigital_ShipperHqOptionCustomizations/js/view/checkout/shipping/shipperhq-option-mixin': true
            }
        } 
    }
};