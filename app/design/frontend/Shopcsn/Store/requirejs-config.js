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
    }
};

