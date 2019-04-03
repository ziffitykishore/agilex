var config = {
    map: {
        '*': {
            'Magento_Checkout/template/shipping-address/list.html':
                'SomethingDigital_CheckoutAddress/template/shipping-address/list.html',
            'Magento_NegotiableQuote/template/shipping-address/list.html':
                'SomethingDigital_CheckoutAddress/template/shipping-address/list.html',
            'Magento_NegotiableQuote/template/billing-address/list.html':
                'SomethingDigital_CheckoutAddress/template/billing-address/list.html',
            'Magento_Checkout/js/model/shipping-save-processor/default':
                'SomethingDigital_CheckoutAddress/js/model/shipping-save-processor/default'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping-address/list': {
                'SomethingDigital_CheckoutAddress/js/view/shipping-address/list-mixin': true
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'SomethingDigital_CheckoutAddress/js/view/shipping-address/address-renderer/default-mixin': true
            },
            'Magento_Customer/js/model/customer/address': {
                'SomethingDigital_CheckoutAddress/js/model/customer/address-mixin': true
            }
        }
    }
};