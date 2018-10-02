var config = {
    paths: {
        'bminjs': 'js/bootstrap.min',
        'googlecap': '//www.google.com/recaptcha/api.js?onload=recaptchaOnload&render=explicit'
    },
    shim: {
        'bminjs': {
            deps: ['jquery']
        }
    }
};
