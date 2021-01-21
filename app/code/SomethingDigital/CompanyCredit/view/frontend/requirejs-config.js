var config = {
    map: {
        '*': {
            'Magento_CompanyCredit/template/payment/companycredit-form.html':
                'SomethingDigital_CompanyCredit/template/payment/companycredit-form.html',
           
            }
    },
    config: {
        mixins: {
            'Magento_CompanyCredit/js/view/payment/method-renderer/companycredit': {
                'SomethingDigital_CompanyCredit/js/view/payment/method-renderer/companycredit-mixin': true
            }
        } 
    }
};