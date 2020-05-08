var config = {
    map: {
        '*': {
            'Magento_Checkout/js/view/shipping-address/list':'Earthlite_Checkout/js/view/shipping-address/list',
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': 'Earthlite_Checkout/js/view/shipping-address/address-renderer/default',
            'Magento_Checkout/template/shipping-address/address-renderer/default.html': 'Earthlite_Checkout/template/shipping-address/address-renderer/default.html',
            'Magento_Checkout/template/shipping-address/list.html': 'Earthlite_Checkout/template/shipping-address/list.html',
            'Magento_Checkout/template/shipping.html': 'Earthlite_Checkout/template/shipping.html',
            'Magento_Checkout/js/view/shipping': 'Earthlite_Checkout/js/view/shipping',
            'Magento_Checkout/template/onepage.html': 'Earthlite_Checkout/template/onepage.html',
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/payment': {
                'Earthlite_Checkout/js/view/payment-mixin': true
            },
            'Magento_Checkout/js/view/authentication': {
                'Earthlite_Checkout/js/view/authentication-mixin': true
            },
        }
    }
};
