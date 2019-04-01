var config = {
    map: {
        '*': {
            'Magento_Checkout/template/shipping-address/list.html':
                'SomethingDigital_CheckoutAddress/template/shipping-address/list.html',
            'Magento_NegotiableQuote/template/shipping-address/list.html':
                'SomethingDigital_CheckoutAddress/template/shipping-address/list.html',
            'Magento_Checkout/template/billing-address.html':
                'SomethingDigital_CheckoutAddress/template/billing-address.html',
            'Magento_Checkout/js/view/billing-address':
                'SomethingDigital_CheckoutAddress/js/view/billing-address',
            'Magento_Checkout/js/model/shipping-save-processor/default':
                'SomethingDigital_CheckoutAddress/js/model/shipping-save-processor/default',
            'Magento_Checkout/js/model/checkout-data-resolver':
                'SomethingDigital_CheckoutAddress/js/model/checkout-data-resolver'
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
        }
    }
};