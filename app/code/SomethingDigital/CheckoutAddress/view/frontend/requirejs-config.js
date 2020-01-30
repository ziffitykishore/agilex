var config = {
    map: {
        '*': {
            'Magento_Checkout/template/shipping-address/list.html':
                'SomethingDigital_CheckoutAddress/template/shipping-address/list.html',
            'Magento_NegotiableQuote/template/shipping-address/list.html':
                'SomethingDigital_CheckoutAddress/template/shipping-address/list.html',
            'Magento_NegotiableQuote/template/shipping-address/address-renderer/default.html':
                'SomethingDigital_CheckoutAddress/template/shipping-address/address-renderer/default.html',
            'Magento_Checkout/template/billing-address/list.html':
                'SomethingDigital_CheckoutAddress/template/billing-address/list.html',
            'Magento_Checkout/template/billing-address/details.html':
                'SomethingDigital_CheckoutAddress/template/billing-address/details.html',
            'Magento_Checkout/template/billing-address.html':
                'SomethingDigital_CheckoutAddress/template/billing-address.html',
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
            },
            'Magento_Checkout/js/view/billing-address': {
                'SomethingDigital_CheckoutAddress/js/view/billing-address-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'SomethingDigital_CheckoutAddress/js/action/create-billing-address-mixin': true
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'SomethingDigital_CheckoutAddress/js/model/checkout-data-resolver-mixin': true
            },
            'Magento_Checkout/js/view/billing-address/list': {
                'SomethingDigital_CheckoutAddress/js/view/billing-address/list-mixin': true
            },
            'Magento_Checkout/js/model/new-customer-address': {
                'SomethingDigital_CheckoutAddress/js/model/new-customer-address-mixin': true
            }
        }
    }
};