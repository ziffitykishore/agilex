var config = {
    paths: {
        'left-sticky': 'js/sticky',
        'fancybox': 'js/fancybox'
    },
    shim: {
        'slick': {
            deps: ['jquery']
        },
        'left-sticky': {
            deps: ['jquery']
        },
        'fancybox': {
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
