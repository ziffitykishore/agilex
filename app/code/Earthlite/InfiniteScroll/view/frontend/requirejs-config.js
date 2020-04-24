var config = {
    map: {
        '*': {
            infinitescroll: 'Earthlite_InfiniteScroll/js/infinitescroll'
        }
    },
    paths: {
        'infiniteAjaxScroll': 'Earthlite_InfiniteScroll/js/jquery-ias.min'
    },
    shim : {
        infinitescroll: {
            deps: ['jquery']
        },
        infiniteAjaxScroll: {
            deps: ['jquery']
        }
    }
};
