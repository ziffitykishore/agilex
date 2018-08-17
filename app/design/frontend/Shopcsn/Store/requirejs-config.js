var config = {
    paths: {
            'bminjs' : 'js/bootstrap.min',
            'slick'  : 'js/slick.min',
            'mainjs'   : 'js/main'
    },
    shim: {
        'bminjs': {
            deps: ['jquery']
        },
        'slick' :{
           deps : ['jquery']
       },
       'mainjs' :{
            deps : ['jquery']
        }
    },
    map: {
        '*': {
            'Magento_Checkout/js/sidebar':'js/sidebar',
            'Magento_Checkout/js/view/minicart':'js/minicart',
            'mainjs' : 'js/main'
        }
    } 
};

