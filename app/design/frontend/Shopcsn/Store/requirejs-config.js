var config = {
    paths: {
        'bminjs': 'js/bootstrap.min',
        'slick': 'js/slick.min',
        'left-sticky': 'js/sticky',
        'fancybox': 'js/fancybox'
    },
    shim: {
        'bminjs': {
            deps: ['jquery']
        },
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
