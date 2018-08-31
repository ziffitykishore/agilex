var config = {
    paths: {
        'bminjs': 'js/bootstrap.min',
        'slick': 'js/slick.min',
        'googlecap': 'https://www.google.com/recaptcha/api'
    },
    shim: {
        'bminjs': {
            deps: ['jquery']
        },
        'slick': {
            deps: ['jquery']
        }
    },
    map: {
        '*': {
            'Magento_Checkout/js/sidebar':'js/sidebar',
            'Magento_Checkout/js/view/minicart':'js/minicart'
        }
    }
};