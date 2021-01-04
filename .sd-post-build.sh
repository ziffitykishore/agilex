#!/bin/bash

echo $MAGENTO_CLOUD_APP_DIR/dwi66h66ybkvo_stg

cp -R $MAGENTO_CLOUD_APP_DIR/dwi66h66ybkvo_stg/pub/static/frontend/Travers/default/en_US $MAGENTO_CLOUD_APP_DIR/dwi66h66ybkvo_stg/pub/static/frontend/Travers/default/en_US_Source

$MAGENTO_CLOUD_APP_DIR/dwi66h66ybkvo_stg/.nvm/versions/node/v8.15.1/bin/r.js -o $MAGENTO_CLOUD_APP_DIR/dwi66h66ybkvo_stg/build.js baseUrl="${MAGENTO_CLOUD_APP_DIR}/dwi66h66ybkvo_stg/pub/static/frontend/Travers/default/en_US_Source/" dir="${MAGENTO_CLOUD_APP_DIR}/dwi66h66ybkvo_stg/pub/static/frontend/Travers/default/en_US/"

