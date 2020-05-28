/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [
        'Magento_Theme/js/custom'
    ],
    map: {
        '*': {
            'mgsaos': 'Magento_Theme/js/aos',
            'mmenu': 'js/mmenu'
        }
    },
    'shim': {
        'mgsaos': ['jquery']
    }
};
