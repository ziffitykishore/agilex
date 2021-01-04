#!/bin/bash

mv ${MAGENTO_CLOUD_APP_DIR}/pub/static/frontend/Travers/default/en_US ${MAGENTO_CLOUD_APP_DIR}/pub/static/frontend/Travers/default/en_US_Source

${MAGENTO_CLOUD_APP_DIR}/.nvm/versions/node/v8.15.1/bin/r.js -o ${MAGENTO_CLOUD_APP_DIR}/build.js baseUrl="${MAGENTO_CLOUD_APP_DIR}/pub/static/frontend/Travers/default/en_US_Source/" dir="${MAGENTO_CLOUD_APP_DIR}/pub/static/frontend/Travers/default/en_US/"

