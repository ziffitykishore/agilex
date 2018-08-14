var config = {
    paths: {
            'bminjs' : 'js/bootstrap.min',
            'jmin'   : 'jquery-1.11.0.min',
            'slick'  : 'slick.min'
    },
    shim: {
        'bminjs': {
            deps: ['jquery']
        },
        'jmin'  :{
            deps: ['jquery']
        },
        'slick' :{
           deps : ['jquery']
       }
    },
    map: {
        '*': {
            'Magento_Checkout/js/sidebar':'js/sidebar',
            'Magento_Checkout/js/view/minicart':'js/minicart',
            
        }
    } 
};

