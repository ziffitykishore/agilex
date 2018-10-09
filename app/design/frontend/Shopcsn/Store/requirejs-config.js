var config = {
    paths: {
        'bminjs': 'js/bootstrap.min',
        'slick': 'js/slick.min',
        'left-sticky': 'js/sticky',
        'googlecap': '//www.google.com/recaptcha/api.js?onload=recaptchaOnload&render=explicit'
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
        }
    },
    map: {
        '*': {
            'Magento_Checkout/js/sidebar':'js/sidebar',
            'Magento_Checkout/js/view/minicart':'js/minicart'
        }
    }
};
