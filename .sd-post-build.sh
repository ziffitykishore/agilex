#!/bin/bash

echo $MAGENTO_CLOUD_APP_DIR/$USER

cp -R $MAGENTO_CLOUD_APP_DIR/$USER/pub/static/frontend/Travers/default/en_US $MAGENTO_CLOUD_APP_DIR/$USER/pub/static/frontend/Travers/default/en_US_Source

$MAGENTO_CLOUD_APP_DIR/$USER/.nvm/versions/node/v8.15.1/bin/r.js -o $MAGENTO_CLOUD_APP_DIR/$USER/build.js baseUrl="${MAGENTO_CLOUD_APP_DIR}/${USER}/pub/static/frontend/Travers/default/en_US_Source/" dir="${MAGENTO_CLOUD_APP_DIR}/${USER}/pub/static/frontend/Travers/default/en_US/"

